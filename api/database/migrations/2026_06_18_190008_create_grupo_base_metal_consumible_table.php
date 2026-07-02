<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupo_base_metal_consumible', function (Blueprint $table) {
            $table->foreignId('grupo_base_metal_id')
                  ->constrained('grupos_base_metal')->cascadeOnDelete();
            $table->foreignId('grupo_consumible_id')
                  ->constrained('grupos_consumible')->cascadeOnDelete();
            $table->primary(['grupo_base_metal_id', 'grupo_consumible_id'], 'gbm_gc_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_base_metal_consumible');
    }
};
