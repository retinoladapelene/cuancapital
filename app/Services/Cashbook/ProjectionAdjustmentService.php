<?php

namespace App\Services\Cashbook;

use App\Services\Cashbook\ProjectionService;

class ProjectionAdjustmentService
{
    protected ProjectionService $projectionService;

    public function __construct(ProjectionService $projectionService)
    {
        $this->projectionService = $projectionService;
    }

    /**
     * Fuses baseline projection with behavioral modifiers to simulate a hypothetical 90-day future.
     * 
     * @param int $userId
     * @param array $behaviorData from BehaviorEngineService
     * @return array
     */
    public function simulateAdjustedProjection(int $userId, array $behaviorData): array
    {
        $baseline = $this->projectionService->getProjection($userId);
        
        $runwayMonths = $baseline['runway_months'];
        $adjustmentFactor = $behaviorData['behavioral_adjustment_factor'] ?? 0.0;
        
        // Phase 7: Risk Sensitivity Mapping
        // If runway < 2, the user is in highly sensitive state. A small adjustment is magnified.
        // If runway > 6, the user is very stable. Adjustments have conservative impacts.
        $sensitivityMultiplier = 1.0;
        if ($runwayMonths < 2) {
            $sensitivityMultiplier = 1.5; // Highly sensitive to changes
        } elseif ($runwayMonths > 6) {
            $sensitivityMultiplier = 0.8; // Conservative
        }

        $effectiveAdjustment = $adjustmentFactor * $sensitivityMultiplier;

        // Simulate new expense
        $simulatedExpense = $baseline['avg_expense'] * (1 - $effectiveAdjustment);
        $simulatedExpense = max(1, $simulatedExpense); // Prevent div by 0

        // Simulate new runway
        $adjustedRunway = $baseline['total_cash_now'] / $simulatedExpense;
        $adjustedRunway = min(999, floor($adjustedRunway * 10) / 10); // Round to 1 decimal

        // Calculate improvement
        $gainedRunway = max(0, $adjustedRunway - $runwayMonths);

        // Simulate Balance in 90 days (3 months)
        $simulatedNetCashflow = $baseline['avg_income'] - $simulatedExpense;
        $simulatedBalance90d = max(0, $baseline['total_cash_now'] + ($simulatedNetCashflow * 3));
        
        return [
            'baseline_runway' => $runwayMonths,
            'adjusted_runway' => $adjustedRunway,
            'simulated_expense' => $simulatedExpense,
            'simulated_balance_90d' => $simulatedBalance90d,
            'runway_gain' => $gainedRunway,
            'projection_impact_text' => $this->generateImpactText($behaviorData['opportunity_flag'], $gainedRunway, $behaviorData['current_bocor_ratio'] ?? 0)
        ];
    }

    private function generateImpactText(?string $flag, float $gainedRunway, float $bocorRatio): string
    {
        if ($flag === 'bocor_reduction') {
            return "Jika pengeluaran non-prioritas diturunkan 10%, *runway* Anda dapat bertambah ±{$gainedRunway} bulan dalam 90 hari ke depan.";
        }
        
        if ($flag === 'start_saving') {
            return "Mulai menyisihkan minimal 10% pendapatan bulan ini akan mulai membangun jaring pengaman awal Anda.";
        }

        if ($gainedRunway > 0) {
            return "Mempertahankan rutinitas positif ini akan menambah ketahanan finansial Anda sebesar {$gainedRunway} bulan.";
        }

        return "Terus pertahankan konsistensi arus kas Anda bulan ini.";
    }
}
