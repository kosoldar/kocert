<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certificate_id')->constrained('certificates')->cascadeOnDelete();
            $table->foreignId('test_type_id')->constrained('test_types')->restrictOnDelete();
            $table->enum('result', ['approved', 'failed', 'na'])->default('na');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['certificate_id', 'test_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_tests');
    }
};
