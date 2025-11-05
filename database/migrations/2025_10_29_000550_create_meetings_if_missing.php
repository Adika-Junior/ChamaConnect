<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contributions')) {
            return;
        }

        if (!Schema::hasTable('meetings')) {
            Schema::create('meetings', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('type', ['video_conference', 'audio_call', 'in_person'])->default('video_conference');
                $table->dateTime('scheduled_at');
                $table->integer('duration')->nullable();
                $table->string('meeting_link')->nullable();
                $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
                $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
                $table->unsignedBigInteger('contribution_id')->nullable()->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('meeting_participants')) {
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

    public function down(): void
    {
        Schema::dropIfExists('meeting_participants');
        Schema::dropIfExists('meetings');
    }
};


