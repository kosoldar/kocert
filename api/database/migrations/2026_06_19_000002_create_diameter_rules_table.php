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
            $table->foreignId('norma_id')->constrained('normas')->restrictOnDelete();
            $table->enum('coupon_type', ['chapa', 'caño', 'ambos']);
            $table->decimal('diameter_from_mm', 8, 2);
            $table->decimal('diameter_to_mm',   8, 2)->nullable();
            $table->decimal('qualifies_min_mm', 8, 2)->nullable(); // null = mismo que ensayado
            $table->string('qualifies_max_formula', 30);            // 'unlimited' | '60.3' | '323.9'
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diameter_rules');
    }
};
