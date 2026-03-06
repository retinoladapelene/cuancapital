<?php

namespace App\Services\Strategic\Modules;

use App\DTO\StrategicAnalysisAggregate;
use App\DTO\StrategicInput;

class StrategyClassifierModule
{
    // Constants for Strategy Labels
    public const STRATEGY_UNREALISTIC = 'Unrealistic Strategy';
    public const STRATEGY_HIGH_EXPOSURE = 'High Exposure Strategy';
    public const STRATEGY_CAPITAL_STRAINED = 'Capital Strained Strategy';
    public const STRATEGY_AGGRESSIVE = 'Aggressive Growth Blueprint';
    public const STRATEGY_STABLE = 'Stable Income Blueprint';
    public const STRATEGY_BALANCED = 'Balanced Strategy';
    public const STRATEGY_DOMINATION = 'Market Domination Blueprint'; // Extra for market_dominance intent

    /**
     * Determines the Strategy Label based on the Priority Rule Tree.
     * Logic is strictly deterministic and order-dependent (First Match Wins).
     */
    public function classify(StrategicAnalysisAggregate $metrics, StrategicInput $input): string
    {
        // 1. ⛔ UNREALISTIC
        // Feasibility < 30: Stop analysis, warn user.
        if ($metrics->feasibilityScore < 30) {
            return self::STRATEGY_UNREALISTIC;
        }

        // 2. ⚠️ HIGH EXPOSURE
        // RiskScore > 80: High burn rate & complexity.
        if ($metrics->riskScore > 80) {
            return self::STRATEGY_HIGH_EXPOSURE;
        }

        // 3. 📉 CAPITAL STRAINED
        // Efficiency < 30 AND Profit < 40: Margin too thin or capital inefficient.
        if ($metrics->efficiencyScore < 30 && $metrics->profitScore < 40) {
            return self::STRATEGY_CAPITAL_STRAINED;
        }

        // 4. 🚀 AGGRESSIVE SCALING
        // Intent = scale_fast AND Feasibility > 60 AND Risk > 40
        if ($input->riskIntent === 'scale_fast' && $metrics->feasibilityScore > 60 && $metrics->riskScore > 40) {
            return self::STRATEGY_AGGRESSIVE;
        }

        // Extra: Market Dominance Handling (Can be similar to Aggressive but maybe stricter?)
        // reusing Aggressive logic or creating specific if needed. 
        // For now, let's map 'market_dominance' to Aggressive if it fits criteria, or fall through.
        if ($input->riskIntent === 'market_dominance' && $metrics->feasibilityScore > 50) {
             return self::STRATEGY_DOMINATION;
        }

        // 5. 🛡️ STABLE GROWTH
        // Intent = stable_income AND Risk < 40 AND Profit > 50
        if ($input->riskIntent === 'stable_income' && $metrics->riskScore < 45 && $metrics->profitScore > 50) {
            return self::STRATEGY_STABLE;
        }

        // 6. ⚖️ BALANCED (Default Fallback)
        return self::STRATEGY_BALANCED;
    }
}
