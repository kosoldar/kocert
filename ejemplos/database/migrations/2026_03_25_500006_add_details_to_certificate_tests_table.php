<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_tests', function (Blueprint $table) {
            $table->jsonb('details')->nullable()->after('notes');
            // Stores actual measured values from the test.
            // Examples:
            //   Bend test:     {"bend_angle": 180, "cracks": false}
            //   Nick break:    {"passes": 3, "fails": 0, "porosity_ok": true}
            //   RT:            {"defect_size_mm": 0, "acceptance_criteria": "API 1104 §9.6"}
            //   Visual:        {"undercut_mm": 0.4, "overlap": false}
        });
    }

    public function down(): void
    {
        Schema::table('certificate_tests', function (Blueprint $table) {
            $table->dropColumn('details');
        });
    }
};
