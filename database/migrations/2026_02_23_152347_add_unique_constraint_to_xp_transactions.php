<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('xp_transactions', function (Blueprint $table) {
            // Add a unique constraint for idempotency
            // Note: In MySQL, multiple NULL reference_ids are allowed.
            // This natively protects referenced actions (e.g. roadmap_toggle for step 5)
            // but for non-referenced actions, the logic layer must still guard it.
            $table->unique(['user_id', 'action', 'reference_id'], 'xp_tx_unique_reward');
        });
    }

    public function down(): void
    {
        Schema::table('xp_transactions', function (Blueprint $table) {
            $table->dropUnique('xp_tx_unique_reward');
        });
    }
};
