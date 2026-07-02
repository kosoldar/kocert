<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsumibleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Helper: obtiene el grupo_id por norma + codigo
        $g = fn(string $norma, string $codigo) => DB::table('grupos_consumible')
            ->join('normas', 'normas.id', '=', 'grupos_consumible.norma_id')
            ->where('normas.nombre', $norma)
            ->where('grupos_consumible.codigo', $codigo)
            ->value('grupos_consumible.id');

        // ── ASME IX ───────────────────────────────────────────────────────────

        // F-No. 1 — plana/horizontal, bajo hidrógeno hierro-polvos
        $f1 = $g('ASME IX', 'F-No. 1');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $f1, 'clasificacion' => 'E6020',    'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Óxido de hierro, plana/horizontal',                 'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f1, 'clasificacion' => 'E6027',    'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Hierro-polvos óxido de hierro, plana/horizontal',  'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f1, 'clasificacion' => 'E7024',    'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Hierro-polvos rutílico, alta deposición, F/H',      'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f1, 'clasificacion' => 'E7028',    'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno hierro-polvos, plana/horizontal',   'created_at' => $now, 'updated_at' => $now],
        ]);

        // F-No. 2 — rutílico todas posiciones
        $f2 = $g('ASME IX', 'F-No. 2');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $f2, 'clasificacion' => 'E6012',    'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Rutílico alta viscosidad, todas posiciones',        'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f2, 'clasificacion' => 'E6013',    'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Rutílico general, todas posiciones',                'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f2, 'clasificacion' => 'E7014',    'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Rutílico hierro-polvos, todas posiciones',          'created_at' => $now, 'updated_at' => $now],
        ]);

        // F-No. 3 — celulósico todas posiciones
        $f3 = $g('ASME IX', 'F-No. 3');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $f3, 'clasificacion' => 'E6010',    'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Celulósico — CCEP, todas posiciones',               'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f3, 'clasificacion' => 'E6011',    'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Celulósico — CA/CCEP, todas posiciones',            'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f3, 'clasificacion' => 'E7010-A1', 'sfa' => 'SFA-5.5',  'proceso' => 'SMAW', 'descripcion' => 'Celulósico baja aleación — 0.5% Mo',                'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f3, 'clasificacion' => 'E7010-P1', 'sfa' => 'SFA-5.5',  'proceso' => 'SMAW', 'descripcion' => 'Celulósico para gasoductos — CCEP',                 'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f3, 'clasificacion' => 'E8010-G',  'sfa' => 'SFA-5.5',  'proceso' => 'SMAW', 'descripcion' => 'Celulósico alta resistencia, gasoductos X60-X65',  'created_at' => $now, 'updated_at' => $now],
        ]);

        // F-No. 4 — bajo hidrógeno todas posiciones
        $f4 = $g('ASME IX', 'F-No. 4');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $f4, 'clasificacion' => 'E7015',    'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno — CCEP, todas posiciones',           'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f4, 'clasificacion' => 'E7016',    'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno — CA/CCEP, todas posiciones',        'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f4, 'clasificacion' => 'E7018',    'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno hierro-polvos — CA/CCEP',            'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f4, 'clasificacion' => 'E7018-1',  'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno, mayor tenacidad — CA/CCEP',         'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f4, 'clasificacion' => 'E7048',    'sfa' => 'SFA-5.1',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno vertical-down',                       'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f4, 'clasificacion' => 'E8016-G',  'sfa' => 'SFA-5.5',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno baja aleación — CA/CCEP',            'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f4, 'clasificacion' => 'E8018-C3', 'sfa' => 'SFA-5.5',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno baja aleación — 1% Ni',              'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f4, 'clasificacion' => 'E9018-M',  'sfa' => 'SFA-5.5',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno alta resistencia',                   'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f4, 'clasificacion' => 'E10018-M', 'sfa' => 'SFA-5.5',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno muy alta resistencia',               'created_at' => $now, 'updated_at' => $now],
        ]);

        // F-No. 5 — inox austenítico SMAW
        $f5 = $g('ASME IX', 'F-No. 5');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $f5, 'clasificacion' => 'E308-16',  'sfa' => 'SFA-5.4',  'proceso' => 'SMAW', 'descripcion' => 'Inox 308 — CA/CCEP, todas posiciones',             'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f5, 'clasificacion' => 'E308L-16', 'sfa' => 'SFA-5.4',  'proceso' => 'SMAW', 'descripcion' => 'Inox 308L bajo carbono — CA/CCEP',                  'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f5, 'clasificacion' => 'E316-16',  'sfa' => 'SFA-5.4',  'proceso' => 'SMAW', 'descripcion' => 'Inox 316 — CA/CCEP, todas posiciones',              'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f5, 'clasificacion' => 'E316L-16', 'sfa' => 'SFA-5.4',  'proceso' => 'SMAW', 'descripcion' => 'Inox 316L bajo carbono — CA/CCEP',                  'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f5, 'clasificacion' => 'E309-16',  'sfa' => 'SFA-5.4',  'proceso' => 'SMAW', 'descripcion' => 'Inox 309, disímiles carbono/inox — CA/CCEP',        'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f5, 'clasificacion' => 'E309L-16', 'sfa' => 'SFA-5.4',  'proceso' => 'SMAW', 'descripcion' => 'Inox 309L bajo carbono — CA/CCEP',                  'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f5, 'clasificacion' => 'E347-16',  'sfa' => 'SFA-5.4',  'proceso' => 'SMAW', 'descripcion' => 'Inox 347 estabilizado — CA/CCEP',                   'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f5, 'clasificacion' => 'E2209-16', 'sfa' => 'SFA-5.4',  'proceso' => 'SMAW', 'descripcion' => 'Dúplex 2205 — CA/CCEP',                             'created_at' => $now, 'updated_at' => $now],
        ]);

        // F-No. 6 — aporte sólido/tubular para GMAW/GTAW/FCAW/SAW (cubre carbono E inox)
        $f6 = $g('ASME IX', 'F-No. 6');
        DB::table('consumibles')->insertOrIgnore([
            // GTAW/GMAW acero al carbono
            ['grupo_id' => $f6, 'clasificacion' => 'ER70S-2',  'sfa' => 'SFA-5.18', 'proceso' => 'GTAW', 'descripcion' => 'Alambre TIG triple-deoxidado, todas posiciones',   'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f6, 'clasificacion' => 'ER70S-3',  'sfa' => 'SFA-5.18', 'proceso' => 'GTAW', 'descripcion' => 'Alambre TIG/MIG acero al carbono',                  'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f6, 'clasificacion' => 'ER70S-6',  'sfa' => 'SFA-5.18', 'proceso' => 'GMAW', 'descripcion' => 'Alambre MIG alta Mn-Si, uso general',              'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f6, 'clasificacion' => 'ER70S-7',  'sfa' => 'SFA-5.18', 'proceso' => 'GMAW', 'descripcion' => 'Alambre MIG alta Mn, buena tenacidad',             'created_at' => $now, 'updated_at' => $now],
            // GTAW/GMAW baja aleación
            ['grupo_id' => $f6, 'clasificacion' => 'ER80S-D2', 'sfa' => 'SFA-5.28', 'proceso' => 'GMAW', 'descripcion' => 'Alambre MIG baja aleación 0.5% Mo',               'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f6, 'clasificacion' => 'ER90S-D2', 'sfa' => 'SFA-5.28', 'proceso' => 'GMAW', 'descripcion' => 'Alambre MIG alta resistencia 0.5% Mo',            'created_at' => $now, 'updated_at' => $now],
            // FCAW acero al carbono
            ['grupo_id' => $f6, 'clasificacion' => 'E70T-1C',  'sfa' => 'SFA-5.20', 'proceso' => 'FCAW', 'descripcion' => 'Tubular con CO2, rutílico, todas posiciones',      'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f6, 'clasificacion' => 'E71T-1C',  'sfa' => 'SFA-5.20', 'proceso' => 'FCAW', 'descripcion' => 'Tubular con CO2, todas posiciones',                'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f6, 'clasificacion' => 'E71T-1M',  'sfa' => 'SFA-5.20', 'proceso' => 'FCAW', 'descripcion' => 'Tubular con gas mixto, todas posiciones',          'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f6, 'clasificacion' => 'E71T-8',   'sfa' => 'SFA-5.20', 'proceso' => 'FCAW', 'descripcion' => 'Tubular autoprotegido, bajo hidrógeno',            'created_at' => $now, 'updated_at' => $now],
            // SAW acero al carbono
            ['grupo_id' => $f6, 'clasificacion' => 'F7A2-EM12K','sfa' => 'SFA-5.17','proceso' => 'SAW',  'descripcion' => 'Arco sumergido — fundente F7A2, alambre EM12K',   'created_at' => $now, 'updated_at' => $now],
            // GTAW/GMAW inox (también F-No.6 en ASME IX)
            ['grupo_id' => $f6, 'clasificacion' => 'ER308L',   'sfa' => 'SFA-5.9',  'proceso' => 'GTAW', 'descripcion' => 'Alambre TIG/MIG 308L — inox austenítico',          'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f6, 'clasificacion' => 'ER316L',   'sfa' => 'SFA-5.9',  'proceso' => 'GTAW', 'descripcion' => 'Alambre TIG/MIG 316L — inox con Mo',              'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f6, 'clasificacion' => 'ER309L',   'sfa' => 'SFA-5.9',  'proceso' => 'GTAW', 'descripcion' => 'Alambre TIG/MIG 309L — disímiles carbono/inox',   'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f6, 'clasificacion' => 'ER347',    'sfa' => 'SFA-5.9',  'proceso' => 'GTAW', 'descripcion' => 'Alambre TIG/MIG 347 — inox estabilizado',         'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $f6, 'clasificacion' => 'ER2209',   'sfa' => 'SFA-5.9',  'proceso' => 'GTAW', 'descripcion' => 'Alambre TIG/MIG 2209 — dúplex 2205',             'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── AWS D1.1 ──────────────────────────────────────────────────────────

        $awsF1 = $g('AWS D1.1', 'F1');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $awsF1, 'clasificacion' => 'E6020', 'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Óxido de hierro, plana/horizontal',                   'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $awsF1, 'clasificacion' => 'E7024', 'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Hierro-polvos rutílico, alta deposición, F/H',        'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $awsF1, 'clasificacion' => 'E7028', 'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno hierro-polvos, plana/horizontal',      'created_at' => $now, 'updated_at' => $now],
        ]);

        $awsF2 = $g('AWS D1.1', 'F2');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $awsF2, 'clasificacion' => 'E6012', 'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Rutílico, todas posiciones',                          'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $awsF2, 'clasificacion' => 'E6013', 'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Rutílico general, todas posiciones',                  'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $awsF2, 'clasificacion' => 'E7014', 'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Rutílico hierro-polvos, todas posiciones',            'created_at' => $now, 'updated_at' => $now],
        ]);

        $awsF3 = $g('AWS D1.1', 'F3');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $awsF3, 'clasificacion' => 'E6010', 'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Celulósico — CCEP, todas posiciones',                 'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $awsF3, 'clasificacion' => 'E6011', 'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Celulósico — CA/CCEP, todas posiciones',              'created_at' => $now, 'updated_at' => $now],
        ]);

        $awsF4 = $g('AWS D1.1', 'F4');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $awsF4, 'clasificacion' => 'E7015', 'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno — CCEP',                               'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $awsF4, 'clasificacion' => 'E7016', 'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno — CA/CCEP',                            'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $awsF4, 'clasificacion' => 'E7018', 'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno hierro-polvos — CA/CCEP',              'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $awsF4, 'clasificacion' => 'E7018M','sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno militar, baja absorción humedad',      'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $awsF4, 'clasificacion' => 'E8018-C3','sfa' => 'A5.5','proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno 1% Ni',                                'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $awsF4, 'clasificacion' => 'E9018-G','sfa' => 'A5.5', 'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno alta resistencia',                     'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── AWS D1.6 — Consumibles inox ───────────────────────────────────────

        $d16f5 = $g('AWS D1.6', 'F-No. 5');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $d16f5, 'clasificacion' => 'E308-16',  'sfa' => 'A5.4',  'proceso' => 'SMAW', 'descripcion' => 'Inox 308 — CA/CCEP',                               'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $d16f5, 'clasificacion' => 'E308L-16', 'sfa' => 'A5.4',  'proceso' => 'SMAW', 'descripcion' => 'Inox 308L bajo carbono — CA/CCEP',                  'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $d16f5, 'clasificacion' => 'E316-16',  'sfa' => 'A5.4',  'proceso' => 'SMAW', 'descripcion' => 'Inox 316 — CA/CCEP',                                'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $d16f5, 'clasificacion' => 'E316L-16', 'sfa' => 'A5.4',  'proceso' => 'SMAW', 'descripcion' => 'Inox 316L bajo carbono — CA/CCEP',                  'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $d16f5, 'clasificacion' => 'E309L-16', 'sfa' => 'A5.4',  'proceso' => 'SMAW', 'descripcion' => 'Inox 309L, disímiles Gr.B/Gr.A — CA/CCEP',         'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $d16f5, 'clasificacion' => 'E2209-16', 'sfa' => 'A5.4',  'proceso' => 'SMAW', 'descripcion' => 'Dúplex 2205 — CA/CCEP',                             'created_at' => $now, 'updated_at' => $now],
        ]);

        $d16f6 = $g('AWS D1.6', 'F-No. 6-SS');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $d16f6, 'clasificacion' => 'ER308L',   'sfa' => 'A5.9',  'proceso' => 'GTAW', 'descripcion' => 'Alambre TIG/MIG 308L — austenítico',               'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $d16f6, 'clasificacion' => 'ER316L',   'sfa' => 'A5.9',  'proceso' => 'GTAW', 'descripcion' => 'Alambre TIG/MIG 316L — con Mo',                    'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $d16f6, 'clasificacion' => 'ER309L',   'sfa' => 'A5.9',  'proceso' => 'GTAW', 'descripcion' => 'Alambre TIG/MIG 309L — disímiles',                 'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $d16f6, 'clasificacion' => 'ER2209',   'sfa' => 'A5.9',  'proceso' => 'GTAW', 'descripcion' => 'Alambre TIG/MIG 2209 — dúplex',                    'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $d16f6, 'clasificacion' => 'E308LT1-1','sfa' => 'A5.22', 'proceso' => 'FCAW', 'descripcion' => 'Tubular inox 308L con gas',                        'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $d16f6, 'clasificacion' => 'E316LT1-1','sfa' => 'A5.22', 'proceso' => 'FCAW', 'descripcion' => 'Tubular inox 316L con gas',                        'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── API 1104 ──────────────────────────────────────────────────────────

        $wf1 = $g('API 1104', 'WF-1');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $wf1, 'clasificacion' => 'E6010',    'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Celulósico — CCEP, todas posiciones',                'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $wf1, 'clasificacion' => 'E6011',    'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Celulósico — CA/CCEP, todas posiciones',             'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $wf1, 'clasificacion' => 'E7010-P1', 'sfa' => 'A5.5',  'proceso' => 'SMAW', 'descripcion' => 'Celulósico pipeline — CCEP',                        'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $wf1, 'clasificacion' => 'E8010-P1', 'sfa' => 'A5.5',  'proceso' => 'SMAW', 'descripcion' => 'Celulósico pipeline alta resistencia — CCEP',       'created_at' => $now, 'updated_at' => $now],
        ]);

        $wf2 = $g('API 1104', 'WF-2');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $wf2, 'clasificacion' => 'E7016',    'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno — CA/CCEP',                           'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $wf2, 'clasificacion' => 'E7018',    'sfa' => 'A5.1',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno hierro-polvos — CA/CCEP',             'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $wf2, 'clasificacion' => 'E8016-G',  'sfa' => 'A5.5',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno baja aleación — CA/CCEP',             'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $wf2, 'clasificacion' => 'E8018-G',  'sfa' => 'A5.5',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno baja aleación hierro-polvos',         'created_at' => $now, 'updated_at' => $now],
        ]);

        $wf3 = $g('API 1104', 'WF-3');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $wf3, 'clasificacion' => 'E8045-P2', 'sfa' => 'A5.5',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno vertical-down — X52-X60',            'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $wf3, 'clasificacion' => 'E9045-P2', 'sfa' => 'A5.5',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno vertical-down — X65-X70',            'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $wf3, 'clasificacion' => 'E10045-P2','sfa' => 'A5.5',  'proceso' => 'SMAW', 'descripcion' => 'Bajo hidrógeno vertical-down — X80',                'created_at' => $now, 'updated_at' => $now],
        ]);

        $wf4 = $g('API 1104', 'WF-4');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $wf4, 'clasificacion' => 'ER70S-2',  'sfa' => 'A5.18', 'proceso' => 'GTAW', 'descripcion' => 'Alambre TIG triple-deoxidado, raíz',                 'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $wf4, 'clasificacion' => 'ER70S-6',  'sfa' => 'A5.18', 'proceso' => 'GMAW', 'descripcion' => 'Alambre MIG acero al carbono, alta Mn-Si',           'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $wf4, 'clasificacion' => 'ER80S-D2', 'sfa' => 'A5.28', 'proceso' => 'GMAW', 'descripcion' => 'Alambre MIG baja aleación 0.5% Mo',                 'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $wf4, 'clasificacion' => 'ER90S-G',  'sfa' => 'A5.28', 'proceso' => 'GMAW', 'descripcion' => 'Alambre MIG alta resistencia X65-X80',              'created_at' => $now, 'updated_at' => $now],
        ]);

        $wf6 = $g('API 1104', 'WF-6');
        DB::table('consumibles')->insertOrIgnore([
            ['grupo_id' => $wf6, 'clasificacion' => 'E71T-1C',  'sfa' => 'A5.20', 'proceso' => 'FCAW', 'descripcion' => 'Tubular con CO2, rutílico, todas posiciones',        'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $wf6, 'clasificacion' => 'E71T-9C',  'sfa' => 'A5.20', 'proceso' => 'FCAW', 'descripcion' => 'Tubular con CO2, mayor tenacidad, todas posiciones', 'created_at' => $now, 'updated_at' => $now],
            ['grupo_id' => $wf6, 'clasificacion' => 'E81T1-Ni1','sfa' => 'A5.29', 'proceso' => 'FCAW', 'descripcion' => 'Tubular con gas, baja aleación 1% Ni',              'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── API 650 — mismos F-groups que AWS D1.1 ────────────────────────────

        foreach (['F1' => 'F1', 'F2' => 'F2', 'F3' => 'F3', 'F4' => 'F4'] as $code => $same) {
            $srcId  = $g('AWS D1.1', $code);
            $destId = $g('API 650',  $code);
            if (!$srcId || !$destId) continue;

            $rows = DB::table('consumibles')->where('grupo_id', $srcId)->get();
            foreach ($rows as $row) {
                DB::table('consumibles')->insertOrIgnore([
                    'grupo_id'      => $destId,
                    'clasificacion' => $row->clasificacion,
                    'sfa'           => $row->sfa,
                    'proceso'       => $row->proceso,
                    'descripcion'   => $row->descripcion,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }
    }
}
