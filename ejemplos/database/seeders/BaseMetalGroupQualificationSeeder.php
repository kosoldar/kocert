<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// ASME IX QW-423 — Substitute Base Metals:
//   A welder qualified on P-No.X qualifies P-No.X and lower P-Numbers per the cross-qual table.
//   P-No.1 (all Groups) → qualifies all P-No.1 groups (groups are interchangeable within P-1).
//   P-No.3 → qualifies P-1 (all) + P-3 (tested group and lower).
//   P-No.4 → qualifies P-1 (all) + P-3 (all) + P-4.
//   P-No.8 (SS austenitic) → qualifies P-8 only.
//
// AWS D1.1: higher Group qualifies lower Groups (Group II → Group I, II).
//
// API 1104: no cross-qualification — each SMYS group qualifies only itself.
class BaseMetalGroupQualificationSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $g = fn(string $standard, string $code) => DB::table('base_metal_groups')
            ->join('standards', 'standards.id', '=', 'base_metal_groups.standard_id')
            ->where('standards.code', $standard)
            ->where('base_metal_groups.code', $code)
            ->value('base_metal_groups.id');

        $asmeId = DB::table('standards')->where('code', 'ASME IX')->value('id');
        $awsId  = DB::table('standards')->where('code', 'AWS D1.1')->value('id');
        $apiId  = DB::table('standards')->where('code', 'API 1104')->value('id');

        $rows = [];

        $add = function (int $standard, int $tested, array $qualifies) use (&$rows, $now) {
            foreach ($qualifies as $qual) {
                $rows[] = [
                    'standard_id'        => $standard,
                    'tested_group_id'    => $tested,
                    'qualifies_group_id' => $qual,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];
            }
        };

        // ── ASME IX — QW-423 ──────────────────────────────────────────────────
        $p1g1 = $g('ASME IX', 'P-No. 1 Gr. 1');
        $p1g2 = $g('ASME IX', 'P-No. 1 Gr. 2');
        $p1g3 = $g('ASME IX', 'P-No. 1 Gr. 3');
        $p3g1 = $g('ASME IX', 'P-No. 3 Gr. 1');
        $p3g2 = $g('ASME IX', 'P-No. 3 Gr. 2');
        $p4g1 = $g('ASME IX', 'P-No. 4 Gr. 1');
        $p8g1 = $g('ASME IX', 'P-No. 8 Gr. 1');

        // QW-423: within P-No.1 all Groups are interchangeable
        $allP1 = [$p1g1, $p1g2, $p1g3];
        $allP3 = [$p3g1, $p3g2];

        $add($asmeId, $p1g1, $allP1);                              // P-1 Gr.1 → all P-1
        $add($asmeId, $p1g2, $allP1);                              // P-1 Gr.2 → all P-1
        $add($asmeId, $p1g3, $allP1);                              // P-1 Gr.3 → all P-1
        $add($asmeId, $p3g1, [...$allP1, $p3g1]);                  // P-3 Gr.1 → all P-1 + P-3 Gr.1
        $add($asmeId, $p3g2, [...$allP1, ...$allP3]);              // P-3 Gr.2 → all P-1 + all P-3
        $add($asmeId, $p4g1, [...$allP1, ...$allP3, $p4g1]);       // P-4 Gr.1 → all P-1, all P-3, P-4
        $add($asmeId, $p8g1, [$p8g1]);                             // P-8 → P-8 only (SS isolated)

        // ── AWS D1.1 — Higher Group qualifies lower Groups ────────────────────
        $awsI   = $g('AWS D1.1', 'Group I');
        $awsII  = $g('AWS D1.1', 'Group II');
        $awsIII = $g('AWS D1.1', 'Group III');
        $awsIV  = $g('AWS D1.1', 'Group IV');

        $add($awsId, $awsI,   [$awsI]);
        $add($awsId, $awsII,  [$awsI, $awsII]);
        $add($awsId, $awsIII, [$awsI, $awsII, $awsIII]);
        $add($awsId, $awsIV,  [$awsI, $awsII, $awsIII, $awsIV]);

        // ── API 1104 — Section 5.3: each SMYS group qualifies only itself ─────
        foreach (['API 5L A/B', 'API 5L X42–X52', 'API 5L X56–X65', 'API 5L X70–X80'] as $grp) {
            $id = $g('API 1104', $grp);
            $add($apiId, $id, [$id]);
        }

        DB::table('base_metal_group_qualifications')->insertOrIgnore($rows);
    }
}
