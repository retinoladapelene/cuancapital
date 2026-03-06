<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\AsyncJob;

class AsyncJobController extends Controller
{
    /**
     * Cache TTL in seconds.
     * 3 seconds: short enough that user won't notice, long enough to absorb polling floods.
     * At 300 users polling every 3s → 100 req/s. With cache: ~97% cache hits after first poll.
     */
    private const CACHE_TTL = 3;

    /**
     * Polling endpoint to check the status of a background job.
     * Hardened to ensure users can only see their own jobs (No IDOR).
     *
     * Performance: Redis cache with 3s TTL drastically reduces DB load.
     * Workers overwrite cache atomically on job completion (see ProcessStrategicEvaluation etc.)
     * so polling sees final state almost immediately after job finishes.
     */
    public function status(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $userId = $request->user()->id;
        $cacheKey = "job_status:{$id}:{$userId}";

        // ── Cache Hit (Cold Miss uses DB fallback below) ──────────────────────
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return response()->json($cached);
        }

        // ── Cache Miss: Query DB ───────────────────────────────────────────────
        $job = AsyncJob::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$job) {
            return response()->json(['message' => 'Job not found or access denied.'], 404);
        }

        // Build response payload
        $response = [
            'id'         => $job->id,
            'status'     => $job->status,
            'type'       => $job->type,
            'created_at' => $job->created_at,
        ];

        if ($job->status === 'completed') {
            $response['reference_type'] = $job->reference_type;
            $response['reference_id']   = $job->reference_id;
        }

        if ($job->status === 'failed') {
            $response['error_message'] = $job->error_message ?? 'An unknown error occurred during background processing.';
        }

        // ── Write to cache (non-final states only get short TTL, final states longer) ─
        // Completed/failed jobs: cache for 30s (no point polling further)
        // Pending/processing: cache 3s (user is actively polling)
        $ttl = in_array($job->status, ['completed', 'failed']) ? 30 : self::CACHE_TTL;
        Cache::put($cacheKey, $response, $ttl);

        return response()->json($response);
    }
}
