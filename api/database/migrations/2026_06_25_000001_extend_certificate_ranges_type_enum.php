<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // PostgreSQL enum is a varchar + CHECK constraint. Drop and re-add with new values.
        DB::statement('ALTER TABLE certificate_ranges DROP CONSTRAINT IF EXISTS certificate_ranges_type_check');
        DB::statement("
            ALTER TABLE certificate_ranges
            ADD CONSTRAINT certificate_ranges_type_check
            CHECK (type IN ('espesor', 'diametro', 'posicion', 'grupo_base_metal', 'grupo_consumible'))
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE certificate_ranges DROP CONSTRAINT IF EXISTS certificate_ranges_type_check');
        DB::statement("
            ALTER TABLE certificate_ranges
            ADD CONSTRAINT certificate_ranges_type_check
            CHECK (type IN ('espesor', 'diametro', 'posicion'))
        ");
    }
};
