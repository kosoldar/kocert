<?php

namespace App\Services;

use App\Models\Certificado;
use App\Models\CertificateRange;
use App\Models\DiameterRule;
use App\Models\Norma;
use App\Models\Posicion;
use App\Models\ThicknessRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RangeCalculatorService
{
    const VERSION = 1;

    /**
     * Calcula y persiste los rangos calificados de un certificado.
     * Retorna las filas de certificate_ranges creadas.
     */
    public function calculate(Certificado $cert): Collection
    {
        $vars      = $cert->variables ?? [];
        $thickness = isset($vars['espesor_cupon']) ? (float) $vars['espesor_cupon'] : 0.0;
        $diameter  = isset($vars['diametro_cupon']) ? (float) $vars['diametro_cupon'] : null;
        $layers    = (int) ($vars['num_pasadas'] ?? 0);
        $coupon    = $cert->tipo_cupon; // 'chapa' | 'caño'

        // Borra rangos anteriores del mismo certificado
        $cert->ranges()->delete();

        $created = collect();

        // ── Rango de espesor ─────────────────────────────────────────────────
        if ($thickness > 0) {
            $r = $this->thicknessRange($cert->norma_id, $coupon, $thickness, $layers);
            if ($r) {
                $created->push($cert->ranges()->create(array_merge(['type' => 'espesor'], $r)));
            }
        }

        // ── Rango de diámetro (solo caño) ────────────────────────────────────
        if ($coupon === 'caño' && $diameter !== null && $diameter > 0) {
            $r = $this->diameterRange($cert->norma_id, $diameter);
            if ($r) {
                $created->push($cert->ranges()->create(array_merge(['type' => 'diametro'], $r)));
            }
        }

        // ── Posiciones calificadas ────────────────────────────────────────────
        $posRanges = $this->positionRanges($cert->norma_id, $cert->posicion);
        foreach ($posRanges as $r) {
            $created->push($cert->ranges()->create(array_merge(['type' => 'posicion'], $r)));
        }

        // ── Grupo metal base calificado ───────────────────────────────────────
        $pNumber = $vars['p_number'] ?? $vars['grupo_base_metal'] ?? null;
        if ($pNumber) {
            $r = $this->baseMetalGroupRange($cert->norma_id, $pNumber);
            if ($r) {
                $created->push($cert->ranges()->create(array_merge(['type' => 'grupo_base_metal'], $r)));
            }
        }

        // ── Grupo consumible calificado ───────────────────────────────────────
        $fNumber = $vars['f_number'] ?? $vars['grupo_consumible'] ?? null;
        if ($fNumber) {
            $r = $this->fillerGroupRange($cert->norma_id, $fNumber);
            if ($r) {
                $created->push($cert->ranges()->create(array_merge(['type' => 'grupo_consumible'], $r)));
            }
        }

        $cert->update([
            'ranges_calculated_at' => now(),
            'rules_version'        => self::VERSION,
        ]);

        return $created;
    }

    // ── Espesor ──────────────────────────────────────────────────────────────

    private function thicknessRange(int $normaId, string $coupon, float $t, int $layers): ?array
    {
        $effective = $this->effectiveNorma($normaId);
        $lookupId  = $effective?->id ?? $normaId;

        $rules = ThicknessRule::where('norma_id', $lookupId)
            ->where(function ($q) use ($coupon) {
                $q->where('coupon_type', $coupon)->orWhere('coupon_type', 'ambos');
            })
            ->where('thickness_from_mm', '<=', $t)
            ->where(function ($q) use ($t) {
                $q->whereNull('thickness_to_mm')->orWhere('thickness_to_mm', '>=', $t);
            })
            ->get();

        $best = null;

        foreach ($rules as $rule) {
            // Descarta regla si requiere más capas de las disponibles
            if ($rule->min_layers && $rule->min_layers > $layers) {
                continue;
            }
            if (! $best || $this->rank($rule->qualifies_max_formula) > $this->rank($best->qualifies_max_formula)) {
                $best = $rule;
            }
        }

        if (! $best) {
            return null;
        }

        $min = $best->qualifies_min_mm ?? $t;
        $max = $this->resolveFormula($best->qualifies_max_formula, $t);
        $desc = $this->thicknessDesc($min, $max);

        return ['min_value' => $min, 'max_value' => $max, 'descripcion' => $desc];
    }

    // ── Diámetro ─────────────────────────────────────────────────────────────

    private function diameterRange(int $normaId, float $d): ?array
    {
        $effective = $this->effectiveNorma($normaId);
        $lookupId  = $effective?->id ?? $normaId;

        $rule = DiameterRule::where('norma_id', $lookupId)
            ->where('coupon_type', 'caño')
            ->where('diameter_from_mm', '<=', $d)
            ->where(function ($q) use ($d) {
                $q->whereNull('diameter_to_mm')->orWhere('diameter_to_mm', '>=', $d);
            })
            ->orderByDesc('diameter_from_mm')
            ->first();

        if (! $rule) {
            return null;
        }

        $min  = $rule->qualifies_min_mm ?? $d;
        $max  = $this->resolveFormula($rule->qualifies_max_formula, $d);
        $desc = $this->diameterDesc($min, $max);

        return ['min_value' => $min, 'max_value' => $max, 'descripcion' => $desc];
    }

    // ── Posiciones ───────────────────────────────────────────────────────────

    private function positionRanges(int $normaId, string $posicionCodigo): array
    {
        $posicion = Posicion::where('codigo', $posicionCodigo)->first();
        if (! $posicion) {
            return [];
        }

        $effective = $this->effectiveNorma($normaId);
        $lookupId  = $effective?->id ?? $normaId;

        $rows = DB::table('qualified_positions as qp')
            ->join('posiciones as p', 'p.id', '=', 'qp.qualified_posicion_id')
            ->where('qp.norma_id', $lookupId)
            ->where('qp.tested_posicion_id', $posicion->id)
            ->select('p.codigo', 'p.descripcion', 'qp.joint_type')
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $ranura = $rows->where('joint_type', 'ranura')->pluck('codigo')->sort()->join(', ');
        $filete = $rows->where('joint_type', 'filete')->pluck('codigo')->sort()->join(', ');

        $results = [];

        if ($ranura) {
            $results[] = [
                'min_value'   => null,
                'max_value'   => null,
                'descripcion' => "Ranura: {$ranura}",
            ];
        }

        if ($filete) {
            $results[] = [
                'min_value'   => null,
                'max_value'   => null,
                'descripcion' => "Filete: {$filete}",
            ];
        }

        return $results;
    }

    // ── Grupo metal base ─────────────────────────────────────────────────────

    private function baseMetalGroupRange(int $normaId, string $pNumber): ?array
    {
        $effective = $this->effectiveNorma($normaId);
        if (!$effective) return null;

        $probadoId = DB::table('grupos_base_metal')
            ->where('norma_id', $effective->id)
            ->where('codigo', $pNumber)
            ->value('id');

        if (!$probadoId) return null;

        $calificados = DB::table('grupo_base_metal_calificado as gbc')
            ->join('grupos_base_metal as cal', 'cal.id', '=', 'gbc.calificado_id')
            ->where('gbc.probado_id', $probadoId)
            ->orderBy('cal.orden')
            ->pluck('cal.codigo')
            ->toArray();

        if (empty($calificados)) return null;

        return ['min_value' => null, 'max_value' => null, 'descripcion' => implode(', ', $calificados)];
    }

    // ── Grupo consumible ─────────────────────────────────────────────────────

    private function fillerGroupRange(int $normaId, string $fNumber): ?array
    {
        $effective = $this->effectiveNorma($normaId);
        if (!$effective) return null;

        $probadoId = DB::table('grupos_consumible')
            ->where('norma_id', $effective->id)
            ->where('codigo', $fNumber)
            ->value('id');

        if (!$probadoId) return null;

        $calificados = DB::table('grupo_consumible_calificado as gcc')
            ->join('grupos_consumible as cal', 'cal.id', '=', 'gcc.calificado_id')
            ->where('gcc.probado_id', $probadoId)
            ->orderBy('cal.orden')
            ->pluck('cal.codigo')
            ->toArray();

        if (empty($calificados)) return null;

        return ['min_value' => null, 'max_value' => null, 'descripcion' => implode(', ', $calificados)];
    }

    private function effectiveNorma(int $normaId): ?Norma
    {
        $norma = Norma::find($normaId);
        return $norma ? ($norma->parent ?? $norma) : null;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function resolveFormula(string $formula, float $t): ?float
    {
        if ($formula === 'unlimited') {
            return null;
        }
        if ($formula === '2t') {
            return round(2 * $t, 2);
        }
        if (is_numeric($formula)) {
            return (float) $formula;
        }
        // max(X,Yt)  e.g. max(3.9,1.5t) | max(19.0,1.5t)
        if (preg_match('/^max\((\d+\.?\d*),([\d.]+)t\)$/', $formula, $m)) {
            return round(max((float) $m[1], (float) $m[2] * $t), 2);
        }

        return null;
    }

    private function rank(string $formula): int
    {
        if ($formula === 'unlimited') {
            return 100;
        }
        if (str_starts_with($formula, 'max(')) {
            return 50;
        }
        if ($formula === '2t') {
            return 30;
        }

        return 10; // valor fijo numérico
    }

    private function thicknessDesc(float $min, ?float $max): string
    {
        $minStr = number_format($min, 1, '.', '') . ' mm';
        $maxStr = $max === null ? 'ilimitado' : number_format($max, 1, '.', '') . ' mm';

        return "{$minStr} a {$maxStr}";
    }

    private function diameterDesc(float $min, ?float $max): string
    {
        $minStr = number_format($min, 1, '.', '') . ' mm';
        $maxStr = $max === null ? 'ilimitado' : number_format($max, 1, '.', '') . ' mm';

        return "Ø {$minStr} a Ø {$maxStr}";
    }
}
