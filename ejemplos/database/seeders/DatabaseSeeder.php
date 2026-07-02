<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Layer 2 — Universal knowledge (no FK dependencies)
            WeldingProcessSeeder::class,
            PositionSeeder::class,
            StandardSeeder::class,

            // Layer 2 — Standard-specific knowledge (depends on standards)
            JointDesignSeeder::class,
            BaseMetalGroupSeeder::class,
            BaseMaterialSeeder::class,
            ConsumableGroupSeeder::class,
            ConsumableSeeder::class,

            // Layer 3 — Qualification rules (depends on standards + positions)
            ThicknessRuleSeeder::class,
            DiameterRuleSeeder::class,
            QualifiedPositionSeeder::class,
            TestTypeSeeder::class,

            // Layer 3 — Cross-qualification matrices (depends on consumable_groups + base_metal_groups)
            ConsumableGroupQualificationSeeder::class,
            BaseMetalGroupQualificationSeeder::class,

            // Translations (run last — updates existing rows with _es fields)
            //TranslationSeeder::class,

            // Roles & permissions (owner + tenant)
            RolePermissionSeeder::class,
        ]);
    }
}
