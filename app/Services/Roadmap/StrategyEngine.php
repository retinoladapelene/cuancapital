<?php

namespace App\Services\Roadmap;

class StrategyEngine
{
    /**
     * Determine High-Level Strategy based on Context + Diagnosis.
     * 
     * @param array $context (Stage, Budget, Risk, etc.)
     * @param array $diagnosis (Primary Problem, Severity)
     * @return array Strategy Object
     */
    public function determineStrategy(array $context, array $diagnosis): array
    {
        $problem = $diagnosis['primary_problem'];
        $stage = $context['stage'] ?? 'startup';
        $budget = $context['budget_level'] ?? 'low';
        
        $primaryStrategy = 'general_growth';
        $secondaryStrategy = 'efficiency';

        // 1. Map Problem -> Strategy
        switch ($problem) {
            case 'traffic':
                if ($budget === 'low') {
                    $primaryStrategy = 'organic_growth'; // Low budget -> Organic
                } else {
                    $primaryStrategy = 'paid_acquisition'; // High budget -> Ads
                }
                break;
            
            case 'conversion':
                $primaryStrategy = 'conversion_optimization';
                $secondaryStrategy = 'offer_refinement';
                break;

            case 'margin':
                $primaryStrategy = 'pricing_strategy';
                $secondaryStrategy = 'cost_reduction';
                // Use 'finance' as focus area for margin issues
                $problem = 'finance'; 
                break;
            
            default:
                if ($stage === 'scaling') {
                    $primaryStrategy = 'market_expansion';
                    $secondaryStrategy = 'operational_efficiency';
                    $problem = 'operations'; // Usually scaling bottlenecks are operations/finance
                } else if ($stage === 'growth') {
                    $primaryStrategy = 'profit_maximization';
                    $secondaryStrategy = 'retention';
                    $problem = 'finance'; // Growth often hits cash flow limits
                } else {
                    $primaryStrategy = 'validation';
                    $problem = 'conversion'; // Default validation is about finding product-market fit
                }
                break;
        }

        // 2. Adjust for Context
        if ($context['risk_tolerance'] === 'low' && $primaryStrategy === 'paid_acquisition') {
            $primaryStrategy = 'conservative_scaling'; // Fallback for risk-averse
        }

        return [
            'primary_strategy' => $primaryStrategy,
            'secondary_strategy' => $secondaryStrategy,
            'focus_area' => $problem
        ];
    }
}
