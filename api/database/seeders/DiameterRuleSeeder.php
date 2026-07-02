<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiameterRuleSeeder extends Seeder
{
    public function run(): void
    {
        $now  = now();
        $asme = DB::table('normas')->where('nombre', 'ASME IX')->value('id');
        $api  = DB::table('normas')->where('nombre', 'API 1104')->value('id');
        $nagD = DB::table('normas')->where('nombre', 'NAG 105 Cat. D')->value('id');

        DB::table('diameter_rules')->insertOrIgnore([
            // ── ASME IX — QW-452.3 (ranuras en caño/tubería) ──────────────────
            [
                'norma_id'              => $asme, 'coupon_type' => 'caño',
                'diameter_from_mm'      => 0,
                'diameter_to_mm'        => 25.4,
                'qualifies_min_mm'      => null,  // desde el diámetro ensayado
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'QW-452.3: OD < 25.4 mm → desde OD ensayado, ilimitado',
                'created_at'            => $now, 'updated_at' => $now,
            ],
            [
                'norma_id'              => $asme, 'coupon_type' => 'caño',
                'diameter_from_mm'      => 25.4,
                'diameter_to_mm'        => 73.0,
                'qualifies_min_mm'      => 25.4,
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'QW-452.3: 25.4 mm ≤ OD ≤ 73 mm → desde 25.4 mm, ilimitado',
                'created_at'            => $now, 'updated_at' => $now,
            ],
            [
                'norma_id'              => $asme, 'coupon_type' => 'caño',
                'diameter_from_mm'      => 73.0,
                'diameter_to_mm'        => null,
                'qualifies_min_mm'      => 73.0,
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'QW-452.3: OD > 73 mm → desde 73 mm, ilimitado',
                'created_at'            => $now, 'updated_at' => $now,
            ],

            // ── API 1104 — §6.2.2(d) — Grupos de diámetro ────────────────────
            [
                'norma_id'              => $api, 'coupon_type' => 'caño',
                'diameter_from_mm'      => 0,
                'diameter_to_mm'        => 60.3,
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => '60.3',
                'notes'                 => 'API 1104 §6.2.2(d) Grupo 1: OD < 60.3 mm → solo Grupo 1',
                'created_at'            => $now, 'updated_at' => $now,
            ],
            [
                'norma_id'              => $api, 'coupon_type' => 'caño',
                'diameter_from_mm'      => 60.3,
                'diameter_to_mm'        => 323.9,
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => '323.9',
                'notes'                 => 'API 1104 §6.2.2(d) Grupo 2: 60.3–323.9 mm → Grupos 1 y 2',
                'created_at'            => $now, 'updated_at' => $now,
            ],
            [
                'norma_id'              => $api, 'coupon_type' => 'caño',
                'diameter_from_mm'      => 323.9,
                'diameter_to_mm'        => null,
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => 'unlimited',
                'notes'                 => 'API 1104 §6.2.2(d) Grupo 3: OD > 323.9 mm → todos los grupos',
                'created_at'            => $now, 'updated_at' => $now,
            ],

            // ── NAG 105 Cat. D — EPS N°1 (OD máx. 323,8 mm = 12¾") ──────────
            // Buckets NAG §6.4: 2"=50,8mm y 12"=304,8mm (≠ API 1104 que usa 60,3/323,9mm)
            [
                'norma_id'              => $nagD, 'coupon_type' => 'caño',
                'diameter_from_mm'      => 0,
                'diameter_to_mm'        => 50.8,
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => '50.8',
                'notes'                 => 'NAG Cat. D EPS N°1 Grupo 1: OD < 50,8 mm (< 2")',
                'created_at'            => $now, 'updated_at' => $now,
            ],
            [
                'norma_id'              => $nagD, 'coupon_type' => 'caño',
                'diameter_from_mm'      => 50.8,
                'diameter_to_mm'        => 304.8,
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => '304.8',
                'notes'                 => 'NAG Cat. D EPS N°1 Grupo 2: 50,8–304,8 mm (2"–12")',
                'created_at'            => $now, 'updated_at' => $now,
            ],
            [
                'norma_id'              => $nagD, 'coupon_type' => 'caño',
                'diameter_from_mm'      => 304.8,
                'diameter_to_mm'        => 323.8,
                'qualifies_min_mm'      => null,
                'qualifies_max_formula' => '323.8',
                'notes'                 => 'NAG Cat. D EPS N°1 Grupo 3: 304,8–323,8 mm (12"–12¾") — techo EPS N°1',
                'created_at'            => $now, 'updated_at' => $now,
            ],
        ]);
    }
}
