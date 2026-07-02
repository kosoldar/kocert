<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welding_processes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();   // SMAW, GTAW, GMAW, SAW, FCAW
            $table->string('name');
            $table->string('name_es')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welding_processes');
    }
};
