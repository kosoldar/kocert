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
            $table->foreignId('certificate_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('sort_order')->default(0);
            // e.g. 1 = root, 2 = hot pass, 3-N = fill/cap
            $table->string('sequence_label');
            // "Raíz", "Pasada caliente", "Relleno", "Presentación" / "Root", "Hot pass", etc.

            // Process can differ per pass (e.g. root=GTAW, fill=SMAW)
            $table->foreignId('process_id')
                  ->nullable()
                  ->constrained('welding_processes')
                  ->nullOnDelete();

            $table->string('filler_classification')->nullable();  // E7018, ER70S-6…
            $table->decimal('filler_diameter_mm', 5, 2)->nullable();

            $table->string('polarity')->nullable();               // DCEP, DCEN, AC
            $table->unsignedSmallInteger('amperage_min')->nullable();
            $table->unsignedSmallInteger('amperage_max')->nullable();
            $table->unsignedSmallInteger('voltage_min')->nullable();
            $table->unsignedSmallInteger('voltage_max')->nullable();
            $table->unsignedSmallInteger('travel_speed_min')->nullable(); // mm/min
            $table->unsignedSmallInteger('travel_speed_max')->nullable();

            // GMAW only
            $table->string('transfer_type')->nullable();
            // short-arc, spray, pulse, globular

            $table->enum('progression', ['uphill', 'downhill', 'na'])->default('na');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_passes');
    }
};
