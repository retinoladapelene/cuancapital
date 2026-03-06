<?php
// Quick helper script to get test token + job ID for k6 tests
// Run: php setup-k6-test.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\AsyncJob;

$user = User::first();

if (!$user) {
    echo "❌ No user found in DB. Please register an account first.\n";
    exit(1);
}

// Create a Sanctum token for k6 tests
$tokenResult = $user->createToken('k6-load-test');
$token = $tokenResult->plainTextToken;

// Get or create a test async job for polling tests
$job = AsyncJob::where('user_id', $user->id)->latest()->first();

if (!$job) {
    // Create a dummy job for polling simulation
    $job = AsyncJob::create([
        'user_id'          => $user->id,
        'type'             => 'mentor_calculation',
        'status'           => 'completed',
        'input_parameters' => ['test' => true],
        'reference_type'   => null,
        'reference_id'     => null,
    ]);
    echo "ℹ️  Created dummy job for polling test.\n\n";
}

echo "───────────────────────────────────────────────\n";
echo "  k6 Test Credentials\n";
echo "───────────────────────────────────────────────\n";
echo "User:    {$user->name} ({$user->email})\n";
echo "User ID: {$user->id}\n";
echo "\n";
echo "TOKEN={$token}\n";
echo "JOB_ID={$job->id}\n";
echo "BASE_URL=http://localhost:8000\n";
echo "\n───────────────────────────────────────────────\n";
echo "Copy and paste commands below to run tests:\n\n";
echo "# 1. Polling Flood Test (2 min, 300 VU)\n";
echo "k6 run -e BASE_URL=http://localhost:8000 -e TOKEN={$token} -e JOB_ID={$job->id} tests/load/k6-polling-flood.js\n\n";
echo "# 2. Heavy Endpoint Test\n";
echo "k6 run -e BASE_URL=http://localhost:8000 -e TOKEN={$token} tests/load/k6-heavy-endpoints.js\n\n";
echo "# 3. Stepwise Ramp (50->1000 VU, 13 min)\n";
echo "k6 run -e BASE_URL=http://localhost:8000 -e TOKEN={$token} -e JOB_ID={$job->id} tests/load/k6-stepwise.js\n";
echo "───────────────────────────────────────────────\n";
