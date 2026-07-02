<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diameter_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->constrained('standards')->restrictOnDelete();
            $table->decimal('diameter_from_mm', 8, 2);
            $table->decimal('diameter_to_mm', 8, 2)->nullable();    // null = no upper bound
            $table->decimal('qualifies_min_mm', 8, 2)->nullable();  // null = same as tested
            $table->string('qualifies_max_formula');                 // "unlimited", "2D"
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diameter_rules');
    }
};
