<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welder_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soldador_id')
                  ->constrained('soldadores')
                  ->cascadeOnDelete();
            $table->enum('event_type', [
                'qualification',    // certificado inicial emitido
                'renewal',          // renovación (re-ensayo o declaración)
                'activity_record',  // registro de actividad (ASME IX QW-322.1 / API 1104 §6.2)
                'suspension',       // suspensión temporal
                'revocation',       // revocación permanente
            ]);
            $table->date('event_date');
            $table->string('notas')->nullable();
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();

            $table->index(['soldador_id', 'event_type', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welder_events');
    }
};
