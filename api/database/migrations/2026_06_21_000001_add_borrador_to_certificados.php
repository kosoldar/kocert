<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support ALTER COLUMN or named CHECK constraints.
        // These DDL changes only apply to PostgreSQL (production).
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE certificados DROP CONSTRAINT IF EXISTS certificados_estado_check');
        DB::statement("ALTER TABLE certificados ADD CONSTRAINT certificados_estado_check CHECK (estado IN ('borrador', 'vigente', 'vencido', 'suspendido'))");

        DB::statement('ALTER TABLE certificados DROP CONSTRAINT IF EXISTS certificados_tipo_cupon_check');
        DB::statement("ALTER TABLE certificados ALTER COLUMN tipo_cupon DROP NOT NULL");
        DB::statement("ALTER TABLE certificados ADD CONSTRAINT certificados_tipo_cupon_check CHECK (tipo_cupon IN ('caño', 'chapa'))");

        DB::statement('ALTER TABLE certificados DROP CONSTRAINT IF EXISTS certificados_resultado_check');
        DB::statement("ALTER TABLE certificados ALTER COLUMN resultado DROP NOT NULL");
        DB::statement("ALTER TABLE certificados ADD CONSTRAINT certificados_resultado_check CHECK (resultado IN ('aprobado', 'rechazado'))");

        DB::statement('ALTER TABLE certificados ALTER COLUMN fecha_calificacion DROP NOT NULL');
        DB::statement('ALTER TABLE certificados ALTER COLUMN fecha_vencimiento DROP NOT NULL');
        DB::statement('ALTER TABLE certificados ALTER COLUMN eps_numero DROP NOT NULL');
        DB::statement('ALTER TABLE certificados ALTER COLUMN proceso DROP NOT NULL');
        DB::statement('ALTER TABLE certificados ALTER COLUMN posicion DROP NOT NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE certificados DROP CONSTRAINT IF EXISTS certificados_estado_check');
        DB::statement("ALTER TABLE certificados ADD CONSTRAINT certificados_estado_check CHECK (estado IN ('vigente', 'vencido', 'suspendido'))");

        DB::statement('ALTER TABLE certificados DROP CONSTRAINT IF EXISTS certificados_tipo_cupon_check');
        DB::statement("ALTER TABLE certificados ALTER COLUMN tipo_cupon SET NOT NULL");
        DB::statement("ALTER TABLE certificados ADD CONSTRAINT certificados_tipo_cupon_check CHECK (tipo_cupon IN ('caño', 'chapa'))");

        DB::statement('ALTER TABLE certificados DROP CONSTRAINT IF EXISTS certificados_resultado_check');
        DB::statement("ALTER TABLE certificados ALTER COLUMN resultado SET NOT NULL");
        DB::statement("ALTER TABLE certificados ADD CONSTRAINT certificados_resultado_check CHECK (resultado IN ('aprobado', 'rechazado'))");

        DB::statement('ALTER TABLE certificados ALTER COLUMN fecha_calificacion SET NOT NULL');
        DB::statement('ALTER TABLE certificados ALTER COLUMN fecha_vencimiento SET NOT NULL');
        DB::statement('ALTER TABLE certificados ALTER COLUMN eps_numero SET NOT NULL');
        DB::statement('ALTER TABLE certificados ALTER COLUMN proceso SET NOT NULL');
        DB::statement('ALTER TABLE certificados ALTER COLUMN posicion SET NOT NULL');
    }
};
