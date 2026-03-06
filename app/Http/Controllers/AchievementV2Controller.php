<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Achievement;
use App\Models\UserMetric;
use App\Models\UserAchievement;
use App\Models\UserBadge;
use App\Models\BorderFrame;
use App\Models\UserBorderFrame;
use Illuminate\Support\Facades\DB;
use App\Support\ApiResponse;

class AchievementV2Controller extends Controller
{
    /**
     * GET /api/me/achievements
     * Get all achievements merged with the user's unlocked state.
     */
    public function getAchievements(Request $request)
    {
        $user = $request->user();
        
        // 1. Load Registry from Database
        $registry = Achievement::orderBy('category', 'asc')->orderBy('condition_value', 'asc')->get();
        
        // 2. Fetch User Unlocked state and progress
        $userAchievements = UserAchievement::where('user_id', $user->id)
            ->get()
            ->keyBy('achievement_id');
            
        // 3. Map to UI Payload
        $achievements = [];
        foreach ($registry as $ach) {
            $ua = $userAchievements->get($ach->id);
            $isUnlocked = $ua && $ua->unlocked_at !== null;
            
            $achievements[] = [
                'id' => $ach->code,
                'name' => $ach->title,
                'description' => $ach->description,
                'icon' => $ach->icon,
                'xp_bonus' => $ach->xp_reward,
                'is_unlocked' => $isUnlocked,
                'unlocked_at' => $isUnlocked ? $ua->unlocked_at->toIso8601String() : null,
                'progress_cached' => $ua ? $ua->progress_cached : 0,
                'condition_value' => $ach->condition_value,
                'category' => $ach->category,
                'is_hidden' => $ach->is_hidden,
                'badge_name' => $ach->badge?->name,
            ];
        }

        // 4. Fetch Global Metrics for UI HUD (Optional but existing feature)
        $metrics = UserMetric::firstOrCreate(['user_id' => $user->id]);

        return ApiResponse::success([
            'metrics' => $metrics,
            'achievements' => $achievements
        ]);
    }

    /**
     * GET /api/me/badges
     * Get all badges unlocked by the user.
     */
    public function getBadges(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin';
        
        // Fetch all user badges to map ownership and equipped status
        $userBadges = UserBadge::where('user_id', $user->id)->get()->keyBy('badge_id');

        // Fetch all badges
        $badges = \App\Models\Badge::orderBy('rarity_weight', 'asc')->get()->map(function ($badge) use ($userBadges, $isAdmin) {
            $ub = $userBadges->get($badge->id);
            $isUnlocked = $isAdmin || ($ub ? true : false);
            $isEquipped = $ub ? $ub->is_equipped : false;
            $unlockedAt = $ub ? $ub->unlocked_at : ($isAdmin ? now() : null);

            $achievement = \App\Models\Achievement::where('badge_id', $badge->id)->first();
            $achievementCategory = $achievement ? $achievement->category : 'unknown';
            $achievementIcon = $achievement ? $achievement->icon : '🏅';

            return [
                'id'                   => $badge->id,
                'name'                 => $badge->name,
                'rarity'               => $badge->rarity,
                'is_unlocked'          => $isUnlocked,
                'is_equipped'          => $isEquipped,
                'unlocked_at'          => $unlockedAt,
                'achievement_category' => $achievementCategory,
                'achievement_icon'     => $achievementIcon
            ];
        });

        return ApiResponse::success([
            'badges' => $badges,
            'total_unlocked' => $isAdmin ? $badges->count() : $userBadges->count()
        ]);
    }

    /**
     * POST /api/me/badge/equip
     * Equip a specific badge for the profile border.
     */
    public function equipBadge(Request $request)
    {
        $request->validate([
            'badge_id' => 'required|integer|exists:badges,id'
        ]);

        $user = $request->user();
        $badgeId = $request->input('badge_id');
        $isAdmin = $user->role === 'admin';

        // Check if user actually owns this badge
        $ownsBadge = UserBadge::where('user_id', $user->id)->where('badge_id', $badgeId)->first();

        if (!$isAdmin && !$ownsBadge) {
            return ApiResponse::forbidden('You have not unlocked this badge yet.');
        }

        DB::transaction(function () use ($user, $badgeId, $isAdmin, $ownsBadge) {
            // Unequip all current badges
            UserBadge::where('user_id', $user->id)->update(['is_equipped' => false]);
            // Equip the new one
            if ($isAdmin && !$ownsBadge) {
                UserBadge::create([
                    'user_id' => $user->id,
                    'badge_id' => $badgeId,
                    'is_equipped' => true,
                    'unlocked_at' => now(),
                ]);
            } else {
                UserBadge::where('user_id', $user->id)->where('badge_id', $badgeId)->update(['is_equipped' => true]);
            }
        });

        return ApiResponse::success(null, 'Badge successfully equipped.');
    }

