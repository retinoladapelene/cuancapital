<?php

namespace App\Repositories;

use App\Models\UserProgress;
use Illuminate\Support\Facades\DB;

class UserProgressRepository
{
    /**
     * Find or create the user's XP progress record.
     */
    public function findOrCreateForUser(int $userId): UserProgress
    {
        return UserProgress::firstOrCreate(
            ['user_id' => $userId],
            ['xp_points' => 0, 'level' => 1]
        );
    }

    /**
     * Apply XP to the user's progress record & log the transaction.
     * Guaranteed atomic via DB transaction.
     * Enforces Idempotency to prevent duplicate action exploits.
     * Returns the updated model.
     */
    public function applyXp(
        UserProgress $progress, 
        int $xp, 
        string $action,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): UserProgress {
        return DB::transaction(function () use ($progress, $xp, $action, $referenceType, $referenceId) {
            
            // 1. Idempotency Check (Application-level guard)
            // If this exact action + reference_id already exists for the user, skip.
            if ($referenceId !== null) {
                $exists = DB::table('xp_transactions')
                    ->where('user_id', $progress->user_id)
                    ->where('action', $action)
                    ->where('reference_id', $referenceId)
                    ->lockForUpdate() // Prevent race conditions
                    ->exists();

                if ($exists) {
                    \Illuminate\Support\Facades\Log::warning("[XP AUDIT] Duplicate action prevented: user={$progress->user_id}, action={$action}, ref_id={$referenceId}");
                    return $progress; // Already awarded, just return unchanged
                }
            }

            $progress->xp_points += $xp;
            $progress->level      = $this->calculateLevel($progress->xp_points);

            // Update achievement milestones
            $achievements         = $progress->achievements ?? [];
            $progress->achievements = $this->evaluateAchievements($progress->xp_points, $achievements);

            // Update boolean milestone flags
            $this->applyMilestoneFlags($progress, $action);

            $progress->save();

            // Sync with UserMetric (V2 Gamification / Leaderboard)
            $metric = \App\Models\UserMetric::firstOrCreate(['user_id' => $progress->user_id]);
            $metric->total_xp = $progress->xp_points;
            $metric->level = $progress->level;
            $metric->save();

            // Audit log for anti-cheat and transparency
            DB::table('xp_transactions')->insert([
                'user_id'        => $progress->user_id,
                'action'         => $action,
                'xp_earned'      => $xp,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'created_at'     => now(),
            ]);

            return $progress->fresh();
        });
    }

    /**
     * Calculate level from raw XP points.
     */
    public function calculateLevel(int $xp): int
    {
        if ($xp < 9500) {
            return (int) floor($xp / 500) + 1;
        }
        return (int) floor(($xp - 9500) / 1000) + 20;
    }

    // ──────────────────────────── Private helpers ─────────────────────────────

    private function evaluateAchievements(int $xp, array $current): array
    {
        $milestones = [
            'ach_first_1000' => 1000,
            'ach_first_5k'   => 5000,
            'ach_first_10k'  => 10000,
        ];

        foreach ($milestones as $key => $threshold) {
            if ($xp >= $threshold && ! in_array($key, $current)) {
                $current[] = $key;
            }
        }

        return $current;
    }

    private function applyMilestoneFlags(UserProgress $progress, string $action): void
    {
        $now = now();
        switch ($action) {
            case 'reverse_calculate':
            case 'feasibility_green':
                $progress->is_rgp_completed = true;
                break;
            case 'profit_over_10m':
                $progress->is_simulator_used = true;
                break;
            case 'mentor_evaluate':
                $progress->is_mentor_completed = true;
                break;
            case 'generate_roadmap':
                $progress->is_roadmap_generated  = true;
                $progress->roadmap_generated_at  = $now;
                break;
        }
    }
}
