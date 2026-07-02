<?php

namespace Database\Seeders;

use App\Models\Soldador;
use Illuminate\Database\Seeder;

class SoldadorSeeder extends Seeder
{
    public function run(): void
    {
        $soldadores = [
            ['apellido' => 'González',   'nombre' => 'Carlos',    'dni' => '28541032', 'cuño' => 'K-001', 'ciudad' => 'Buenos Aires',   'telefono' => '11-4523-8871', 'email' => 'cgonzalez@mail.com',   'activo' => true ],
            ['apellido' => 'Ramírez',    'nombre' => 'Luis',      'dni' => '31287654', 'cuño' => 'K-002', 'ciudad' => 'Rosario',         'telefono' => '341-567-9012', 'email' => 'lramirez@mail.com',    'activo' => true ],
            ['apellido' => 'Fernández',  'nombre' => 'Martín',    'dni' => '25963148', 'cuño' => 'K-003', 'ciudad' => 'Córdoba',         'telefono' => '351-234-5678', 'email' => null,                   'activo' => true ],
            ['apellido' => 'López',      'nombre' => 'Sebastián', 'dni' => '33741025', 'cuño' => 'K-004', 'ciudad' => 'Mendoza',         'telefono' => '261-890-1234', 'email' => 'slopez@mail.com',      'activo' => true ],
            ['apellido' => 'Martínez',   'nombre' => 'Diego',     'dni' => '29874561', 'cuño' => 'K-005', 'ciudad' => 'La Plata',        'telefono' => null,           'email' => 'dmartinez@mail.com',   'activo' => true ],
            ['apellido' => 'Sánchez',    'nombre' => 'Pablo',     'dni' => '27652341', 'cuño' => 'K-006', 'ciudad' => 'Mar del Plata',   'telefono' => '223-456-7890', 'email' => null,                   'activo' => true ],
            ['apellido' => 'Rodríguez',  'nombre' => 'Alejandro', 'dni' => '35214987', 'cuño' => 'K-007', 'ciudad' => 'Tucumán',         'telefono' => '381-234-5670', 'email' => 'arodriguez@mail.com',  'activo' => true ],
            ['apellido' => 'Pérez',      'nombre' => 'Ricardo',   'dni' => '24178963', 'cuño' => 'K-008', 'ciudad' => 'Salta',           'telefono' => '387-678-9012', 'email' => null,                   'activo' => false],
            ['apellido' => 'Torres',     'nombre' => 'Federico',  'dni' => '30256714', 'cuño' => 'K-009', 'ciudad' => 'San Juan',        'telefono' => '264-012-3456', 'email' => 'ftorres@mail.com',     'activo' => true ],
            ['apellido' => 'Herrera',    'nombre' => 'Daniel',    'dni' => '32698745', 'cuño' => 'K-010', 'ciudad' => 'Neuquén',         'telefono' => '299-890-1234', 'email' => null,                   'activo' => true ],
            ['apellido' => 'Díaz',       'nombre' => 'Gustavo',   'dni' => '26543219', 'cuño' => 'K-011', 'ciudad' => 'Corrientes',      'telefono' => '379-456-7890', 'email' => 'gdiaz@mail.com',       'activo' => true ],
            ['apellido' => 'Moreno',     'nombre' => 'Claudio',   'dni' => '34187256', 'cuño' => 'K-012', 'ciudad' => 'Resistencia',     'telefono' => null,           'email' => null,                   'activo' => false],
            ['apellido' => 'Álvarez',    'nombre' => 'Javier',    'dni' => '23654789', 'cuño' => 'K-013', 'ciudad' => 'Posadas',         'telefono' => '376-012-3456', 'email' => 'jalvarez@mail.com',    'activo' => true ],
            ['apellido' => 'Romero',     'nombre' => 'Néstor',    'dni' => '31089654', 'cuño' => 'K-014', 'ciudad' => 'Santa Rosa',      'telefono' => '954-678-9012', 'email' => null,                   'activo' => true ],
            ['apellido' => 'Vargas',     'nombre' => 'Marcelo',   'dni' => '28963147', 'cuño' => 'K-015', 'ciudad' => 'Bahía Blanca',    'telefono' => '291-234-5678', 'email' => 'mvargas@mail.com',     'activo' => true ],
        ];

        foreach ($soldadores as $data) {
            Soldador::firstOrCreate(['dni' => $data['dni']], $data);
        }
    }
}
