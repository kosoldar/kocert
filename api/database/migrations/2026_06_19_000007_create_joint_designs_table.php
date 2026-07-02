<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('joint_designs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();    // single-v, double-v, single-bevel...
            $table->string('nombre');
            $table->text('svg');                 // markup SVG para el PDF
            $table->json('parametros')->nullable(); // ['angulo','apertura_raiz','cara_raiz']
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('joint_designs');
    }
};
