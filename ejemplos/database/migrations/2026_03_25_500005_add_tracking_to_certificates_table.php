<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->timestamp('ranges_calculated_at')->nullable()->after('pdf_path');
            $table->smallInteger('rules_version')->nullable()->after('ranges_calculated_at');
            // Set by RangeCalculatorService::VERSION constant on each calculation.
            // Allows querying certificates calculated with outdated logic:
            //   SELECT * FROM certificates WHERE rules_version < RangeCalculatorService::VERSION
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['ranges_calculated_at', 'rules_version']);
        });
    }
};
