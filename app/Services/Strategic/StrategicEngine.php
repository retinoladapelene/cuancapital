<?php

namespace App\Services\Strategic;

use App\DTO\StrategicInput;
use App\DTO\StrategicResult;
use App\DTO\StrategicAnalysisAggregate;
use App\Services\Strategic\Modules\GoalValidationModule;
use App\Services\Strategic\Modules\ProfitSimulationModule;
use App\Services\Strategic\Modules\RiskModule;
use App\Services\Strategic\Modules\CapitalEfficiencyModule;
use App\Services\Strategic\Modules\StrategyClassifierModule;
use App\Services\Strategic\Modules\RecommendationEngine;
use App\Services\Strategic\Modules\DiagnosticClassifierModule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Events\StrategicAnalysisEvaluated;

/**
 * Main Orchestrator for the Strategic Decision Engine V3.
 * Coordinates input mapping, module execution, diagnostic classification, and result aggregation.
 */
class StrategicEngine
{
    public function __construct(
        private GoalValidationModule      $goalValidation,
        private ProfitSimulationModule    $profitSimulation,
        private RiskModule                $riskModule,
        private CapitalEfficiencyModule   $capitalEfficiency,
        private StrategyClassifierModule  $classifier,
        private RecommendationEngine      $recommender,
        private DiagnosticClassifierModule $diagnosticClassifier,
    ) {}

