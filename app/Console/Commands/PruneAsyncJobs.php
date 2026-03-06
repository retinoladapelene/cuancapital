<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PruneAsyncJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:prune-async {--days=7 : The number of days to retain completed/failed async job histories}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune old async jobs from the database that are completed or failed to save space.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Define retention periods
        $retentionCompleted = now()->subDays(7);
        $retentionFailed = now()->subDays(2);
        // Stuck jobs (e.g. worker died while processing, or pending too long)
        $retentionStuck = now()->subMinutes(30);
        
        $totalDeleted = 0;

        // 1. Delete completed jobs older than 7 days
        \App\Models\AsyncJob::where('status', 'completed')
            ->where('created_at', '<=', $retentionCompleted)
            ->chunkById(100, function ($jobs) use (&$totalDeleted) {
                // To safely chunk and delete, we gather the IDs then delete
                $ids = $jobs->pluck('id')->toArray();
                $deleted = \App\Models\AsyncJob::whereIn('id', $ids)->delete();
                $totalDeleted += $deleted;
            });

        // 2. Delete failed jobs older than 2 days
        \App\Models\AsyncJob::where('status', 'failed')
            ->where('created_at', '<=', $retentionFailed)
            ->chunkById(100, function ($jobs) use (&$totalDeleted) {
                $ids = $jobs->pluck('id')->toArray();
                $deleted = \App\Models\AsyncJob::whereIn('id', $ids)->delete();
                $totalDeleted += $deleted;
            });

        // 3. Delete or mark failed pending/processing jobs older than 30 minutes
        // We will just mark them as failed, and they'll get cleaned up in 2 days by the failed logic.
        $markedFailed = \App\Models\AsyncJob::whereIn('status', ['pending', 'processing'])
            ->where('created_at', '<=', $retentionStuck)
            ->update([
                'status' => 'failed',
                'error_message' => 'Job timed out or worker stalled before completion (auto-failed by pruner).'
            ]);

        $this->info("Successfully pruned {$totalDeleted} old async jobs from database.");
        if ($markedFailed > 0) {
            $this->info("Marked {$markedFailed} stalled jobs as failed.");
        }
    }
}
