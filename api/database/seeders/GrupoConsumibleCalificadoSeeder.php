<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoConsumibleCalificadoSeeder extends Seeder
{
    public function run(): void
    {
        $gc = fn(string $norma, string $codigo) => DB::table('grupos_consumible')
            ->join('normas', 'normas.id', '=', 'grupos_consumible.norma_id')
            ->where('normas.nombre', $norma)
            ->where('grupos_consumible.codigo', $codigo)
            ->value('grupos_consumible.id');

        // Wipe existing rows so re-seeding is idempotent (insertOrIgnore won't remove changed rows).
        DB::table('grupo_consumible_calificado')->delete();

        $rows = [];
        $link = function (int|null $probadoId, int|null $calificadoId) use (&$rows): void {
            if ($probadoId && $calificadoId) {
                $rows[] = ['probado_id' => $probadoId, 'calificado_id' => $calificadoId];
            }
        };

        // ── ASME IX (QW-433) ─────────────────────────────────────────────────
        // Upward qualification (con respaldo): F-No.1 califica 1-5; F-No.4 califica 4-5.
        // Regla ASME IX: F-número más bajo = más amplia cobertura (≠ AWS D1.1).
        // F-5 (inox) y F-6 (alambre) califica solo self.
        $aF = fn(string $c) => $gc('ASME IX', $c);

        $link($aF('F-No. 1'), $aF('F-No. 1'));
        $link($aF('F-No. 1'), $aF('F-No. 2'));
        $link($aF('F-No. 1'), $aF('F-No. 3'));
        $link($aF('F-No. 1'), $aF('F-No. 4'));
        $link($aF('F-No. 1'), $aF('F-No. 5'));

        $link($aF('F-No. 2'), $aF('F-No. 2'));
        $link($aF('F-No. 2'), $aF('F-No. 3'));
        $link($aF('F-No. 2'), $aF('F-No. 4'));
        $link($aF('F-No. 2'), $aF('F-No. 5'));

        $link($aF('F-No. 3'), $aF('F-No. 3'));
        $link($aF('F-No. 3'), $aF('F-No. 4'));
        $link($aF('F-No. 3'), $aF('F-No. 5'));

        $link($aF('F-No. 4'), $aF('F-No. 4'));
        $link($aF('F-No. 4'), $aF('F-No. 5'));

        $link($aF('F-No. 5'), $aF('F-No. 5')); // inox: solo self

        $link($aF('F-No. 6'), $aF('F-No. 6')); // sólido/tubular: solo self

        // ── AWS D1.1 (Table 4.12 — electrode group qualification) ─────────────
        $dF = fn(string $c) => $gc('AWS D1.1', $c);

        $link($dF('F1'), $dF('F1'));

        $link($dF('F2'), $dF('F1'));
        $link($dF('F2'), $dF('F2'));

        $link($dF('F3'), $dF('F1'));
        $link($dF('F3'), $dF('F2'));
        $link($dF('F3'), $dF('F3'));

        $link($dF('F4'), $dF('F1'));
        $link($dF('F4'), $dF('F2'));
        $link($dF('F4'), $dF('F3'));
        $link($dF('F4'), $dF('F4'));

        // ── AWS D1.6 ──────────────────────────────────────────────────────────
        // Inox: cada grupo califica solo self
        $link($gc('AWS D1.6', 'F-No. 5'),    $gc('AWS D1.6', 'F-No. 5'));
        $link($gc('AWS D1.6', 'F-No. 6-SS'), $gc('AWS D1.6', 'F-No. 6-SS'));

        // ── API 1104 (§6.3) ───────────────────────────────────────────────────
        // Cada WF califica solo self (no hay downward en API 1104 para consumibles)
        foreach (['WF-1', 'WF-2', 'WF-3', 'WF-4', 'WF-5', 'WF-6'] as $wf) {
            $id = $gc('API 1104', $wf);
            $link($id, $id);
        }

        // ── API 650 ───────────────────────────────────────────────────────────
        // Misma lógica que AWS D1.1 (D1.1 se referencia en API 650 §7.2.2)
        $aF650 = fn(string $c) => $gc('API 650', $c);

        $link($aF650('F1'), $aF650('F1'));

        $link($aF650('F2'), $aF650('F1'));
        $link($aF650('F2'), $aF650('F2'));

        $link($aF650('F3'), $aF650('F1'));
        $link($aF650('F3'), $aF650('F2'));
        $link($aF650('F3'), $aF650('F3'));

        $link($aF650('F4'), $aF650('F1'));
        $link($aF650('F4'), $aF650('F2'));
        $link($aF650('F4'), $aF650('F3'));
        $link($aF650('F4'), $aF650('F4'));

        // ── IRAM / ISO 9606-1 (§8.3) ──────────────────────────────────────────
        // FM2 (básico/celulósico) califica FM1 (rutílico).
        // FM4 califica FM1-FM4. FM5 y FM6 solo self.
        $iF = fn(string $c) => $gc('IRAM', $c);

        $link($iF('FM1'), $iF('FM1'));

        $link($iF('FM2'), $iF('FM1'));
        $link($iF('FM2'), $iF('FM2'));

        $link($iF('FM3'), $iF('FM1'));
        $link($iF('FM3'), $iF('FM3'));

        $link($iF('FM4'), $iF('FM1'));
        $link($iF('FM4'), $iF('FM2'));
        $link($iF('FM4'), $iF('FM3'));
        $link($iF('FM4'), $iF('FM4'));

        $link($iF('FM5'), $iF('FM5')); // autógena: solo self

        $link($iF('FM6'), $iF('FM6')); // alambre sólido/tubular: solo self

        DB::table('grupo_consumible_calificado')->insertOrIgnore($rows);
    }
}
