<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ASME IX QW-433: a welder qualified with F-No.4 may weld with F-4, F-3, F-2, F-1 (downward only).
// AWS D1.1 follows the same F-Number substitution logic.
// API 1104: no cross-qualification — each WF group qualifies only itself.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumable_group_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->constrained('standards')->cascadeOnDelete();
            $table->foreignId('tested_group_id')->constrained('consumable_groups')->cascadeOnDelete();
            $table->foreignId('qualifies_group_id')->constrained('consumable_groups')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['standard_id', 'tested_group_id', 'qualifies_group_id'], 'cgq_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumable_group_qualifications');
    }
};
