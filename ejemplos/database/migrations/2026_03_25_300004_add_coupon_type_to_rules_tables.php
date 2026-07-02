<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('thickness_rules', function (Blueprint $table) {
            $table->enum('coupon_type', ['plate', 'pipe', 'both'])
                  ->default('both')
                  ->after('standard_id');
        });

        Schema::table('diameter_rules', function (Blueprint $table) {
            $table->enum('coupon_type', ['plate', 'pipe', 'both'])
                  ->default('both')
                  ->after('standard_id');
        });
    }

    public function down(): void
    {
        Schema::table('thickness_rules', function (Blueprint $table) {
            $table->dropColumn('coupon_type');
        });

        Schema::table('diameter_rules', function (Blueprint $table) {
            $table->dropColumn('coupon_type');
        });
    }
};
