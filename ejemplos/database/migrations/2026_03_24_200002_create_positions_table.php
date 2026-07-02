<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();       // 1G, 2G, 3G, 4G, 5G, 6G, 1F, 2F...
            $table->string('name');
            $table->string('name_es')->nullable();
            $table->enum('type', ['groove', 'fillet']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
