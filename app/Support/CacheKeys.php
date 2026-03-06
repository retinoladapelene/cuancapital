<?php

namespace App\Support;

/**
 * Centralized cache key builder.
 *
 * NEVER hardcode cache key strings in controllers or services.
 * Always use this class so key naming is consistent and refactorable.
 *
 * Convention: {entity}:user:{id}[:{qualifier}]
 */
class CacheKeys
{
    // ──────────────────────────── TTL Constants (seconds) ─────────────────────
    public const TTL_XP         = 60;      // 1 minute  — XP progress bar
    public const TTL_BLUEPRINTS = 120;     // 2 minutes — blueprint list
    public const TTL_RGP        = 300;     // 5 minutes — latest RGP session
    public const TTL_DASHBOARD  = 120;     // 2 minutes — dashboard stats
    public const TTL_LEADERBOARD = 300;    // 5 minutes — leaderboard
    public const TTL_USER_PROFILE = 600;   // 10 minutes — user profile data

    // ──────────────────────────── Key Builders ────────────────────────────────

    /**
     * User XP & level progress.
     * Used by: GamificationController
     */
    public static function xp(int $userId): string
    {
        return "xp:user:{$userId}";
    }

    /**
     * User blueprint list.
     * Used by: MentorController, BlueprintStorageController
     */
    public static function blueprints(int $userId, string $type = 'mentor_lab'): string
    {
        return "blueprints:user:{$userId}:{$type}";
    }

    /**
     * Latest Reverse Goal Planner session.
     * Used by: ReverseGoalPlannerController
     */
    public static function rgpLatest(int $userId): string
    {
        return "rgp:latest:user:{$userId}";
    }

    /**
     * Dashboard aggregate stats.
     * Used by: AdminController, DashboardController
     */
    public static function dashboard(int $userId): string
    {
        return "dashboard:user:{$userId}";
    }

    /**
     * Latest simulation result.
     * Used by: MentorController, ProfitSimulatorController
     */
    public static function latestSimulation(int $userId): string
    {
        return "simulation:latest:user:{$userId}";
    }

    /**
     * Global leaderboard (not per-user).
     */
    public static function leaderboard(string $period = 'all'): string
    {
        return "leaderboard:{$period}";
    }

    /**
     * User profile/settings.
     */
    public static function userProfile(int $userId): string
    {
        return "user:profile:{$userId}";
    }

    /**
     * Flush all cache keys related to a specific user.
     * Returns an array of keys to pass to Cache::forget().
     */
    public static function allForUser(int $userId): array
    {
        return [
            static::xp($userId),
            static::blueprints($userId),
            static::rgpLatest($userId),
            static::dashboard($userId),
            static::latestSimulation($userId),
            static::userProfile($userId),
        ];
    }
}
