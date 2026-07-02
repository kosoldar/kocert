<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Solo se puebla cuando el grupo RESTRINGE posiciones.
        // Sin filas = todas las posiciones válidas.
        Schema::create('grupo_consumible_posicion', function (Blueprint $table) {
            $table->foreignId('grupo_consumible_id')
                  ->constrained('grupos_consumible')->cascadeOnDelete();
            $table->foreignId('posicion_id')
                  ->constrained('posiciones')->cascadeOnDelete();
            $table->primary(['grupo_consumible_id', 'posicion_id'], 'gc_pos_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_consumible_posicion');
    }
};
