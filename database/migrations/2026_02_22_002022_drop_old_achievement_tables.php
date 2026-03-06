<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Drop the showcase badges (Phase 5)
        Schema::dropIfExists('user_showcase_badges');
        
        // As requested by user, drop these if they exist 
        // (they might not have existed in our specific iteration, but better safe)
        Schema::dropIfExists('achievement_progress');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible
    }
};
