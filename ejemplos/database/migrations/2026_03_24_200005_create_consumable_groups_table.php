<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumable_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->constrained('standards')->restrictOnDelete();
            $table->string('code');                 // F3, F4, F6 (ASME), Grupo 1 (API)
            $table->string('description')->nullable();
            $table->string('description_es')->nullable();
            $table->timestamps();

            $table->unique(['standard_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumable_groups');
    }
};
