<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('base_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('base_metal_groups')->restrictOnDelete();
            $table->string('specification');        // A36, A106 B, API 5L X52
            $table->string('description')->nullable();
            $table->string('description_es')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('base_materials');
    }
};
