<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Downward qualification de consumibles: ensayar con "probado" → quedar habilitado en "calificado".
        // ASME IX QW-433: F-No.4 califica F-No.1/2/3/4.
        Schema::create('grupo_consumible_calificado', function (Blueprint $table) {
            $table->foreignId('probado_id')
                  ->constrained('grupos_consumible')->cascadeOnDelete();
            $table->foreignId('calificado_id')
                  ->constrained('grupos_consumible')->cascadeOnDelete();
            $table->primary(['probado_id', 'calificado_id'], 'gc_cal_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_consumible_calificado');
    }
};
