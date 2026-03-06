<?php

namespace App\Services\Cashbook;

use App\Models\CashbookDisciplineLog;
use App\Models\CashbookMonthlySummary;
use App\Models\CashbookUserSettings;
use App\Models\CashbookAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DisciplineService
{
    /**
     * Record a user's daily input interaction to track consistency.
     */
    public function recordDailyInput(int $userId): CashbookDisciplineLog
    {
        $today = Carbon::today()->toDateString();
        
        $log = CashbookDisciplineLog::firstOrCreate(
            ['user_id' => $userId, 'date' => $today],
            ['did_input_today' => true, 'transaction_count' => 0, 'is_skipped' => false]
        );

        $log->transaction_count += 1;
        $log->last_input_time = now();
        $log->save();

        return $log;
    }

    /**
     * Compute and statically snapshot the Financial Health Score.
     * Formula: (Saving Rate Score × 35%) + (Expense Stability Score × 25%) + (Bocor Control Score × 20%) + (Runway Score × 20%)
     */
    public function calculateAndSnapshotHealthScore(int $userId, string $month): int
    {
        $summary = CashbookMonthlySummary::where('user_id', $userId)->where('month', $month)->first();
        if (!$summary) return 0;

        $settings = CashbookUserSettings::where('user_id', $userId)->first();
        $targetSavingRate = $settings ? $settings->saving_rate_target : 20.00;

        // 1. Saving Rate Score (35%)
        $savingRatePercentage = $summary->saving_rate;
        if ($savingRatePercentage >= $targetSavingRate) {
            $savingScore = 100;
        } elseif ($savingRatePercentage > 0) {
            $savingScore = ($savingRatePercentage / $targetSavingRate) * 100;
        } else {
            $savingScore = 0;
        }

        // 2. Expense Stability Score (25%) - Simplification: Checks if net cashflow is positive
        $stabilityScore = $summary->net_cashflow >= 0 ? 100 : max(0, 100 - (abs($summary->net_cashflow) / max(1, $summary->total_income) * 100));

        // 3. Bocor Control Score (20%) - Perfect if 0%, scales down if high
        $bocorScore = max(0, 100 - ($summary->bocor_ratio * 2)); // E.g., 50% bocor = 0 score. 10% bocor = 80 score.

        // 4. Runway Score (20%)
        // Runway = Total Cash / Avg Monthly Expense. Let's aim for 6 months (100 score).
        $totalCash = CashbookAccount::where('user_id', $userId)->where('is_active', true)->sum('balance_cached');
        $avgExpense = $summary->total_expense > 0 ? $summary->total_expense : 1; // Prevent div by 0
        $runwayMonths = $totalCash / $avgExpense;
        $runwayScore = min(100, ($runwayMonths / 6) * 100);

        // Calculate Weighted Sum
        $finalScore = ($savingScore * 0.35) + ($stabilityScore * 0.25) + ($bocorScore * 0.20) + ($runwayScore * 0.20);
        $clampedScore = (int) round(min(100, max(0, $finalScore)));

        // Snapshot to Database (Immutable unless triggered again by user action in that specific month)
        $summary->health_score = $clampedScore;
        $summary->save();

        return $clampedScore;
    }
}
