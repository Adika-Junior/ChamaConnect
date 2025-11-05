<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Extra defensive: some environments can misreport hasTable; double-check via information_schema
        $meetingsExists = Schema::hasTable('meetings') || !empty(DB::select("SHOW TABLES LIKE 'meetings'"));
        if ($meetingsExists) {
            // Table already exists (dev re-run safety)
            return;
        }

        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['video_conference', 'audio_call', 'in_person'])->default('video_conference');
            $table->dateTime('scheduled_at');
            $table->integer('duration')->nullable(); // in minutes
            $table->string('meeting_link')->nullable(); // WebRTC room or URL
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            // Defer FK for contributions to a later migration to avoid ordering issues
            $table->unsignedBigInteger('contribution_id')->nullable()->index();
            $table->timestamps();
        });

        $participantsExists = Schema::hasTable('meeting_participants') || !empty(DB::select("SHOW TABLES LIKE 'meeting_participants'"));
        if (!$participantsExists) {
        Schema::create('meeting_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['invited', 'confirmed', 'attended', 'declined', 'no_show'])->default('invited');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
            $table->unique(['meeting_id', 'user_id']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_participants');
        Schema::dropIfExists('meetings');
    }
};
