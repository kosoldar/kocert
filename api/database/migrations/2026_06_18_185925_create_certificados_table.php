<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('numero');
            $table->unsignedSmallInteger('anio');
            $table->foreignId('soldador_id')->constrained('soldadores')->restrictOnDelete();
            $table->foreignId('empresa_id')->constrained('empresas')->restrictOnDelete();
            $table->foreignId('proceso_id')->constrained('procesos')->restrictOnDelete();
            $table->foreignId('norma_id')->constrained('normas')->restrictOnDelete();
            $table->foreignId('material_id')->constrained('materiales')->restrictOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->restrictOnDelete();
            $table->enum('tipo', ['inicial', 'renovacion', 'ampliacion'])->default('inicial');
            $table->unsignedTinyInteger('revision')->default(0);
            $table->enum('resultado', ['aprobado', 'rechazado'])->default('aprobado');
            $table->date('fecha_examen');
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['vigente', 'vencido', 'suspendido'])->default('vigente');
            $table->string('pdf_path')->nullable();
            $table->string('qr_token')->unique()->nullable();
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
