<?php

namespace Database\Seeders;

use App\Models\Inspector;
use Illuminate\Database\Seeder;

class InspectorSeeder extends Seeder
{
    public function run(): void
    {
        Inspector::firstOrCreate(
            ['email' => 'ricardokosik@gmail.com'],
            [
                'nombre'        => 'Ricardo Kosik',
                'certificacion' => 'IRAM-IAS Nivel II - Cert. 4542',
                'email'         => 'ricardokosik@gmail.com',
                'telefono'      => '3873 651982',
                'activo'        => true,
            ]
        );
    }
}
