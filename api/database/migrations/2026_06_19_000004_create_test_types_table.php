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
            $table->foreignId('norma_id')->constrained('normas')->restrictOnDelete();
            $table->string('code', 10);         // VT, BT-F, BT-R, BT-S, NB, RT, UT...
            $table->string('nombre');
            $table->boolean('requerido')->default(true);
            $table->timestamps();

            $table->unique(['norma_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_types');
    }
};
