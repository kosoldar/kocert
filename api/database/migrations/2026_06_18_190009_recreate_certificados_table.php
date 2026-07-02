<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('certificados');

        Schema::create('certificados', function (Blueprint $table) {
            $table->id();

            // Numeración
            $table->unsignedInteger('numero');
            $table->unsignedSmallInteger('anio');
            $table->unsignedTinyInteger('revision')->default(0);

            // FK core
            $table->foreignId('soldador_id')->constrained('soldadores')->restrictOnDelete();
            $table->foreignId('empresa_id')->constrained('empresas')->restrictOnDelete();
            $table->foreignId('norma_id')->constrained('normas')->restrictOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->restrictOnDelete();

            // Ciclo de vida
            $table->enum('tipo', ['inicial', 'renovacion', 'ampliacion'])->default('inicial');
            $table->enum('estado', ['vigente', 'vencido', 'suspendido'])->default('vigente');
            $table->enum('resultado', ['aprobado', 'rechazado'])->default('aprobado');

            // Fechas
            $table->date('fecha_calificacion');
            $table->date('fecha_vencimiento');           // siempre = calificacion + 6 meses

            // Procedimiento de referencia (presentes en todos los RCS)
            $table->string('eps_numero', 50);
            $table->string('pqr_numero', 50)->nullable();

            // Proceso y cupón — consultados/filtrados en SQL
            $table->string('proceso', 20);               // SMAW, GTAW, GTAW+SMAW
            $table->string('posicion', 10);              // 6G, 5G, 3G, 4F
            $table->enum('progresion', ['ascendente', 'descendente'])->nullable();
            $table->enum('tipo_cupon', ['caño', 'chapa']);

            // Variables específicas por norma (JSONB queryable en PostgreSQL)
            $table->jsonb('variables')->nullable();

            // Archivos y verificación
            $table->string('pdf_path')->nullable();
            $table->string('qr_token', 64)->unique()->nullable();
            $table->text('observaciones')->nullable();

            $table->unique(['numero', 'anio']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
