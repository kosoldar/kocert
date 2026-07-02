<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoBaseMetalSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $id  = fn(string $nombre) => DB::table('normas')->where('nombre', $nombre)->value('id');

        $asme  = $id('ASME IX');
        $aws   = $id('AWS D1.1');
        $d16   = $id('AWS D1.6');
        $api   = $id('API 1104');
        $a650  = $id('API 650');
        $iram  = $id('IRAM');

        // ASME IX — P-Numbers (QW/QB-422)
        DB::table('grupos_base_metal')->insertOrIgnore([
            // Aceros al carbono y de baja aleación (P-1 a P-4)
            ['norma_id' => $asme, 'codigo' => 'P-No. 1 Gr. 1', 'descripcion' => 'Acero al carbono, baja resistencia (fy ≤ 55 ksi) — A36, A53 Gr.B, A106 Gr.B',         'orden' => 1,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'P-No. 1 Gr. 2', 'descripcion' => 'Acero al carbono, resistencia media (55–70 ksi) — A516 Gr.70, A537',                   'orden' => 2,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'P-No. 1 Gr. 3', 'descripcion' => 'Acero al carbono, alta resistencia (70–80 ksi) — A333 Gr.6',                           'orden' => 3,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'P-No. 3 Gr. 1', 'descripcion' => 'Acero aleado 1/2–1 Cr, 1/2 Mo — A387 Gr.2, A335 P2',                                  'orden' => 4,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'P-No. 3 Gr. 2', 'descripcion' => 'Acero aleado 1-1/4–2-1/4 Cr, 1/2–1 Mo — A387 Gr.11/22, A335 P11/P22',               'orden' => 5,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'P-No. 4 Gr. 1', 'descripcion' => 'Acero aleado 2–5 Cr, 1/2–1 Mo — A387 Gr.21/5, A335 P5',                               'orden' => 6,  'created_at' => $now, 'updated_at' => $now],
            // Alto Cr-Mo (P-5A, P-5B) — resistentes al creep
            ['norma_id' => $asme, 'codigo' => 'P-No. 5A Gr. 1','descripcion' => '5 Cr – 0.5 Mo — A335 P5, A182 F5',                                                     'orden' => 7,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'P-No. 5B Gr. 1','descripcion' => '9 Cr – 1 Mo — A335 P9, A182 F9',                                                        'orden' => 8,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'P-No. 5B Gr. 2','descripcion' => '9 Cr – 1 Mo – V (P91) — A335 P91, A182 F91',                                           'orden' => 9,  'created_at' => $now, 'updated_at' => $now],
            // Inoxidables (P-6, P-7, P-8, P-10H)
            ['norma_id' => $asme, 'codigo' => 'P-No. 6 Gr. 1', 'descripcion' => 'Inoxidable martensítico — 410, 415, 420, CA15',                                         'orden' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'P-No. 7 Gr. 1', 'descripcion' => 'Inoxidable ferrítico — 405, 409, 429, 430, 434, 436',                                   'orden' => 11, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'P-No. 8 Gr. 1', 'descripcion' => 'Inoxidable austenítico — 304, 304L, 316, 316L, 321, 347',                              'orden' => 12, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'P-No. 8 Gr. 2', 'descripcion' => 'Inoxidable austenítico alta aleación — 309, 310, 316H, 317',                            'orden' => 13, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'P-No. 8 Gr. 3', 'descripcion' => 'Inoxidable austenítico con N — 304N, 316N',                                             'orden' => 14, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'codigo' => 'P-No. 10H',     'descripcion' => 'Inoxidable dúplex — 2205 (S31803), 2304, LDX 2101',                                    'orden' => 15, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // AWS D1.1 — Grupos metal base (Tabla 5.3)
        DB::table('grupos_base_metal')->insertOrIgnore([
            ['norma_id' => $aws, 'codigo' => 'Grupo I',   'descripcion' => 'Acero al carbono, fy ≤ 36 ksi (250 MPa) — A36, A53 Gr.B, A500 Gr.A/B',                    'orden' => 1,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $aws, 'codigo' => 'Grupo II',  'descripcion' => 'Acero al carbono alta resistencia, fy 42–65 ksi — A441, A572 Gr.42/50, A588',              'orden' => 2,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $aws, 'codigo' => 'Grupo III', 'descripcion' => 'Acero de baja aleación, fy 46–70 ksi — A514 < 63mm, A517, A709 Gr.100',                   'orden' => 3,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $aws, 'codigo' => 'Grupo IV',  'descripcion' => 'Acero de alta resistencia templado, fy 90–100 ksi — A514 ≥ 63mm',                          'orden' => 4,  'created_at' => $now, 'updated_at' => $now],
        ]);

        // AWS D1.6 — Grupos metal base (Table 1.2)
        DB::table('grupos_base_metal')->insertOrIgnore([
            ['norma_id' => $d16, 'codigo' => 'Grupo A', 'descripcion' => 'Austenítico — 304, 304L, 316, 316L, 321, 347',                                               'orden' => 1,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $d16, 'codigo' => 'Grupo B', 'descripcion' => 'Ferrítico / Martensítico — 409, 430, 410, 420',                                               'orden' => 2,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $d16, 'codigo' => 'Grupo C', 'descripcion' => 'Dúplex — 2205 (UNS S31803), 2304',                                                            'orden' => 3,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $d16, 'codigo' => 'Grupo D', 'descripcion' => 'Endurecible por precipitación — 17-4 PH (S17400)',                                            'orden' => 4,  'created_at' => $now, 'updated_at' => $now],
        ]);

        // API 1104 — Grupos de material (§5.3, por SMYS)
        DB::table('grupos_base_metal')->insertOrIgnore([
            ['norma_id' => $api, 'codigo' => 'API 5L A/B',     'descripcion' => 'API 5L Grados A y B — SMYS ≤ 42 ksi (290 MPa)',                                       'orden' => 1,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $api, 'codigo' => 'API 5L X42–X52', 'descripcion' => 'API 5L Grados X42, X46, X52 — SMYS 42–52 ksi',                                       'orden' => 2,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $api, 'codigo' => 'API 5L X56–X65', 'descripcion' => 'API 5L Grados X56, X60, X65 — SMYS 56–65 ksi',                                       'orden' => 3,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $api, 'codigo' => 'API 5L X70–X80', 'descripcion' => 'API 5L Grados X70, X80 — Alta resistencia (SMYS ≥ 70 ksi)',                           'orden' => 4,  'created_at' => $now, 'updated_at' => $now],
        ]);

        // API 650 — Grupos metal base (Table 7.1)
        DB::table('grupos_base_metal')->insertOrIgnore([
            ['norma_id' => $a650, 'codigo' => 'Grupo I',   'descripcion' => 'Bajo carbono — A36, A283 Gr.C/D, A285 Gr.A/B/C',                                          'orden' => 1,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $a650, 'codigo' => 'Grupo II',  'descripcion' => 'Media resistencia — A516 Gr.55/60/65/70, A537 Cl.1',                                       'orden' => 2,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $a650, 'codigo' => 'Grupo III', 'descripcion' => 'Mayor resistencia — A537 Cl.2, A633 Gr.C/D, A678 Gr.B',                                   'orden' => 3,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $a650, 'codigo' => 'Grupo IV',  'descripcion' => 'Tanques especiales — API 5L Gr.X42–X65',                                                   'orden' => 4,  'created_at' => $now, 'updated_at' => $now],
        ]);

        // IRAM (ISO/TR 15608) — Grupos de material W
        DB::table('grupos_base_metal')->insertOrIgnore([
            ['norma_id' => $iram, 'codigo' => 'W01', 'descripcion' => 'Aceros al carbono, fy ≤ 355 MPa — S235, S355, P265GH, API 5L A-X52',                            'orden' => 1,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'W02', 'descripcion' => 'Aceros de alta resistencia, 355 < fy ≤ 460 MPa — S420, S460',                                    'orden' => 2,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'W03', 'descripcion' => 'Aceros de grano fino normalizado, fy ≤ 460 MPa',                                                  'orden' => 3,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'W04', 'descripcion' => 'Aceros Cr-Mo de baja aleación (Cr ≤ 0.75%) — A335 P11/P22',                                      'orden' => 4,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'W11', 'descripcion' => 'Aceros ferríticos resistentes al creep (1–12% Cr) — P5, P9, P91',                                'orden' => 5,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'W21', 'descripcion' => 'Inoxidable austenítico Cr 18–20 / Ni 8–12 — 304, 316',                                           'orden' => 6,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'W22', 'descripcion' => 'Inoxidable austenítico alta aleación — 309, 310',                                                 'orden' => 7,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'W31', 'descripcion' => 'Aleaciones de níquel (Ni ≥ 30%) — Inconel, Monel',                                               'orden' => 8,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'W41', 'descripcion' => 'Aluminio y aleaciones',                                                                           'orden' => 9,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'W51', 'descripcion' => 'Cobre y aleaciones',                                                                              'orden' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $iram, 'codigo' => 'W61', 'descripcion' => 'Titanio y aleaciones',                                                                            'orden' => 11, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
