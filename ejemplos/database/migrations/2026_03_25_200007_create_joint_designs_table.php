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
            $table->string('code')->unique();        // single-v, double-v, single-bevel...
            $table->string('name');                  // Single-V Groove
            $table->string('name_es')->nullable();   // Ranura en V simple
            $table->text('svg');                     // SVG markup for rendering in certificates
            $table->json('parameters')->nullable();  // ['angle','root_opening','root_face']
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('joint_designs');
    }
};
