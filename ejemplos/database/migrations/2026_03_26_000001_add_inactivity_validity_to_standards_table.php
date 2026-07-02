<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('standards', function (Blueprint $table) {
            // How many days of inactivity invalidate qualification (ASME/AWS: 180, NAG: 90, null = no fixed limit)
            $table->unsignedSmallInteger('inactivity_limit_days')->nullable()->after('active');

            // Fixed credential validity in years (null = no norm-mandated expiry, only inactivity applies)
            $table->unsignedTinyInteger('validity_years')->nullable()->after('inactivity_limit_days');

            // NAG categories delegate to a parent norm's qualification rules (e.g. NAG 105 Cat.C → API 1104)
            $table->foreignId('parent_standard_id')
                  ->nullable()
                  ->after('validity_years')
                  ->constrained('standards')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('standards', function (Blueprint $table) {
            $table->dropForeign(['parent_standard_id']);
            $table->dropColumn(['inactivity_limit_days', 'validity_years', 'parent_standard_id']);
        });
    }
};
