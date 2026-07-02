<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeldingProcessSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('welding_processes')->insertOrIgnore([
            ['code' => 'SMAW', 'name' => 'Shielded Metal Arc Welding',     'name_es' => 'Soldadura de Arco con Electrodo Revestido',          'created_at' => $now, 'updated_at' => $now],
            ['code' => 'GTAW', 'name' => 'Gas Tungsten Arc Welding (TIG)', 'name_es' => 'Soldadura de Arco con Electrodo de Tungsteno (TIG)', 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'GMAW', 'name' => 'Gas Metal Arc Welding (MIG/MAG)','name_es' => 'Soldadura de Arco en Atmósfera de Gas (MIG/MAG)',    'created_at' => $now, 'updated_at' => $now],
            ['code' => 'FCAW', 'name' => 'Flux Cored Arc Welding',         'name_es' => 'Soldadura de Arco con Núcleo Fundente',              'created_at' => $now, 'updated_at' => $now],
            ['code' => 'SAW',  'name' => 'Submerged Arc Welding',           'name_es' => 'Soldadura de Arco Sumergido',                       'created_at' => $now, 'updated_at' => $now],
            ['code' => 'OFW',  'name' => 'Oxyfuel Gas Welding',             'name_es' => 'Soldadura Oxiacetilénica',                          'created_at' => $now, 'updated_at' => $now],
            ['code' => 'PAW',   'name' => 'Plasma Arc Welding',                            'name_es' => 'Soldadura de Arco de Plasma',                              'created_at' => $now, 'updated_at' => $now],
            // ASME IX QW-410.26: GMAW short-circuit transfer is an essential variable.
            // A welder qualified with spray/pulse does NOT qualify for short-circuit and vice versa.
            ['code' => 'GMAW-S','name' => 'Gas Metal Arc Welding — Short-Circuit Transfer', 'name_es' => 'Soldadura GMAW con Transferencia por Cortocircuito (Short-Arc)', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
