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
        // Redundant migration superseded by 2026_02_23_212748_sync_user_achievements_schema.php
        // Schema::create('user_achievements', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        //     $table->string('achievement_key', 50)->index();
        //     $table->timestamp('unlocked_at');
        //     $table->timestamps();

        //     // O(1) Lookup and ensure atomic single unlocks
        //     $table->unique(['user_id', 'achievement_key']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_achievements');
    }
};