    /**
     * GET /api/me/borders
     * Get all border frames (all available) with user unlock status.
     */
    public function getBorders(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin';

        // Fetch user's unlocked borders
        $userBorders = UserBorderFrame::where('user_id', $user->id)->get()->keyBy('border_frame_id');

        // Fetch current equipped border from user_metrics
        $metrics = UserMetric::firstOrCreate(['user_id' => $user->id]);
        $equippedBorderId = $metrics->equipped_border_id;

        // Fetch all available borders
        $borders = BorderFrame::orderBy('rarity_weight', 'asc')->get()->map(function ($border) use ($userBorders, $isAdmin, $equippedBorderId) {
            $ub = $userBorders->get($border->id);
            $isUnlocked = $isAdmin || ($ub ? true : false);
            $isEquipped = $border->id === $equippedBorderId;

            return [
                'id'               => $border->id,
                'name'             => $border->name,
                'rarity'           => $border->rarity,
                'rarity_weight'    => $border->rarity_weight,
                'css_class'        => $border->css_class,
                'icon'             => $border->icon ?? '🔲',
                'unlock_condition' => $border->unlock_condition,
                'is_unlocked'      => $isUnlocked,
                'is_equipped'      => $isEquipped,
                'unlocked_at'      => $ub ? $ub->unlocked_at : null,
            ];
        });

        return ApiResponse::success([
            'borders'        => $borders,
            'equipped_border' => $borders->firstWhere('is_equipped', true),
        ]);
    }

    /**
     * POST /api/me/border/equip
     * Equip a specific border frame for the user's avatar.
     */
    public function equipBorder(Request $request)
    {
        $request->validate([
            'border_id' => 'required|integer|exists:border_frames,id'
        ]);

        $user = $request->user();
        $borderId = $request->input('border_id');
        $isAdmin = $user->role === 'admin';

        // Check if user owns this border
        $ownsBorder = UserBorderFrame::where('user_id', $user->id)->where('border_frame_id', $borderId)->first();

        if (!$isAdmin && !$ownsBorder) {
            return ApiResponse::forbidden('You have not unlocked this border frame yet.');
        }

        // Update equipped border on user_metrics
        UserMetric::where('user_id', $user->id)->update(['equipped_border_id' => $borderId]);

        return ApiResponse::success(null, 'Border frame successfully equipped.');
    }

    /**
     * POST /api/me/border/unequip
     * Remove the currently equipped border.
     */
    public function unequipBorder(Request $request)
    {
        $user = $request->user();
        UserMetric::where('user_id', $user->id)->update(['equipped_border_id' => null]);
        return ApiResponse::success(null, 'Border frame removed.');
    }

    /**
     * POST /api/me/track-time
     * Receive a heartbeat ping from the frontend conveying active time spent.
     * This directly feeds into the Gamification Engine for time-based achievements.
     */
    public function trackTime(Request $request)
    {
        $request->validate([
            'minutes' => 'required|integer|min:0|max:60'
        ]);

        $user = $request->user();
        $minutes = $request->input('minutes');

        try {
            if ($minutes > 0) {
                app(\App\Services\AchievementEngine::class)->check($user, 'time_spent', [
                    'minutes' => $minutes
                ]);
            }

            // Phase 8: Check for Admin Reward Notification Ping
            $pingKey = "admin_reward_notify:user:{$user->id}";
            $rewardPing = \Illuminate\Support\Facades\Cache::pull($pingKey);

            return ApiResponse::success([
                'tracked_minutes' => $minutes,
                'reward_ping' => $rewardPing
            ], 'Time tracked successfully.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to track time API: ' . $e->getMessage());
            return ApiResponse::error('Failed to track time', 500);
        }
    }
}
