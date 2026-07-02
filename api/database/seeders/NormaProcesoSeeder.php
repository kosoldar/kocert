<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NormaProcesoSeeder extends Seeder
{
    public function run(): void
    {
        $norma   = fn(string $n) => DB::table('normas')  ->where('nombre', $n)->value('id');
        $proceso = fn(string $n) => DB::table('procesos')->where('nombre', $n)->value('id');

        $asme   = $norma('ASME IX');
        $b313   = $norma('ASME B31.3');
        $b318   = $norma('ASME B31.8');
        $d11    = $norma('AWS D1.1');
        $d16    = $norma('AWS D1.6');
        $api04  = $norma('API 1104');
        $api650 = $norma('API 650');
        $iram   = $norma('IRAM');

        $smaw  = $proceso('SMAW');
        $gtaw  = $proceso('GTAW');
        $gmaw  = $proceso('GMAW');
        $fcaw  = $proceso('FCAW');
        $saw   = $proceso('SAW');
        $paw   = $proceso('PAW');
        $ofw   = $proceso('OFW');

        $rows = [];

        // ASME IX — todos (incluyendo PAW y OFW)
        foreach ([$smaw, $gtaw, $gmaw, $fcaw, $saw, $paw, $ofw] as $p) {
            if ($p) $rows[] = ['norma_id' => $asme, 'proceso_id' => $p];
        }

        // ASME B31.3 y B31.8 — heredan de ASME IX pero se registran igual para filter directo
        foreach ([$smaw, $gtaw, $gmaw, $fcaw, $saw, $paw, $ofw] as $p) {
            if ($p) {
                $rows[] = ['norma_id' => $b313, 'proceso_id' => $p];
                $rows[] = ['norma_id' => $b318, 'proceso_id' => $p];
            }
        }

        // AWS D1.1 — sin PAW; OFW limitado pero permitido por Table 4.5
        foreach ([$smaw, $gtaw, $gmaw, $fcaw, $saw, $ofw] as $p) {
            if ($p) $rows[] = ['norma_id' => $d11, 'proceso_id' => $p];
        }

        // AWS D1.6 — sin SAW (solo SMAW, GTAW, GMAW, FCAW)
        foreach ([$smaw, $gtaw, $gmaw, $fcaw] as $p) {
            if ($p) $rows[] = ['norma_id' => $d16, 'proceso_id' => $p];
        }

        // API 1104 — SMAW, GTAW, GMAW, FCAW, OFW (SAW rarísimo en gasoductos)
        foreach ([$smaw, $gtaw, $gmaw, $fcaw, $ofw] as $p) {
            if ($p) $rows[] = ['norma_id' => $api04, 'proceso_id' => $p];
        }

        // API 650 — incluye SAW (tanques grandes usan arco sumergido)
        foreach ([$smaw, $gtaw, $gmaw, $fcaw, $saw] as $p) {
            if ($p) $rows[] = ['norma_id' => $api650, 'proceso_id' => $p];
        }

        // IRAM / ISO 9606-1 — todos
        foreach ([$smaw, $gtaw, $gmaw, $fcaw, $saw, $paw, $ofw] as $p) {
            if ($p) $rows[] = ['norma_id' => $iram, 'proceso_id' => $p];
        }

        DB::table('norma_proceso')->insertOrIgnore($rows);
    }
}
