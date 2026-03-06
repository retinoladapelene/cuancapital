<?php

namespace App\Jobs;

use App\Repositories\ReverseGoalSessionRepository;
use App\Support\CacheKeys;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SaveReverseGoalSession implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 30, 120];
    public int $timeout = 15;

    public function __construct(
        public readonly int $userId,
        public readonly array $result
    ) {}

    public function handle(ReverseGoalSessionRepository $repo): void
    {
        try {
            $session = $repo->createFromResult($this->userId, $this->result);

            // Invalidate cached latest session so next read is fresh
            Cache::forget(CacheKeys::rgpLatest($this->userId));

            Log::info("[RGP JOB] Session saved for user={$this->userId} id={$session->id}");

            // Domain Event Trigger
            $isFeasible = $this->result['is_feasible'] ?? false;
            event(new \App\Domain\ReverseGoal\Events\ReverseGoalSessionCreated(
                $this->userId,
                $session->id,
                $isFeasible
            ));

        } catch (\Throwable $e) {
            Log::error("[RGP JOB] Failed for user={$this->userId}: " . $e->getMessage());
            throw $e;
        }
    }
}
