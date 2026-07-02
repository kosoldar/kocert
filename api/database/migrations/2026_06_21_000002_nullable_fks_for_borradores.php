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

        DB::statement('ALTER TABLE certificados ALTER COLUMN soldador_id DROP NOT NULL');
        DB::statement('ALTER TABLE certificados ALTER COLUMN empresa_id  DROP NOT NULL');
        DB::statement('ALTER TABLE certificados ALTER COLUMN norma_id    DROP NOT NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE certificados ALTER COLUMN soldador_id SET NOT NULL');
        DB::statement('ALTER TABLE certificados ALTER COLUMN empresa_id  SET NOT NULL');
        DB::statement('ALTER TABLE certificados ALTER COLUMN norma_id    SET NOT NULL');
    }
};
