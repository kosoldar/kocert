<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificado_id')
                  ->constrained('certificados')
                  ->cascadeOnDelete();
            $table->foreignId('test_type_id')
                  ->constrained('test_types')
                  ->restrictOnDelete();
            $table->enum('resultado', ['aprobado', 'rechazado', 'na'])->default('na');
            $table->text('notas')->nullable();
            $table->jsonb('detalles')->nullable(); // valores reales: ángulo doblado, tamaño defecto, etc.
            $table->timestamps();

            $table->unique(['certificado_id', 'test_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_tests');
    }
};
