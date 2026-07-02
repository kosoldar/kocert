<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cert_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            // Array of norma.id this layout applies to. null = all normas (use with es_default).
            $table->json('norma_ids')->nullable();
            $table->boolean('es_default')->default(false);
            $table->string('orientacion')->default('portrait');
            // Full layout JSON: { pageSize, orientation, marginMm, pages[{blocks[]}] }
            $table->json('blocks');
            // Canvas PNG snapshot data URI for list thumbnails
            $table->text('thumbnail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cert_layouts');
    }
};
