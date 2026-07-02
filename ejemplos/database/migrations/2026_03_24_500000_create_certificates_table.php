<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('number');                // 1604
            $table->string('year', 2);               // 25
            $table->unsignedTinyInteger('revision')->default(0);
            $table->foreignId('welder_id')->constrained('welders')->restrictOnDelete();
            $table->foreignId('inspector_id')->constrained('inspectors')->restrictOnDelete();
            $table->foreignId('standard_id')->constrained('standards')->restrictOnDelete();
            $table->foreignId('process_id')->constrained('welding_processes')->restrictOnDelete();
            $table->foreignId('wps_id')->nullable()->constrained('wps')->nullOnDelete();
            $table->enum('coupon_type', ['plate', 'pipe']);
            $table->foreignId('base_metal_id')->constrained('base_materials')->restrictOnDelete();
            $table->decimal('tested_thickness_mm', 8, 2);
            $table->decimal('tested_diameter_mm', 8, 2)->nullable();
            $table->foreignId('position_id')->constrained('positions')->restrictOnDelete();
            $table->enum('progression', ['uphill', 'downhill', 'na'])->default('na');
            $table->foreignId('consumable_id')->constrained('consumables')->restrictOnDelete();
            $table->string('current')->nullable();   // DCEP, DCEN, AC
            $table->enum('backing', ['with_backing', 'without_backing', 'both'])->default('without_backing');
            $table->date('qualification_date');
            $table->date('expiration_date');
            $table->enum('status', ['approved', 'failed'])->default('approved');
            $table->uuid('qr_uuid')->unique();
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'number', 'year', 'revision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
