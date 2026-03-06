<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\CashbookAccount;
use App\Models\CashbookCategory;
use App\Services\Cashbook\TransactionService;
use Carbon\Carbon;

class CashbookStressTest extends Command
{
    protected $signature = 'cashbook:stress-test';
    protected $description = 'Run a 10k transaction stress test on the Cashbook V2 system.';

    public function handle(TransactionService $transactionService)
    {
        $this->info('Starting Cashbook V2 10k Stress Test...');

        // Create a dummy user
        $user = User::firstOrCreate(
            ['email' => 'stresstest@example.com'],
            ['name' => 'Stress Test', 'password' => bcrypt('password'), 'role' => 'user']
        );

        $this->info("User ID: {$user->id}");

        // Create dummy account
        $account = CashbookAccount::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Stress Account'],
            ['type' => 'bank', 'balance_cached' => 0]
        );

        // Create dummy category
        $category = CashbookCategory::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Stress Food'],
            ['pillar' => 'wajib', 'is_default' => 0]
        );

        $start = microtime(true);
        $count = 10000;
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            $transactionService->createTransaction($user->id, [
                'account_id' => $account->id,
                'category_id' => $category->id,
                'type' => 'expense',
                'amount' => 10.50,
                'note' => 'Load test ' . $i,
                'transaction_date' => Carbon::today()->subDays(rand(0, 30))->format('Y-m-d'),
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->line('');

        $end = microtime(true);
        $time = round($end - $start, 2);

        $this->info("Successfully inserted {$count} transactions with full DB wrapper, incremental triggers, and audit logging.");
        $this->info("Time elapsed: {$time} seconds.");
        $this->info("Average speed: " . round($count / $time, 2) . " transactions/sec.");
    }
}
