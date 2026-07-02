<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos_consumible', function (Blueprint $table) {
            $table->id();
            $table->foreignId('norma_id')->constrained('normas')->restrictOnDelete();
            $table->string('codigo', 20);           // F1/F2/F3/F4/F6 (ASME) / WF-1..WF-6 (API) / FM1..FM6 (ISO)
            $table->string('descripcion')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->unique(['norma_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos_consumible');
    }
};
