<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('chats', 'meeting_id')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->foreignId('meeting_id')->nullable()->after('created_by')->constrained()->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('chats', 'meeting_id')) {
            Schema::table('chats', function (Blueprint $table) {
                $table->dropConstrainedForeignId('meeting_id');
            });
        }
    }
};


