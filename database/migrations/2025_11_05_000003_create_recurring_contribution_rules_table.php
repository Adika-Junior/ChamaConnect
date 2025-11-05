<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_contribution_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->unsignedBigInteger('amount_cents');
            $table->string('currency', 3)->default('KES');
            $table->enum('interval', ['weekly','monthly','quarterly']);
            $table->unsignedTinyInteger('day_of_month')->nullable();
            $table->unsignedTinyInteger('weekday')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active','paused'])->default('active');
            $table->timestamp('next_run_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_contribution_rules');
    }
};


