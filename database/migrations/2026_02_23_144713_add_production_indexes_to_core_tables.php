<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Production-grade composite indexes.
 * Uses try/catch to be idempotent — safe to run multiple times.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->safeIndex('blueprints', ['user_id', 'type'], 'blueprints_user_id_type_index');
        $this->safeIndex('blueprints', ['user_id', 'created_at'], 'blueprints_user_id_created_at_index');

        $this->safeIndex('reverse_goal_sessions', ['user_id', 'created_at'], 'rgs_user_id_created_at_index');

        $this->safeIndex('user_progress', ['xp_points'], 'user_progress_xp_points_index');

        $this->safeIndex('simulations', ['user_id', 'created_at'], 'simulations_user_id_created_at_index');
        $this->safeIndex('simulations', ['user_id', 'mode'], 'simulations_user_id_mode_index');

        $this->safeIndex('mentor_sessions', ['user_id', 'created_at'], 'mentor_sessions_user_id_created_at_index');

        $this->safeIndex('roadmaps', ['user_id', 'status'], 'roadmaps_user_id_status_index');

        $this->safeIndex('roadmap_steps', ['roadmap_id', 'order'], 'roadmap_steps_roadmap_id_order_index');
    }

    public function down(): void
    {
        $drops = [
            'blueprints'            => ['blueprints_user_id_type_index', 'blueprints_user_id_created_at_index'],
            'reverse_goal_sessions' => ['rgs_user_id_created_at_index'],
            'user_progress'         => ['user_progress_xp_points_index'],
            'simulations'           => ['simulations_user_id_created_at_index', 'simulations_user_id_mode_index'],
            'mentor_sessions'       => ['mentor_sessions_user_id_created_at_index'],
            'roadmaps'              => ['roadmaps_user_id_status_index'],
            'roadmap_steps'         => ['roadmap_steps_roadmap_id_order_index'],
        ];

        foreach ($drops as $table => $indexes) {
            if (! Schema::hasTable($table)) continue;
            Schema::table($table, function (Blueprint $t) use ($indexes) {
                foreach ($indexes as $idx) {
                    try { $t->dropIndex($idx); } catch (\Throwable) {}
                }
            });
        }
    }

    private function safeIndex(string $table, array $columns, string $name): void
    {
        if (! Schema::hasTable($table)) return;
        try {
            Schema::table($table, fn (Blueprint $t) => $t->index($columns, $name));
        } catch (\Throwable) {
            // Index already exists — skip silently
        }
    }
};
