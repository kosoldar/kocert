<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsumableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $g = fn(string $standard, string $code) => DB::table('consumable_groups')
            ->join('standards', 'standards.id', '=', 'consumable_groups.standard_id')
            ->where('standards.code', $standard)
            ->where('consumable_groups.code', $code)
            ->value('consumable_groups.id');

        // ── ASME IX — F-No. 1 (flat/horizontal only) ────────────────────────
        $f1 = $g('ASME IX', 'F-No. 1');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $f1, 'classification' => 'E6020',   'sfa' => 'SFA-5.1',  'description' => 'High iron oxide — flat/horizontal only',               'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f1, 'classification' => 'E6022',   'sfa' => 'SFA-5.1',  'description' => 'High iron oxide — flat only',                          'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f1, 'classification' => 'E6027',   'sfa' => 'SFA-5.1',  'description' => 'High iron powder oxide — flat/horizontal only',        'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f1, 'classification' => 'E7024',   'sfa' => 'SFA-5.1',  'description' => 'High iron powder — flat/horizontal only',              'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f1, 'classification' => 'E7027',   'sfa' => 'SFA-5.1',  'description' => 'High iron powder oxide — flat/horizontal only',        'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f1, 'classification' => 'E7028',   'sfa' => 'SFA-5.1',  'description' => 'Low-hydrogen, iron powder — flat/horizontal only',     'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── ASME IX — F-No. 2 (rutile, all positions) ────────────────────────
        $f2 = $g('ASME IX', 'F-No. 2');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $f2, 'classification' => 'E6012',   'sfa' => 'SFA-5.1',  'description' => 'Rutile, high-deposition — all positions',              'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f2, 'classification' => 'E6013',   'sfa' => 'SFA-5.1',  'description' => 'Rutile, soft arc — all positions',                     'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f2, 'classification' => 'E7014',   'sfa' => 'SFA-5.1',  'description' => 'Rutile, iron powder — all positions',                  'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── ASME IX — F-No. 3 (Cellulosic) ───────────────────────────────────
        $f3 = $g('ASME IX', 'F-No. 3');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $f3, 'classification' => 'E6010',    'sfa' => 'SFA-5.1',  'description' => 'Cellulosic electrode — DCEP, all positions',                    'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f3, 'classification' => 'E6011',    'sfa' => 'SFA-5.1',  'description' => 'Cellulosic electrode — AC/DCEP, all positions',                  'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f3, 'classification' => 'E7010-A1', 'sfa' => 'SFA-5.5',  'description' => 'Low-alloy cellulosic — 0.5% Mo',                                 'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f3, 'classification' => 'E7010-P1', 'sfa' => 'SFA-5.5',  'description' => 'Low-alloy cellulosic — pipeline grade',                          'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── ASME IX — F-No. 4 (Low-hydrogen) ─────────────────────────────────
        $f4 = $g('ASME IX', 'F-No. 4');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $f4, 'classification' => 'E7015',    'sfa' => 'SFA-5.1',  'description' => 'Low-hydrogen — DCEP, all positions',                             'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f4, 'classification' => 'E7016',    'sfa' => 'SFA-5.1',  'description' => 'Low-hydrogen — AC/DCEP, all positions',                          'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f4, 'classification' => 'E7018',    'sfa' => 'SFA-5.1',  'description' => 'Low-hydrogen, iron powder — AC/DCEP, all positions',             'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f4, 'classification' => 'E7018-1',  'sfa' => 'SFA-5.1',  'description' => 'Low-hydrogen, improved toughness — AC/DCEP',                    'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f4, 'classification' => 'E7048',    'sfa' => 'SFA-5.1',  'description' => 'Low-hydrogen — vertical-down position',                          'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f4, 'classification' => 'E8018-C3', 'sfa' => 'SFA-5.5',  'description' => 'Low-alloy, low-hydrogen, 1% Ni',                                 'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f4, 'classification' => 'E9018-M',  'sfa' => 'SFA-5.5',  'description' => 'Low-alloy, low-hydrogen, military grade',                        'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── ASME IX — F-No. 6 (GMAW/GTAW/FCAW/SAW wire) ─────────────────────
        $f6 = $g('ASME IX', 'F-No. 6');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $f6, 'classification' => 'ER70S-2',  'sfa' => 'SFA-5.18', 'description' => 'GTAW/GMAW wire, triple-deoxidized, all position',                'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f6, 'classification' => 'ER70S-3',  'sfa' => 'SFA-5.18', 'description' => 'GTAW/GMAW wire, carbon steel',                                   'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f6, 'classification' => 'ER70S-6',  'sfa' => 'SFA-5.18', 'description' => 'GTAW/GMAW wire, high-manganese silicon, all purpose',            'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f6, 'classification' => 'ER80S-D2', 'sfa' => 'SFA-5.28', 'description' => 'GTAW/GMAW wire, 0.5% Mo low-alloy',                              'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f6, 'classification' => 'E71T-1C',  'sfa' => 'SFA-5.20', 'description' => 'FCAW wire with CO2 shielding — all positions',                   'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f6, 'classification' => 'E71T-1M',  'sfa' => 'SFA-5.20', 'description' => 'FCAW wire with mixed gas shielding — all positions',             'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── API 1104 — WF-1 (Cellulosic SMAW) ────────────────────────────────
        $wf1 = $g('API 1104', 'WF-1');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $wf1, 'classification' => 'E6010',   'sfa' => 'A5.1',     'description' => 'Cellulosic SMAW — DCEP, all positions',                          'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $wf1, 'classification' => 'E6011',   'sfa' => 'A5.1',     'description' => 'Cellulosic SMAW — AC/DCEP, all positions',                       'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $wf1, 'classification' => 'E7010-P1','sfa' => 'A5.5',     'description' => 'Pipeline cellulosic SMAW',                                       'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── API 1104 — WF-2 (Low-hydrogen SMAW) ──────────────────────────────
        $wf2 = $g('API 1104', 'WF-2');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $wf2, 'classification' => 'E7016',   'sfa' => 'A5.1',     'description' => 'Low-hydrogen SMAW — AC/DCEP',                                    'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $wf2, 'classification' => 'E7018',   'sfa' => 'A5.1',     'description' => 'Low-hydrogen SMAW — AC/DCEP',                                    'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $wf2, 'classification' => 'E8018-G', 'sfa' => 'A5.5',     'description' => 'Low-alloy low-hydrogen SMAW',                                    'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── ASME IX — F-No. 5 (austenitic stainless SMAW) ───────────────────
        $f5 = $g('ASME IX', 'F-No. 5');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $f5, 'classification' => 'E308-16',  'sfa' => 'SFA-5.4',  'description' => 'Austenitic SS SMAW — 308 alloy, AC/DCEP',              'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f5, 'classification' => 'E308L-16', 'sfa' => 'SFA-5.4',  'description' => 'Austenitic SS SMAW — 308L low-carbon, AC/DCEP',        'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f5, 'classification' => 'E316-16',  'sfa' => 'SFA-5.4',  'description' => 'Austenitic SS SMAW — 316 alloy (Mo), AC/DCEP',         'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f5, 'classification' => 'E316L-16', 'sfa' => 'SFA-5.4',  'description' => 'Austenitic SS SMAW — 316L low-carbon (Mo), AC/DCEP',   'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $f5, 'classification' => 'E347-16',  'sfa' => 'SFA-5.4',  'description' => 'Austenitic SS SMAW — 347 alloy (Nb stabilized)',        'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── API 1104 — WF-3 (low-hydrogen vertical-down SMAW) ────────────────
        $wf3 = $g('API 1104', 'WF-3');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $wf3, 'classification' => 'E8045-P1', 'sfa' => 'A5.5',   'description' => 'Low-hydrogen vertical-down pipeline SMAW — 80 ksi',    'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $wf3, 'classification' => 'E9045-P2', 'sfa' => 'A5.5',   'description' => 'Low-hydrogen vertical-down pipeline SMAW — 90 ksi',    'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $wf3, 'classification' => 'E10045-P2','sfa' => 'A5.5',   'description' => 'Low-hydrogen vertical-down pipeline SMAW — 100 ksi',   'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── API 1104 — WF-4 (GMAW / GTAW) ────────────────────────────────────
        $wf4 = $g('API 1104', 'WF-4');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $wf4, 'classification' => 'ER70S-6', 'sfa' => 'A5.18',    'description' => 'GMAW/GTAW solid wire — carbon steel',                            'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $wf4, 'classification' => 'ER70S-2', 'sfa' => 'A5.18',    'description' => 'GMAW/GTAW solid wire — triple deox',                             'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $wf4, 'classification' => 'ER80S-D2','sfa' => 'A5.28',    'description' => 'GMAW/GTAW low-alloy wire — 0.5% Mo',                             'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── API 1104 — WF-5 (Oxyfuel Welding) ───────────────────────────────
        $wf5 = $g('API 1104', 'WF-5');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $wf5, 'classification' => 'RG60',   'sfa' => 'A5.2',    'description' => 'Oxyfuel welding rod — 60 ksi tensile',                   'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $wf5, 'classification' => 'RG65',   'sfa' => 'A5.2',    'description' => 'Oxyfuel welding rod — 65 ksi tensile',                   'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── AWS D1.1 — F1 (flat/horizontal only) ─────────────────────────────
        $awsF1 = $g('AWS D1.1', 'F1');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $awsF1, 'classification' => 'E6020', 'sfa' => 'A5.1',   'description' => 'High iron oxide — flat/horizontal only',                 'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsF1, 'classification' => 'E7024', 'sfa' => 'A5.1',   'description' => 'High iron powder — flat/horizontal only',                'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsF1, 'classification' => 'E7027', 'sfa' => 'A5.1',   'description' => 'High iron powder oxide — flat/horizontal only',          'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsF1, 'classification' => 'E7028', 'sfa' => 'A5.1',   'description' => 'Low-hydrogen iron powder — flat/horizontal only',        'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── AWS D1.1 — F2 (rutile, all positions) ────────────────────────────
        $awsF2 = $g('AWS D1.1', 'F2');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $awsF2, 'classification' => 'E6012', 'sfa' => 'A5.1',   'description' => 'Rutile, high-deposition — all positions',                'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsF2, 'classification' => 'E6013', 'sfa' => 'A5.1',   'description' => 'Rutile, soft arc — all positions',                       'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsF2, 'classification' => 'E7014', 'sfa' => 'A5.1',   'description' => 'Rutile, iron powder — all positions',                    'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── AWS D1.1 — F3 (Cellulosic) ───────────────────────────────────────
        $awsF3 = $g('AWS D1.1', 'F3');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $awsF3, 'classification' => 'E6010', 'sfa' => 'A5.1',     'description' => 'Cellulosic SMAW — DCEP, all positions',                          'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsF3, 'classification' => 'E6011', 'sfa' => 'A5.1',     'description' => 'Cellulosic SMAW — AC/DCEP',                                     'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── AWS D1.1 — F4 (Low-hydrogen) ─────────────────────────────────────
        $awsF4 = $g('AWS D1.1', 'F4');
        DB::table('consumables')->insertOrIgnore([
            ['group_id' => $awsF4, 'classification' => 'E7015', 'sfa' => 'A5.1',     'description' => 'Low-hydrogen SMAW — DCEP',                                      'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsF4, 'classification' => 'E7016', 'sfa' => 'A5.1',     'description' => 'Low-hydrogen SMAW — AC/DCEP',                                   'created_at' => $now, 'updated_at' => $now],
            ['group_id' => $awsF4, 'classification' => 'E7018', 'sfa' => 'A5.1',     'description' => 'Low-hydrogen iron powder SMAW — AC/DCEP',                       'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
