<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->foreignId('joint_design_id')
                  ->nullable()
                  ->after('tipo_cupon')
                  ->constrained('joint_designs')
                  ->nullOnDelete();
            $table->string('joint_detail')->nullable()->after('joint_design_id');
            // e.g. "65°±5°, apertura de raíz 1mm, cara de raíz 3.2mm"

            $table->timestamp('ranges_calculated_at')->nullable()->after('observaciones');
            $table->smallInteger('rules_version')->nullable()->after('ranges_calculated_at');
        });

        Schema::create('certificate_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificado_id')
                  ->constrained('certificados')
                  ->cascadeOnDelete();
            $table->enum('type', ['espesor', 'diametro', 'posicion', 'grupo_base_metal', 'grupo_consumible']);
            $table->string('descripcion');       // "3.2mm a ilimitado", "Todas las posiciones ascendente"
            $table->decimal('min_value', 8, 2)->nullable();
            $table->decimal('max_value', 8, 2)->nullable(); // null = ilimitado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_ranges');
        Schema::table('certificados', function (Blueprint $table) {
            $table->dropForeign(['joint_design_id']);
            $table->dropColumn(['joint_design_id', 'joint_detail', 'ranges_calculated_at', 'rules_version']);
        });
    }
};
