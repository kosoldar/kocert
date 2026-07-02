<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('normas', function (Blueprint $table) {
            // Normas que delegan su calificación a otra norma.
            // Ej: ASME B31.3 → ASME IX (párrafo 328.2.1)
            //     ASME B31.8 → ASME IX (párrafo 817.1)
            $table->foreignId('parent_norma_id')
                  ->nullable()
                  ->after('descripcion')
                  ->constrained('normas')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('normas', function (Blueprint $table) {
            $table->dropForeign(['parent_norma_id']);
            $table->dropColumn('parent_norma_id');
        });
    }
};
