<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaseMetalGroupSeeder extends Seeder
{
    public function run(): void
    {
        $now   = now();
        $asme  = DB::table('standards')->where('code', 'ASME IX')->value('id');
        $aws   = DB::table('standards')->where('code', 'AWS D1.1')->value('id');
        $api   = DB::table('standards')->where('code', 'API 1104')->value('id');

        // ASME IX — P-Numbers (QW/QB-422)
        DB::table('base_metal_groups')->insertOrIgnore([
            ['standard_id' => $asme, 'code' => 'P-No. 1 Gr. 1', 'description' => 'Carbon steel, low strength (fy ≤ 55 ksi)',            'description_es' => 'Acero al carbono, baja resistencia (fy ≤ 55 ksi)',              'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'P-No. 1 Gr. 2', 'description' => 'Carbon steel, medium strength (55–70 ksi)',           'description_es' => 'Acero al carbono, resistencia media (55–70 ksi)',               'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'P-No. 1 Gr. 3', 'description' => 'Carbon steel, high strength (70–80 ksi)',             'description_es' => 'Acero al carbono, alta resistencia (70–80 ksi)',                'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'P-No. 3 Gr. 1', 'description' => 'Alloy steel — 1/2 Cr, 1/2 Mo; 1 Cr, 1/2 Mo',        'description_es' => 'Acero aleado — 1/2 Cr, 1/2 Mo; 1 Cr, 1/2 Mo',                 'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'P-No. 3 Gr. 2', 'description' => 'Alloy steel — 1-1/4 Cr, 1/2 Mo; 2-1/4 Cr, 1 Mo',   'description_es' => 'Acero aleado — 1-1/4 Cr, 1/2 Mo; 2-1/4 Cr, 1 Mo',            'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'P-No. 4 Gr. 1', 'description' => 'Alloy steel — 2 Cr, 1 Mo; 5 Cr, 1/2 Mo',            'description_es' => 'Acero aleado — 2 Cr, 1 Mo; 5 Cr, 1/2 Mo',                    'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'P-No. 8 Gr. 1', 'description' => 'Austenitic stainless steel — 304, 316, 347',         'description_es' => 'Acero inoxidable austenítico — 304, 316, 347',                 'created_at' => $now, 'updated_at' => $now],
        ]);

        // AWS D1.1 — Base Metal Groups (Tabla 5.3 — precalificadas)
        DB::table('base_metal_groups')->insertOrIgnore([
            ['standard_id' => $aws, 'code' => 'Group I',   'description' => 'Carbon steel, fy ≤ 36 ksi (250 MPa)',                      'description_es' => 'Acero al carbono, fy ≤ 36 ksi (250 MPa)',                     'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $aws, 'code' => 'Group II',  'description' => 'High-strength carbon steel, fy 42–65 ksi',                'description_es' => 'Acero al carbono de alta resistencia, fy 42–65 ksi',           'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $aws, 'code' => 'Group III', 'description' => 'High-strength low-alloy steel, fy 46–70 ksi',             'description_es' => 'Acero de baja aleación y alta resistencia, fy 46–70 ksi',     'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $aws, 'code' => 'Group IV',  'description' => 'High-strength quenched & tempered steel, fy 90–100 ksi',  'description_es' => 'Acero de alta resistencia templado y revenido, fy 90–100 ksi', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // API 1104 — Material Groups (agrupados por SMYS, Sección 5.3)
        DB::table('base_metal_groups')->insertOrIgnore([
            ['standard_id' => $api, 'code' => 'API 5L A/B',    'description' => 'API 5L Grade A & B — SMYS ≤ 42 ksi (290 MPa)',         'description_es' => 'API 5L Grados A y B — SMYS ≤ 42 ksi (290 MPa)',             'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $api, 'code' => 'API 5L X42–X52','description' => 'API 5L Grade X42, X46, X52 — SMYS 42–52 ksi',          'description_es' => 'API 5L Grados X42, X46, X52 — SMYS 42–52 ksi',              'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $api, 'code' => 'API 5L X56–X65','description' => 'API 5L Grade X56, X60, X65 — SMYS 56–65 ksi',          'description_es' => 'API 5L Grados X56, X60, X65 — SMYS 56–65 ksi',              'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $api, 'code' => 'API 5L X70–X80','description' => 'API 5L Grade X70, X80 — High strength (SMYS ≥ 70 ksi)','description_es' => 'API 5L Grados X70, X80 — Alta resistencia (SMYS ≥ 70 ksi)', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
