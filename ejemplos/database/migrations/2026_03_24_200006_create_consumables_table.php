<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('consumable_groups')->restrictOnDelete();
            $table->string('classification');       // E7018, E6010, E7010-A1
            $table->string('sfa')->nullable();      // SFA-5.1, SFA-5.18
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumables');
    }
};
