<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now  = now();
        $asme = DB::table('normas')->where('nombre', 'ASME IX')->value('id');
        $api  = DB::table('normas')->where('nombre', 'API 1104')->value('id');
        $aws  = DB::table('normas')->where('nombre', 'AWS D1.1')->value('id');

        DB::table('test_types')->insertOrIgnore([
            // ── ASME IX — QW-452.1(a) ────────────────────────────────────────
            ['norma_id' => $asme, 'code' => 'VT',   'nombre' => 'Examen Visual',                    'requerido' => true,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'code' => 'BT-F', 'nombre' => 'Ensayo de Doblado de Cara',        'requerido' => true,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'code' => 'BT-R', 'nombre' => 'Ensayo de Doblado de Raíz',        'requerido' => true,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'code' => 'BT-S', 'nombre' => 'Ensayo de Doblado Lateral',        'requerido' => false, 'created_at' => $now, 'updated_at' => $now], // t ≥ 3/8 in.
            ['norma_id' => $asme, 'code' => 'RT',   'nombre' => 'Examen Radiográfico (alternativo)', 'requerido' => false, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $asme, 'code' => 'UT',   'nombre' => 'Examen Ultrasónico',               'requerido' => false, 'created_at' => $now, 'updated_at' => $now],

            // ── API 1104 — Sección 6.4 / Tabla 7 ─────────────────────────────
            ['norma_id' => $api, 'code' => 'VT',   'nombre' => 'Examen Visual',                    'requerido' => true,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $api, 'code' => 'NB',   'nombre' => 'Ensayo de Fractura (Nick Break)',  'requerido' => true,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $api, 'code' => 'BT-R', 'nombre' => 'Ensayo de Doblado de Raíz',       'requerido' => true,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $api, 'code' => 'BT-F', 'nombre' => 'Ensayo de Doblado de Cara',       'requerido' => false, 'created_at' => $now, 'updated_at' => $now], // OD > 12.75 in.
            ['norma_id' => $api, 'code' => 'BT-S', 'nombre' => 'Ensayo de Doblado Lateral',       'requerido' => false, 'created_at' => $now, 'updated_at' => $now], // t > 0.5 in.
            ['norma_id' => $api, 'code' => 'RT',   'nombre' => 'Examen Radiográfico (alternativo)', 'requerido' => false, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $api, 'code' => 'UT',   'nombre' => 'Examen Ultrasónico',              'requerido' => false, 'created_at' => $now, 'updated_at' => $now],

            // ── AWS D1.1 — Sección 6.28 ───────────────────────────────────────
            ['norma_id' => $aws, 'code' => 'VT',   'nombre' => 'Examen Visual',                    'requerido' => true,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $aws, 'code' => 'BT-F', 'nombre' => 'Ensayo de Doblado de Cara',        'requerido' => true,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $aws, 'code' => 'BT-R', 'nombre' => 'Ensayo de Doblado de Raíz',        'requerido' => true,  'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $aws, 'code' => 'BT-S', 'nombre' => 'Ensayo de Doblado Lateral',        'requerido' => false, 'created_at' => $now, 'updated_at' => $now], // T ≥ 3/8 in.
            ['norma_id' => $aws, 'code' => 'RT',   'nombre' => 'Examen Radiográfico (alternativo)', 'requerido' => false, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $aws, 'code' => 'MC',   'nombre' => 'Macro Examen (filete)',             'requerido' => false, 'created_at' => $now, 'updated_at' => $now], // Clause 6.28.2 fillet
            ['norma_id' => $aws, 'code' => 'MT',   'nombre' => 'Examen de Partículas Magnéticas',  'requerido' => false, 'created_at' => $now, 'updated_at' => $now],
            ['norma_id' => $aws, 'code' => 'PT',   'nombre' => 'Examen de Líquidos Penetrantes',   'requerido' => false, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
