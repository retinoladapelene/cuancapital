<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\AsyncJob;
use App\Services\Strategic\StrategicEngine;
use App\DTO\StrategicAnalysisAggregate;
use Exception;
use Throwable;

class ProcessStrategicEvaluation implements ShouldQueue
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

    /**
     * Create a new job instance.
     */
    public function __construct(string $jobId)
    {
        $this->jobId = $jobId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Atomic Status Switch (pending -> processing)
        // Ensures if worker crashes and retries, it doesn't double-process if already done.
        $affected = DB::table('async_jobs')
            ->where('id', $this->jobId)
            ->where('status', 'pending')
            ->update(['status' => 'processing']);

        if ($affected === 0) {
            return; // Job is not pending or doesn't exist. Abort safely.
        }

        $asyncJob = AsyncJob::find($this->jobId);

        try {
            // 2. Build DTO inside the Job (Not from raw queue payload)
            $inputs = $asyncJob->input_parameters;
            
            $inputDto = \App\DTO\StrategicInput::fromArray($inputs);

            // 3. Heavy Computation Simulation
            $engine = app(StrategicEngine::class);
            $result = $engine->evaluate($inputDto);

            // 4. Create proper Session for auditability
            $session = \App\Models\MentorSession::create([
                'user_id' => $asyncJob->user_id,
                'mode' => 'planner',
                'input_json' => $inputs,
                'baseline_json' => $result->toArray(),
                // Or any other relevant structure mentor session requires 
            ]);

            // 5. Mark Completed (Atomic)
            $asyncJob->update([
                'status'         => 'completed',
                'reference_type' => 'App\Models\MentorSession',
                'reference_id'   => $session->id,
            ]);

            // 6. Atomic cache overwrite — push final state directly so polling
            //    controller sees 'completed' immediately without a DB round-trip.
            //    TTL 30s: user has 30s to read result before cache expires.
            Cache::put("job_status:{$this->jobId}:{$asyncJob->user_id}", [
                'id'             => $asyncJob->id,
                'status'         => 'completed',
                'type'           => $asyncJob->type,
                'created_at'     => $asyncJob->created_at,
                'reference_type' => 'App\Models\MentorSession',
                'reference_id'   => $session->id,
            ], 30);

        } catch (Throwable $e) {
            // Fail safely
            $asyncJob->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            // Push failed state to cache so polling stops hitting DB immediately
            Cache::put("job_status:{$this->jobId}:{$asyncJob->user_id}", [
                'id'            => $asyncJob->id,
                'status'        => 'failed',
                'type'          => $asyncJob->type,
                'created_at'    => $asyncJob->created_at,
                'error_message' => $e->getMessage(),
            ], 30);

            // Re-throw so Laravel's native failed_jobs table catches it too
            throw $e;
        }
    }
}
