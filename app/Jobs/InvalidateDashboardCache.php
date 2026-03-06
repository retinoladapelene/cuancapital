<?php

namespace App\Jobs;

use App\Support\CacheKeys;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class InvalidateDashboardCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [2, 10, 30];
    public int $timeout = 10;

    public function __construct(
        public readonly int $userId,
        public readonly array $specificKeys = []
    ) {}

    public function handle(): void
    {
        if (! empty($this->specificKeys)) {
            foreach ($this->specificKeys as $key) {
                Cache::forget($key);
            }
        } else {
            // Flush all user-related cache keys
            foreach (CacheKeys::allForUser($this->userId) as $key) {
                Cache::forget($key);
            }
        }

        Log::info("[CACHE JOB] Invalidated cache for user={$this->userId}");
    }
}
