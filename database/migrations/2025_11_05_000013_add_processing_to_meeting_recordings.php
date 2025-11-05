<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meeting_recordings', function (Blueprint $table) {
            if (!Schema::hasColumn('meeting_recordings', 'processing_status')) {
                $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->after('duration_seconds');
            }
            if (!Schema::hasColumn('meeting_recordings', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('processing_status');
            }
            if (!Schema::hasColumn('meeting_recordings', 'processing_error')) {
                $table->text('processing_error')->nullable()->after('processed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('meeting_recordings', function (Blueprint $table) {
            if (Schema::hasColumn('meeting_recordings', 'processing_status')) {
                $table->dropColumn('processing_status');
            }
            if (Schema::hasColumn('meeting_recordings', 'processed_at')) {
                $table->dropColumn('processed_at');
            }
            if (Schema::hasColumn('meeting_recordings', 'processing_error')) {
                $table->dropColumn('processing_error');
            }
        });
    }
};

