<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThicknessRuleSeeder extends Seeder
{
    public function run(): void
    {
        $now  = now();
        $asme = DB::table('standards')->where('code', 'ASME IX')->value('id');
        $api  = DB::table('standards')->where('code', 'API 1104')->value('id');
        $aws  = DB::table('standards')->where('code', 'AWS D1.1')->value('id');

        DB::table('thickness_rules')->insertOrIgnore([
            // ── ASME IX — QW-452.1(b) ──────────────────────────────────────────
            // Rule 1: any t → qualifies up to 2t
            [
                'standard_id'           => $asme, 'coupon_type' => 'both',
                'thickness_from_mm'     => 0,
                'thickness_to_mm'       => null,    // applies to ALL thicknesses
                'min_layers'            => null,
                'qualifies_min_mm'      => null,     // same as tested
                'qualifies_max_formula' => '2t',
                'notes'                 => 'QW-452.1(b): any deposited weld metal thickness t qualifies up to 2t',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            // Rule 2: t ≥ 13mm with ≥3 layers → unlimited
            [
                'standard_id'           => $asme, 'coupon_type' => 'both',
                'thickness_from_mm'     => 13,      // 1/2 in. (13 mm)
                'thickness_to_mm'       => null,
                'min_layers'            => 3,
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'QW-452.1(b): t ≥ 13mm AND ≥3 layers deposited → qualifies unlimited thickness',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],

            // ── API 1104 — Table 5 ─────────────────────────────────────────────
            // t < 3.9mm → max(3.9mm, 1.5t)
            [
                'standard_id'           => $api, 'coupon_type' => 'pipe',
                'thickness_from_mm'     => 0,
                'thickness_to_mm'       => 3.9,     // < 0.154 in.
                'min_layers'            => null,
                'qualifies_min_mm'      => null,     // same as t
                'qualifies_max_formula' => 'max(3.9,1.5t)',
                'notes'                 => 'API 1104 Table 5: t < 0.154 in. (3.9 mm)',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            // 3.9mm ≤ t < 19mm → from 3.9mm to max(19mm, 1.5t)
            [
                'standard_id'           => $api, 'coupon_type' => 'pipe',
                'thickness_from_mm'     => 3.9,
                'thickness_to_mm'       => 19.0,    // < 0.750 in.
                'min_layers'            => null,
                'qualifies_min_mm'      => 3.9,
                'qualifies_max_formula' => 'max(19.0,1.5t)',
                'notes'                 => 'API 1104 Table 5: 0.154 in. ≤ t < 0.750 in. (3.9–19 mm)',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            // t ≥ 19mm → from 19mm to unlimited
            [
                'standard_id'           => $api, 'coupon_type' => 'pipe',
                'thickness_from_mm'     => 19.0,
                'thickness_to_mm'       => null,
                'min_layers'            => null,
                'qualifies_min_mm'      => 19.0,
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'API 1104 Table 5: t ≥ 0.750 in. (19 mm) → qualifies unlimited',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],

            // ── AWS D1.1 — Tabla 6.11 ──────────────────────────────────────────
            // T < 9.5mm (< 3/8 in.) → qualifies tested thickness only (no range extension)
            [
                'standard_id'           => $aws, 'coupon_type' => 'plate',
                'thickness_from_mm'     => 0,
                'thickness_to_mm'       => 9.5,     // < 3/8 in.
                'min_layers'            => null,
                'qualifies_min_mm'      => null,     // same as t
                'qualifies_max_formula' => 't',      // no range: qualifies exactly t
                'notes'                 => 'AWS D1.1 Table 6.11: T < 3/8 in. (9.5 mm) — qualifies tested thickness only',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            // 9.5mm ≤ t < 25.4mm → qualifies min 3.2mm, max 2T
            [
                'standard_id'           => $aws, 'coupon_type' => 'plate',
                'thickness_from_mm'     => 9.5,     // 3/8 in.
                'thickness_to_mm'       => 25.4,    // < 1 in.
                'min_layers'            => null,
                'qualifies_min_mm'      => 3.2,     // 1/8 in.
                'qualifies_max_formula' => '2t',
                'notes'                 => 'AWS D1.1 Table 6.11: 3/8 in. ≤ T < 1 in. — Figures 6.16, 6.17, or 6.19',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            // t ≥ 25.4mm → unlimited
            [
                'standard_id'           => $aws, 'coupon_type' => 'plate',
                'thickness_from_mm'     => 25.4,    // ≥ 1 in.
                'thickness_to_mm'       => null,
                'min_layers'            => null,
                'qualifies_min_mm'      => 3.2,
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'AWS D1.1 Table 6.11: T ≥ 1 in. — qualifies unlimited thickness',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            // Limited qualification: t = 9.5mm (3/8 in.) → max 19.0mm (3/4 in.)
            [
                'standard_id'           => $aws, 'coupon_type' => 'plate',
                'thickness_from_mm'     => 9.5,
                'thickness_to_mm'       => 9.5,
                'min_layers'            => null,
                'qualifies_min_mm'      => 3.2,
                'qualifies_max_formula' => '19.0',
                'notes'                 => 'AWS D1.1 Table 6.11: Limited qualification at 3/8 in. — max 3/4 in. (Fig. 6.20 or 6.21)',
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
        ]);
    }
}
