<?php

namespace App\Services\Strategic\Modules;

use App\DTO\StrategicInput;

class ProfitSimulationModule
{
    /**
     * Calculates Profit Score based on ROI Potential.
     *
     * Diagnostic mode: uses actual revenue - expenses as real profit.
     * Planning mode:   estimates from target revenue × gross margin.
     */
    public function calculate(StrategicInput $input): array
    {
        if ($input->hasDiagnosticData() && $input->actualRevenue > 0) {
            // ── DIAGNOSTIC MODE ──────────────────────────────────────────
            $estimatedProfit = $input->actualProfit();

            // Capital for ROI denominator: prefer cash balance, fall back to capital field
            $capitalBase = max($input->capital, $input->cashBalance ?: $input->capital);
            if ($capitalBase <= 0) {
                return ['score' => 50, 'roi_multiplier' => 0, 'estimated_profit' => $estimatedProfit];
            }

            $roiMultiplier = $estimatedProfit / $capitalBase;
            $rawScore      = $roiMultiplier * 200;
            $finalScore    = (int) max(0, min(100, $rawScore));

            return [
                'score'            => $finalScore,
                'roi_multiplier'   => round($roiMultiplier, 2),
                'estimated_profit' => $estimatedProfit,
            ];
        }

        // ── PLANNING MODE — original logic ───────────────────────────────
        $estimatedOpEx   = $input->targetRevenue * 0.15;
        $estimatedProfit = ($input->targetRevenue * $input->grossMargin) - $estimatedOpEx;

        if ($input->capital <= 0) {
            return ['score' => 100, 'roi_multiplier' => 999, 'estimated_profit' => $estimatedProfit];
        }

        $roiMultiplier = $estimatedProfit / $input->capital;
        $rawScore      = $roiMultiplier * 200;
        $finalScore    = (int) max(0, min(100, $rawScore));

        return [
            'score'            => $finalScore,
            'roi_multiplier'   => round($roiMultiplier, 2),
            'estimated_profit' => $estimatedProfit,
        ];
    }
}
