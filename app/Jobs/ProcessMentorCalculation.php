<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\AsyncJob;
use Exception;
use Throwable;

class ProcessMentorCalculation implements ShouldQueue
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
        $affected = DB::table('async_jobs')
            ->where('id', $this->jobId)
            ->where('status', 'pending')
            ->update(['status' => 'processing']);

        if ($affected === 0) return;

        $asyncJob = AsyncJob::find($this->jobId);

        try {
            $inputs = $asyncJob->input_parameters;
            
            // Reconstruct the logic from FinancialEngine using BusinessInputDTO
            $dto = new \App\DTO\BusinessInputDTO(
                $inputs['traffic'] ?? 0,
                $inputs['conversion'] ?? 0,
                $inputs['price'] ?? 0,
                $inputs['cost'] ?? 0,
                $inputs['fixed_cost'] ?? 0,
                $inputs['target_revenue'] ?? 0
            );

            $baseline = \App\Services\FinancialEngine::calculateBaseline($dto);
            
            // Create a MentorSession to track this result persistently
            $session = \App\Models\MentorSession::create([
                'user_id' => $asyncJob->user_id,
                'mode' => 'calculator',
                'input_json' => $inputs,
                'baseline_json' => $baseline
            ]);

            $asyncJob->update([
                'status'         => 'completed',
                'reference_type' => 'App\Models\MentorSession',
                'reference_id'   => $session->id,
            ]);

            // Atomic cache overwrite: polling controller reads this instantly
            Cache::put("job_status:{$this->jobId}:{$asyncJob->user_id}", [
                'id'             => $asyncJob->id,
                'status'         => 'completed',
                'type'           => $asyncJob->type,
                'created_at'     => $asyncJob->created_at,
                'reference_type' => 'App\Models\MentorSession',
                'reference_id'   => $session->id,
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
