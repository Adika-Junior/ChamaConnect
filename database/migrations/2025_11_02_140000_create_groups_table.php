<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('sacco'); // sacco, committee, project, etc.
            $table->text('description')->nullable();
            $table->foreignId('treasurer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('secretary_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->decimal('total_contributions', 15, 2)->default(0);
            $table->decimal('total_expenses', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0); // total_contributions - total_expenses
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};

