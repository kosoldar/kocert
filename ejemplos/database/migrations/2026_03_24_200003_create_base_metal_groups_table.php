<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('base_metal_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->constrained('standards')->restrictOnDelete();
            $table->string('code');                 // P1 Gr1, S-1, Grupo I (API)
            $table->string('description')->nullable();
            $table->string('description_es')->nullable();
            $table->timestamps();

            $table->unique(['standard_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('base_metal_groups');
    }
};
