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
        Schema::table('user_metrics', function (Blueprint $table) {
            $table->integer('mentor_lab_count')->default(0)->after('roadmap_items_completed');
            $table->integer('goal_planner_count')->default(0)->after('mentor_lab_count');
            $table->integer('minutes_spent')->default(0)->after('goal_planner_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_metrics', function (Blueprint $table) {
            $table->dropColumn(['mentor_lab_count', 'goal_planner_count', 'minutes_spent']);
        });
    }
};
