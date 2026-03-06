<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AsyncJob;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessMentorCalculation;
use App\Jobs\ProcessStrategicEvaluation;

// 1. Setup Test User
$user = User::first() ?? \App\Models\User::factory()->create();

// 2. Create a pending job with intentionally BAD data to trigger an exception 
// For example, trigger division by zero in FinancialEngine or just bad schema
$job = AsyncJob::create([
    'user_id' => $user->id,
    'type' => 'mentor_calculation',
    'status' => 'pending',
    'input_parameters' => [
        'bad_payload' => true,
        // Missing required inputs will throw an error in BusinessInputDTO formatting or offset
    ]
]);

echo "Created pending job ID: {$job->id} with bad payload.\n";

$worker = new ProcessMentorCalculation($job->id);

echo "Executing worker to simulate runtime failure...\n";
try {
    $worker->handle();
} catch (\Throwable $e) {
    echo "Caught expected exception: " . $e->getMessage() . "\n";
}

$jobAfter = AsyncJob::find($job->id);
echo "Final Job Status: {$jobAfter->status}\n";
echo "Error Message Logged: " . substr($jobAfter->error_message, 0, 100) . "...\n";

// 3. Clean up
$job->delete();
echo "Cleanup done.\n";
