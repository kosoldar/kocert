<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('positions')->insertOrIgnore([
            // Groove positions
            ['code' => '1G',  'name' => 'Flat (groove)',                    'name_es' => 'Plana (ranura)',                    'type' => 'groove', 'created_at' => $now, 'updated_at' => $now],
            ['code' => '2G',  'name' => 'Horizontal (groove)',              'name_es' => 'Horizontal (ranura)',               'type' => 'groove', 'created_at' => $now, 'updated_at' => $now],
            ['code' => '3G',  'name' => 'Vertical (groove)',                'name_es' => 'Vertical (ranura)',                 'type' => 'groove', 'created_at' => $now, 'updated_at' => $now],
            ['code' => '4G',  'name' => 'Overhead (groove)',                'name_es' => 'Sobre cabeza (ranura)',             'type' => 'groove', 'created_at' => $now, 'updated_at' => $now],
            ['code' => '5G',  'name' => 'Horizontal Fixed Pipe (groove)',   'name_es' => 'Tubería fija horizontal (ranura)',  'type' => 'groove', 'created_at' => $now, 'updated_at' => $now],
            ['code' => '6G',  'name' => 'Inclined Fixed Pipe 45° (groove)', 'name_es' => 'Tubería fija inclinada 45° (ranura)', 'type' => 'groove', 'created_at' => $now, 'updated_at' => $now],
            // Fillet positions
            ['code' => '1F',  'name' => 'Flat (fillet)',                    'name_es' => 'Plana (filete)',                   'type' => 'fillet', 'created_at' => $now, 'updated_at' => $now],
            ['code' => '2F',  'name' => 'Horizontal (fillet)',              'name_es' => 'Horizontal (filete)',              'type' => 'fillet', 'created_at' => $now, 'updated_at' => $now],
            ['code' => '2FR', 'name' => 'Horizontal Rotated Pipe (fillet)', 'name_es' => 'Tubería rotada horizontal (filete)', 'type' => 'fillet', 'created_at' => $now, 'updated_at' => $now],
            ['code' => '3F',  'name' => 'Vertical (fillet)',                'name_es' => 'Vertical (filete)',                'type' => 'fillet', 'created_at' => $now, 'updated_at' => $now],
            ['code' => '4F',  'name' => 'Overhead (fillet)',                'name_es' => 'Sobre cabeza (filete)',            'type' => 'fillet', 'created_at' => $now, 'updated_at' => $now],
            ['code' => '5F',  'name' => 'All Positions Pipe (fillet)',                    'name_es' => 'Tubería todas posiciones (filete)',          'type' => 'fillet', 'created_at' => $now, 'updated_at' => $now],
            // ASME IX QW-461.9: 6GR (with restriction ring) qualifies the same positions as 6G
            // but is specifically used for branch connections. Tested with a restriction ring
            // that simulates access restrictions of real branch welds.
            ['code' => '6GR', 'name' => 'Inclined Fixed Pipe 45° with Restriction Ring (groove)', 'name_es' => 'Tubería fija inclinada 45° con anillo de restricción (ranura)', 'type' => 'groove', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
