<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('soldadores', function (Blueprint $table) {
            $table->date('fecha_nacimiento')->nullable()->after('dni');
            $table->string('nacionalidad', 100)->nullable()->after('fecha_nacimiento');
        });
    }

    public function down(): void
    {
        Schema::table('soldadores', function (Blueprint $table) {
            $table->dropColumn(['fecha_nacimiento', 'nacionalidad']);
        });
    }
};
