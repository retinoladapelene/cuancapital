<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add performance indexes to async_jobs table.
     *
     * Two indexes for two different query patterns:
     *
     * 1. (user_id, id)     — polling + IDOR check:
     *    WHERE id = ? AND user_id = ?
     *    Without this: full table scan at 100+ req/s.
     *
     * 2. (user_id, status) — dashboard / admin monitoring:
     *    WHERE user_id = ? AND status IN ('pending', 'processing')
     */
    public function up(): void
    {
        Schema::table('async_jobs', function (Blueprint $table) {
            // Cover the polling + IDOR check query pattern
            $table->index(['user_id', 'id'], 'idx_async_jobs_user_id');

            // Cover the "show all my active jobs" query pattern
            $table->index(['user_id', 'status'], 'idx_async_jobs_user_status');
        });
    }

    public function down(): void
    {
        Schema::table('async_jobs', function (Blueprint $table) {
            $table->dropIndex('idx_async_jobs_user_id');
            $table->dropIndex('idx_async_jobs_user_status');
        });
    }
};

