<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->foreignId('inspector_id')
                  ->nullable()
                  ->after('usuario_id')
                  ->constrained('inspectors')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->dropForeign(['inspector_id']);
            $table->dropColumn('inspector_id');
        });
    }
};
