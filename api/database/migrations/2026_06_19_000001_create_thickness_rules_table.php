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
            $table->foreignId('norma_id')->constrained('normas')->restrictOnDelete();
            $table->enum('coupon_type', ['chapa', 'caño', 'ambos']);
            $table->decimal('thickness_from_mm', 8, 2);
            $table->decimal('thickness_to_mm',   8, 2)->nullable();
            $table->unsignedTinyInteger('min_layers')->nullable();
            $table->decimal('qualifies_min_mm',  8, 2)->nullable(); // null = mismo que ensayado
            $table->string('qualifies_max_formula', 30);             // '2t' | 'unlimited' | 'max(19.0,1.5t)' | '19.0'
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thickness_rules');
    }
};
