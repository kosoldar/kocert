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
            $table->foreignId('standard_id')->constrained('standards')->restrictOnDelete();
            $table->foreignId('tested_position_id')->constrained('positions')->restrictOnDelete();
            $table->foreignId('qualified_position_id')->constrained('positions')->restrictOnDelete();
            $table->enum('joint_type', ['groove', 'fillet', 'both']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qualified_positions');
    }
};
