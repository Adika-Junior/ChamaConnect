<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_pledges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id')->index();
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->string('donor_phone')->nullable();
            $table->unsignedBigInteger('amount_cents');
            $table->string('currency', 3)->default('KES');
            $table->date('due_date')->nullable()->index();
            $table->enum('status', ['pending','fulfilled','cancelled','overdue'])->default('pending')->index();
            $table->unsignedTinyInteger('reminder_count')->default(0);
            $table->timestamp('fulfilled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_pledges');
    }
};


