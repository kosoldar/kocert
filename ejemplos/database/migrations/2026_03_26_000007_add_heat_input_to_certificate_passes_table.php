<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// API 1104 Ed.22ª requires heat input to be recorded in the qualification record.
// Heat input (kJ/mm) = (Amperage × Voltage × 60) / (Travel Speed mm/min × 1000)
// Nullable: not required by ASME IX or AWS D1.1 welder qualification records.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_passes', function (Blueprint $table) {
            $table->decimal('heat_input_kj_mm', 8, 3)->nullable()->after('transfer_type');
        });
    }

    public function down(): void
    {
        Schema::table('certificate_passes', function (Blueprint $table) {
            $table->dropColumn('heat_input_kj_mm');
        });
    }
};
