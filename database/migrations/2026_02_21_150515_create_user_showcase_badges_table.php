<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * User Badge Showcase — max 3 pinned achievement badges per user.
     * slot_index: 1, 2, or 3 (enforced by UNIQUE constraint)
     */
    public function up(): void
    {
        Schema::create('user_showcase_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('achievement_id', 100); // e.g. "roadmap_builder"
            $table->tinyInteger('slot_index');      // 1, 2, or 3

            // One badge per slot per user
            $table->unique(['user_id', 'slot_index']);
            // Same badge can't be pinned twice
            $table->unique(['user_id', 'achievement_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_showcase_badges');
    }
};
