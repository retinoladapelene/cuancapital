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
        Schema::dropIfExists('user_streaks');

        if (Schema::hasTable('user_metrics')) {
            Schema::table('user_metrics', function (Blueprint $table) {
                if (Schema::hasColumn('user_metrics', 'current_streak')) {
                    $table->dropColumn('current_streak');
                }
                if (Schema::hasColumn('user_metrics', 'longest_streak')) {
                    $table->dropColumn('longest_streak');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_metrics')) {
            Schema::table('user_metrics', function (Blueprint $table) {
                if (!Schema::hasColumn('user_metrics', 'current_streak')) {
                    $table->integer('current_streak')->default(0);
                }
                if (!Schema::hasColumn('user_metrics', 'longest_streak')) {
                    $table->integer('longest_streak')->default(0);
                }
            });
        }

        Schema::create('user_streaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('last_activity_date')->nullable();
            $table->integer('current_streak')->default(0);
            $table->integer('longest_streak')->default(0);
            $table->date('streak_started_at')->nullable();
            $table->boolean('grace_used')->default(false);
            $table->timestamps();

            // Indexes for fast querying
            $table->index('user_id');
            $table->index('last_activity_date');
        });
    }
};
