<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now  = now();
        $asme = DB::table('standards')->where('code', 'ASME IX')->value('id');
        $api  = DB::table('standards')->where('code', 'API 1104')->value('id');
        $aws  = DB::table('standards')->where('code', 'AWS D1.1')->value('id');
        $d13  = DB::table('standards')->where('code', 'AWS D1.3')->value('id');

        DB::table('test_types')->insertOrIgnore([
            // ── ASME IX — QW-452.1(a) ────────────────────────────────────────
            ['standard_id' => $asme, 'code' => 'VT',   'name' => 'Visual Examination',            'name_es' => 'Examen Visual',                       'required' => true,  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'BT-F', 'name' => 'Face Bend Test',                'name_es' => 'Ensayo de Doblado de Cara',            'required' => true,  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'BT-R', 'name' => 'Root Bend Test',                'name_es' => 'Ensayo de Doblado de Raíz',            'required' => true,  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $asme, 'code' => 'BT-S', 'name' => 'Side Bend Test',                'name_es' => 'Ensayo de Doblado Lateral',            'required' => false, 'created_at' => $now, 'updated_at' => $now], // required when t ≥ 3/8 in.
            ['standard_id' => $asme, 'code' => 'RT',   'name' => 'Radiographic Examination',      'name_es' => 'Examen Radiográfico',                  'required' => false, 'created_at' => $now, 'updated_at' => $now], // alternative to bend tests
            ['standard_id' => $asme, 'code' => 'UT',   'name' => 'Ultrasonic Examination',        'name_es' => 'Examen Ultrasónico',                   'required' => false, 'created_at' => $now, 'updated_at' => $now],

            // ── API 1104 — Section 6.4 / Table 7 ─────────────────────────────
            ['standard_id' => $api,  'code' => 'VT',   'name' => 'Visual Examination',            'name_es' => 'Examen Visual',                       'required' => true,  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $api,  'code' => 'NB',   'name' => 'Nick Break Test',               'name_es' => 'Ensayo de Fractura (Nick Break)',      'required' => true,  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $api,  'code' => 'BT-R', 'name' => 'Root Bend Test',                'name_es' => 'Ensayo de Doblado de Raíz',            'required' => true,  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $api,  'code' => 'BT-F', 'name' => 'Face Bend Test',                'name_es' => 'Ensayo de Doblado de Cara',            'required' => false, 'created_at' => $now, 'updated_at' => $now], // required when OD > 12.75 in.
            ['standard_id' => $api,  'code' => 'BT-S', 'name' => 'Side Bend Test',                'name_es' => 'Ensayo de Doblado Lateral',            'required' => false, 'created_at' => $now, 'updated_at' => $now], // required when t > 0.5 in.
            ['standard_id' => $api,  'code' => 'RT',   'name' => 'Radiographic Examination',      'name_es' => 'Examen Radiográfico',                  'required' => false, 'created_at' => $now, 'updated_at' => $now], // alternative (Section 6.5)
            ['standard_id' => $api,  'code' => 'UT',   'name' => 'Ultrasonic Examination',        'name_es' => 'Examen Ultrasónico',                   'required' => false, 'created_at' => $now, 'updated_at' => $now],

            // ── AWS D1.1 — Section 6.28 ───────────────────────────────────────
            ['standard_id' => $aws,  'code' => 'VT',   'name' => 'Visual Examination',            'name_es' => 'Examen Visual',                       'required' => true,  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $aws,  'code' => 'BT-F', 'name' => 'Face Bend Test',                'name_es' => 'Ensayo de Doblado de Cara',            'required' => true,  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $aws,  'code' => 'BT-R', 'name' => 'Root Bend Test',                'name_es' => 'Ensayo de Doblado de Raíz',            'required' => true,  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $aws,  'code' => 'BT-S', 'name' => 'Side Bend Test',                'name_es' => 'Ensayo de Doblado Lateral',            'required' => false, 'created_at' => $now, 'updated_at' => $now], // required when T ≥ 3/8 in.
            ['standard_id' => $aws,  'code' => 'RT',   'name' => 'Radiographic Examination',      'name_es' => 'Examen Radiográfico',                  'required' => false, 'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $aws,  'code' => 'MT',   'name' => 'Magnetic Particle Examination', 'name_es' => 'Examen de Partículas Magnéticas',      'required' => false, 'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $aws,  'code' => 'PT',   'name' => 'Liquid Penetrant Examination',  'name_es' => 'Examen de Líquidos Penetrantes',       'required' => false, 'created_at' => $now, 'updated_at' => $now],

            // ── AWS D1.3 — Section 4 ──────────────────────────────────────────
            ['standard_id' => $d13,  'code' => 'VT',   'name' => 'Visual Examination',            'name_es' => 'Examen Visual',                       'required' => true,  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $d13,  'code' => 'BT-F', 'name' => 'Face Bend Test',                'name_es' => 'Ensayo de Doblado de Cara',            'required' => true,  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $d13,  'code' => 'BT-R', 'name' => 'Root Bend Test',                'name_es' => 'Ensayo de Doblado de Raíz',            'required' => true,  'created_at' => $now, 'updated_at' => $now],
            ['standard_id' => $d13,  'code' => 'MS',   'name' => 'Macro Section Examination',     'name_es' => 'Examen de Macrografía',                'required' => false, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
