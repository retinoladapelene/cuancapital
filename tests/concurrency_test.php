<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AsyncJob;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessMentorCalculation;

// 1. Setup Test User
$user = User::first() ?? User::factory()->create();

// 2. Create multiple pending jobs manually
$jobIds = [];
for ($i = 0; $i < 5; $i++) {
    $job = AsyncJob::create([
        'user_id' => $user->id,
        'type' => 'mentor_calculation',
        'status' => 'pending',
        'input_parameters' => [
            'traffic' => 1000,
            'conversion' => 2,
            'price' => 50000,
            'cost' => 20000,
            'fixed_cost' => 1000000,
            'target_revenue' => 10000000
        ]
    ]);
    $jobIds[] = $job->id;
}

echo "Created 5 pending jobs.\n";

// 3. Simulate concurrency by manually invoking the Handle on the same Job ID simultaneously
// Since we only have one run thread here, we will just prove the atomic update:
// Let's pretend 2 workers grabbed the EXACT same Job ID at the exact same moment.
$targetJobId = $jobIds[0];

echo "Simulating Worker 1 grabbing Job ID: {$targetJobId}\n";
$worker1 = new ProcessMentorCalculation($targetJobId);

echo "Simulating Worker 2 grabbing Job ID: {$targetJobId}\n";
$worker2 = new ProcessMentorCalculation($targetJobId);

// Worker 1 executes first
echo "Worker 1 Executing...\n";
$worker1->handle();
$jobAfterW1 = AsyncJob::find($targetJobId);
echo "- Status after W1: {$jobAfterW1->status} (Real ID: {$jobAfterW1->reference_id})\n";

// Worker 2 executes immediately after on the SAME job
echo "Worker 2 Executing (Should abort gracefully due to atomic check)...\n";
$worker2->handle();
$jobAfterW2 = AsyncJob::find($targetJobId);
echo "- Status after W2: {$jobAfterW2->status} (Should not throw exception or double process)\n";

// 4. Clean up
AsyncJob::whereIn('id', $jobIds)->delete();
echo "Cleanup done.\n";
