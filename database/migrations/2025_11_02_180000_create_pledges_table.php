<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pledges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contribution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('pledged_at');
            $table->date('due_date');
            $table->enum('status', ['pending', 'partially_paid', 'fulfilled', 'overdue', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['contribution_id', 'user_id']);
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pledges');
    }
};

