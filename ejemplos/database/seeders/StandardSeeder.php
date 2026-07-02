<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StandardSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ── Base standards (no parent) ────────────────────────────────────────
        DB::table('standards')->insertOrIgnore([
            [
                'code'                  => 'ASME IX',
                'full_name'             => 'Boiler and Pressure Vessel Code, Section IX — Welding, Brazing, and Fusing Qualifications',
                'organization'          => 'ASME',
                'edition'               => '2021',
                'active'                => true,
                'inactivity_limit_days' => 180,  // QW-322.1: 6-month inactivity per process
                'validity_years'        => null,  // No norm-mandated expiry; only inactivity applies
                'parent_standard_id'    => null,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'code'                  => 'AWS D1.1',
                'full_name'             => 'Structural Welding Code — Steel',
                'organization'          => 'AWS',
                'edition'               => '2020',
                'active'                => true,
                'inactivity_limit_days' => 180,  // Clause 6.28: 6-month inactivity invalidates
                'validity_years'        => null,
                'parent_standard_id'    => null,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'code'                  => 'API 1104',
                'full_name'             => 'Welding of Pipelines and Related Facilities',
                'organization'          => 'API',
                'edition'               => '2021',
                'active'                => true,
                'inactivity_limit_days' => 180,  // Section 6.1: "within the preceding 6 months"
                'validity_years'        => null,
                'parent_standard_id'    => null,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'code'                  => 'AWS D1.3',
                'full_name'             => 'Structural Welding Code — Sheet Steel',
                'organization'          => 'AWS',
                'edition'               => '2018',
                'active'                => true,
                'inactivity_limit_days' => 180,
                'validity_years'        => null,
                'parent_standard_id'    => null,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'code'                  => 'NAG 100',
                'full_name'             => 'Norma Argentina de Gas 100 — Habilitación de Personal',
                'organization'          => 'ENARGAS',
                'edition'               => '2022',
                'active'                => true,
                'inactivity_limit_days' => 90,   // NAG 100 §7: 90-day inactivity limit
                'validity_years'        => 2,     // NAG 100 §8: 2-year credential validity
                'parent_standard_id'    => null,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'code'                  => 'NAG 201',
                'full_name'             => 'Norma Argentina de Gas 201 — Instalaciones de Gas para Uso Residencial, Comercial e Industrial',
                'organization'          => 'ENARGAS',
                'edition'               => '2022',
                'active'                => true,
                'inactivity_limit_days' => 90,
                'validity_years'        => 2,
                'parent_standard_id'    => null,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
        ]);

        // ── NAG 105 categories — parent_standard_id resolved after base records exist ──
        // Each category delegates its qualification method to a parent norm:
        //   Cat. A/B → ASME IX rules (pressure vessels, process piping)
        //   Cat. C   → API 1104 rules (distribution & transmission pipelines)
        //   Cat. D   → EPS N°1 internal standard (no parent — enforced in code)
        //              Restrictions: SMAW only, E6010/E6015, OD ≤ 323.8 mm, t ≤ 19 mm, P ≤ 25 kg/cm², i < 20% SMYS

        $asmeId = DB::table('standards')->where('code', 'ASME IX')->value('id');
        $apiId  = DB::table('standards')->where('code', 'API 1104')->value('id');

        DB::table('standards')->insertOrIgnore([
            [
                'code'                  => 'NAG 105 Cat. A',
                'full_name'             => 'NAG 105 — Calificación de Soldadores — Categoría A (recipientes a presión, intercambiadores)',
                'organization'          => 'ENARGAS',
                'edition'               => '2022',
                'active'                => true,
                'inactivity_limit_days' => 90,
                'validity_years'        => 2,
                'parent_standard_id'    => $asmeId,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'code'                  => 'NAG 105 Cat. B',
                'full_name'             => 'NAG 105 — Calificación de Soldadores — Categoría B (cañería de proceso)',
                'organization'          => 'ENARGAS',
                'edition'               => '2022',
                'active'                => true,
                'inactivity_limit_days' => 90,
                'validity_years'        => 2,
                'parent_standard_id'    => $asmeId,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'code'                  => 'NAG 105 Cat. C',
                'full_name'             => 'NAG 105 — Calificación de Soldadores — Categoría C (ductos de distribución y transporte)',
                'organization'          => 'ENARGAS',
                'edition'               => '2022',
                'active'                => true,
                'inactivity_limit_days' => 90,
                'validity_years'        => 2,
                'parent_standard_id'    => $apiId,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'code'                  => 'NAG 105 Cat. D',
                'full_name'             => 'NAG 105 — Calificación de Soldadores — Categoría D (EPS N°1: SMAW, OD ≤ 323.8 mm, t ≤ 19 mm, P ≤ 25 kg/cm², i < 20% SMYS)',
                'organization'          => 'ENARGAS',
                'edition'               => '2022',
                'active'                => true,
                'inactivity_limit_days' => 90,
                'validity_years'        => 2,
                'parent_standard_id'    => null,  // EPS N°1 internal — no registered parent standard
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
        ]);
    }
}
