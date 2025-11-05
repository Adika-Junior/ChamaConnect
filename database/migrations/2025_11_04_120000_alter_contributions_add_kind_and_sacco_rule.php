<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->enum('kind', ['one_time', 'sacco'])->default('one_time')->after('category');
            $table->string('sacco_rule')->nullable()->after('kind');
        });
    }

    public function down(): void
    {
        Schema::table('contributions', function (Blueprint $table) {
            $table->dropColumn(['kind', 'sacco_rule']);
        });
    }
};


