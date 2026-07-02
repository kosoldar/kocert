<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            // Flag for certificates that use multiple welding processes (root GTAW + fill SMAW, etc.)
            // When true, process_id and consumable_id refer to the root/primary pass only.
            $table->boolean('is_multi_process')->default(false)->after('process_id');

            // NAG 105 Cat.D only: SMYS percentage i = (P × D × 100) / (2 × t × S) — must be < 20%
            // Stored after calculation by the inspector; null for non-NAG or non-Cat.D certificates.
            $table->decimal('nag_smys_pct', 5, 2)->nullable()->after('is_multi_process');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['is_multi_process', 'nag_smys_pct']);
        });
    }
};
