<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PosicionSeeder extends Seeder
{
    public function run(): void
    {
        // Abreviaturas: F=Plana, H=Horizontal, V=Vertical, O=Sobrecabeza
        // Fuente: QW-461.9 (ASME IX) + Tabla 6.10 (AWS D1.1) + Sección 6 (API 1104)
        $posiciones = [
            // ── Soldaduras de ranura en tubería ──────────────────────────────
            [
                'codigo'          => '1G',
                'descripcion'     => 'Tubería — plana (eje horizontal rotado)',
                'norma'           => null,
                'tipo'            => 'ranura',
                'es_tuberia'      => true,
                'califica_ranura' => json_encode(['F']),
                'califica_filete' => json_encode(['F']),
            ],
            [
                'codigo'          => '2G',
                'descripcion'     => 'Tubería — horizontal (eje vertical fijo)',
                'norma'           => null,
                'tipo'            => 'ranura',
                'es_tuberia'      => true,
                'califica_ranura' => json_encode(['F', 'H']),
                'califica_filete' => json_encode(['F', 'H']),
            ],
            [
                'codigo'          => '5G',
                'descripcion'     => 'Tubería — eje horizontal fijo (soldeo en F, V, O)',
                'norma'           => null,
                'tipo'            => 'ranura',
                'es_tuberia'      => true,
                'califica_ranura' => json_encode(['F', 'V', 'O']),
                'califica_filete' => json_encode(['F', 'H', 'V', 'O']),
            ],
            [
                'codigo'          => '6G',
                'descripcion'     => 'Tubería — eje inclinado 45° fijo (todo posición)',
                'norma'           => null,
                'tipo'            => 'ranura',
                'es_tuberia'      => true,
                'califica_ranura' => json_encode(['F', 'H', 'V', 'O']),
                'califica_filete' => json_encode(['F', 'H', 'V', 'O']),
            ],
            // ── Soldaduras de ranura en chapa ────────────────────────────────
            [
                'codigo'          => '1G-P',
                'descripcion'     => 'Chapa — plana',
                'norma'           => null,
                'tipo'            => 'ranura',
                'es_tuberia'      => false,
                'califica_ranura' => json_encode(['F']),
                'califica_filete' => json_encode(['F']),
            ],
            [
                'codigo'          => '2G-P',
                'descripcion'     => 'Chapa — horizontal',
                'norma'           => null,
                'tipo'            => 'ranura',
                'es_tuberia'      => false,
                'califica_ranura' => json_encode(['F', 'H']),
                'califica_filete' => json_encode(['F', 'H']),
            ],
            [
                'codigo'          => '3G',
                'descripcion'     => 'Chapa — vertical (soldeo ascendente o descendente)',
                'norma'           => null,
                'tipo'            => 'ranura',
                'es_tuberia'      => false,
                'califica_ranura' => json_encode(['F', 'H', 'V']),
                'califica_filete' => json_encode(['F', 'H', 'V']),
            ],
            [
                'codigo'          => '4G',
                'descripcion'     => 'Chapa — sobrecabeza',
                'norma'           => null,
                'tipo'            => 'ranura',
                'es_tuberia'      => false,
                'califica_ranura' => json_encode(['F', 'O']),
                'califica_filete' => json_encode(['F', 'H', 'O']),
            ],
            // ── Soldaduras de filete ──────────────────────────────────────────
            [
                'codigo'          => '1F',
                'descripcion'     => 'Filete — plano',
                'norma'           => null,
                'tipo'            => 'filete',
                'es_tuberia'      => false,
                'califica_ranura' => json_encode([]),
                'califica_filete' => json_encode(['F']),
            ],
            [
                'codigo'          => '2F',
                'descripcion'     => 'Filete — horizontal',
                'norma'           => null,
                'tipo'            => 'filete',
                'es_tuberia'      => false,
                'califica_ranura' => json_encode([]),
                'califica_filete' => json_encode(['F', 'H']),
            ],
            [
                'codigo'          => '3F',
                'descripcion'     => 'Filete — vertical',
                'norma'           => null,
                'tipo'            => 'filete',
                'es_tuberia'      => false,
                'califica_ranura' => json_encode([]),
                'califica_filete' => json_encode(['F', 'H', 'V']),
            ],
            [
                'codigo'          => '4F',
                'descripcion'     => 'Filete — sobrecabeza',
                'norma'           => null,
                'tipo'            => 'filete',
                'es_tuberia'      => false,
                'califica_ranura' => json_encode([]),
                'califica_filete' => json_encode(['F', 'H', 'O']),
            ],
            [
                'codigo'          => '5F',
                'descripcion'     => 'Filete — tubería todo posición',
                'norma'           => null,
                'tipo'            => 'filete',
                'es_tuberia'      => true,
                'califica_ranura' => json_encode([]),
                'califica_filete' => json_encode(['F', 'H', 'V', 'O']),
            ],
        ];

        // ── Posiciones ISO 9606-1 (para IRAM) ────────────────────────────────────
        $posicionesIso = [
            ['codigo' => 'PA',     'descripcion' => 'Plana (ISO) — equivalente 1G/1F',                    'norma' => 'iso', 'tipo' => 'ambas',  'es_tuberia' => false],
            ['codigo' => 'PB',     'descripcion' => 'Horizontal para filete (ISO) — equivalente 2F',      'norma' => 'iso', 'tipo' => 'filete', 'es_tuberia' => false],
            ['codigo' => 'PC',     'descripcion' => 'Horizontal para ranura (ISO) — equivalente 2G',      'norma' => 'iso', 'tipo' => 'ranura', 'es_tuberia' => false],
            ['codigo' => 'PD',     'descripcion' => 'Sobrecabeza filete (ISO) — equivalente 4F',          'norma' => 'iso', 'tipo' => 'filete', 'es_tuberia' => false],
            ['codigo' => 'PE',     'descripcion' => 'Sobrecabeza ranura (ISO) — equivalente 4G',          'norma' => 'iso', 'tipo' => 'ranura', 'es_tuberia' => false],
            ['codigo' => 'PF',     'descripcion' => 'Vertical ascendente (ISO) — equivalente 3G up',      'norma' => 'iso', 'tipo' => 'ambas',  'es_tuberia' => false],
            ['codigo' => 'PG',     'descripcion' => 'Vertical descendente (ISO) — equivalente 3G down',   'norma' => 'iso', 'tipo' => 'ambas',  'es_tuberia' => false],
            ['codigo' => 'PH',     'descripcion' => 'Tubo eje horizontal fijo ascendente (ISO) — 5G up',  'norma' => 'iso', 'tipo' => 'ranura', 'es_tuberia' => true],
            ['codigo' => 'PJ',     'descripcion' => 'Tubo eje horizontal fijo descendente (ISO) — 5G down','norma' => 'iso', 'tipo' => 'ranura', 'es_tuberia' => true],
            ['codigo' => 'H-L045', 'descripcion' => 'Tubo eje inclinado 45° (ISO) — equivalente 6G',     'norma' => 'iso', 'tipo' => 'ranura', 'es_tuberia' => true],
        ];

        foreach ($posicionesIso as $row) {
            DB::table('posiciones')->updateOrInsert(
                ['codigo' => $row['codigo']],
                array_merge($row, [
                    'califica_ranura' => json_encode([]),
                    'califica_filete' => json_encode([]),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ])
            );
        }

        foreach ($posiciones as $row) {
            DB::table('posiciones')->updateOrInsert(
                ['codigo' => $row['codigo']],
                array_merge($row, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
