<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('welder_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('welder_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->enum('event_type', [
                'qualification',      // new certificate issued
                'renewal',            // continuity renewed (re-test or attestation)
                'activity_record',    // proof of welding activity (ASME IX QW-322.1 / ISO 9606-1 §9.3)
                'suspension',         // temporarily suspended
                'revocation',         // permanently revoked
            ]);
            $table->date('event_date');
            $table->string('notes')->nullable();
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('tenant_users')
                  ->nullOnDelete();
            $table->timestamps();

            $table->index(['welder_id', 'event_type', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('welder_events');
    }
};
