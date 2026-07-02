<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rango de calificación de metal base: ensayar en "probado" → quedar habilitado en "calificado".
        // Incluye la fila self-referential (probado = calificado).
        Schema::create('grupo_base_metal_calificado', function (Blueprint $table) {
            $table->foreignId('probado_id')
                  ->constrained('grupos_base_metal')->cascadeOnDelete();
            $table->foreignId('calificado_id')
                  ->constrained('grupos_base_metal')->cascadeOnDelete();
            $table->primary(['probado_id', 'calificado_id'], 'gbm_cal_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_base_metal_calificado');
    }
};
