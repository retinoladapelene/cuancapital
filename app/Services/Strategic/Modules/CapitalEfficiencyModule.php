<?php

namespace App\Services\Strategic\Modules;

use App\DTO\StrategicInput;

class CapitalEfficiencyModule
{
    /**
     * Calculates Capital Efficiency Score (0–100).
     *
     * Diagnostic mode: uses actual ROAS (Revenue / AdSpend) and LTV signals.
     * Planning mode:   uses target revenue / capital ratio.
     */
    public function calculate(StrategicInput $input): int
    {
        if ($input->hasDiagnosticData() && $input->actualRevenue > 0) {
            // ── DIAGNOSTIC MODE ──────────────────────────────────────────
            $score = 50; // Neutral baseline

            // ROAS efficiency: Revenue generated per IDR of ad spend
            if ($input->adSpend > 0) {
                $roas = $input->actualRevenue / $input->adSpend;
                // ROAS 3x = neutral, 5x+ = good, <2x = bad
                if ($roas >= 5)     $score += 25;
                elseif ($roas >= 3) $score += 10;
                elseif ($roas >= 2) $score -= 5;
                else                $score -= 20;
            }

            // Retention efficiency: high repeat rate = capital-efficient growth
            if ($input->repeatRate > 0) {
                if ($input->repeatRate >= 40)     $score += 20;
                elseif ($input->repeatRate >= 25) $score += 10;
                elseif ($input->repeatRate < 15)  $score -= 10;
            }

            // Profit yield: % of revenue becoming profit
            $profitPct = $input->actualRevenue > 0
                ? ($input->actualProfit() / $input->actualRevenue) * 100
                : 0;
            if ($profitPct >= 30)    $score += 15;
            elseif ($profitPct >= 15) $score += 5;
            elseif ($profitPct < 0)   $score -= 20;

            return (int) max(5, min(100, $score));
        }

        // ── PLANNING MODE — original ratio-based logic ───────────────────
        if ($input->capital <= 0) return 80;

        $ratio = $input->targetRevenue / $input->capital;
        $rawScore = min(999, $ratio * 100);

        return (int) max(10, min(100, $rawScore));
    }
}
