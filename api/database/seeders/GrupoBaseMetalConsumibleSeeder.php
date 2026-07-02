<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoBaseMetalConsumibleSeeder extends Seeder
{
    public function run(): void
    {
        $gbm = fn(string $norma, string $codigo) => DB::table('grupos_base_metal')
            ->join('normas', 'normas.id', '=', 'grupos_base_metal.norma_id')
            ->where('normas.nombre', $norma)
            ->where('grupos_base_metal.codigo', $codigo)
            ->value('grupos_base_metal.id');

        $gc = fn(string $norma, string $codigo) => DB::table('grupos_consumible')
            ->join('normas', 'normas.id', '=', 'grupos_consumible.norma_id')
            ->where('normas.nombre', $norma)
            ->where('grupos_consumible.codigo', $codigo)
            ->value('grupos_consumible.id');

        $rows = [];

        $link = function (int|null $metalId, int|null $consumibleId) use (&$rows): void {
            if ($metalId && $consumibleId) {
                $rows[] = ['grupo_base_metal_id' => $metalId, 'grupo_consumible_id' => $consumibleId];
            }
        };

        // ── ASME IX ───────────────────────────────────────────────────────────
        // P-No. 1 (carbono) → F-No. 1, 2, 3, 4, 6
        foreach (['P-No. 1 Gr. 1', 'P-No. 1 Gr. 2', 'P-No. 1 Gr. 3'] as $p) {
            $mid = $gbm('ASME IX', $p);
            foreach (['F-No. 1', 'F-No. 2', 'F-No. 3', 'F-No. 4', 'F-No. 6'] as $f) {
                $link($mid, $gc('ASME IX', $f));
            }
        }

        // P-No. 3 (baja aleación Cr-Mo ≤1.5%) → F-No. 4, 6
        foreach (['P-No. 3 Gr. 1', 'P-No. 3 Gr. 2'] as $p) {
            $mid = $gbm('ASME IX', $p);
            foreach (['F-No. 4', 'F-No. 6'] as $f) {
                $link($mid, $gc('ASME IX', $f));
            }
        }

        // P-No. 4 (5Cr-Mo) → F-No. 4, 6
        $link($gbm('ASME IX', 'P-No. 4 Gr. 1'), $gc('ASME IX', 'F-No. 4'));
        $link($gbm('ASME IX', 'P-No. 4 Gr. 1'), $gc('ASME IX', 'F-No. 6'));

        // P-No. 5A/5B (9Cr-1Mo, P91) → F-No. 4, 6
        foreach (['P-No. 5A Gr. 1', 'P-No. 5B Gr. 1', 'P-No. 5B Gr. 2'] as $p) {
            $mid = $gbm('ASME IX', $p);
            foreach (['F-No. 4', 'F-No. 6'] as $f) {
                $link($mid, $gc('ASME IX', $f));
            }
        }

        // P-No. 6 (martensítico inox) → F-No. 5, 6
        $link($gbm('ASME IX', 'P-No. 6 Gr. 1'), $gc('ASME IX', 'F-No. 5'));
        $link($gbm('ASME IX', 'P-No. 6 Gr. 1'), $gc('ASME IX', 'F-No. 6'));

        // P-No. 7 (ferrítico inox) → F-No. 5, 6
        $link($gbm('ASME IX', 'P-No. 7 Gr. 1'), $gc('ASME IX', 'F-No. 5'));
        $link($gbm('ASME IX', 'P-No. 7 Gr. 1'), $gc('ASME IX', 'F-No. 6'));

        // P-No. 8 (austenítico) → F-No. 5, 6
        foreach (['P-No. 8 Gr. 1', 'P-No. 8 Gr. 2', 'P-No. 8 Gr. 3'] as $p) {
            $mid = $gbm('ASME IX', $p);
            foreach (['F-No. 5', 'F-No. 6'] as $f) {
                $link($mid, $gc('ASME IX', $f));
            }
        }

        // P-No. 10H (dúplex 2205) → F-No. 5, 6
        $link($gbm('ASME IX', 'P-No. 10H'), $gc('ASME IX', 'F-No. 5'));
        $link($gbm('ASME IX', 'P-No. 10H'), $gc('ASME IX', 'F-No. 6'));

        // ── AWS D1.1 ──────────────────────────────────────────────────────────
        // Todos los grupos de material son compatibles con todos los grupos de electrodo
        foreach (['Grupo I', 'Grupo II', 'Grupo III', 'Grupo IV'] as $grp) {
            $mid = $gbm('AWS D1.1', $grp);
            foreach (['F1', 'F2', 'F3', 'F4'] as $f) {
                $link($mid, $gc('AWS D1.1', $f));
            }
        }

        // ── AWS D1.6 (inox) ───────────────────────────────────────────────────
        foreach (['Grupo A', 'Grupo B', 'Grupo C', 'Grupo D'] as $grp) {
            $mid = $gbm('AWS D1.6', $grp);
            foreach (['F-No. 5', 'F-No. 6-SS'] as $f) {
                $link($mid, $gc('AWS D1.6', $f));
            }
        }

        // ── API 1104 ──────────────────────────────────────────────────────────
        // A/B → WF-1, WF-2, WF-4, WF-5 (sin vertical-down ni FCAW)
        $mid = $gbm('API 1104', 'API 5L A/B');
        foreach (['WF-1', 'WF-2', 'WF-4', 'WF-5'] as $f) {
            $link($mid, $gc('API 1104', $f));
        }

        // X42–X52 → WF-1, WF-2, WF-4, WF-5, WF-6
        $mid = $gbm('API 1104', 'API 5L X42–X52');
        foreach (['WF-1', 'WF-2', 'WF-4', 'WF-5', 'WF-6'] as $f) {
            $link($mid, $gc('API 1104', $f));
        }

        // X56–X65 → todos los WF
        $mid = $gbm('API 1104', 'API 5L X56–X65');
        foreach (['WF-1', 'WF-2', 'WF-3', 'WF-4', 'WF-5', 'WF-6'] as $f) {
            $link($mid, $gc('API 1104', $f));
        }

        // X70–X80 → WF-2, WF-3, WF-4, WF-6 (sin celulósico WF-1)
        $mid = $gbm('API 1104', 'API 5L X70–X80');
        foreach (['WF-2', 'WF-3', 'WF-4', 'WF-6'] as $f) {
            $link($mid, $gc('API 1104', $f));
        }

        // ── API 650 ───────────────────────────────────────────────────────────
        // Grupo I → F1, F2, F3, F4
        $link($gbm('API 650', 'Grupo I'), $gc('API 650', 'F1'));
        $link($gbm('API 650', 'Grupo I'), $gc('API 650', 'F2'));
        $link($gbm('API 650', 'Grupo I'), $gc('API 650', 'F3'));
        $link($gbm('API 650', 'Grupo I'), $gc('API 650', 'F4'));

        // Grupo II → F2, F3, F4 (acero mayor Fy, no usar celulósico bajo H)
        $link($gbm('API 650', 'Grupo II'), $gc('API 650', 'F2'));
        $link($gbm('API 650', 'Grupo II'), $gc('API 650', 'F3'));
        $link($gbm('API 650', 'Grupo II'), $gc('API 650', 'F4'));

        // Grupo III → F3, F4
        $link($gbm('API 650', 'Grupo III'), $gc('API 650', 'F3'));
        $link($gbm('API 650', 'Grupo III'), $gc('API 650', 'F4'));

        // Grupo IV (alta resistencia) → F4 únicamente
        $link($gbm('API 650', 'Grupo IV'), $gc('API 650', 'F4'));

        // ── IRAM / ISO 9606-1 ─────────────────────────────────────────────────
        // W01-W03 (carbono sin/bajo-aleación) → FM1, FM2, FM3, FM4, FM6
        foreach (['W01', 'W02', 'W03'] as $grp) {
            $mid = $gbm('IRAM', $grp);
            foreach (['FM1', 'FM2', 'FM3', 'FM4', 'FM6'] as $f) {
                $link($mid, $gc('IRAM', $f));
            }
        }

        // W04/W11 (Cr-Mo aleados) → FM2, FM6
        foreach (['W04', 'W11'] as $grp) {
            $mid = $gbm('IRAM', $grp);
            foreach (['FM2', 'FM6'] as $f) {
                $link($mid, $gc('IRAM', $f));
            }
        }

        // W21/W22 (austenítico inox) → FM1, FM2, FM6
        foreach (['W21', 'W22'] as $grp) {
            $mid = $gbm('IRAM', $grp);
            foreach (['FM1', 'FM2', 'FM6'] as $f) {
                $link($mid, $gc('IRAM', $f));
            }
        }

        // W31 (níquel y aleaciones) → FM2, FM6
        $mid = $gbm('IRAM', 'W31');
        $link($mid, $gc('IRAM', 'FM2'));
        $link($mid, $gc('IRAM', 'FM6'));

        // W41 (aluminio) → FM6 (solo GTAW/GMAW con alambre Al)
        $mid = $gbm('IRAM', 'W41');
        $link($mid, $gc('IRAM', 'FM6'));

        DB::table('grupo_base_metal_consumible')->insertOrIgnore($rows);
    }
}