    public function evaluate(StrategicInput $input): StrategicResult
    {
        $scenarioId = (string) Str::uuid();

        // ── 1. Core Module Calculations ──────────────────────────────────
        $feasibilityData  = $this->goalValidation->calculate($input);
        $profitData       = $this->profitSimulation->calculate($input);
        $riskScore        = $this->riskModule->calculate($input);
        $efficiencyScore  = $this->capitalEfficiency->calculate($input);

        // ── 2. Extended Financial Metrics ────────────────────────────────
        $estProfit   = $profitData['estimated_profit'] ?? 0;
        $monthlyBurn = $feasibilityData['monthly_burn'] ?? 0;
        $baseRevenue = $input->hasDiagnosticData() ? $input->actualRevenue : $input->targetRevenue;

        $netMargin = $baseRevenue > 0
            ? ($estProfit / $baseRevenue) * 100
            : 0;

        $capitalEffPct = $input->capital > 0
            ? ($input->targetRevenue / $input->capital) * 100
            : 999;

        // Break-even
        $breakEvenMonth  = 0;
        $cumProfit       = 0;
        for ($m = 1; $m <= $input->timeframeMonths; $m++) {
            $cumProfit += $estProfit;
            if ($cumProfit >= $input->capital && $breakEvenMonth === 0) {
                $breakEvenMonth = $m;
            }
        }
        if ($breakEvenMonth === 0 && $estProfit > 0) {
            $breakEvenMonth = (int) ceil($input->capital / $estProfit);
        }

        // ── 3. Aggregate Scores ───────────────────────────────────────────
        $aggregate = new StrategicAnalysisAggregate(
            feasibilityScore:     $feasibilityData['score'],
            profitScore:          $profitData['score'],
            riskScore:            $riskScore,
            efficiencyScore:      $efficiencyScore,
            estimatedMonthlyBurn: $feasibilityData['monthly_burn'],
            runwayMonths:         $feasibilityData['runway_months'],
            roiMultiplier:        $profitData['roi_multiplier'],
            estimatedProfit:      round($estProfit),
            breakEvenMonth:       $breakEvenMonth,
            netMarginPct:         $netMargin,
            capitalEfficiencyPct: min(999, $capitalEffPct),
        );

        // ── 4. Strategy Classification ────────────────────────────────────
        $label       = $this->classifier->classify($aggregate, $input);
        $description = $this->recommender->getDescription($label);

        // ── 5. Diagnostic Classification (V3) ────────────────────────────
        $diagnoses      = $this->diagnosticClassifier->diagnose($input);
        $problemSummary = $this->diagnosticClassifier->buildSummary($diagnoses);
        $recommendations = $this->recommender->getRecommendationsForDiagnoses($diagnoses, $label);

        // ── 6. Gap Analysis ───────────────────────────────────────────────
        $gapAnalysis = [];
        if ($input->hasDiagnosticData() && $input->targetRevenue > 0) {
            $revGap    = $input->targetRevenue - $input->actualRevenue;
            $revGapPct = ($revGap / $input->targetRevenue) * 100;
            $gapAnalysis = [
                'actual_revenue'  => $input->actualRevenue,
                'target_revenue'  => $input->targetRevenue,
                'revenue_gap'     => $revGap,
                'revenue_gap_pct' => round($revGapPct, 1),
                'actual_profit'   => $input->actualProfit(),
                'est_profit'      => round($estProfit),
            ];
        }

        // ── 7. Cash Health ────────────────────────────────────────────────
        $cashHealth = [];
        if ($input->hasDiagnosticData() && $input->actualExpenses > 0) {
            $coverageMonths = $input->cashBalance / $input->actualExpenses;
            $cashScore = (int) min(100, ($coverageMonths / 3) * 100); // 3 months = 100
            $cashLevel  = $coverageMonths >= 3 ? 'healthy' : ($coverageMonths >= 1.5 ? 'caution' : 'critical');
            $cashHealth = [
                'score'           => $cashScore,
                'coverage_months' => round($coverageMonths, 1),
                'cash_balance'    => $input->cashBalance,
                'monthly_burn'    => $input->actualExpenses,
                'level'           => $cashLevel,
            ];
        }

        // ── 8. Month-by-Month Projections (V3: start from actual revenue) ─
        $projections = [];
        $growthRate  = match(true) {
            $input->experienceLevel >= 2.0 => 0.08,
            $input->experienceLevel >= 1.5 => 0.05,
            $input->experienceLevel >= 1.0 => 0.03,
            default                        => 0.01,
        };

        // Start from actual revenue if available, otherwise 60% of target
        $startRevenue        = $input->hasDiagnosticData() && $input->actualRevenue > 0
            ? $input->actualRevenue
            : ($input->targetRevenue * 0.6);
        $cumProfitProjection = -$input->capital;

        for ($m = 1; $m <= $input->timeframeMonths; $m++) {
            $startRevenue        = min($startRevenue * (1 + $growthRate), $input->targetRevenue * 1.3);

            // Use actual expense ratio if available
            $costRatio = $input->hasDiagnosticData() && $input->actualRevenue > 0
                ? ($input->actualExpenses / $input->actualRevenue)
                : ((1 - $input->grossMargin) + 0.15);

            $costs  = $startRevenue * min($costRatio, 0.95); // cap at 95%
            $profit = $startRevenue - $costs;
            $cumProfitProjection += $profit;

            $projections[] = [
                'month'      => $m,
                'revenue'    => round($startRevenue),
                'costs'      => round($costs),
                'profit'     => round($profit),
                'cumulative' => round($cumProfitProjection),
            ];
        }

        // ── 9. Build Result ───────────────────────────────────────────────
        $result = new StrategicResult(
            metrics:           $aggregate,
            strategyLabel:     $label,
            strategyDescription: $description,
            recommendations:   $recommendations,
            inputSummary:      $input->toArray(),
            scenarioId:        $scenarioId,
            projections:       $projections,
            diagnoses:         $diagnoses,
            gapAnalysis:       $gapAnalysis,
            cashHealth:        $cashHealth,
            isDiagnostic:      $input->hasDiagnosticData(),
            problemSummary:    $problemSummary,
        );

        // ── 10. Async Logging ─────────────────────────────────────────────
        StrategicAnalysisEvaluated::dispatch(
            $input,
            $result,
            Auth::id(),
            Session::getId(),
            $scenarioId
        );

        return $result;
    }
}
