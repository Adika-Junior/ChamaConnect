<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 64)->index();
            $table->string('idempotency_key', 128);
            $table->string('signature', 255)->nullable();
            $table->longText('payload');
            $table->enum('status', ['received', 'processed', 'failed'])->default('received');
            $table->timestamp('processed_at')->nullable();
            $table->string('error', 512)->nullable();
            $table->timestamps();

            $table->unique(['provider', 'idempotency_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};


