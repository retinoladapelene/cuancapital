<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Badges Table
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('rarity', ['bronze', 'silver', 'gold', 'platinum', 'diamond', 'mythic'])->default('bronze');
            $table->integer('rarity_weight')->default(0); // For sorting, e.g. mythic=60, bronze=10
            $table->string('css_class')->nullable();
            $table->text('border_svg')->nullable();
            $table->timestamps();
        });

        // 2. Achievements Table
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('category')->default('learning'); // learning, skill, consistency, mastery
            
            // Engine triggers
            $table->string('trigger_event')->index('idx_achievements_event'); // Event that triggers check (e.g. lesson_completed)
            $table->string('condition_type'); // e.g. lessons_completed, perfect_runs
            $table->integer('condition_value'); // e.g. 5
            
            $table->foreignId('badge_id')->nullable()->constrained('badges')->nullOnDelete();
            $table->integer('xp_reward')->default(0);
            $table->boolean('is_hidden')->default(false);
            $table->timestamps();
        });

        // 3. User Metrics (Realtime progress tracker)
        Schema::create('user_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            $table->integer('lessons_completed')->default(0);
            $table->integer('courses_completed')->default(0);
            $table->integer('simulations_passed')->default(0);
            $table->integer('perfect_runs')->default(0);
            
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->integer('total_xp')->default(0);
            $table->integer('level')->default(1);
            
            $table->timestamps();
            
            $table->index('user_id', 'idx_user_metrics_user');
        });

        // 4. User Achievements
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            
            $table->integer('progress_cached')->default(0); // Progress tracker per achievement
            $table->timestamp('unlocked_at')->nullable(); // Null if not fully unlocked
            
            $table->timestamps();
            
            $table->index('user_id', 'idx_user_achievements_user');
            $table->unique(['user_id', 'achievement_id']);
        });

        // 5. User Badges
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_equipped')->default(false);
            $table->timestamp('unlocked_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['user_id', 'badge_id']);
        });

        // 6. Achievement Audit Logs
        Schema::create('achievement_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('achievement_id')->constrained()->cascadeOnDelete();
            $table->string('trigger_event');
            $table->json('context_json')->nullable();
            $table->timestamp('granted_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievement_logs');
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('user_metrics');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('badges');
    }
};
