<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('campaigns') && !Schema::hasColumn('campaigns', 'tags')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->json('tags')->nullable()->after('status');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('campaigns') && Schema::hasColumn('campaigns', 'tags')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropColumn('tags');
            });
        }
    }
};


