<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('id');
            $table->integer('level')->default(1)->after('department_id'); // Role hierarchy level
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            $table->dropUnique(['name']);
            $table->unique(['name', 'department_id']); // Unique name per department
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropUnique(['name', 'department_id']);
            $table->unique(['name']);
            $table->dropColumn(['department_id', 'level']);
        });
    }
};

