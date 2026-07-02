<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ASME IX QW-423: higher P-Number qualifies lower P-Numbers as specified.
//   P-4 → qualifies P-1, P-3, P-4.  P-8 → qualifies P-8 only.
// AWS D1.1: higher Group qualifies lower Groups.
//   Group III → qualifies Group I, II, III.
// API 1104: no cross-qualification — each SMYS group qualifies only itself.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('base_metal_group_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->constrained('standards')->cascadeOnDelete();
            $table->foreignId('tested_group_id')->constrained('base_metal_groups')->cascadeOnDelete();
            $table->foreignId('qualifies_group_id')->constrained('base_metal_groups')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['standard_id', 'tested_group_id', 'qualifies_group_id'], 'bmgq_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('base_metal_group_qualifications');
    }
};
