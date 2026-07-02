<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thickness_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->constrained('standards')->restrictOnDelete();
            $table->decimal('thickness_from_mm', 8, 2);
            $table->decimal('thickness_to_mm', 8, 2)->nullable();   // null = no upper bound for this tier
            $table->unsignedTinyInteger('min_layers')->nullable();   // e.g. 3 layers for unlimited range
            $table->decimal('qualifies_min_mm', 8, 2)->nullable();
            $table->string('qualifies_max_formula');                 // "2t", "unlimited", "13.0"
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thickness_rules');
    }
};
