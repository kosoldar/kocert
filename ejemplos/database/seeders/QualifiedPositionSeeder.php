<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QualifiedPositionSeeder extends Seeder
{
    public function run(): void
    {
        $now  = now();
        $asme = DB::table('standards')->where('code', 'ASME IX')->value('id');
        $api  = DB::table('standards')->where('code', 'API 1104')->value('id');
        $aws  = DB::table('standards')->where('code', 'AWS D1.1')->value('id');

        // Helper: get position id by code
        $pos = DB::table('positions')->pluck('id', 'code');

        $rows = [];

        $add = function (int $standard, string $tested, array $qualifies, string $jointType) use (&$rows, $now, $pos) {
            foreach ($qualifies as $qualified) {
                $rows[] = [
                    'standard_id'           => $standard,
                    'tested_position_id'    => $pos[$tested],
                    'qualified_position_id' => $pos[$qualified],
                    'joint_type'            => $jointType,
                    'created_at'            => $now,
                    'updated_at'            => $now,
                ];
            }
        };

        $allGroove  = ['1G', '2G', '3G', '4G', '5G', '6G', '6GR'];
        $allFillet  = ['1F', '2F', '2FR', '3F', '4F', '5F'];

        // ── ASME IX — QW-461.9 ───────────────────────────────────────────────
        // Groove on plate
        $add($asme, '1G', ['1G'],                    'groove');
        $add($asme, '1G', ['1F'],                    'fillet');

        $add($asme, '2G', ['1G', '2G'],              'groove');
        $add($asme, '2G', ['1F', '2F'],              'fillet');

        $add($asme, '3G', ['1G', '3G'],              'groove');   // plate: F and V
        $add($asme, '3G', ['1F', '2F', '3F'],        'fillet');

        $add($asme, '4G', ['1G', '4G'],              'groove');   // plate: F and O
        $add($asme, '4G', ['1F', '2F', '4F'],        'fillet');

        // Groove on pipe
        $add($asme, '5G', ['1G', '3G', '4G', '5G'], 'groove');   // plate/pipe: F, V, O
        $add($asme, '5G', $allFillet,                'fillet');

        $add($asme, '6G',  $allGroove,                'groove');   // ALL positions
        $add($asme, '6G',  $allFillet,               'fillet');

        // 6GR (with restriction ring) — qualifies same positions as 6G (QW-461.9)
        $add($asme, '6GR', $allGroove,               'groove');
        $add($asme, '6GR', $allFillet,               'fillet');

        // Fillet only tests
        $add($asme, '1F', ['1F'],                    'fillet');
        $add($asme, '2F', ['1F', '2F'],              'fillet');
        $add($asme, '3F', ['1F', '2F', '3F'],        'fillet');
        $add($asme, '4F', ['1F', '2F', '4F'],        'fillet');
        $add($asme, '5F', $allFillet,                'fillet');

        // ── API 1104 — Section 6.2.2(f) ──────────────────────────────────────
        // Rolled (equivalent to 1G) → only rolled/flat
        $add($api, '1G', ['1G'],                     'groove');

        // Fixed horizontal (equivalent to 5G) → qualifies all fixed positions
        $add($api, '5G', ['1G', '3G', '4G', '5G'],  'groove');

        // Fixed 45° inclined (equivalent to 6G) → qualifies all positions
        $add($api, '6G', $allGroove,                 'groove');

        // ── AWS D1.1 — Tabla 6.10 ────────────────────────────────────────────
        // Groove on plate
        $add($aws, '1G', ['1G'],                     'groove');
        $add($aws, '1G', ['1F', '2F'],               'fillet');   // D1.1: 1G qualifies F,H fillet

        $add($aws, '2G', ['1G', '2G'],               'groove');
        $add($aws, '2G', ['1F', '2F'],               'fillet');

        $add($aws, '3G', ['1G', '2G', '3G'],         'groove');
        $add($aws, '3G', ['1F', '2F', '3F'],         'fillet');

        $add($aws, '4G', ['1G', '2G', '4G'],         'groove');
        $add($aws, '4G', ['1F', '2F', '4F'],         'fillet');

        // 5G on pipe qualifies same as ASME 5G for D1.1 pipe tests
        $add($aws, '5G', ['1G', '2G', '3G', '4G', '5G'], 'groove');
        $add($aws, '5G', $allFillet,                       'fillet');

        $add($aws, '6G', $allGroove,                 'groove');
        $add($aws, '6G', $allFillet,                 'fillet');

        // Fillet tests (AWS D1.1)
        $add($aws, '1F', ['1F'],                     'fillet');
        $add($aws, '2F', ['1F', '2F'],               'fillet');
        $add($aws, '3F', ['1F', '2F', '3F'],         'fillet');
        $add($aws, '4F', ['1F', '2F', '4F'],         'fillet');

        DB::table('qualified_positions')->insertOrIgnore($rows);
    }
}
