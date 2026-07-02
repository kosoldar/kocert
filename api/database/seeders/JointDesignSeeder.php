<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JointDesignSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // SVGs usan viewBox="0 0 80 80", corte transversal, ranura abre hacia arriba
        $designs = [
            [
                'code'       => 'square-groove',
                'nombre'     => 'Ranura cuadrada',
                'parametros' => ['apertura_raiz'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<rect x="3" y="12" width="33" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<rect x="44" y="12" width="33" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'single-v',
                'nombre'     => 'Ranura en V simple',
                'parametros' => ['angulo', 'apertura_raiz', 'cara_raiz'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<polygon points="3,12 38,12 40,68 3,68" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<polygon points="77,12 42,12 40,68 77,68" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'double-v',
                'nombre'     => 'Ranura en doble V (bisel X)',
                'parametros' => ['angulo', 'apertura_raiz', 'cara_raiz'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<polygon points="3,12 38,12 40,40 38,68 3,68" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<polygon points="77,12 42,12 40,40 42,68 77,68" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'single-bevel',
                'nombre'     => 'Ranura en bisel simple',
                'parametros' => ['angulo', 'apertura_raiz', 'cara_raiz'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<rect x="3" y="12" width="37" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<polygon points="44,12 77,12 77,68 40,68" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'double-bevel',
                'nombre'     => 'Ranura en doble bisel (bisel K)',
                'parametros' => ['angulo', 'apertura_raiz', 'cara_raiz'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<rect x="3" y="12" width="37" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<polygon points="44,12 77,12 77,68 44,68 40,40" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'single-u',
                'nombre'     => 'Ranura en U simple',
                'parametros' => ['angulo_ranura', 'apertura_raiz', 'cara_raiz', 'radio_ranura'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<path d="M 3,12 L 36,12 Q 40,40 36,68 L 3,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<path d="M 77,12 L 44,12 Q 40,40 44,68 L 77,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'single-j',
                'nombre'     => 'Ranura en J simple',
                'parametros' => ['angulo_ranura', 'apertura_raiz', 'cara_raiz', 'radio_ranura'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<rect x="3" y="12" width="37" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<path d="M 44,12 L 77,12 L 77,68 L 40,68 Q 44,68 44,52 L 44,12 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'double-u',
                'nombre'     => 'Ranura en doble U',
                'parametros' => ['angulo_ranura', 'apertura_raiz', 'cara_raiz', 'radio_ranura'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<path d="M 3,12 L 34,12 Q 40,40 34,68 L 3,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<path d="M 77,12 L 46,12 Q 40,40 46,68 L 77,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'double-j',
                'nombre'     => 'Ranura en doble J',
                'parametros' => ['angulo_ranura', 'apertura_raiz', 'cara_raiz', 'radio_ranura'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<rect x="3" y="12" width="37" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<path d="M 44,12 L 77,12 L 77,68 L 44,68 Q 40,54 40,40 Q 40,26 44,12 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'flare-v',
                'nombre'     => 'Ranura en V abierta (flare-V)',
                'parametros' => ['apertura_raiz', 'garganta_efectiva'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<path d="M 3,12 L 30,12 Q 42,40 30,68 L 3,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<path d="M 77,12 L 50,12 Q 38,40 50,68 L 77,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'flare-bevel',
                'nombre'     => 'Ranura en bisel abierto (flare-bevel)',
                'parametros' => ['apertura_raiz', 'garganta_efectiva'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<rect x="3" y="12" width="37" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<path d="M 77,12 L 50,12 Q 38,40 50,68 L 77,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'fillet',
                'nombre'     => 'Soldadura en filete (ángulo)',
                'parametros' => ['cateto', 'garganta'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<polygon points="3,68 77,68 77,54 60,54 46,40 46,12 34,12 34,40 20,54 3,54"'
                    . ' fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
        ];

        foreach ($designs as $design) {
            DB::table('joint_designs')->insertOrIgnore([
                'code'       => $design['code'],
                'nombre'     => $design['nombre'],
                'svg'        => $design['svg'],
                'parametros' => json_encode($design['parametros']),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
