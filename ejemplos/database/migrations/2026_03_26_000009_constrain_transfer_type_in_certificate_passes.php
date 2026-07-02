<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// ASME IX QW-410.26: GMAW transfer mode is an essential variable.
// A welder qualified with spray/pulse does NOT qualify for short-circuit transfer (GMAW-S).
// Valid values: short_circuit | spray | pulse | globular | na (for non-GMAW processes).
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("
                ALTER TABLE certificate_passes
                ADD CONSTRAINT certificate_passes_transfer_type_check
                CHECK (transfer_type IS NULL OR transfer_type IN ('short_circuit','spray','pulse','globular','na'))
            ");
        } else {
            // MySQL 8.0.16+ supports CHECK constraints
            DB::statement("
                ALTER TABLE certificate_passes
                ADD CONSTRAINT certificate_passes_transfer_type_check
                CHECK (transfer_type IS NULL OR transfer_type IN ('short_circuit','spray','pulse','globular','na'))
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE certificate_passes DROP CONSTRAINT IF EXISTS certificate_passes_transfer_type_check');
        } else {
            DB::statement('ALTER TABLE certificate_passes DROP CHECK certificate_passes_transfer_type_check');
        }
    }
};
