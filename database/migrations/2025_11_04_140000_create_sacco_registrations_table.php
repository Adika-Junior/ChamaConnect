<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sacco_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('registration_number')->unique();
            $table->date('registered_at')->nullable();
            $table->string('address')->nullable();
            $table->string('county')->nullable();
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->string('certificate_path')->nullable();
            $table->string('bylaws_path')->nullable();
            $table->json('officials')->nullable(); // chair/secretary/treasurer
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->foreignId('submitted_by')->nullable()->constrained('users');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sacco_registrations');
    }
};


