<?php

namespace App\Services\Strategic\Modules;

use App\DTO\StrategicInput;

class RiskModule
{
    /**
     * Calculates Risk Score (Exposure), 0–100 (higher = more risk).
     *
     * In diagnostic mode, incorporates:
     * - Cash coverage risk (actual cash vs monthly burn)
     * - Ads dependency risk (adSpend / actualRevenue)
     * - Margin thinness risk
     */
    public function calculate(StrategicInput $input): int
    {
        // ── Base structural risk (intent-driven) ─────────────────────────
        $structuralRisk = match($input->riskIntent) {
            'stable_income'    => -10,
            'scale_fast'       =>  20,
            'market_dominance' =>  40,
            default            =>   0,
        };

        // ── Experience reducer ───────────────────────────────────────────
        $experienceReducer = $input->experienceLevel * 10;

        // ── Capital / target ratio risk (planning mode baseline) ─────────
        $ratio       = $input->capital > 0 ? ($input->targetRevenue / $input->capital) : 100;
        $capitalRisk = min(30, $ratio * 3);

        $rawScore = 50 + $structuralRisk + $capitalRisk - $experienceReducer;

        // ── Diagnostic additions ──────────────────────────────────────────
        if ($input->hasDiagnosticData()) {
            // Cash coverage risk: below 2 months = +20, below 1 month = +30
            if ($input->actualExpenses > 0 && $input->cashBalance >= 0) {
                $coverageMonths = $input->cashBalance / $input->actualExpenses;
                if ($coverageMonths < 1)      $rawScore += 30;
                elseif ($coverageMonths < 2)  $rawScore += 15;
            }

            // Ads dependency risk: adSpend > 30% of revenue = +15
            if ($input->actualRevenue > 0 && $input->adSpend > 0) {
                $adRatio = $input->adSpend / $input->actualRevenue;
                if ($adRatio > 0.5)      $rawScore += 20;
                elseif ($adRatio > 0.3)  $rawScore += 10;
            }

            // Negative profit = operating at loss = +25
            if ($input->actualRevenue > 0 && $input->actualProfit() < 0) {
                $rawScore += 25;
            }

            // Low retention = higher churn risk = +10
            if ($input->repeatRate > 0 && $input->repeatRate < 20) {
                $rawScore += 10;
            }
        }

        return (int) max(10, min(100, $rawScore));
    }
}
