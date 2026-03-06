<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\AsyncJob;
use App\Services\RoadmapGeneratorService;
use Exception;
use Throwable;

class ProcessRoadmapGeneration implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 10;
    public $timeout = 120;

    public function retryUntil()
    {
        return now()->addMinutes(5);
    }

    protected $jobId;

    public function __construct(string $jobId)
    {
        $this->jobId = $jobId;
    }

    public function handle(): void
    {
        // 1. Atomic Status Switch
        $affected = DB::table('async_jobs')
            ->where('id', $this->jobId)
            ->where('status', 'pending')
            ->update(['status' => 'processing']);

        if ($affected === 0) return;

        $asyncJob = AsyncJob::find($this->jobId);

        try {
            // Load latest session
            $session = \App\Models\MentorSession::where('user_id', $asyncJob->user_id)->latest()->first();
            
            // 3. Heavy Computation
            $service = new RoadmapGeneratorService();
            $roadmapResult = $service->generateForUser($asyncJob->user_id, $session);

            // 4. Mark Completed Using Real ID
            $asyncJob->update([
                'status'         => 'completed',
                'reference_type' => 'App\Models\Roadmap',
                'reference_id'   => $roadmapResult->id,
            ]);

            // Atomic cache overwrite: polling controller reads this instantly
            Cache::put("job_status:{$this->jobId}:{$asyncJob->user_id}", [
                'id'             => $asyncJob->id,
                'status'         => 'completed',
                'type'           => $asyncJob->type,
                'created_at'     => $asyncJob->created_at,
                'reference_type' => 'App\Models\Roadmap',
                'reference_id'   => $roadmapResult->id,
            ], 30);

        } catch (Throwable $e) {
            $asyncJob->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Cache::put("job_status:{$this->jobId}:{$asyncJob->user_id}", [
                'id'            => $asyncJob->id,
                'status'        => 'failed',
                'type'          => $asyncJob->type,
                'created_at'    => $asyncJob->created_at,
                'error_message' => $e->getMessage(),
            ], 30);

            throw $e;
        }
    }
}
