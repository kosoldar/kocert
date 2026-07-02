<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoConsumibleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $id  = fn(string $nombre) => DB::table('normas')->where('nombre', $nombre)->value('id');

        $asme = $id('ASME IX');
        $aws  = $id('AWS D1.1');
        $d16  = $id('AWS D1.6');
        $api  = $id('API 1104');
        $a650 = $id('API 650');
        $iram = $id('IRAM');

        // ASME IX — F-Numbers (QW-432)
        DB::table('grupos_consumible')->insertOrIgnore([
            ['norma_id' => $asme, 'codigo' => 'F-No. 1', 'descripcion' => 'Plana/horizontal, hierro-polvos — EXX20/24/27/28',                           'orden' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'F-No. 2', 'descripcion' => 'Rutílico todas posiciones — EXX12/13/14',                                    'orden' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'F-No. 3', 'descripcion' => 'Celulósico todas posiciones — EXX10/11 (E6010, E6011)',                      'orden' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'F-No. 4', 'descripcion' => 'Bajo hidrógeno todas posiciones — EXX15/16/18 (E7018)',                      'orden' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'F-No. 5', 'descripcion' => 'Inox austenítico SMAW — E308/316/309-XX (E308L-16, E316L-16)',               'orden' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'F-No. 6', 'descripcion' => 'Aporte sólido/tubular GMAW/GTAW/FCAW/SAW — ER70S, E71T, ER308L, ER316L',    'orden' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // AWS D1.1 — Grupos electrodo (Tabla 6.13)
        DB::table('grupos_consumible')->insertOrIgnore([
            ['norma_id' => $aws, 'codigo' => 'F1', 'descripcion' => 'Plana/horizontal — EXX20/24/27/28',                                               'orden' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $aws, 'codigo' => 'F2', 'descripcion' => 'Rutílico todas posiciones — EXX12/13/14',                                          'orden' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $aws, 'codigo' => 'F3', 'descripcion' => 'Celulósico todas posiciones — EXX10/11',                                           'orden' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $aws, 'codigo' => 'F4', 'descripcion' => 'Bajo hidrógeno todas posiciones — EXX15/16/18',                                    'orden' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // AWS D1.6 — Grupos consumible para inox (AWS A5.4 / A5.9)
        DB::table('grupos_consumible')->insertOrIgnore([
            ['norma_id' => $d16, 'codigo' => 'F-No. 5',   'descripcion' => 'Inox austenítico SMAW — E308L-XX, E316L-XX, E309-XX',                     'orden' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $d16, 'codigo' => 'F-No. 6-SS','descripcion' => 'Aporte sólido/tubular inox — ER308L, ER316L, ER309L',                      'orden' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // API 1104 — WF-Groups (Tabla 4)
        DB::table('grupos_consumible')->insertOrIgnore([
            ['norma_id' => $api, 'codigo' => 'WF-1', 'descripcion' => 'SMAW celulósico — EXX10, EXX11 (A5.1/A5.5)',                                   'orden' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $api, 'codigo' => 'WF-2', 'descripcion' => 'SMAW bajo hidrógeno — EXX15/16/18 (A5.1/A5.5)',                                 'orden' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $api, 'codigo' => 'WF-3', 'descripcion' => 'SMAW bajo hidrógeno vertical-down — E8045/9045/10045',                          'orden' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $api, 'codigo' => 'WF-4', 'descripcion' => 'Alambre GMAW/GTAW — ERXXS-X (A5.18/A5.28)',                                     'orden' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $api, 'codigo' => 'WF-5', 'descripcion' => 'Soldadura oxiacetilénica — RG60, RG65 (A5.2)',                                  'orden' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $api, 'codigo' => 'WF-6', 'descripcion' => 'FCAW con gas — E71T-1C/M, E71T-9C/M (A5.20/A5.36)',                             'orden' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // API 650 — mismos F-numbers que AWS D1.1 (Annex B referencia AWS)
        DB::table('grupos_consumible')->insertOrIgnore([
            ['norma_id' => $a650, 'codigo' => 'F1', 'descripcion' => 'Plana/horizontal — EXX20/24/27/28',                                              'orden' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $a650, 'codigo' => 'F2', 'descripcion' => 'Rutílico todas posiciones — EXX12/13/14',                                         'orden' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $a650, 'codigo' => 'F3', 'descripcion' => 'Celulósico todas posiciones — EXX10/11',                                          'orden' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $a650, 'codigo' => 'F4', 'descripcion' => 'Bajo hidrógeno todas posiciones — EXX15/16/18',                                   'orden' => 4, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // IRAM — FM Groups (ISO 9606-1 Anexo B)
        DB::table('grupos_consumible')->insertOrIgnore([
            ['norma_id' => $iram, 'codigo' => 'FM1', 'descripcion' => 'Rutílico escoria lenta — E XX R, E XX RR (todas posiciones con escoria viscosa)',  'orden' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'FM2', 'descripcion' => 'Básico/celulósico escoria rápida — E XX B, E XX C (para posición vertical)',       'orden' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'FM3', 'descripcion' => 'Rutílico escoria rápida todas posiciones — E XX RC',                               'orden' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'FM4', 'descripcion' => 'Otros tipos (ácido, oxidante)',                                                    'orden' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'FM5', 'descripcion' => 'Sin aporte — soldadura autógena',                                                  'orden' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'FM6', 'descripcion' => 'Aporte sólido o tubular — GMAW/GTAW/FCAW wire',                                   'orden' => 6, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
