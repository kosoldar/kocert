<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consumibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos_consumible')->restrictOnDelete();
            $table->string('clasificacion', 25);    // E6010, E7018, ER70S-6, E308L-16
            $table->string('sfa', 15)->nullable();  // SFA-5.1, SFA-5.18, SFA-5.9
            $table->string('proceso', 10)->nullable(); // SMAW, GTAW, GMAW, FCAW
            $table->string('descripcion')->nullable();
            $table->timestamps();

            $table->unique(['grupo_id', 'clasificacion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumibles');
    }
};
