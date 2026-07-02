<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qualified_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('norma_id')->constrained('normas')->restrictOnDelete();
            $table->foreignId('tested_posicion_id')
                  ->constrained('posiciones')
                  ->restrictOnDelete();
            $table->foreignId('qualified_posicion_id')
                  ->constrained('posiciones')
                  ->restrictOnDelete();
            $table->enum('joint_type', ['ranura', 'filete', 'ambas']);
            $table->timestamps();

            $table->unique(['norma_id', 'tested_posicion_id', 'qualified_posicion_id', 'joint_type'], 'uq_qp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qualified_positions');
    }
};
