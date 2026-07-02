<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JointDesignSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // All SVGs use viewBox="0 0 80 80", fill="#c8d0d8", stroke="#34495e" stroke-width="2"
        // Cross-section view: groove opens at top (where weld is deposited), root at bottom.

        $designs = [
            [
                'code'       => 'square-groove',
                'name'       => 'Square Groove',
                'name_es'    => 'Ranura cuadrada',
                'parameters' => ['root_opening'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<rect x="3" y="12" width="33" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<rect x="44" y="12" width="33" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'single-v',
                'name'       => 'Single-V Groove',
                'name_es'    => 'Ranura en V simple',
                'parameters' => ['angle', 'root_opening', 'root_face'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<polygon points="3,12 38,12 40,68 3,68" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<polygon points="77,12 42,12 40,68 77,68" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'double-v',
                'name'       => 'Double-V Groove (X-Groove)',
                'name_es'    => 'Ranura en doble V (bisel X)',
                'parameters' => ['angle', 'root_opening', 'root_face'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<polygon points="3,12 38,12 40,40 38,68 3,68" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<polygon points="77,12 42,12 40,40 42,68 77,68" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'single-bevel',
                'name'       => 'Single-Bevel Groove',
                'name_es'    => 'Ranura en bisel simple',
                'parameters' => ['angle', 'root_opening', 'root_face'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<rect x="3" y="12" width="37" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<polygon points="44,12 77,12 77,68 40,68" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'double-bevel',
                'name'       => 'Double-Bevel Groove (K-Groove)',
                'name_es'    => 'Ranura en doble bisel (bisel K)',
                'parameters' => ['angle', 'root_opening', 'root_face'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<rect x="3" y="12" width="37" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<polygon points="44,12 77,12 77,68 44,68 40,40" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'single-u',
                'name'       => 'Single-U Groove',
                'name_es'    => 'Ranura en U simple',
                'parameters' => ['groove_angle', 'root_opening', 'root_face', 'groove_radius'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<path d="M 3,12 L 36,12 Q 40,40 36,68 L 3,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<path d="M 77,12 L 44,12 Q 40,40 44,68 L 77,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                'code'       => 'single-j',
                'name'       => 'Single-J Groove',
                'name_es'    => 'Ranura en J simple',
                'parameters' => ['groove_angle', 'root_opening', 'root_face', 'groove_radius'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<rect x="3" y="12" width="37" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<path d="M 44,12 L 77,12 L 77,68 L 40,68 Q 44,68 44,52 L 44,12 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                // Both plates have concave (U-shaped) faces — welded from both sides
                'code'       => 'double-u',
                'name'       => 'Double-U Groove',
                'name_es'    => 'Ranura en doble U',
                'parameters' => ['groove_angle', 'root_opening', 'root_face', 'groove_radius'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<path d="M 3,12 L 34,12 Q 40,40 34,68 L 3,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<path d="M 77,12 L 46,12 Q 40,40 46,68 L 77,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                // One flat plate, other plate has J-curves on both top and bottom
                'code'       => 'double-j',
                'name'       => 'Double-J Groove',
                'name_es'    => 'Ranura en doble J',
                'parameters' => ['groove_angle', 'root_opening', 'root_face', 'groove_radius'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<rect x="3" y="12" width="37" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<path d="M 44,12 L 77,12 L 77,68 L 44,68 Q 40,54 40,40 Q 40,26 44,12 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                // Two convex (rounded) surfaces forming a V — typical of tubes / round bars side by side
                'code'       => 'flare-v',
                'name'       => 'Flare-V Groove',
                'name_es'    => 'Ranura en V abierta (flare-V)',
                'parameters' => ['root_opening', 'effective_throat'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<path d="M 3,12 L 30,12 Q 42,40 30,68 L 3,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<path d="M 77,12 L 50,12 Q 38,40 50,68 L 77,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                // One flat plate + one convex (rounded) surface — tube / bar against a flat plate
                'code'       => 'flare-bevel',
                'name'       => 'Flare-Bevel Groove',
                'name_es'    => 'Ranura en bisel abierto (flare-bevel)',
                'parameters' => ['root_opening', 'effective_throat'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<rect x="3" y="12" width="37" height="56" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '<path d="M 77,12 L 50,12 Q 38,40 50,68 L 77,68 Z" fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
            [
                // T-joint fillet weld — no groove geometry; joint_detail captures leg size and throat
                'code'       => 'fillet',
                'name'       => 'Fillet Weld',
                'name_es'    => 'Soldadura en filete (ángulo)',
                'parameters' => ['leg_size', 'throat'],
                'svg'        => '<svg viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg">'
                    . '<polygon points="3,68 77,68 77,54 60,54 46,40 46,12 34,12 34,40 20,54 3,54"'
                    . ' fill="#c8d0d8" stroke="#34495e" stroke-width="2"/>'
                    . '</svg>',
            ],
        ];

        foreach ($designs as $design) {
            DB::table('joint_designs')->insertOrIgnore([
                'code'       => $design['code'],
                'name'       => $design['name'],
                'name_es'    => $design['name_es'],
                'svg'        => $design['svg'],
                'parameters' => json_encode($design['parameters']),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
