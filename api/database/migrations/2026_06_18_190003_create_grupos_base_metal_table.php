<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos_base_metal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('norma_id')->constrained('normas')->restrictOnDelete();
            $table->string('codigo', 30);           // P-No. 1 Gr. 1 / Group I / W01 / Grupo I
            $table->string('descripcion')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->unique(['norma_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos_base_metal');
    }
};
