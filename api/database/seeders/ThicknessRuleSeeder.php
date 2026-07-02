<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThicknessRuleSeeder extends Seeder
{
    public function run(): void
    {
        $now  = now();
        $asme = DB::table('normas')->where('nombre', 'ASME IX')->value('id');
        $api  = DB::table('normas')->where('nombre', 'API 1104')->value('id');
        $aws  = DB::table('normas')->where('nombre', 'AWS D1.1')->value('id');
        $nagD = DB::table('normas')->where('nombre', 'NAG 105 Cat. D')->value('id');

        DB::table('thickness_rules')->insertOrIgnore([
            // ── ASME IX — QW-452.1(b) ──────────────────────────────────────────
            [
                'norma_id'              => $asme, 'coupon_type' => 'ambos',
                'thickness_from_mm'     => 0,
                'thickness_to_mm'       => null,
                'min_layers'            => null,
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => '2t',
                'notes'                 => 'QW-452.1(b): cualquier espesor t califica hasta 2t',
                'created_at'            => $now, 'updated_at' => $now,
            ],
            [
                'norma_id'              => $asme, 'coupon_type' => 'ambos',
                'thickness_from_mm'     => 13,
                'thickness_to_mm'       => null,
                'min_layers'            => 3,
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'QW-452.1(b): t ≥ 13mm Y ≥3 capas depositadas → ilimitado',
                'created_at'            => $now, 'updated_at' => $now,
            ],

            // ── API 1104 — Tabla 5 ─────────────────────────────────────────────
            [
                'norma_id'              => $api, 'coupon_type' => 'caño',
                'thickness_from_mm'     => 0,
                'thickness_to_mm'       => 3.9,
                'min_layers'            => null,
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => 'max(3.9,1.5t)',
                'notes'                 => 'API 1104 Tabla 5: t < 3.9 mm (0.154 in.)',
                'created_at'            => $now, 'updated_at' => $now,
            ],
            [
                'norma_id'              => $api, 'coupon_type' => 'caño',
                'thickness_from_mm'     => 3.9,
                'thickness_to_mm'       => 19.0,
                'min_layers'            => null,
                'qualifies_min_mm'      => 3.9,
                'qualifies_max_formula' => 'max(19.0,1.5t)',
                'notes'                 => 'API 1104 Tabla 5: 3.9 mm ≤ t < 19 mm',
                'created_at'            => $now, 'updated_at' => $now,
            ],
            [
                'norma_id'              => $api, 'coupon_type' => 'caño',
                'thickness_from_mm'     => 19.0,
                'thickness_to_mm'       => null,
                'min_layers'            => null,
                'qualifies_min_mm'      => 19.0,
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'API 1104 Tabla 5: t ≥ 19 mm → ilimitado',
                'created_at'            => $now, 'updated_at' => $now,
            ],

            // ── AWS D1.1 — Tabla 6.11 ──────────────────────────────────────────
            [
                'norma_id'              => $aws, 'coupon_type' => 'chapa',
                'thickness_from_mm'     => 9.5,
                'thickness_to_mm'       => 25.4,
                'min_layers'            => null,
                'qualifies_min_mm'      => 3.2,
                'qualifies_max_formula' => '2t',
                'notes'                 => 'AWS D1.1 Tabla 6.11: 9.5 mm ≤ T < 25.4 mm',
                'created_at'            => $now, 'updated_at' => $now,
            ],
            [
                'norma_id'              => $aws, 'coupon_type' => 'chapa',
                'thickness_from_mm'     => 25.4,
                'thickness_to_mm'       => null,
                'min_layers'            => null,
                'qualifies_min_mm'      => 3.2,
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'AWS D1.1 Tabla 6.11: T ≥ 25.4 mm → ilimitado',
                'created_at'            => $now, 'updated_at' => $now,
            ],
            [
                'norma_id'              => $aws, 'coupon_type' => 'chapa',
                'thickness_from_mm'     => 9.5,
                'thickness_to_mm'       => 9.5,
                'min_layers'            => null,
                'qualifies_min_mm'      => 3.2,
                'qualifies_max_formula' => '19.0',
                'notes'                 => 'AWS D1.1 Tabla 6.11: calificación limitada T = 9.5 mm → max 19 mm',
                'created_at'            => $now, 'updated_at' => $now,
            ],

            // ── NAG 105 Cat. D — EPS N°1 (espesor máx. 19 mm = ¾") ────────────
            [
                'norma_id'              => $nagD, 'coupon_type' => 'caño',
                'thickness_from_mm'     => 0,
                'thickness_to_mm'       => 9.5,
                'min_layers'            => null,
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => '2t',
                'notes'                 => 'NAG Cat. D EPS N°1: t ≤ 9.5 mm → califica hasta 2t (< 19 mm)',
                'created_at'            => $now, 'updated_at' => $now,
            ],
            [
                'norma_id'              => $nagD, 'coupon_type' => 'caño',
                'thickness_from_mm'     => 9.5,
                'thickness_to_mm'       => 19.0,
                'min_layers'            => null,
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => '19.0',
                'notes'                 => 'NAG Cat. D EPS N°1: 9.5 mm < t ≤ 19 mm → califica hasta 19 mm (cap EPS N°1)',
                'created_at'            => $now, 'updated_at' => $now,
            ],
        ]);
    }
}
