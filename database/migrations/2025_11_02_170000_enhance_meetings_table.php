<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->boolean('has_waiting_room')->default(false)->after('status');
            $table->string('password')->nullable()->after('has_waiting_room');
            $table->boolean('is_locked')->default(false)->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropColumn(['has_waiting_room', 'password', 'is_locked']);
        });
    }
};

