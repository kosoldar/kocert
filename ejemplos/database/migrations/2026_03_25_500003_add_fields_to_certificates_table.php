<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            // Joint design
            $table->foreignId('joint_design_id')
                  ->nullable()
                  ->after('backing')
                  ->constrained('joint_designs')
                  ->nullOnDelete();
            $table->string('joint_detail')->nullable()->after('joint_design_id');
            // e.g. "65°±5°, root opening 1mm, root face 3.2mm"

            // Procedure reference
            $table->string('pqr_code')->nullable()->after('wps_id');
            // e.g. "PQR-2024-001"

            // Weld geometry details
            $table->decimal('root_opening_mm', 6, 2)->nullable()->after('joint_detail');
            $table->decimal('deposit_thickness_mm', 6, 2)->nullable()->after('root_opening_mm');

            // Thermal conditions
            $table->smallInteger('preheat_temp_c')->nullable()->after('deposit_thickness_mm');
            $table->smallInteger('interpass_temp_c')->nullable()->after('preheat_temp_c');

            // Shielding gas (root / cover)
            $table->string('gas_type')->nullable()->after('interpass_temp_c');
            // e.g. "75% Ar + 25% CO2", "CO2", "Argon"
            $table->string('gas_flow')->nullable()->after('gas_type');
            // e.g. "15-20 L/min"

            // Backing gas (inert purge for root — GTAW, stainless, etc.)
            $table->string('inert_gas_backing_type')->nullable()->after('gas_flow');
            $table->string('inert_gas_backing_flow')->nullable()->after('inert_gas_backing_type');

            // Project / work reference (used by Kosoldar)
            $table->string('obra')->nullable()->after('pdf_path');
            // e.g. "YPF — Planta Luján de Cuyo"
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['joint_design_id']);
            $table->dropColumn([
                'joint_design_id',
                'joint_detail',
                'pqr_code',
                'root_opening_mm',
                'deposit_thickness_mm',
                'preheat_temp_c',
                'interpass_temp_c',
                'gas_type',
                'gas_flow',
                'inert_gas_backing_type',
                'inert_gas_backing_flow',
                'obra',
            ]);
        });
    }
};
