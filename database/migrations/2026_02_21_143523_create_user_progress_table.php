<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add integer level + achievements JSON column to user_progress.
     * The table was originally created in 2026_02_20 with xp_points and milestone columns.
     */
    public function up(): void
    {
        Schema::table('user_progress', function (Blueprint $table) {
            // Integer level so the API can return it without re-computing every time
            $table->integer('level')->default(1)->after('xp_points');
            // JSON blob for achievement keys e.g. ["ach_first_1000", "ach_first_10k"]
            $table->json('achievements')->nullable()->after('level');
        });
    }

    public function down(): void
    {
        Schema::table('user_progress', function (Blueprint $table) {
            $table->dropColumn(['level', 'achievements']);
        });
    }
};
