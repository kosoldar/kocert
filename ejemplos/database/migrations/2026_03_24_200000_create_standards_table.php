<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('standards', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();       // ASME IX, AWS D1.1, API 1104
            $table->string('full_name');
            $table->string('organization');          // ASME, AWS, API
            $table->string('edition')->nullable();   // 2021, 2020
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standards');
    }
};
