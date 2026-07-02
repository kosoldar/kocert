<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsumableGroupSeeder extends Seeder
{
    public function run(): void
    {
        $now  = now();
        $asme = DB::table('standards')->where('code', 'ASME IX')->value('id');
        $aws  = DB::table('standards')->where('code', 'AWS D1.1')->value('id');
        $api  = DB::table('standards')->where('code', 'API 1104')->value('id');

        // ASME IX — F-Numbers (QW-432)
        // Rule QW-433: F4 qualifies F3, F2, F1. F3 qualifies F2, F1.
        DB::table('consumable_groups')->insertOrIgnore([
            ['standard_id' => $asme, 'code' => 'F-No. 1', 'description' => 'Low-hydrogen electrodes, flat/horizontal only (EXX20/24/27/28)',   'description_es' => 'Electrodos de bajo hidrógeno, plana/horizontal solamente (EXX20/24/27/28)',  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'F-No. 2', 'description' => 'Rutile-coated electrodes, all positions (EXX12/13/14)',            'description_es' => 'Electrodos con revestimiento rutílico, todas posiciones (EXX12/13/14)',       'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'F-No. 3', 'description' => 'Cellulosic electrodes, all positions (EXX10/11) — E6010, E6011',  'description_es' => 'Electrodos celulósicos, todas posiciones (EXX10/11) — E6010, E6011',         'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'F-No. 4', 'description' => 'Low-hydrogen electrodes, all positions (EXX15/16/18) — E7018',    'description_es' => 'Electrodos de bajo hidrógeno, todas posiciones (EXX15/16/18) — E7018',       'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'F-No. 5', 'description' => 'Austenitic stainless steel electrodes (EXXX(X)-15/16)',            'description_es' => 'Electrodos de acero inoxidable austenítico (EXXX(X)-15/16)',                  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'F-No. 6', 'description' => 'All steel filler metals for GMAW, GTAW, FCAW, SAW (ER70S, E71T)', 'description_es' => 'Metales de aporte de acero para GMAW, GTAW, FCAW, SAW (ER70S, E71T)',        'created_at' => $now, 'updated_at' => $now],
        ]);

        // AWS D1.1 — Electrode Groups (Tabla 6.13)
        DB::table('consumable_groups')->insertOrIgnore([
            ['standard_id' => $aws, 'code' => 'F1', 'description' => 'Flat/horizontal only — EXX20/24/27/28',      'description_es' => 'Plana/horizontal solamente — EXX20/24/27/28',   'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $aws, 'code' => 'F2', 'description' => 'Rutile, all positions — EXX12/13/14',        'description_es' => 'Rutílico, todas posiciones — EXX12/13/14',       'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $aws, 'code' => 'F3', 'description' => 'Cellulosic, all positions — EXX10/11',       'description_es' => 'Celulósico, todas posiciones — EXX10/11',        'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $aws, 'code' => 'F4', 'description' => 'Low-hydrogen, all positions — EXX15/16/18',  'description_es' => 'Bajo hidrógeno, todas posiciones — EXX15/16/18', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // API 1104 — WF-Groups (Tabla 4 — Welder qualification filler metal groups)
        DB::table('consumable_groups')->insertOrIgnore([
            ['standard_id' => $api, 'code' => 'WF-1', 'description' => 'Cellulosic SMAW — EXX10, EXX11 (A5.1 / A5.5)',                        'description_es' => 'SMAW celulósico — EXX10, EXX11 (A5.1 / A5.5)',                             'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $api, 'code' => 'WF-2', 'description' => 'Low-hydrogen SMAW — EXX15/16/18 (A5.1 / A5.5)',                       'description_es' => 'SMAW bajo hidrógeno — EXX15/16/18 (A5.1 / A5.5)',                          'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $api, 'code' => 'WF-3', 'description' => 'Low-hydrogen vertical-down SMAW — E8045/9045/10045',                   'description_es' => 'SMAW bajo hidrógeno descendente — E8045/9045/10045',                        'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $api, 'code' => 'WF-4', 'description' => 'GMAW / GTAW wire — ERXXS-X (A5.18 / A5.28)',                          'description_es' => 'Alambre GMAW / GTAW — ERXXS-X (A5.18 / A5.28)',                            'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $api, 'code' => 'WF-5', 'description' => 'Oxyfuel welding — RG60, RG65 (A5.2)',                                 'description_es' => 'Soldadura oxiacetilénica — RG60, RG65 (A5.2)',                              'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $api, 'code' => 'WF-6', 'description' => 'FCAW with shielding gas — E71T-1C/M, E71T-9C/M (A5.20 / A5.36)',     'description_es' => 'FCAW con gas de protección — E71T-1C/M, E71T-9C/M (A5.20 / A5.36)',       'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
