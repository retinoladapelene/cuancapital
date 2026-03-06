<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Avatar Border Frames — standalone equippable items, independent from badges.
     */
    public function up(): void
    {
        Schema::create('border_frames', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('rarity', ['bronze', 'silver', 'gold', 'platinum', 'diamond', 'mythic'])->default('bronze');
            $table->integer('rarity_weight')->default(0);
            $table->string('css_class')->nullable();  // e.g. border-badge-gold
            $table->string('unlock_condition')->nullable(); // human-readable hint e.g. "Raih 5 Perfect Run"
            $table->string('icon')->nullable();       // emoji or FA class
            $table->timestamps();
        });

        // Track which border frame each user has equipped
        Schema::table('user_metrics', function (Blueprint $table) {
            $table->unsignedBigInteger('equipped_border_id')->nullable()->after('level');
            $table->foreign('equipped_border_id')->references('id')->on('border_frames')->nullOnDelete();
        });

        // Track which border frames a user has unlocked
        Schema::create('user_border_frames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('border_frame_id')->constrained('border_frames')->cascadeOnDelete();
            $table->timestamp('unlocked_at')->useCurrent();
            $table->timestamps();
            $table->unique(['user_id', 'border_frame_id']);
        });
    }

    public function down(): void
    {
        Schema::table('user_metrics', function (Blueprint $table) {
            $table->dropForeign(['equipped_border_id']);
            $table->dropColumn('equipped_border_id');
        });
        Schema::dropIfExists('user_border_frames');
        Schema::dropIfExists('border_frames');
    }
};
