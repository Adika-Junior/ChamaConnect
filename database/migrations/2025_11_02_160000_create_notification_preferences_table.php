<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // task_assigned, contribution_approved, meeting_reminder, etc.
            $table->boolean('email')->default(true);
            $table->boolean('sms')->default(false);
            $table->boolean('in_app')->default(true);
            $table->boolean('push')->default(false);
            $table->json('quiet_hours')->nullable(); // {start: "22:00", end: "08:00"}
            $table->timestamps();
            $table->unique(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};

