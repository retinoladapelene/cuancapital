<?php

namespace App\Services\Cashbook;

use App\Models\CashbookAccount;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountService
{
    /**
     * Create a new account with strict isolation.
     */
    public function createAccount(int $userId, array $data): CashbookAccount
    {
        return DB::transaction(function () use ($userId, $data) {
            $account = CashbookAccount::create([
                'user_id' => $userId,
                'name' => $data['name'],
                'type' => $data['type'] ?? 'cash',
                'balance_cached' => $data['initial_balance'] ?? 0,
                'is_active' => true,
            ]);

            return $account;
        });
    }

    /**
     * Update account details.
     */
    public function updateAccount(int $accountId, int $userId, array $data): CashbookAccount
    {
        return DB::transaction(function () use ($accountId, $userId, $data) {
            $account = CashbookAccount::where('id', $accountId)
                ->where('user_id', $userId)
                ->firstOrFail();
            
            $account->update([
                'name' => $data['name'] ?? $account->name,
                'type' => $data['type'] ?? $account->type,
            ]);

            return $account;
        });
    }

    /**
     * Deactivate an account.
     */
    public function deactivateAccount(int $accountId, int $userId): CashbookAccount
    {
        return DB::transaction(function () use ($accountId, $userId) {
            $account = CashbookAccount::where('id', $accountId)
                ->where('user_id', $userId)
                ->firstOrFail();
            
            $account->update(['is_active' => false]);
            return $account;
        });
    }
    
    /**
     * Internal method to strictly enforce balance integrity.
     */
    public function recalculateBalance(int $accountId, int $userId, float $amount, bool $isCredit): void
    {
        $account = CashbookAccount::where('id', $accountId)
            ->where('user_id', $userId)
            ->lockForUpdate() // Pessimistic locking for concurrency
            ->firstOrFail();
        
        if ($isCredit) {
            $account->balance_cached += $amount;
        } else {
            $account->balance_cached -= $amount;
        }
        
        $account->save();
    }
}
