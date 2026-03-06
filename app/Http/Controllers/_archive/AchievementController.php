<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserProgress;
use App\Services\AchievementProgressService;

class AchievementController extends Controller
{
    /**
     * Static achievement definitions — title, icon, description.
     * The XP unlock logic lives in GamificationController.
     * This controller only calculates and returns progress.
     */
    private const ACHIEVEMENTS = [
        'first_simulation'  => ['title' => 'First Simulation',     'icon' => '🎯', 'description' => 'Run your first Profit Simulation'],
        'power_simulator'   => ['title' => 'Power Simulator',      'icon' => '⚡', 'description' => 'Run 10 simulations'],
        'first_goal'        => ['title' => 'Goal Getter',          'icon' => '🎪', 'description' => 'Calculate your first Reverse Goal'],
        'regular_planner'   => ['title' => 'Regular Planner',      'icon' => '📋', 'description' => 'Make 5 goal calculations'],
        'blueprint_saver'   => ['title' => 'Blueprint Saver',      'icon' => '💾', 'description' => 'Save your first blueprint'],
        'blueprint_hoarder' => ['title' => 'Blueprint Hoarder',    'icon' => '🗂️',  'description' => 'Save 5 blueprints'],
        'mentor_graduate'   => ['title' => 'Mentor Graduate',      'icon' => '🎓', 'description' => 'Complete a Mentor Lab session'],
        'mentor_master'     => ['title' => 'Mentor Master',        'icon' => '🧠', 'description' => 'Complete 3 Mentor Lab sessions'],
        'roadmap_builder'   => ['title' => 'Roadmap Builder',      'icon' => '🗺️',  'description' => 'Generate your first Execution Roadmap'],
        'first_1000_xp'     => ['title' => 'First 1,000 XP',       'icon' => '⭐', 'description' => 'Earn 1,000 XP'],
        'xp_5k'             => ['title' => 'XP Overload',          'icon' => '💥', 'description' => 'Earn 5,000 XP'],
        'level_5'           => ['title' => 'Smart Executor',       'icon' => '🚀', 'description' => 'Reach Level 5'],
        'level_10'          => ['title' => 'Growth Hacker',        'icon' => '📈', 'description' => 'Reach Level 10'],
    ];

    /**
     * GET /api/achievements/dashboard
     * Returns all achievements with:
     *   - Unlocked: { id, title, icon, description, unlocked: true }
     *   - Locked:   { id, title, icon, description, unlocked: false, progress: {...} }
     */
    public function dashboard(Request $request)
    {
        $user     = $request->user();
        $progress = UserProgress::firstOrCreate(
            ['user_id' => $user->id],
            ['xp_points' => 0, 'level' => 1]
        );

        // Attach progress for XP/level metric resolution
        $user->progress = $progress;

        // Bulk-load all DB counts upfront — no N+1
        $service = app(AchievementProgressService::class);
        $service->preloadUserCounts($user);

        // Keys already unlocked (stored as JSON array in user_progress)
        $unlockedKeys = $progress->achievements ?? [];

        $achievements = [];

        foreach (self::ACHIEVEMENTS as $key => $meta) {
            $isUnlocked = in_array($key, $unlockedKeys);

            $entry = [
                'id'          => $key,
                'title'       => $meta['title'],
                'icon'        => $meta['icon'],
                'description' => $meta['description'],
                'unlocked'    => $isUnlocked,
            ];

            // Attach progress only for locked achievements
            if (!$isUnlocked) {
                $progressData = $service->buildProgress($user, $key, $unlockedKeys);
                if ($progressData) {
                    $entry['progress'] = $progressData;
                }
            }

            $achievements[] = $entry;
        }

        // Summary stats
        $total    = count(self::ACHIEVEMENTS);
        $unlocked = count($unlockedKeys);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'achievements'    => $achievements,
                'total'           => $total,
                'unlocked'        => $unlocked,
                'locked'          => $total - $unlocked,
                'completion_pct'  => $total > 0 ? (int) round(($unlocked / $total) * 100) : 0,
            ],
        ]);
    }

    /**
     * GET /api/profile/showcase
     * Returns the user's 3 pinned showcase badges.
     */
    public function getShowcase(Request $request)
    {
        $badges = \App\Models\UserShowcaseBadge::where('user_id', $request->user()->id)
            ->orderBy('slot_index')
            ->get(['achievement_id', 'slot_index']);

        // Enrich with static achievement metadata
        $enriched = $badges->map(function ($badge) {
            $meta = self::ACHIEVEMENTS[$badge->achievement_id] ?? null;
            return [
                'slot_index'     => $badge->slot_index,
                'achievement_id' => $badge->achievement_id,
                'title'          => $meta['title'] ?? $badge->achievement_id,
                'icon'           => $meta['icon'] ?? '🏅',
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => $enriched,
        ]);
    }

    /**
     * POST /api/profile/showcase
     * Body: { "badges": ["roadmap_builder", "first_goal", "mentor_graduate"] }
     * Max 3 badges. Must all be unlocked by the user.
     */
    public function updateShowcase(Request $request)
    {
        $request->validate([
            'badges'   => 'required|array|max:3',
            'badges.*' => 'string',
        ]);

        $user         = $request->user();
        $badgeKeys    = array_values(array_unique($request->input('badges')));

        // Verify all submitted badges are actually unlocked
        $progress     = \App\Models\UserProgress::where('user_id', $user->id)->first();
        $unlockedKeys = $progress?->achievements ?? [];

        foreach ($badgeKeys as $key) {
            if (!array_key_exists($key, self::ACHIEVEMENTS)) {
                return response()->json(['status' => 'error', 'message' => "Unknown achievement: {$key}"], 422);
            }
            if (!in_array($key, $unlockedKeys)) {
                return response()->json(['status' => 'error', 'message' => "Achievement not unlocked: {$key}"], 403);
            }
        }

        // Atomic replace: delete all then re-insert with slot indices
        \App\Models\UserShowcaseBadge::where('user_id', $user->id)->delete();

        foreach ($badgeKeys as $index => $key) {
            \App\Models\UserShowcaseBadge::create([
                'user_id'        => $user->id,
                'achievement_id' => $key,
                'slot_index'     => $index + 1,
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Showcase updated.',
        ]);
    }
}

