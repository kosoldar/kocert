<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// NAG 100/105: issues a physical credential card (Form 513-780-0) with a unique credential number
// separate from the certificate number. Required for Category A, B, C, D welders.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('welders', function (Blueprint $table) {
            $table->string('credential_number', 50)->nullable()->after('stamp');
        });
    }

    public function down(): void
    {
        Schema::table('welders', function (Blueprint $table) {
            $table->dropColumn('credential_number');
        });
    }
};
