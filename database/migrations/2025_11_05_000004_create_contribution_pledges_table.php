<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contribution_pledges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rule_id')->nullable()->index();
            $table->unsignedBigInteger('group_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('campaign_id')->nullable()->index();
            $table->date('due_date')->index();
            $table->unsignedBigInteger('amount_cents');
            $table->string('currency', 3)->default('KES');
            $table->enum('status', ['pending','paid','overdue','cancelled'])->default('pending')->index();
            $table->timestamp('paid_at')->nullable();
            $table->unsignedTinyInteger('reminder_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contribution_pledges');
    }
};


