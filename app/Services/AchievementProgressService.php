<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProgress;

class AchievementProgressService
{
    /**
     * Build progress data for a single achievement key.
     * Returns null if: already unlocked, metric not defined, or target <= 0.
     */
    public function buildProgress(User $user, string $achievementKey, array $unlockedKeys): ?array
    {
        // Skip if already unlocked
        if (in_array($achievementKey, $unlockedKeys)) {
            return null;
        }

        $config = config("achievement_metrics.{$achievementKey}");
        if (!$config) return null;

        $target = (int) ($config['target'] ?? 0);
        if ($target <= 0) return null;

        $current   = $this->resolveMetric($user, $config['metric']);
        $current   = min($current, $target); // Cap at target
        $remaining = $target - $current;
        $percent   = (int) round(($current / $target) * 100);

        // Already completed but not yet in unlockedKeys (edge case) — omit progress
        if ($current >= $target) return null;

        return [
            'current'   => $current,
            'target'    => $target,
            'percent'   => $percent,
            'remaining' => $remaining,
            'hint'      => str_replace('{remaining}', $remaining, $config['hint']),
            'tone'      => $this->getTone($percent),
        ];
    }

    /**
     * Pre-load all needed counts on the user model to avoid N+1 queries.
     * Call this ONCE before looping over achievements.
     */
    public function preloadUserCounts(User $user): void
    {
        // Eloquent relationship counts
        $user->loadCount([
            'profitSimulations',
            'reverseGoalSessions',
        ]);

        // Blueprint counts (total + mentor-specific)
        $user->blueprints_count        = \App\Models\Blueprint::where('user_id', $user->id)->count();
        $user->blueprints_mentor_count = \App\Models\Blueprint::where('user_id', $user->id)
                                            ->where('type', 'mentor_lab')
                                            ->count();

        // Roadmap count (via Roadmap model)
        $user->roadmaps_count = \App\Models\Roadmap::where('user_id', $user->id)->count();
    }

    // ─────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────

    private function resolveMetric(User $user, string $metric): int
    {
        return match ($metric) {
            'profit_simulations_count'  => (int) ($user->profit_simulations_count ?? 0),
            'reverse_goal_sessions_count' => (int) ($user->reverse_goal_sessions_count ?? 0),
            'blueprints_count'          => (int) ($user->blueprints_count ?? 0),
            'blueprints_mentor_count'   => (int) ($user->blueprints_mentor_count ?? 0),
            'roadmaps_count'            => (int) ($user->roadmaps_count ?? 0),
            'xp_points'                 => (int) ($user->progress?->xp_points ?? 0),
            'level'                     => (int) ($user->progress?->level ?? 1),
            default                     => 0,
        };
    }

    private function getTone(int $percent): string
    {
        if ($percent >= 70) return 'urgency';
        if ($percent >= 30) return 'motivating';
        return 'gentle';
    }
}
