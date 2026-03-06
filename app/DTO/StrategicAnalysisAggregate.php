<?php

namespace App\DTO;

/**
 * Immutable object holding normalized metrics from all modules.
 * This is the raw data used by the StrategyClassifier.
 */
class StrategicAnalysisAggregate
{
    public function __construct(
        public readonly int $feasibilityScore, // 0-100
        public readonly int $profitScore, // 0-100
        public readonly int $riskScore, // 0-100
        public readonly int $efficiencyScore, // 0-100
        public readonly float $estimatedMonthlyBurn, // Calculated value for reference
        public readonly float $runwayMonths, // Calculated value for reference
        public readonly float $roiMultiplier, // Calculated value for reference
        // V2 Enhanced fields
        public readonly float $estimatedProfit = 0, // Monthly estimated profit
        public readonly int $breakEvenMonth = 0, // Month where cumulative profit > 0
        public readonly float $netMarginPct = 0, // Net margin percentage
        public readonly float $capitalEfficiencyPct = 0, // How % of capital generates revenue
    ) {}

    public function toArray(): array
    {
        return [
            'feasibilityScore' => $this->feasibilityScore,
            'profitScore' => $this->profitScore,
            'riskScore' => $this->riskScore,
            'efficiencyScore' => $this->efficiencyScore,
            'estimatedMonthlyBurn' => $this->estimatedMonthlyBurn,
            'runwayMonths' => $this->runwayMonths,
            'roiMultiplier' => $this->roiMultiplier,
            // V2 Enhanced
            'estimatedProfit' => $this->estimatedProfit,
            'breakEvenMonth' => $this->breakEvenMonth,
            'netMarginPct' => round($this->netMarginPct, 1),
            'capitalEfficiencyPct' => round($this->capitalEfficiencyPct, 1),
        ];
    }
}
