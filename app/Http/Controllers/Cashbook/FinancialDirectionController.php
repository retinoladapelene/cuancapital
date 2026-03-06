<?php

namespace App\Http\Controllers\Cashbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Cashbook\ProjectionService;
use App\Services\Cashbook\BehaviorEngineService;
use App\Services\Cashbook\ProjectionAdjustmentService;

class FinancialDirectionController extends Controller
{
    protected ProjectionService $projectionService;
    protected BehaviorEngineService $behaviorEngine;
    protected ProjectionAdjustmentService $projectionAdjustment;

    public function __construct(
        ProjectionService $projectionService,
        BehaviorEngineService $behaviorEngine,
        ProjectionAdjustmentService $projectionAdjustment
    ) {
        $this->projectionService = $projectionService;
        $this->behaviorEngine = $behaviorEngine;
        $this->projectionAdjustment = $projectionAdjustment;
    }

    /**
     * GET /api/cashbook/financial-direction
     */
    public function getDirection(Request $request)
    {
        $userId = $request->user()->id;

        // 1. Get Core Baseline
        $baseline = $this->projectionService->getProjection($userId);
        $totalCash = $baseline['total_cash_now'];
        $netCashflow = $baseline['avg_net_cashflow'];
        
        // Direction indicator
        $direction = 'Stable';
        if ($netCashflow > 0) $direction = 'Positive';
        elseif ($netCashflow < 0) $direction = 'Negative';

        // 2. Run Behavior Engine
        $behaviorData = $this->behaviorEngine->analyzeBehavior($userId);

        // 3. Fused Simulation
        // Passing the behavior results through the adjustment layer
        $adjustedProjection = $this->projectionAdjustment->simulateAdjustedProjection($userId, $behaviorData);

        // Calculate potential gain
        $simulatedRunway = $adjustedProjection['adjusted_runway'];
        $baselineRunway = $adjustedProjection['baseline_runway'];
        $potentialRunwayGain = max(0, $simulatedRunway - $baselineRunway);

        // Construct Reflection Prompts dynamically
        $reflectionPrompt = $this->generateReflectionPrompt($behaviorData['opportunity_flag']);

        return response()->json([
            'state' => $baseline['risk_level'],
            'direction' => $direction,
            'baseline_projection' => [
                'balance_now' => $totalCash,
                'runway_months' => $baselineRunway,
                'projected_balance_90d' => max(0, $totalCash + ($netCashflow * 3))
            ],
            'adjusted_projection' => [
                'projected_balance_90d' => $adjustedProjection['simulated_balance_90d'],
                'runway_months' => $simulatedRunway,
                'potential_runway_gain' => $potentialRunwayGain
            ],
            'primary_insight' => $behaviorData['primary_insight'] ?? '',
            'projection_impact' => $adjustedProjection['projection_impact_text'] ?? '',
            'reflection_prompt' => $reflectionPrompt
        ]);
    }

    private function generateReflectionPrompt(?string $flag): string
    {
        if ($flag === 'bocor_reduction') {
            return "Apakah ada satu area pengeluaran kecil yang bisa mulai dirapikan dalam 30 hari ke depan?";
        }
        
        if ($flag === 'start_saving') {
            return "Apakah memungkinkan untuk menyisihkan sedikit dana ke tabungan di awal bulan depan?";
        }

        return "Apa rutinitas baik yang bisa Anda pertahankan untuk bulan depan?";
    }
}
