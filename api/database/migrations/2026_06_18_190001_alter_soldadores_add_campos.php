<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soldadores', function (Blueprint $table) {
            $table->string('cuño', 20)->nullable()->after('dni');
            $table->string('ciudad', 100)->nullable()->after('cuño');
        });
    }

    public function down(): void
    {
        Schema::table('soldadores', function (Blueprint $table) {
            $table->dropColumn(['cuño', 'ciudad']);
        });
    }
};
