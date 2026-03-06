<?php

namespace App\Services\Cashbook;

use App\Models\CashbookMonthlySummary;
use App\Models\CashbookTransaction;
use App\Models\CashbookCategory;
use Illuminate\Support\Facades\DB;

class MonthlySummaryService
{
    /**
     * Incrementally apply a single transaction amount to a month's summary.
     * Use this when creating or modifying transactions to avoid full table sequential reads.
     */
    public function adjustSummary(int $userId, string $month, float $amountChange, string $type): void
    {
        // Must use pessimistic lock if inside a larger transaction to avoid race condition
        $summary = CashbookMonthlySummary::firstOrCreate(
            ['user_id' => $userId, 'month' => $month],
            ['total_income' => 0, 'total_expense' => 0, 'net_cashflow' => 0]
        );

        if ($type === 'income') {
            $summary->total_income += $amountChange;
        } elseif ($type === 'expense') {
            $summary->total_expense += $amountChange;
        }

        $summary->net_cashflow = $summary->total_income - $summary->total_expense;

        // Perform complex ratio recalcs only if there's actual income
        if ($summary->total_income > 0) {
            $summary->saving_rate = ($summary->net_cashflow / $summary->total_income) * 100;
        } else {
            $summary->saving_rate = $summary->net_cashflow > 0 ? 100 : 0;
        }

        // To recalculate Bocor Ratio dynamically fast: We could do a quick sum on just the bocor for the month
        $summary->bocor_ratio = $this->calculateBocorRatio($userId, $month, $summary->total_income);

        $summary->save();
    }

    /**
     * Strict rule: When a transaction changes month, decrement old, increment new.
     */
    public function handleMonthShift(int $userId, string $oldMonth, string $newMonth, float $amount, string $type): void
    {
        // 1. Subtract from old month
        $this->adjustSummary($userId, $oldMonth, -$amount, $type);

        // 2. Add to new month
        $this->adjustSummary($userId, $newMonth, $amount, $type);
    }

    private function calculateBocorRatio(int $userId, string $month, float $totalIncome): float
    {
        if ($totalIncome <= 0) return 0;

        $bocorPillarCategories = CashbookCategory::where('user_id', $userId)
            ->where('pillar', 'bocor')
            ->pluck('id');

        if ($bocorPillarCategories->isEmpty()) return 0;

        $totalBocor = CashbookTransaction::where('user_id', $userId)
            ->whereIn('category_id', $bocorPillarCategories)
            ->where('type', 'expense')
            ->where('transaction_date', 'like', $month . '%')
            ->sum('amount');

        return min(100, max(0, ($totalBocor / $totalIncome) * 100));
    }
}
