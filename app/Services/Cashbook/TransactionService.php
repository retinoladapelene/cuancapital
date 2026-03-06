<?php

namespace App\Services\Cashbook;

use App\Models\CashbookTransaction;
use App\Models\CashbookAuditLog;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionService
{
    protected AccountService $accountService;
    protected MonthlySummaryService $monthlySummaryService;

    public function __construct(AccountService $accountService, MonthlySummaryService $monthlySummaryService)
    {
        $this->accountService = $accountService;
        $this->monthlySummaryService = $monthlySummaryService;
    }

    /**
     * Create an income or expense transaction.
     */
    public function createTransaction(int $userId, array $data): CashbookTransaction
    {
        if ($data['amount'] <= 0) {
            throw new Exception("Amount must be strictly greater than zero.");
        }

        return DB::transaction(function () use ($userId, $data) {
            $transaction = CashbookTransaction::create([
                'user_id' => $userId,
                'account_id' => $data['account_id'],
                'category_id' => $data['category_id'] ?? null,
                'type' => $data['type'], // 'income' or 'expense'
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
                'transaction_date' => $data['transaction_date'],
            ]);

            // Adjust Account Balance
            $isCredit = ($data['type'] === 'income');
            $this->accountService->recalculateBalance($data['account_id'], $userId, $data['amount'], $isCredit);

            // Audit Trail
            $this->logAudit($userId, 'created', 'transaction', $transaction->id, null, $transaction->toArray());

            // Trigger MonthlySummaryService Incremental Update
            if ($data['type'] !== 'transfer') {
                $month = substr($data['transaction_date'], 0, 7); // YYYY-MM
                $this->monthlySummaryService->adjustSummary($userId, $month, $data['amount'], $data['type']);
            }

            return $transaction;
        });
    }

    /**
     * Transfer funds between two accounts securely.
     */
    public function transferBetweenAccounts(int $userId, array $data): array
    {
        if ($data['amount'] <= 0) {
            throw new Exception("Transfer amount must be strictly greater than zero.");
        }

        return DB::transaction(function () use ($userId, $data) {
            // 1. Create Outgoing (Expense) Transaction
            $outTx = CashbookTransaction::create([
                'user_id' => $userId,
                'account_id' => $data['from_account_id'],
                'category_id' => null, // Transfers don't strictly need a category
                'type' => 'transfer',
                'amount' => $data['amount'],
                'note' => $data['note'] ?? 'Transfer out',
                'transaction_date' => $data['transaction_date'],
            ]);

            // 2. Create Incoming (Income) Transaction
            $inTx = CashbookTransaction::create([
                'user_id' => $userId,
                'account_id' => $data['to_account_id'],
                'category_id' => null,
                'type' => 'transfer',
                'amount' => $data['amount'],
                'note' => $data['note'] ?? 'Transfer in',
                'transaction_date' => $data['transaction_date'],
                'reference_id' => $outTx->id,
            ]);

            // Link them together
            $outTx->update(['reference_id' => $inTx->id]);

            // 3. Adjust Balances
            $this->accountService->recalculateBalance($data['from_account_id'], $userId, $data['amount'], false);
            $this->accountService->recalculateBalance($data['to_account_id'], $userId, $data['amount'], true);

            // 4. Audit Trail
            $this->logAudit($userId, 'created_transfer', 'transaction_pair', $outTx->id, null, [
                'out' => $outTx->toArray(),
                'in' => $inTx->toArray()
            ]);

            return ['out' => $outTx, 'in' => $inTx];
        });
    }

    /**
     * Soft Delete Transaction. Reverts balance and adjusts monthly summary incrementally.
     */
    public function deleteTransaction(int $transactionId, int $userId): void
    {
        DB::transaction(function () use ($transactionId, $userId) {
            $transaction = CashbookTransaction::where('id', $transactionId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $oldValues = $transaction->toArray();

            // Revert balances
            if ($transaction->type === 'transfer') {
                // Revert sender
                $this->accountService->recalculateBalance($transaction->account_id, $userId, $transaction->amount, true);
                
                // If paired, revert the pair too
                if ($transaction->reference_id) {
                    $pair = CashbookTransaction::where('id', $transaction->reference_id)->where('user_id', $userId)->first();
                    if ($pair) {
                        $this->accountService->recalculateBalance($pair->account_id, $userId, $pair->amount, false);
                        $pair->delete();
                    }
                }
            } else {
                $isCredit = ($transaction->type === 'income');
                // Revert effect by doing the opposite of the original
                $this->accountService->recalculateBalance($transaction->account_id, $userId, $transaction->amount, !$isCredit);
            }

            // Perform soft delete
            $transaction->delete();

            // Audit
            $this->logAudit($userId, 'deleted', 'transaction', $transactionId, $oldValues, null);

            // Trigger MonthlySummaryService Incremental Revert
            if ($transaction->type !== 'transfer') {
                $month = substr($transaction->transaction_date->format('Y-m'), 0, 7); // Ensure YYYY-MM
                $this->monthlySummaryService->adjustSummary($userId, $month, -$transaction->amount, $transaction->type);
            }
        });
    }

    private function logAudit(int $userId, string $action, string $entity, int $entityId, ?array $oldValues, ?array $newValues): void
    {
        CashbookAuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
