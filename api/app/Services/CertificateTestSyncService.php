<?php

namespace App\Services;

use App\Models\Certificado;
use App\Models\TestType;
use Illuminate\Support\Facades\DB;

/**
 * Syncs certificate_tests rows from the variables JSON blob.
 *
 * The form stores test results as variables.resultado_vt, variables.resultado_bend, etc.
 * PDF templates read from cert->tests (CertificateTest model, resolved by TestType.code).
 * This service bridges the two on every store/update.
 */
class CertificateTestSyncService
{
    // variable key → TestType code
    private const VAR_MAP = [
        'resultado_vt'              => 'VT',
        'resultado_bend'            => 'BT-R',
        'resultado_rt'              => 'RT',
        'resultado_nick_break'      => 'NB',
        'resultado_fractura_filete' => 'BT-F',
        'resultado_macro'           => 'MC',
    ];

    public function sync(Certificado $cert): void
    {
        $vars = $cert->variables ?? [];
        if (empty($vars)) {
            return;
        }

        // Effective norma: use parent for NAG-style delegating norms
        $normaId = $cert->norma?->parent_norma_id ?? $cert->norma_id;

        $typeIds = $this->resolveTypeIds($normaId);

        // Delete existing then re-insert — simpler than upsert given unique constraint
        $cert->tests()->delete();

        $rows = [];
        $now  = now();

        foreach (self::VAR_MAP as $varKey => $code) {
            $raw = $vars[$varKey] ?? null;
            if ($raw === null || $raw === '') {
                continue;
            }

            $testTypeId = $typeIds[$code] ?? null;
            if (!$testTypeId) {
                continue;
            }

            $resultado = match (strtolower((string) $raw)) {
                'na', 'n/a' => 'na',
                'rechazado' => 'rechazado',
                default     => 'aprobado',
            };

            $rows[] = [
                'certificado_id' => $cert->id,
                'test_type_id'   => $testTypeId,
                'resultado'      => $resultado,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        if (!empty($rows)) {
            DB::table('certificate_tests')->insert($rows);
        }
    }

    /**
     * Builds a code→id map for the relevant test types.
     * Prefers rows matching the effective norma_id; falls back to any norma that has the code.
     */
    private function resolveTypeIds(int $normaId): array
    {
        $rows = TestType::whereIn('code', array_values(self::VAR_MAP))
            ->orderByRaw('CASE WHEN norma_id = ? THEN 0 ELSE 1 END', [$normaId])
            ->get(['id', 'code']);

        $map = [];
        foreach ($rows as $row) {
            // First occurrence wins (norma match comes first due to ORDER BY)
            $map[$row->code] ??= $row->id;
        }

        return $map;
    }
}
