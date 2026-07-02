<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_passes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificado_id')
                  ->constrained('certificados')
                  ->cascadeOnDelete();
            $table->unsignedTinyInteger('orden')->default(0);
            $table->string('etiqueta');                              // "Raíz", "Pasada caliente", "Relleno", "Presentación"

            $table->foreignId('proceso_id')
                  ->nullable()
                  ->constrained('procesos')
                  ->nullOnDelete();

            $table->string('clasificacion_aporte')->nullable();      // E7018, ER70S-6...
            $table->decimal('diametro_aporte_mm', 5, 2)->nullable();

            $table->string('polaridad')->nullable();                 // DCEP, DCEN, CA
            $table->unsignedSmallInteger('amperaje_min')->nullable();
            $table->unsignedSmallInteger('amperaje_max')->nullable();
            $table->unsignedSmallInteger('voltaje_min')->nullable();
            $table->unsignedSmallInteger('voltaje_max')->nullable();
            $table->unsignedSmallInteger('velocidad_avance_min')->nullable(); // mm/min
            $table->unsignedSmallInteger('velocidad_avance_max')->nullable();

            $table->string('tipo_transferencia')->nullable();        // solo GMAW: cortocircuito, spray, pulso, globular

            $table->enum('progresion', ['ascendente', 'descendente'])->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_passes');
    }
};
