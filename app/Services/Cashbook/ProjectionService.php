<?php

namespace App\Services\Cashbook;

use App\Models\CashbookMonthlySummary;
use App\Models\CashbookAccount;
use Carbon\Carbon;

class ProjectionService
{
    /**
     * Compute 90-Day projection with fallback logic for users < 3 months old.
     */
    public function getProjection(int $userId): array
    {
        // 1. Get up to the last 3 months of data
        $summaries = CashbookMonthlySummary::where('user_id', $userId)
            ->orderBy('month', 'desc')
            ->limit(3)
            ->get();

        $count = $summaries->count();
        $totalCash = CashbookAccount::where('user_id', $userId)->where('is_active', true)->sum('balance_cached');

        if ($count === 0) {
            return $this->fallbackProjection($totalCash);
        }

        // Calculate Average Monthly Expense and Income
        $avgIncome = $summaries->avg('total_income');
        $avgExpense = $summaries->avg('total_expense');
        $avgNetCashflow = $summaries->avg('net_cashflow');

        // Fallback for extreme new users where average income/expense is 0 (just registered, logged one 0 amount thing)
        if ($avgExpense == 0 && $avgIncome == 0) {
            return $this->fallbackProjection($totalCash);
        }

        // 2. Runway
        $runwayMonths = $avgExpense > 0 ? floor($totalCash / $avgExpense) : 999; 

        // 3. Risk Classification
        $riskLevel = $this->determineRisk($runwayMonths, $avgNetCashflow);

        // 4. Projection Array for Next 3 Months
        $projections = [];
        $currentCash = $totalCash;
        for ($i = 1; $i <= 3; $i++) {
            $currentCash += $avgNetCashflow;
            $projections[] = [
                'month' => Carbon::now()->addMonths($i)->format('Y-m'),
                'projected_balance' => max(0, $currentCash),
            ];
        }

        return [
            'total_cash_now' => $totalCash,
            'avg_income' => $avgIncome,
            'avg_expense' => $avgExpense,
            'avg_net_cashflow' => $avgNetCashflow,
            'runway_months' => $runwayMonths,
            'risk_level' => $riskLevel,
            'confidence' => $count >= 3 ? 'High (3+ months data)' : 'Low (' . $count . ' months data)',
            'projections' => $projections,
        ];
    }

    private function fallbackProjection(float $totalCash): array
    {
        return [
            'total_cash_now' => $totalCash,
            'avg_income' => 0,
            'avg_expense' => 0,
            'avg_net_cashflow' => 0,
            'runway_months' => 0,
            'risk_level' => 'Unknown (Insufficient Data)',
            'confidence' => 'None',
            'projections' => [],
        ];
    }

    private function determineRisk(float $runwayMonths, float $avgNetCashflow): string
    {
        if ($runwayMonths >= 6 && $avgNetCashflow > 0) return 'Safe (Green)';
        if ($runwayMonths >= 3 && $avgNetCashflow > 0) return 'Stable (Yellow)';
        if ($runwayMonths >= 3 && $avgNetCashflow <= 0) return 'Warning (Orange)';
        
        return 'Critical (Red)'; // < 3 months runway OR negative flow taking you down.
    }
}
