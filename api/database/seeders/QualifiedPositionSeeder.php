<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QualifiedPositionSeeder extends Seeder
{
    public function run(): void
    {
        $now  = now();
        $asme = DB::table('normas')->where('nombre', 'ASME IX')->value('id');
        $api  = DB::table('normas')->where('nombre', 'API 1104')->value('id');
        $aws  = DB::table('normas')->where('nombre', 'AWS D1.1')->value('id');
        $nagD = DB::table('normas')->where('nombre', 'NAG 105 Cat. D')->value('id');

        // kocert usa 'codigo' en lugar de 'code'
        $pos = DB::table('posiciones')->pluck('id', 'codigo');

        $rows = [];

        // Agrega filas tested → qualified para un tipo de junta
        $add = function (int $norma, string $tested, array $qualifies, string $jointType)
            use (&$rows, $now, $pos)
        {
            if (! isset($pos[$tested])) return;
            foreach ($qualifies as $qualified) {
                if (! isset($pos[$qualified])) continue;
                $rows[] = [
                    'norma_id'               => $norma,
                    'tested_posicion_id'     => $pos[$tested],
                    'qualified_posicion_id'  => $pos[$qualified],
                    'joint_type'             => $jointType,
                    'created_at'             => $now,
                    'updated_at'             => $now,
                ];
            }
        };

        // Conjuntos de posiciones kocert
        // Plate groove: 1G-P, 2G-P, 3G, 4G
        // Pipe groove:  1G (rotado), 2G (horiz fijo), 5G (horiz fijo), 6G (45°)
        $allGroove = ['1G-P', '2G-P', '3G', '4G', '1G', '2G', '5G', '6G'];
        $allFillet = ['1F', '2F', '3F', '4F', '5F'];

        // ── ASME IX — QW-461.9 ───────────────────────────────────────────────

        // Ranuras en chapa (plate groove)
        $add($asme, '1G-P', ['1G-P'],                            'ranura');
        $add($asme, '1G-P', ['1F'],                              'filete');

        $add($asme, '2G-P', ['1G-P', '2G-P'],                   'ranura');
        $add($asme, '2G-P', ['1F', '2F'],                        'filete');

        $add($asme, '3G',   ['1G-P', '3G'],                     'ranura');
        $add($asme, '3G',   ['1F', '2F', '3F'],                  'filete');

        $add($asme, '4G',   ['1G-P', '4G'],                     'ranura');
        $add($asme, '4G',   ['1F', '2F', '4F'],                  'filete');

        // Ranuras en tubería (pipe groove)
        // 1G (tubería rotada) = posición plana — equivale a 1G de placa
        $add($asme, '1G',   ['1G', '1G-P'],                     'ranura');
        $add($asme, '1G',   ['1F'],                              'filete');

        // 2G (tubería eje vertical fijo) = plana + horizontal
        $add($asme, '2G',   ['1G', '1G-P', '2G', '2G-P'],      'ranura');
        $add($asme, '2G',   ['1F', '2F'],                        'filete');

        // 5G (tubería eje horizontal fijo) = F + V + O
        $add($asme, '5G',   ['1G', '1G-P', '3G', '4G', '5G'],  'ranura');
        $add($asme, '5G',   $allFillet,                           'filete');

        // 6G (tubería eje 45° fijo) = todo posición
        $add($asme, '6G',   $allGroove,                          'ranura');
        $add($asme, '6G',   $allFillet,                           'filete');

        // Sólo filete
        $add($asme, '1F',   ['1F'],                              'filete');
        $add($asme, '2F',   ['1F', '2F'],                        'filete');
        $add($asme, '3F',   ['1F', '2F', '3F'],                  'filete');
        $add($asme, '4F',   ['1F', '2F', '4F'],                  'filete');
        $add($asme, '5F',   $allFillet,                           'filete');

        // ── API 1104 — §6.2.2(f) ─────────────────────────────────────────────

        // Rolled (= 1G rotado) → solo posición plana
        $add($api, '1G',    ['1G', '1G-P'],                     'ranura');

        // Fixed horizontal (= 5G) → F + V + O (todas posiciones fijas)
        $add($api, '5G',    ['1G', '1G-P', '3G', '4G', '5G'],  'ranura');

        // Fixed 45° (= 6G) → todas
        $add($api, '6G',    $allGroove,                          'ranura');

        // ── AWS D1.1 — Tabla 6.10 ────────────────────────────────────────────

        // Chapa (plate)
        $add($aws, '1G-P', ['1G-P'],                             'ranura');
        $add($aws, '1G-P', ['1F', '2F'],                         'filete'); // D1.1: 1G califica F y H filete

        $add($aws, '2G-P', ['1G-P', '2G-P'],                    'ranura');
        $add($aws, '2G-P', ['1F', '2F'],                         'filete');

        $add($aws, '3G',   ['1G-P', '2G-P', '3G'],              'ranura');
        $add($aws, '3G',   ['1F', '2F', '3F'],                   'filete');

        $add($aws, '4G',   ['1G-P', '2G-P', '4G'],              'ranura');
        $add($aws, '4G',   ['1F', '2F', '4F'],                   'filete');

        // Tubería
        $add($aws, '5G',   $allGroove,                           'ranura');
        $add($aws, '5G',   $allFillet,                            'filete');

        $add($aws, '6G',   $allGroove,                           'ranura');
        $add($aws, '6G',   $allFillet,                            'filete');

        // Sólo filete (AWS D1.1)
        $add($aws, '1F',   ['1F'],                               'filete');
        $add($aws, '2F',   ['1F', '2F'],                         'filete');
        $add($aws, '3F',   ['1F', '2F', '3F'],                   'filete');
        $add($aws, '4F',   ['1F', '2F', '4F'],                   'filete');

        // ── NAG 105 Cat. D — EPS N°1 ─────────────────────────────────────────
        // Posición de ensayo: eje horizontal fijo (= 5G en tubería)
        // Califica: plana (1G) + vertical (3G/4G) + sobre cabeza (4G) + horizontal fijo (5G)
        if ($nagD) {
            $add($nagD, '5G', ['1G', '1G-P', '3G', '4G', '5G'], 'ranura');
            $add($nagD, '2G', ['1G', '1G-P', '2G'],              'ranura');
            $add($nagD, '1G', ['1G', '1G-P'],                    'ranura');
        }

        DB::table('qualified_positions')->insertOrIgnore($rows);
    }
}
