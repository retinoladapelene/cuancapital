<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProgress;

/**
 * XPService — single source of truth for XP rewards.
 * Wraps the existing UserProgress table used by GamificationController.
 * No duplicate XP logic — same column, same level formula.
 */
class XPService
{
    public function addXP(User $user, int $amount): UserProgress
    {
        $progress = UserProgress::firstOrCreate(
            ['user_id' => $user->id],
            ['xp_points' => 0, 'level' => 1]
        );

        $progress->xp_points += $amount;
        $progress->level      = $this->calculateLevel($progress->xp_points);
        $progress->save();

        // Bust gamification cache so front-end real-time refresh gets the new XP
        \Illuminate\Support\Facades\Cache::forget(\App\Support\CacheKeys::xp($user->id));
        \Illuminate\Support\Facades\Cache::forget(\App\Support\CacheKeys::dashboard($user->id));

        return $progress;
    }

    public function calculateLevel(int $xp): int
    {
        if ($xp < 9500) {
            return (int) floor($xp / 500) + 1;
        }
        return (int) floor(($xp - 9500) / 1000) + 20;
    }
}
