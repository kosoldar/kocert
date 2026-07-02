<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiameterRuleSeeder extends Seeder
{
    public function run(): void
    {
        $now  = now();
        $asme = DB::table('standards')->where('code', 'ASME IX')->value('id');
        $api  = DB::table('standards')->where('code', 'API 1104')->value('id');

        DB::table('diameter_rules')->insertOrIgnore([
            // ── ASME IX — QW-452.3 (Groove welds on pipe) ─────────────────────
            // OD < 25.4mm → qualifies from tested size to unlimited
            [
                'standard_id'           => $asme, 'coupon_type' => 'pipe',
                'diameter_from_mm'      => 0,
                'diameter_to_mm'        => 25.4,    // < 1 in.
                'qualifies_min_mm'      => null,     // null = same as tested OD
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'QW-452.3: OD < 1 in. (25.4 mm) → qualifies from tested OD to unlimited',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            // 25.4mm ≤ OD ≤ 73.0mm → qualifies from 25.4mm to unlimited
            [
                'standard_id'           => $asme, 'coupon_type' => 'pipe',
                'diameter_from_mm'      => 25.4,    // 1 in.
                'diameter_to_mm'        => 73.0,    // 2-7/8 in. (NPS 2-1/2 / DN 65)
                'qualifies_min_mm'      => 25.4,
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'QW-452.3: 1 in. ≤ OD ≤ 2-7/8 in. (25.4–73 mm) → min 1 in. OD, unlimited max',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            // OD > 73.0mm → qualifies from 73.0mm to unlimited
            [
                'standard_id'           => $asme, 'coupon_type' => 'pipe',
                'diameter_from_mm'      => 73.0,    // > 2-7/8 in.
                'diameter_to_mm'        => null,
                'qualifies_min_mm'      => 73.0,
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'QW-452.3: OD > 2-7/8 in. (73 mm) → min 2-7/8 in. OD, unlimited max',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],

            // ── API 1104 — Section 6.2.2(d) — Diameter Groups ─────────────────
            // Group 1: OD < 60.3mm → qualifies only Group 1 (same range)
            [
                'standard_id'           => $api, 'coupon_type' => 'pipe',
                'diameter_from_mm'      => 0,
                'diameter_to_mm'        => 60.3,    // < 2.375 in.
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => '60.3',
                'notes'                 => 'API 1104 §6.2.2(d) Group 1: OD < 2.375 in. (60.3 mm) — qualifies Group 1 only',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            // Group 2: 60.3mm ≤ OD ≤ 323.9mm → qualifies Groups 1 & 2
            [
                'standard_id'           => $api, 'coupon_type' => 'pipe',
                'diameter_from_mm'      => 60.3,    // 2.375 in.
                'diameter_to_mm'        => 323.9,   // 12.750 in.
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => '323.9',
                'notes'                 => 'API 1104 §6.2.2(d) Group 2: 2.375–12.750 in. — qualifies Groups 1 and 2',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            // Group 3: OD > 323.9mm → qualifies all groups
            [
                'standard_id'           => $api, 'coupon_type' => 'pipe',
                'diameter_from_mm'      => 323.9,   // > 12.750 in.
                'diameter_to_mm'        => null,
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'API 1104 §6.2.2(d) Group 3: OD > 12.750 in. (323.9 mm) — qualifies all diameter groups',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
        ]);
    }
}
