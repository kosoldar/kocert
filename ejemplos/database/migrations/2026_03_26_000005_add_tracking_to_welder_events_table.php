<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ASME IX QW-322.1: inactivity applies per process, not globally.
// A welder inactive in SMAW for 6+ months retains GTAW qualification.
// process_id allows per-process inactivity auditing.
// certificate_id links activity_record / renewal events to specific certificates.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('welder_events', function (Blueprint $table) {
            $table->foreignId('process_id')
                  ->nullable()
                  ->after('event_type')
                  ->constrained('welding_processes')
                  ->nullOnDelete();

            $table->foreignId('certificate_id')
                  ->nullable()
                  ->after('process_id')
                  ->constrained('certificates')
                  ->nullOnDelete();

            // Update the composite index to include process_id for efficient inactivity queries
            $table->dropIndex(['welder_id', 'event_type', 'event_date']);
            $table->index(['welder_id', 'process_id', 'event_type', 'event_date'], 'we_welder_process_type_date');
        });
    }

    public function down(): void
    {
        Schema::table('welder_events', function (Blueprint $table) {
            $table->dropIndex('we_welder_process_type_date');
            $table->index(['welder_id', 'event_type', 'event_date']);
            $table->dropForeign(['process_id']);
            $table->dropForeign(['certificate_id']);
            $table->dropColumn(['process_id', 'certificate_id']);
        });
    }
};
