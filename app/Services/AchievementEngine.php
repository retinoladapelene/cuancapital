<?php

namespace App\Services;

use App\Models\User;
use App\Models\Achievement;
use App\Models\UserMetric;
use App\Models\UserAchievement;
use App\Models\UserBadge;
use App\Models\AchievementLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AchievementEngine
{
    /**
     * Entry point for checking and granting achievements based on an event.
     *
     * @param User $user
     * @param string $event (e.g., 'lesson_completed', 'level_up')
     * @param array $context Additional context for logs
     * @return array List of newly unlocked achievements
     */
    public function check(User $user, string $event, array $context = []): array
    {
        // 1. Anti-Spam Guard (2 seconds cooldown per event per user)
        $lockKey = "ach_check_{$user->id}_{$event}";
        if (Cache::has($lockKey)) {
            return []; // Fast return if spamming
        }
        Cache::put($lockKey, true, now()->addSeconds(2));

        return DB::transaction(function () use ($user, $event, $context) {
            // 2. Fetch or Create Metrics
            $metric = UserMetric::firstOrCreate(['user_id' => $user->id]);

            // 3. Update Specific Metrics based on Event
            $this->updateMetricForEvent($metric, $event, $context);

            // 4. Find all achievements triggered by this event that the user HAS NOT unlocked
            // We use leftJoin to filter out achievements already existing for this user
            $eligibleAchievements = Achievement::where('trigger_event', $event)
                ->leftJoin('user_achievements', function ($join) use ($user) {
                    $join->on('achievements.id', '=', 'user_achievements.achievement_id')
                         ->where('user_achievements.user_id', '=', $user->id);
                })
                ->whereNull('user_achievements.unlocked_at')
                ->select('achievements.*', 'user_achievements.id as ua_id', 'user_achievements.progress_cached')
                ->get();

            $unlocked = [];

            // 5. Check Conditions
            foreach ($eligibleAchievements as $achievement) {
                if ($this->conditionMet($achievement, $metric, $context)) {
                    $granted = $this->grantAchievement($user, $achievement, $context);
                    if ($granted) {
                        $unlocked[] = $granted;
                    }
                } else {
                    // Update progress cache if applicable
                    $this->updateProgressCache($user, $achievement, $metric);
                }
            }

            return $unlocked;
        });
    }

    /**
     * Updates the global user metric counters based on the triggered event.
     */
    protected function updateMetricForEvent(UserMetric $metric, string $event, array $context)
    {
        $dirty = false;

        switch ($event) {
            case 'lesson_completed':
                $metric->lessons_completed += 1;
                $dirty = true;
                break;

            case 'course_completed':
                $metric->courses_completed += 1;
                $dirty = true;
                break;

            case 'simulation_finished':
                $metric->simulations_passed += 1;
                if (!empty($context['is_perfect'])) {
                    $metric->perfect_runs += 1;
                }
                $dirty = true;
                break;
                
            case 'xp_earned':
                if (isset($context['total_xp'])) {
                    $metric->total_xp = $context['total_xp'];
                }
                if (isset($context['level'])) {
                    $metric->level = $context['level'];
                }
                $dirty = true;
                break;
                
            case 'blueprint_saved':
                $metric->blueprints_saved += 1;
                $metric->mentor_lab_count += 1;
                $dirty = true;
                break;
                
            case 'roadmap_step_completed':
                $metric->roadmap_items_completed += 1;
                $dirty = true;
                break;

            case 'goal_planner_used':
                $metric->goal_planner_count += 1;
                $dirty = true;
                break;

            case 'time_spent':
                if (isset($context['minutes'])) {
                    $metric->minutes_spent += $context['minutes'];
                    $dirty = true;
                }
                break;
        }

        if ($dirty) {
            $metric->save();
        }
    }

    /**
     * Evaluates if the condition for an achievement has been completely met.
     */
    protected function conditionMet(Achievement $achievement, UserMetric $metric, array $context): bool
    {
        $currentValue = $this->getCurrentMetricValue($achievement->condition_type, $metric, $context);
        return $currentValue >= $achievement->condition_value;
    }

    /**
     * Maps the condition string to the actual metric value.
     */
    protected function getCurrentMetricValue(string $conditionType, UserMetric $metric, array $context): int
    {
        return match ($conditionType) {
            'lessons_completed'  => $metric->lessons_completed,
            'courses_completed'  => $metric->courses_completed,
            'simulations_passed' => $metric->simulations_passed,
            'perfect_runs'      => $metric->perfect_runs,
            'total_xp'          => $metric->total_xp,
            'level'             => $metric->level,
            'blueprints_saved'  => $metric->blueprints_saved,
            'roadmap_items_completed' => $metric->roadmap_items_completed,
            'mentor_lab_used'   => $metric->mentor_lab_count,
            'goal_planner_used' => $metric->goal_planner_count,
            'minutes'           => $metric->minutes_spent,
            default             => 0,
        };
    }

    /**
     * Updates the running progress in the user_achievements pivot table.
     */
    protected function updateProgressCache(User $user, Achievement $achievement, UserMetric $metric)
    {
        $currentValue = $this->getCurrentMetricValue($achievement->condition_type, $metric, []);
        
        // Ensure not to exceed max required value for cosmetic reasons
        if ($currentValue > $achievement->condition_value) {
            $currentValue = $achievement->condition_value;
        }

        if (!empty($achievement->ua_id)) {
            // Already tracking progress, just update
            if ($currentValue > $achievement->progress_cached) {
                UserAchievement::where('id', $achievement->ua_id)->update([
                    'progress_cached' => $currentValue
                ]);
            }
        } else {
            // Start tracking
            if ($currentValue > 0) {
                UserAchievement::create([
                    'user_id' => $user->id,
                    'achievement_id' => $achievement->id,
                    'progress_cached' => $currentValue,
                    'unlocked_at' => null
                ]);
            }
        }
    }

    /**
     * Grants the achievement to the user, rewards XP and Badge, and records the log.
     */
    protected function grantAchievement(User $user, Achievement $achievement, array $context): array
    {
        // 1. Mark as unlocked
        $ua = UserAchievement::updateOrCreate(
            ['user_id' => $user->id, 'achievement_id' => $achievement->id],
            [
                'progress_cached' => $achievement->condition_value,
                'unlocked_at' => now(),
            ]
        );

        // 2. Grant Badge if Any
        $badgeGranted = false;
        if ($achievement->badge_id) {
            UserBadge::updateOrCreate(
                ['user_id' => $user->id, 'badge_id' => $achievement->badge_id],
                [
                    'unlocked_at' => now(),
                ]
            );
            $badgeGranted = true;
        }

        // Add Border Frame Grant logic
        $borderGranted = false;
        if ($achievement->border_frame_id) {
            \App\Models\UserBorderFrame::updateOrCreate(
                ['user_id' => $user->id, 'border_frame_id' => $achievement->border_frame_id],
                [
                    'unlocked_at' => now(),
                ]
            );
            $borderGranted = true;
        }

        // 3. Grant XP if Any
        if ($achievement->xp_reward > 0) {
            $progressRepo = app(\App\Repositories\UserProgressRepository::class);
            $progress = $progressRepo->findOrCreateForUser($user->id);
            
            // Apply XP via the central repository. 
            // The repository will automatically handle syncing to UserMetric as well!
            $progressRepo->applyXp(
                $progress, 
                $achievement->xp_reward, 
                "achievement_unlocked", 
                "achievement_id",
                $achievement->id
            );
        }

        // 4. Write Audit Log
        AchievementLog::create([
            'user_id'        => $user->id,
            'achievement_id' => $achievement->id,
            'trigger_event'  => $achievement->trigger_event,
            'context_json'   => $context,
        ]);

        return [
            'achievement' => $achievement,
            'badge_granted' => $badgeGranted,
            'border_granted' => $borderGranted,
            'xp_rewarded' => $achievement->xp_reward
        ];
    }
}
