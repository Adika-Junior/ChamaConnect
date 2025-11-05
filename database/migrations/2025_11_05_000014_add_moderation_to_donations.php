<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'show_on_wall')) {
                $table->boolean('show_on_wall')->default(true)->after('is_anonymous');
            }
            if (!Schema::hasColumn('donations', 'moderation_status')) {
                $table->enum('moderation_status', ['pending', 'approved', 'rejected'])->default('pending')->after('show_on_wall');
            }
            if (!Schema::hasColumn('donations', 'avatar_url')) {
                $table->string('avatar_url', 255)->nullable()->after('donor_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (Schema::hasColumn('donations', 'show_on_wall')) $table->dropColumn('show_on_wall');
            if (Schema::hasColumn('donations', 'moderation_status')) $table->dropColumn('moderation_status');
            if (Schema::hasColumn('donations', 'avatar_url')) $table->dropColumn('avatar_url');
        });
    }
};

