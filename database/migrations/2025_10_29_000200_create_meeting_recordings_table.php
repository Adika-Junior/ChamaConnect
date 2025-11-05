<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('meeting_recordings')) {
            return;
        }

        Schema::create('meeting_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contribution_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_recordings');
    }
};


