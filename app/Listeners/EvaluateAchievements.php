<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\UserAchievement;
use App\Jobs\ProcessXpAward;
use Illuminate\Support\Facades\DB;

use App\Gamification\Events\ReverseGoalSessionCreated;
use App\Gamification\Events\RoadmapStepCompleted;
use App\Gamification\Events\MentorSessionEvaluated;
use App\Gamification\Events\BlueprintSaved;
use App\Gamification\Events\RoadmapGenerated;

class EvaluateAchievements implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;
    public $backoff = [10, 30, 60];

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $userId = $this->getUserIdFromEvent($event);
        if (!$userId) return;

        // NOTE: AchievementEngine is now the primary tracker for most behaviors.
        // This listener remains for specific manual/legacy hooks.

        // --- 1. First Calculation ---
        if ($event instanceof ReverseGoalSessionCreated) {
            $this->unlock($userId, 'planner_1');
        }

        // --- 2. Action Taker / Roadmap Starter ---
        if ($event instanceof RoadmapStepCompleted) {
            $this->unlock($userId, 'roadmap_1');
        }

        // --- 3. First Architecture Blueprint Saved ---
        if ($event instanceof BlueprintSaved) {
            $this->unlock($userId, 'blueprint_1');
        }
        
        // --- 5. Profit Milestones (RGP Evaluator hook) ---
        if ($event instanceof ReverseGoalSessionCreated) {
            // Check if user target profit >= 10,000,000
            $targetProfit = data_get($event->result, 'data.targetProfit', 0);
            if ($targetProfit >= 10000000) {
                $this->unlock($userId, 'profit_10m');
            }
        }
    }

    /**
     * Extract user_id dynamically from the Domain Events
     */
    private function getUserIdFromEvent(object $event): ?int
    {
        return $event->userId ?? null; 
    }

    /**
     * Idempotent unlock mechanism
     */
    private function unlock(int $userId, string $code): void
    {
        $achievement = \App\Models\Achievement::where('code', $code)->first();
        if (!$achievement) return;

        DB::transaction(function () use ($userId, $achievement) {
            $exists = UserAchievement::where('user_id', $userId)
                ->where('achievement_id', $achievement->id)
                ->exists();

            if ($exists) return;

            UserAchievement::create([
                'user_id' => $userId,
                'achievement_id' => $achievement->id,
                'unlocked_at' => now(),
            ]);

            // Phase 18: Push to volatile cache for the frontend to Toast instantly
            $cacheKey = "unlocked_toast:user:{$userId}";
            $toasts = \Illuminate\Support\Facades\Cache::get($cacheKey, []);
            if (!in_array($achievement->code, $toasts)) {
                $toasts[] = $achievement->code;
                \Illuminate\Support\Facades\Cache::put($cacheKey, $toasts, 300);
            }

            if ($achievement->xp_reward > 0) {
                ProcessXpAward::dispatch(
                    $userId,
                    'achievement_bonus',
                    $achievement->code,
                    $achievement->xp_reward
                );
            }
        });
    }
}
