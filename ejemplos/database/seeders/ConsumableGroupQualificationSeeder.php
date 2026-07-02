<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// ASME IX QW-433 — Substitute F-Numbers:
//   A welder qualified with a higher F-Number may weld with that number and all lower F-Numbers.
//   Example: F-No.4 (E7018) qualifies F-4, F-3, F-2, F-1.
//   F-No.5 (SS) and F-No.6 (bare wire) qualify only themselves — no cross-qualification.
//
// AWS D1.1 Table 6.13 follows the same F-Number substitution logic for SMAW.
//
// API 1104: no cross-qualification — each WF group qualifies only itself (Section 6.3.1).
class ConsumableGroupQualificationSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $g = fn(string $standard, string $code) => DB::table('consumable_groups')
            ->join('standards', 'standards.id', '=', 'consumable_groups.standard_id')
            ->where('standards.code', $standard)
            ->where('consumable_groups.code', $code)
            ->value('consumable_groups.id');

        $asmeId = DB::table('standards')->where('code', 'ASME IX')->value('id');
        $awsId  = DB::table('standards')->where('code', 'AWS D1.1')->value('id');
        $apiId  = DB::table('standards')->where('code', 'API 1104')->value('id');

        // ── ASME IX — QW-433 ──────────────────────────────────────────────────
        $asmeF1 = $g('ASME IX', 'F-No. 1');
        $asmeF2 = $g('ASME IX', 'F-No. 2');
        $asmeF3 = $g('ASME IX', 'F-No. 3');
        $asmeF4 = $g('ASME IX', 'F-No. 4');
        $asmeF5 = $g('ASME IX', 'F-No. 5');
        $asmeF6 = $g('ASME IX', 'F-No. 6');

        $rows = [];

        $add = function (int $standard, int $tested, array $qualifies) use (&$rows, $now) {
            foreach ($qualifies as $qual) {
                $rows[] = [
                    'standard_id'      => $standard,
                    'tested_group_id'  => $tested,
                    'qualifies_group_id' => $qual,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }
        };

        // ASME IX: F-No.4 → qualifies F-4, F-3, F-2, F-1
        $add($asmeId, $asmeF4, [$asmeF4, $asmeF3, $asmeF2, $asmeF1]);
        // F-No.3 → qualifies F-3, F-2, F-1
        $add($asmeId, $asmeF3, [$asmeF3, $asmeF2, $asmeF1]);
        // F-No.2 → qualifies F-2, F-1
        $add($asmeId, $asmeF2, [$asmeF2, $asmeF1]);
        // F-No.1 → qualifies F-1 only
        $add($asmeId, $asmeF1, [$asmeF1]);
        // F-No.5 (SS austenitic) — no cross-qualification
        $add($asmeId, $asmeF5, [$asmeF5]);
        // F-No.6 (bare wire GMAW/GTAW/FCAW/SAW) — no cross-qualification
        $add($asmeId, $asmeF6, [$asmeF6]);

        // ── AWS D1.1 — Table 6.13 (same F-Number substitution as ASME IX) ─────
        $awsF1 = $g('AWS D1.1', 'F1');
        $awsF2 = $g('AWS D1.1', 'F2');
        $awsF3 = $g('AWS D1.1', 'F3');
        $awsF4 = $g('AWS D1.1', 'F4');

        $add($awsId, $awsF4, [$awsF4, $awsF3, $awsF2, $awsF1]);
        $add($awsId, $awsF3, [$awsF3, $awsF2, $awsF1]);
        $add($awsId, $awsF2, [$awsF2, $awsF1]);
        $add($awsId, $awsF1, [$awsF1]);

        // ── API 1104 — Section 6.3.1: each WF group qualifies only itself ─────
        foreach (['WF-1', 'WF-2', 'WF-3', 'WF-4', 'WF-5', 'WF-6'] as $wf) {
            $id = $g('API 1104', $wf);
            $add($apiId, $id, [$id]);
        }

        DB::table('consumable_group_qualifications')->insertOrIgnore($rows);
    }
}
