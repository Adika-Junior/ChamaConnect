<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_role_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->unique(); // admin, treasurer, secretary, member
            $table->string('display_name', 120);
            $table->json('permissions'); // array of permission strings
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_role_templates');
    }
};

