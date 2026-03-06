<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Simple Circuit Breaker for external API calls.
 *
 * Usage:
 *   $result = CircuitBreaker::call('openai', function() {
 *       return $this->aiService->analyze($input);
 *   }, fallback: fn() => ['error' => 'AI unavailable']);
 *
 * State:
 *   - CLOSED  : Calls pass through normally
 *   - OPEN    : Too many failures, calls go to fallback
 *   - HALF-OPEN: Probing if service recovered (1 attempt allowed)
 */
class CircuitBreaker
{
    private const FAILURE_THRESHOLD = 3;    // Failures before opening circuit
    private const OPEN_DURATION_SEC = 60;   // Seconds circuit stays open
    private const HALF_OPEN_PROBE   = 1;    // Attempts allowed in half-open state

    /**
     * @param  string        $service   Unique service name (used as cache key)
     * @param  callable      $operation The external call to attempt
     * @param  callable|null $fallback  What to return when circuit is OPEN
     * @param  int           $timeout   Max seconds for $operation (not enforced at PHP level, informational)
     */
    public static function call(string $service, callable $operation, ?callable $fallback = null, int $timeout = 10): mixed
    {
        $state = static::getState($service);

        if ($state === 'OPEN') {
            Log::warning("[CIRCUIT BREAKER] OPEN — skipping call to {$service}");
            return $fallback ? $fallback() : null;
        }

        try {
            $result = $operation();
            static::recordSuccess($service);
            return $result;

        } catch (\Throwable $e) {
            static::recordFailure($service);
            Log::error("[CIRCUIT BREAKER] Failure for {$service}: " . $e->getMessage());

            if (static::getState($service) === 'OPEN') {
                Log::warning("[CIRCUIT BREAKER] Circuit OPENED for {$service} after repeated failures");
            }

            if ($fallback) {
                return $fallback();
            }

            throw $e;
        }
    }

    // ──────────────────────────── State Management ─────────────────────────────

    private static function getState(string $service): string
    {
        $failures    = Cache::get("cb:{$service}:failures", 0);
        $openUntil   = Cache::get("cb:{$service}:open_until", 0);

        if ($openUntil && now()->timestamp < $openUntil) {
            return 'OPEN';
        }

        if ($failures >= self::FAILURE_THRESHOLD) {
            return 'HALF-OPEN';
        }

        return 'CLOSED';
    }

    private static function recordSuccess(string $service): void
    {
        Cache::forget("cb:{$service}:failures");
        Cache::forget("cb:{$service}:open_until");
    }

    private static function recordFailure(string $service): void
    {
        $failures = Cache::increment("cb:{$service}:failures");
        Cache::put("cb:{$service}:failures", $failures, now()->addMinutes(5));

        if ($failures >= self::FAILURE_THRESHOLD) {
            $openUntil = now()->addSeconds(self::OPEN_DURATION_SEC)->timestamp;
            Cache::put("cb:{$service}:open_until", $openUntil, now()->addSeconds(self::OPEN_DURATION_SEC + 10));
        }
    }
}
