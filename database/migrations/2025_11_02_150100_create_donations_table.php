<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('donor_id')->nullable()->constrained('users')->nullOnDelete(); // Null for anonymous
            $table->string('donor_name')->nullable(); // For anonymous or public display
            $table->decimal('amount', 15, 2);
            $table->boolean('is_anonymous')->default(false);
            $table->string('message')->nullable();
            $table->foreignId('payment_id')->nullable()->constrained('contribution_payments')->nullOnDelete();
            $table->text('metadata')->nullable(); // Store M-Pesa checkout request ID
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};

