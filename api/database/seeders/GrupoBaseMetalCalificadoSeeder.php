<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoBaseMetalCalificadoSeeder extends Seeder
{
    public function run(): void
    {
        $gbm = fn(string $norma, string $codigo) => DB::table('grupos_base_metal')
            ->join('normas', 'normas.id', '=', 'grupos_base_metal.norma_id')
            ->where('normas.nombre', $norma)
            ->where('grupos_base_metal.codigo', $codigo)
            ->value('grupos_base_metal.id');

        $rows = [];
        $link = function (int|null $probadoId, int|null $calificadoId) use (&$rows): void {
            if ($probadoId && $calificadoId) {
                $rows[] = ['probado_id' => $probadoId, 'calificado_id' => $calificadoId];
            }
        };

        // ── ASME IX (QW-422 / QW-423) ─────────────────────────────────────────
        // P-No.1: cualquier grupo califica todo P-No.1
        $p1g1 = $gbm('ASME IX', 'P-No. 1 Gr. 1');
        $p1g2 = $gbm('ASME IX', 'P-No. 1 Gr. 2');
        $p1g3 = $gbm('ASME IX', 'P-No. 1 Gr. 3');
        foreach ([$p1g1, $p1g2, $p1g3] as $probado) {
            foreach ([$p1g1, $p1g2, $p1g3] as $calificado) {
                $link($probado, $calificado);
            }
        }

        // P-No.3: cualquier grupo califica todo P-No.3 (y también P-No.1)
        $p3g1 = $gbm('ASME IX', 'P-No. 3 Gr. 1');
        $p3g2 = $gbm('ASME IX', 'P-No. 3 Gr. 2');
        foreach ([$p3g1, $p3g2] as $probado) {
            foreach ([$p3g1, $p3g2] as $calificado) {
                $link($probado, $calificado);
            }
            // P-No.3 también califica P-No.1 (acero carbono más simple)
            foreach ([$p1g1, $p1g2, $p1g3] as $calificado) {
                $link($probado, $calificado);
            }
        }

        // P-No.4: self + califica P-No.3 y P-No.1
        $p4g1 = $gbm('ASME IX', 'P-No. 4 Gr. 1');
        $link($p4g1, $p4g1);
        foreach ([$p3g1, $p3g2, $p1g1, $p1g2, $p1g3] as $cal) {
            $link($p4g1, $cal);
        }

        // P-No.5A: self únicamente (5Cr-Mo es específico)
        $p5a = $gbm('ASME IX', 'P-No. 5A Gr. 1');
        $link($p5a, $p5a);

        // P-No.5B Gr.1: self + 5A
        $p5b1 = $gbm('ASME IX', 'P-No. 5B Gr. 1');
        $link($p5b1, $p5b1);
        $link($p5b1, $p5a);

        // P-No.5B Gr.2 (P91): self únicamente — material muy específico
        $p5b2 = $gbm('ASME IX', 'P-No. 5B Gr. 2');
        $link($p5b2, $p5b2);

        // P-No.6 (martensítico inox): self
        $p6 = $gbm('ASME IX', 'P-No. 6 Gr. 1');
        $link($p6, $p6);

        // P-No.7 (ferrítico inox): self + P-No.6
        $p7 = $gbm('ASME IX', 'P-No. 7 Gr. 1');
        $link($p7, $p7);
        $link($p7, $p6);

        // P-No.8 (austenítico): cualquier grupo califica todo P-No.8
        $p8g1 = $gbm('ASME IX', 'P-No. 8 Gr. 1');
        $p8g2 = $gbm('ASME IX', 'P-No. 8 Gr. 2');
        $p8g3 = $gbm('ASME IX', 'P-No. 8 Gr. 3');
        foreach ([$p8g1, $p8g2, $p8g3] as $probado) {
            foreach ([$p8g1, $p8g2, $p8g3] as $calificado) {
                $link($probado, $calificado);
            }
        }

        // P-No.10H (dúplex): self únicamente
        $p10h = $gbm('ASME IX', 'P-No. 10H');
        $link($p10h, $p10h);

        // ── AWS D1.1 (Table 4.12 — upward qualification) ─────────────────────
        // Mayor grupo califica todos los inferiores
        $ag1 = $gbm('AWS D1.1', 'Grupo I');
        $ag2 = $gbm('AWS D1.1', 'Grupo II');
        $ag3 = $gbm('AWS D1.1', 'Grupo III');
        $ag4 = $gbm('AWS D1.1', 'Grupo IV');

        $link($ag1, $ag1);
        $link($ag2, $ag2); $link($ag2, $ag1);
        $link($ag3, $ag3); $link($ag3, $ag2); $link($ag3, $ag1);
        $link($ag4, $ag4); $link($ag4, $ag3); $link($ag4, $ag2); $link($ag4, $ag1);

        // ── AWS D1.6 ──────────────────────────────────────────────────────────
        // Cada grupo inox califica solo a sí mismo (material específico)
        foreach (['Grupo A', 'Grupo B', 'Grupo C', 'Grupo D'] as $grp) {
            $id = $gbm('AWS D1.6', $grp);
            $link($id, $id);
        }

        // ── API 1104 (§6.2) ───────────────────────────────────────────────────
        // Grado mayor califica todos los grados inferiores (grupos agrupados)
        $grades = [
            'API 5L A/B'     => 0,
            'API 5L X42–X52' => 1,
            'API 5L X56–X65' => 2,
            'API 5L X70–X80' => 3,
        ];

        $gradeIds = [];
        foreach (array_keys($grades) as $gradeCode) {
            $gradeIds[$gradeCode] = $gbm('API 1104', $gradeCode);
        }

        foreach ($grades as $probadoCode => $probadoLevel) {
            $probadoId = $gradeIds[$probadoCode];
            foreach ($grades as $calCode => $calLevel) {
                if ($calLevel <= $probadoLevel) {
                    $link($probadoId, $gradeIds[$calCode]);
                }
            }
        }

        // ── API 650 ───────────────────────────────────────────────────────────
        // Mismo patrón: mayor califica menores
        $a650g1 = $gbm('API 650', 'Grupo I');
        $a650g2 = $gbm('API 650', 'Grupo II');
        $a650g3 = $gbm('API 650', 'Grupo III');
        $a650g4 = $gbm('API 650', 'Grupo IV');

        $link($a650g1, $a650g1);
        $link($a650g2, $a650g2); $link($a650g2, $a650g1);
        $link($a650g3, $a650g3); $link($a650g3, $a650g2); $link($a650g3, $a650g1);
        $link($a650g4, $a650g4); $link($a650g4, $a650g3); $link($a650g4, $a650g2); $link($a650g4, $a650g1);

        // ── IRAM / ISO 9606-1 (Tabla 3) ───────────────────────────────────────
        // Dentro de la misma familia (W01-W03): nivel superior califica inferiores.
        // Entre familias distintas (W04 vs W21): no hay calificación cruzada.
        $iramGroups = [
            'W01' => ['W01'],
            'W02' => ['W01', 'W02'],
            'W03' => ['W01', 'W02', 'W03'],
            'W04' => ['W04'],              // Cr-Mo solo califica Cr-Mo
            'W11' => ['W04', 'W11'],       // W11 califica también W04
            'W21' => ['W21'],
            'W22' => ['W21', 'W22'],
            'W31' => ['W31'],
            'W41' => ['W41'],
        ];

        foreach ($iramGroups as $probadoCode => $calCodes) {
            $probadoId = $gbm('IRAM', $probadoCode);
            foreach ($calCodes as $calCode) {
                $link($probadoId, $gbm('IRAM', $calCode));
            }
        }

        DB::table('grupo_base_metal_calificado')->insertOrIgnore($rows);
    }
}
