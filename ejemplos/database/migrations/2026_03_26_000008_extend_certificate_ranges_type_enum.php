<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Extends the certificate_ranges.type enum to include filler_group and base_metal_group,
// which are required for the RangeCalculatorService to store F-Number and P-Number ranges.
// Written for PostgreSQL (uses varchar + check constraint). MySQL variant included in comment.
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE certificate_ranges DROP CONSTRAINT IF EXISTS certificate_ranges_type_check');
            DB::statement("
                ALTER TABLE certificate_ranges
                ADD CONSTRAINT certificate_ranges_type_check
                CHECK (type IN ('thickness', 'diameter', 'position', 'filler_group', 'base_metal_group'))
            ");
        } else {
            // MySQL 8+
            DB::statement("
                ALTER TABLE certificate_ranges
                MODIFY COLUMN type ENUM('thickness','diameter','position','filler_group','base_metal_group') NOT NULL
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE certificate_ranges DROP CONSTRAINT IF EXISTS certificate_ranges_type_check');
            DB::statement("
                ALTER TABLE certificate_ranges
                ADD CONSTRAINT certificate_ranges_type_check
                CHECK (type IN ('thickness', 'diameter', 'position'))
            ");
        } else {
            DB::statement("
                ALTER TABLE certificate_ranges
                MODIFY COLUMN type ENUM('thickness','diameter','position') NOT NULL
            ");
        }
    }
};
