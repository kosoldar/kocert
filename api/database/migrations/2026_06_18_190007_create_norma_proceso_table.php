<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('norma_proceso', function (Blueprint $table) {
            $table->foreignId('norma_id')  ->constrained('normas')   ->cascadeOnDelete();
            $table->foreignId('proceso_id')->constrained('procesos')  ->cascadeOnDelete();
            $table->primary(['norma_id', 'proceso_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('norma_proceso');
    }
};
