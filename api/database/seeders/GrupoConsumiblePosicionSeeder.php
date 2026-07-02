<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GrupoConsumiblePosicionSeeder extends Seeder
{
    public function run(): void
    {
        // Solo se carga si el grupo RESTRINGE posiciones.
        // Grupos sin filas aquí = todas las posiciones permitidas.

        $gc = fn(string $norma, string $codigo) => DB::table('grupos_consumible')
            ->join('normas', 'normas.id', '=', 'grupos_consumible.norma_id')
            ->where('normas.nombre', $norma)
            ->where('grupos_consumible.codigo', $codigo)
            ->value('grupos_consumible.id');

        $pos = fn(string $codigo) => DB::table('posiciones')->where('codigo', $codigo)->value('id');

        $rows = [];
        $link = function (int|null $gcId, int|null $posId) use (&$rows): void {
            if ($gcId && $posId) {
                $rows[] = ['grupo_consumible_id' => $gcId, 'posicion_id' => $posId];
            }
        };

        // ── ASME IX F-No.1 ─── solo plana / horizontal ───────────────────────
        // EXX20, EXX24, EXX27, EXX28 no pueden usarse en vertical ni sobrecabeza.
        $f1 = $gc('ASME IX', 'F-No. 1');
        foreach (['1G-P', '2G-P', '1G', '2G', '1F', '2F'] as $c) {
            $link($f1, $pos($c));
        }

        // ── AWS D1.1 F1 ─── misma restricción (EXX24, EXX28) ─────────────────
        $awsF1 = $gc('AWS D1.1', 'F1');
        foreach (['1G-P', '2G-P', '1F', '2F'] as $c) {
            $link($awsF1, $pos($c));
        }

        // ── API 1104 WF-5 ─── OFW; limitado a plana/horizontal ───────────────
        // OFW (oxigas) casi siempre se usa en posiciones plana/horizontal en campo.
        $wf5 = $gc('API 1104', 'WF-5');
        foreach (['1G', '2G', '5G', '6G'] as $c) { // posiciones de tubería API
            $link($wf5, $pos($c));
        }

        DB::table('grupo_consumible_posicion')->insertOrIgnore($rows);
    }
}
