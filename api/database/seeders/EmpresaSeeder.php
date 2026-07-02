<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        $empresas = [
            ['nombre' => 'Petroquímica San Lorenzo S.A.',  'cuit' => '30-65432198-7', 'contacto' => 'Ing. Horacio Blanco',   'activo' => true ],
            ['nombre' => 'Techint Construcciones S.A.',    'cuit' => '30-51234567-9', 'contacto' => 'Lic. Valeria Peralta',  'activo' => true ],
            ['nombre' => 'YPF S.A.',                       'cuit' => '30-54667159-5', 'contacto' => 'Ing. Pablo Acuña',      'activo' => true ],
            ['nombre' => 'Tenaris Global Services S.A.',   'cuit' => '30-70772208-3', 'contacto' => 'Arq. Fernanda Ruiz',    'activo' => true ],
            ['nombre' => 'IMPSA Industrias Metalúrgicas',  'cuit' => '30-60647777-2', 'contacto' => 'Ing. Roberto Cáceres',  'activo' => true ],
            ['nombre' => 'Constructora Del Valle S.R.L.',  'cuit' => '30-71234560-1', 'contacto' => 'Martín Esquivel',       'activo' => true ],
            ['nombre' => 'Metalmecánica Patagónica S.A.',  'cuit' => '30-68975432-6', 'contacto' => null,                    'activo' => true ],
            ['nombre' => 'Gasoducto Centro-Oeste S.A.',    'cuit' => '30-57834129-4', 'contacto' => 'Ing. Claudia Ibáñez',   'activo' => true ],
            ['nombre' => 'Astilleros Río Santiago',        'cuit' => '30-54898030-8', 'contacto' => 'Luis Domínguez',        'activo' => false],
            ['nombre' => 'Talleres Metalúrgicos Norte',    'cuit' => '20-32145678-9', 'contacto' => null,                    'activo' => true ],
        ];

        foreach ($empresas as $data) {
            Empresa::firstOrCreate(['nombre' => $data['nombre']], $data);
        }
    }
}
