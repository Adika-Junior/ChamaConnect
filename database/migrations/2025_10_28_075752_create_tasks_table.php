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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['todo','in_progress','blocked','done'])->default('todo');
            $table->enum('priority', ['low','medium','high','urgent'])->default('medium');
            $table->timestamp('due_at')->nullable();
            $table->foreignId('creator_id')->constrained('users');
            $table->foreignId('department_id')->nullable()->constrained('departments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
