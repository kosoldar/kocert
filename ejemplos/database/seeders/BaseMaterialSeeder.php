<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaseMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $g = fn(string $standard, string $code) => DB::table('base_metal_groups')
            ->join('standards', 'standards.id', '=', 'base_metal_groups.standard_id')
            ->where('standards.code', $standard)
            ->where('base_metal_groups.code', $code)
            ->value('base_metal_groups.id');

        // ── ASME IX — P-No. 1 Gr. 1 (most common for Kosoldar) ──────────────
        $p1g1 = $g('ASME IX', 'P-No. 1 Gr. 1');
        DB::table('base_materials')->insertOrIgnore([
            ['group_id' => $p1g1, 'specification' => 'A/SA-36',      'description' => 'Structural carbon steel plate, bars & shapes',          'description_es' => 'Planchuela, barras y perfiles de acero al carbono estructural',              'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g1, 'specification' => 'A/SA-53 Gr. A','description' => 'Seamless and welded steel pipe — Grade A',              'description_es' => 'Caño de acero sin costura y soldado — Grado A',                              'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g1, 'specification' => 'A/SA-53 Gr. B','description' => 'Seamless and welded steel pipe — Grade B',              'description_es' => 'Caño de acero sin costura y soldado — Grado B',                              'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g1, 'specification' => 'A/SA-106 Gr. A','description' => 'Seamless carbon steel pipe for high-temp service — A', 'description_es' => 'Caño de acero al carbono sin costura para alta temperatura — Grado A',        'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g1, 'specification' => 'A/SA-106 Gr. B','description' => 'Seamless carbon steel pipe for high-temp service — B', 'description_es' => 'Caño de acero al carbono sin costura para alta temperatura — Grado B',        'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g1, 'specification' => 'A/SA-106 Gr. C','description' => 'Seamless carbon steel pipe for high-temp service — C', 'description_es' => 'Caño de acero al carbono sin costura para alta temperatura — Grado C',        'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g1, 'specification' => 'API 5L Gr. A',  'description' => 'Line pipe — Grade A',                                  'description_es' => 'Tubería de conducción — Grado A',                                             'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g1, 'specification' => 'API 5L Gr. B',  'description' => 'Line pipe — Grade B',                                  'description_es' => 'Tubería de conducción — Grado B',                                             'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g1, 'specification' => 'API 5L X42',    'description' => 'Line pipe — Grade X42',                                'description_es' => 'Tubería de conducción — Grado X42',                                           'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g1, 'specification' => 'API 5L X46',    'description' => 'Line pipe — Grade X46',                                'description_es' => 'Tubería de conducción — Grado X46',                                           'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g1, 'specification' => 'API 5L X52',    'description' => 'Line pipe — Grade X52',                                'description_es' => 'Tubería de conducción — Grado X52',                                           'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g1, 'specification' => 'A/SA-516 Gr. 55','description' => 'Pressure vessel steel plate — Grade 55',              'description_es' => 'Planchuela de acero para recipientes a presión — Grado 55',                   'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g1, 'specification' => 'A/SA-516 Gr. 60','description' => 'Pressure vessel steel plate — Grade 60',              'description_es' => 'Planchuela de acero para recipientes a presión — Grado 60',                   'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g1, 'specification' => 'A/SA-105',       'description' => 'Forgings for piping components',                      'description_es' => 'Forjados para componentes de cañería',                                        'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── ASME IX — P-No. 1 Gr. 2 ──────────────────────────────────────────
        $p1g2 = $g('ASME IX', 'P-No. 1 Gr. 2');
        DB::table('base_materials')->insertOrIgnore([
            ['group_id' => $p1g2, 'specification' => 'A/SA-516 Gr. 65','description' => 'Pressure vessel steel plate — Grade 65',              'description_es' => 'Planchuela de acero para recipientes a presión — Grado 65',                   'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g2, 'specification' => 'A/SA-516 Gr. 70','description' => 'Pressure vessel steel plate — Grade 70',              'description_es' => 'Planchuela de acero para recipientes a presión — Grado 70',                   'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g2, 'specification' => 'API 5L X56',     'description' => 'Line pipe — Grade X56',                               'description_es' => 'Tubería de conducción — Grado X56',                                           'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g2, 'specification' => 'API 5L X60',     'description' => 'Line pipe — Grade X60',                               'description_es' => 'Tubería de conducción — Grado X60',                                           'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g2, 'specification' => 'API 5L X65',     'description' => 'Line pipe — Grade X65',                               'description_es' => 'Tubería de conducción — Grado X65',                                           'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g2, 'specification' => 'A/SA-285 Gr. C', 'description' => 'Pressure vessel plate, carbon steel — Grade C',       'description_es' => 'Planchuela de acero al carbono para recipientes a presión — Grado C',         'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── ASME IX — P-No. 1 Gr. 3 ──────────────────────────────────────────
        $p1g3 = $g('ASME IX', 'P-No. 1 Gr. 3');
        DB::table('base_materials')->insertOrIgnore([
            ['group_id' => $p1g3, 'specification' => 'API 5L X70',     'description' => 'Line pipe — Grade X70',                               'description_es' => 'Tubería de conducción — Grado X70',                                           'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g3, 'specification' => 'API 5L X80',     'description' => 'Line pipe — Grade X80',                               'description_es' => 'Tubería de conducción — Grado X80',                                           'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $p1g3, 'specification' => 'A/SA-572 Gr. 65','description' => 'High-strength low-alloy structural steel — Grade 65', 'description_es' => 'Acero estructural de baja aleación y alta resistencia — Grado 65',            'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── AWS D1.1 — Group I ────────────────────────────────────────────────
        $awsI = $g('AWS D1.1', 'Group I');
        DB::table('base_materials')->insertOrIgnore([
            ['group_id' => $awsI, 'specification' => 'ASTM A36',       'description' => 'Structural carbon steel',                             'description_es' => 'Acero al carbono estructural',                                                'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsI, 'specification' => 'ASTM A53 Gr. B', 'description' => 'Pipe — Grade B',                                      'description_es' => 'Caño — Grado B',                                                              'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsI, 'specification' => 'ASTM A106 Gr. B','description' => 'Seamless pipe — Grade B',                             'description_es' => 'Caño sin costura — Grado B',                                                  'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsI, 'specification' => 'API 5L Gr. B',   'description' => 'Line pipe — Grade B',                                 'description_es' => 'Tubería de conducción — Grado B',                                             'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsI, 'specification' => 'API 5L X42',     'description' => 'Line pipe — Grade X42',                               'description_es' => 'Tubería de conducción — Grado X42',                                           'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── AWS D1.1 — Group II ───────────────────────────────────────────────
        $awsII = $g('AWS D1.1', 'Group II');
        DB::table('base_materials')->insertOrIgnore([
            ['group_id' => $awsII, 'specification' => 'ASTM A572 Gr. 42','description' => 'High-strength low-alloy steel — Grade 42',          'description_es' => 'Acero de baja aleación y alta resistencia — Grado 42',                       'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsII, 'specification' => 'ASTM A572 Gr. 50','description' => 'High-strength low-alloy steel — Grade 50',          'description_es' => 'Acero de baja aleación y alta resistencia — Grado 50',                       'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsII, 'specification' => 'ASTM A588 Gr. A', 'description' => 'HSLA weathering steel — Grade A',                   'description_es' => 'Acero de baja aleación resistente a la intemperie — Grado A',                 'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsII, 'specification' => 'API 5L X46',      'description' => 'Line pipe — Grade X46',                             'description_es' => 'Tubería de conducción — Grado X46',                                           'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsII, 'specification' => 'API 5L X52',      'description' => 'Line pipe — Grade X52',                             'description_es' => 'Tubería de conducción — Grado X52',                                           'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── AWS D1.1 — Group III ──────────────────────────────────────────────
        $awsIII = $g('AWS D1.1', 'Group III');
        DB::table('base_materials')->insertOrIgnore([
            ['group_id' => $awsIII, 'specification' => 'ASTM A514 Gr. A',  'description' => 'Quenched & tempered alloy steel plate — Grade A (T < 63mm)',   'description_es' => 'Acero aleado templado y revenido — Grado A (T < 63mm)',          'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsIII, 'specification' => 'ASTM A514 Gr. B',  'description' => 'Quenched & tempered alloy steel plate — Grade B (T < 63mm)',   'description_es' => 'Acero aleado templado y revenido — Grado B (T < 63mm)',          'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsIII, 'specification' => 'ASTM A517 Gr. A',  'description' => 'Pressure vessel QT steel — Grade A',                          'description_es' => 'Acero para recipientes a presión templado y revenido — Grado A', 'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsIII, 'specification' => 'ASTM A709 Gr. 100','description' => 'Structural steel bridge application — Grade 100 (T ≤ 64mm)',   'description_es' => 'Acero estructural para puentes — Grado 100 (T ≤ 64mm)',         'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsIII, 'specification' => 'ASTM A572 Gr. 65', 'description' => 'High-strength low-alloy steel — Grade 65',                    'description_es' => 'Acero de baja aleación y alta resistencia — Grado 65',           'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── AWS D1.1 — Group IV ───────────────────────────────────────────────
        $awsIV = $g('AWS D1.1', 'Group IV');
        DB::table('base_materials')->insertOrIgnore([
            ['group_id' => $awsIV, 'specification' => 'ASTM A514 Gr. A',  'description' => 'Quenched & tempered alloy steel plate — Grade A (T ≥ 63mm)',    'description_es' => 'Acero aleado templado y revenido — Grado A (T ≥ 63mm)',          'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsIV, 'specification' => 'ASTM A514 Gr. B',  'description' => 'Quenched & tempered alloy steel plate — Grade B (T ≥ 63mm)',    'description_es' => 'Acero aleado templado y revenido — Grado B (T ≥ 63mm)',          'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsIV, 'specification' => 'ASTM A709 Gr. 100W','description' => 'Weathering structural steel — Grade 100W (T ≥ 64mm)',          'description_es' => 'Acero estructural resistente a intemperie — Grado 100W',         'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── API 1104 — grouped by SMYS ────────────────────────────────────────
        $apiAB  = $g('API 1104', 'API 5L A/B');
        $apiX52 = $g('API 1104', 'API 5L X42–X52');
        $apiX65 = $g('API 1104', 'API 5L X56–X65');
        $apiX80 = $g('API 1104', 'API 5L X70–X80');

        DB::table('base_materials')->insertOrIgnore([
            ['group_id' => $apiAB,  'specification' => 'API 5L Gr. A',  'description' => 'Line pipe — Grade A (SMYS 25 ksi / 172 MPa)',        'description_es' => 'Tubería de conducción — Grado A (SMYS 25 ksi / 172 MPa)',                    'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $apiAB,  'specification' => 'API 5L Gr. B',  'description' => 'Line pipe — Grade B (SMYS 35 ksi / 241 MPa)',        'description_es' => 'Tubería de conducción — Grado B (SMYS 35 ksi / 241 MPa)',                    'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $apiAB,  'specification' => 'ASTM A53 Gr. B','description' => 'Pipe — Grade B',                                     'description_es' => 'Caño — Grado B',                                                              'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $apiAB,  'specification' => 'ASTM A106 Gr. B','description' => 'Seamless pipe — Grade B',                           'description_es' => 'Caño sin costura — Grado B',                                                  'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $apiX52, 'specification' => 'API 5L X42',    'description' => 'Line pipe — Grade X42 (SMYS 42 ksi / 290 MPa)',      'description_es' => 'Tubería de conducción — Grado X42 (SMYS 42 ksi / 290 MPa)',                  'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $apiX52, 'specification' => 'API 5L X46',    'description' => 'Line pipe — Grade X46 (SMYS 46 ksi / 317 MPa)',      'description_es' => 'Tubería de conducción — Grado X46 (SMYS 46 ksi / 317 MPa)',                  'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $apiX52, 'specification' => 'API 5L X52',    'description' => 'Line pipe — Grade X52 (SMYS 52 ksi / 359 MPa)',      'description_es' => 'Tubería de conducción — Grado X52 (SMYS 52 ksi / 359 MPa)',                  'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $apiX65, 'specification' => 'API 5L X56',    'description' => 'Line pipe — Grade X56 (SMYS 56 ksi / 386 MPa)',      'description_es' => 'Tubería de conducción — Grado X56 (SMYS 56 ksi / 386 MPa)',                  'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $apiX65, 'specification' => 'API 5L X60',    'description' => 'Line pipe — Grade X60 (SMYS 60 ksi / 414 MPa)',      'description_es' => 'Tubería de conducción — Grado X60 (SMYS 60 ksi / 414 MPa)',                  'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $apiX65, 'specification' => 'API 5L X65',    'description' => 'Line pipe — Grade X65 (SMYS 65 ksi / 448 MPa)',      'description_es' => 'Tubería de conducción — Grado X65 (SMYS 65 ksi / 448 MPa)',                  'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $apiX80, 'specification' => 'API 5L X70',    'description' => 'Line pipe — Grade X70 (SMYS 70 ksi / 483 MPa)',      'description_es' => 'Tubería de conducción — Grado X70 (SMYS 70 ksi / 483 MPa)',                  'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $apiX80, 'specification' => 'API 5L X80',    'description' => 'Line pipe — Grade X80 (SMYS 80 ksi / 552 MPa)',      'description_es' => 'Tubería de conducción — Grado X80 (SMYS 80 ksi / 552 MPa)',                  'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
