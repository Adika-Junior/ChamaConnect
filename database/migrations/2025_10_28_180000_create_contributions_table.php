<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contributions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', ['fundraiser', 'funeral', 'wedding', 'project', 'other'])->default('other');
            $table->decimal('target_amount', 12, 2)->nullable();
            $table->decimal('collected_amount', 12, 2)->default(0);
            $table->enum('status', ['pending_approval', 'approved', 'active', 'completed', 'closed', 'rejected'])->default('pending_approval');
            $table->dateTime('deadline');
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            // Defer FK to meetings to avoid ordering issues; add via later migration
            $table->unsignedBigInteger('meeting_id')->nullable()->index();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('contribution_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribution_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_contributed', 12, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->timestamp('contributed_at')->nullable();
            $table->boolean('notified')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            $table->unique(['contribution_id', 'user_id']);
        });

        Schema::create('contribution_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribution_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['approval', 'reminder', 'update', 'deadline'])->default('approval');
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contribution_notifications');
        Schema::dropIfExists('contribution_participants');
        Schema::dropIfExists('contributions');
    }
};
