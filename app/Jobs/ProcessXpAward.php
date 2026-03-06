<?php

namespace App\Jobs;

use App\Repositories\UserProgressRepository;
use App\Support\CacheKeys;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessXpAward implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Max retry attempts before the job is moved to the failed_jobs table.
     */
    public int $tries = 3;

    /**
     * Exponential backoff in seconds: 5s, 30s, 120s.
     */
    public array $backoff = [5, 30, 120];

    /**
     * Max seconds the job may run before being killed.
     */
    public int $timeout = 30;

    public function __construct(
        public readonly int $userId,
        public readonly string $action,
        public readonly ?string $referenceType = null,
        public readonly ?int $referenceId = null
    ) {}

    public function handle(UserProgressRepository $repo): void
    {
        try {
            $progress = $repo->findOrCreateForUser($this->userId);
            $xpRewards = $this->xpRewardMap();

            $baseReward = $xpRewards[$this->action] ?? null;

            if ($baseReward === null) {
                Log::warning("[XP JOB] Unknown action: {$this->action} for user {$this->userId}");
                return;
            }

            // Feature: Level-Scaled XP System
            // Higher level users get a compounding XP bonus to keep scaling rewarding
            // e.g., Level 1 = 1x, Level 2 = 1.05x, Level 10 = 1.45x
            $multiplier = 1.0 + (($progress->level - 1) * 0.05);
            $reward = (int) ceil($baseReward * $multiplier);

            $repo->applyXp($progress, $reward, $this->action, $this->referenceType, $this->referenceId);

            // Invalidate the cached XP progress so next request is fresh
            Cache::forget(CacheKeys::xp($this->userId));
            Cache::forget(CacheKeys::dashboard($this->userId));

            Log::info("[XP JOB] +{$reward}XP (Base: {$baseReward}XP | Level Multiplier: {$multiplier}x) action={$this->action} user={$this->userId} ref={$this->referenceId}");

        } catch (\Throwable $e) {
            Log::error("[XP JOB] Failed for user={$this->userId} action={$this->action}: " . $e->getMessage());
            throw $e; // Rethrow so the queue retries
        }
    }

    /**
     * Server-side XP reward map.
     * Centralized here — frontend ONLY sends the action key, never the amount.
     */
    private function xpRewardMap(): array
    {
        return [
            'reverse_calculate' => 0,
            'feasibility_green' => 0,
            'save_blueprint'    => 500,
            'profit_over_10m'   => 0,
            'mentor_evaluate'   => 0,
            'generate_roadmap'  => 0,
            'roadmap_toggle'    => 0,
        ];
    }
}
