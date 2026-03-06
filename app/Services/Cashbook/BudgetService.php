<?php

namespace App\Services\Cashbook;

use App\Models\CashbookBudget;
use App\Models\CashbookTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class BudgetService
{
    /**
     * Set a monthly budget limit for a specific category.
     */
    public function setMonthlyBudget(int $userId, int $categoryId, string $month, float $limitAmount): CashbookBudget
    {
        if ($limitAmount < 0) {
            throw new Exception("Budget limit cannot be negative.");
        }

        return CashbookBudget::updateOrCreate(
            ['user_id' => $userId, 'category_id' => $categoryId, 'month' => $month],
            ['limit_amount' => $limitAmount]
        );
    }

    /**
     * Get real-time budget status (limit vs usage).
     */
    public function getBudgetStatus(int $userId, string $month): array
    {
        $budgets = CashbookBudget::with('category')->where('user_id', $userId)->where('month', $month)->get();

        $status = [];
        foreach ($budgets as $budget) {
            $usage = CashbookTransaction::where('user_id', $userId)
                ->where('category_id', $budget->category_id)
                ->where('type', 'expense')
                ->where('transaction_date', 'like', $month . '%')
                ->sum('amount');

            $status[] = [
                'category_id' => $budget->category_id,
                'category_name' => $budget->category->name,
                'limit' => $budget->limit_amount,
                'usage' => $usage,
                'remaining' => $budget->limit_amount - $usage,
                'is_overflow' => $usage > $budget->limit_amount,
            ];
        }

        return $status;
    }
}
