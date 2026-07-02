<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standard_id')->constrained('standards')->restrictOnDelete();
            $table->string('code');                 // VT, BEND, NB, RT, UT
            $table->string('name');                 // Visual, Bend, Nick Break, Radiographic...
            $table->string('name_es')->nullable();
            $table->boolean('required')->default(true);
            $table->timestamps();

            $table->unique(['standard_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_types');
    }
};
