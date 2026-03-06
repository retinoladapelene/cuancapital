<?php

namespace App\Services\Strategic\Modules;

use App\DTO\StrategicInput;

class GoalValidationModule
{
    /**
     * Calculates Feasibility Score.
     *
     * Diagnostic mode: uses actual revenue + expenses for real burn/runway.
     * Planning mode:   estimates from target revenue + gross margin (original logic).
     */
    public function calculate(StrategicInput $input): array
    {
        if ($input->hasDiagnosticData() && $input->actualExpenses > 0) {
            // ── DIAGNOSTIC MODE — use real numbers ───────────────────────
            $monthlyBurn  = $input->actualExpenses;
            $runwayMonths = $input->cashBalance > 0
                ? $input->cashBalance / $monthlyBurn
                : ($input->capital > 0 ? $input->capital / $monthlyBurn : 0);

            // Feasibility: 6+ months runway = 100, 0 months = 0
            $rawScore   = ($runwayMonths / 6) * 100;
            $finalScore = (int) max(5, min(100, $rawScore));

            return [
                'score'         => $finalScore,
                'runway_months' => round($runwayMonths, 1),
                'monthly_burn'  => $monthlyBurn,
            ];
        }

        // ── PLANNING MODE — original estimation logic ────────────────────
        $estimatedCOGS = $input->targetRevenue * (1 - $input->grossMargin);
        $estimatedOpEx = $input->targetRevenue * 0.15;
        $monthlyBurn   = $estimatedCOGS + $estimatedOpEx;

        if ($monthlyBurn <= 0) {
            return ['score' => 100, 'runway_months' => 999, 'monthly_burn' => 0];
        }

        $runwayMonths = $input->capital / $monthlyBurn;
        $rawScore     = ($runwayMonths / 6) * 100;
        $finalScore   = (int) max(10, min(100, $rawScore));

        return [
            'score'         => $finalScore,
            'runway_months' => round($runwayMonths, 1),
            'monthly_burn'  => $monthlyBurn,
        ];
    }
}
