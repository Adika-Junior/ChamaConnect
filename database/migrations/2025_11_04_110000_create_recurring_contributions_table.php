<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->default('monthly');
            $table->date('next_run_date');
            $table->date('last_run_date')->nullable();
            $table->enum('status', ['active', 'paused', 'cancelled'])->default('active');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_contributions');
    }
};


