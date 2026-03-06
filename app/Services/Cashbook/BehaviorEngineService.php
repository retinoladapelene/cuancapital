<?php

namespace App\Services\Cashbook;

use App\Models\CashbookMonthlySummary;

class BehaviorEngineService
{
    /**
     * Reads user behavior and generates modifiers based on financial actions.
     * 
     * @param int $userId
     * @return array Contains behavioral_adjustment_factor, risk_modifier, and opportunity_flag.
     */
    public function analyzeBehavior(int $userId): array
    {
        // Get the last 2 months to observe behavior changes
        $summaries = CashbookMonthlySummary::where('user_id', $userId)
            ->orderBy('month', 'desc')
            ->limit(2)
            ->get();

        $behavioralAdjustmentFactor = 0.0;
        $riskModifier = 1.0;
        $opportunityFlag = null;

        if ($summaries->isEmpty()) {
            return [
                'behavioral_adjustment_factor' => $behavioralAdjustmentFactor,
                'risk_modifier' => $riskModifier,
                'opportunity_flag' => 'Belum cukup data perilaku bulanan untuk dianalisis.',
                'primary_insight' => 'Data Anda masih baru. Lanjutkan pencatatan untuk melihat insight.'
            ];
        }

        $currentMonth = $summaries->first();
        $previousMonth = $summaries->count() > 1 ? $summaries->last() : null;

        // 1. Analyze 'Bocor' (Leakage) Behavior
        if ($currentMonth->bocor_ratio > 30) {
            $behavioralAdjustmentFactor -= 0.05; // -5% adjustment mapping
            $riskModifier += 0.15; // Increased risk
            $opportunityFlag = 'bocor_reduction';
        } elseif ($previousMonth && $currentMonth->bocor_ratio < $previousMonth->bocor_ratio) {
            $behavioralAdjustmentFactor += 0.02; // Positive reinforcement for reducing leaks
            $riskModifier -= 0.05;
        }

        // 2. Analyze Saving Behavior
        if ($previousMonth && $currentMonth->saving_rate > $previousMonth->saving_rate) {
            $behavioralAdjustmentFactor += 0.03; // +3% adjustment mapping
            $riskModifier -= 0.10;
        } elseif ($currentMonth->saving_rate == 0) {
            $behavioralAdjustmentFactor -= 0.02;
            $riskModifier += 0.10;
            if (!$opportunityFlag) {
                $opportunityFlag = 'start_saving';
            }
        }

        // Determine Insight Text
        $primaryInsight = $this->generateInsight($currentMonth, $previousMonth, $opportunityFlag);

        return [
            'behavioral_adjustment_factor' => $behavioralAdjustmentFactor,
            'risk_modifier' => max(0.5, $riskModifier), // Cap minimum risk modifier
            'opportunity_flag' => $opportunityFlag,
            'primary_insight' => $primaryInsight,
            'current_bocor_ratio' => (float) $currentMonth->bocor_ratio
        ];
    }

    private function generateInsight($current, $previous, $flag): string
    {
        if ($flag === 'bocor_reduction') {
            return "Pengeluaran non-prioritas (bocor) Anda berada di atas 30% bulan ini.";
        }
        
        if ($flag === 'start_saving') {
            return "Bulan ini Anda belum mengalokasikan dana ke tabungan atau instrumen *growth*.";
        }

        if ($previous && $current->saving_rate > $previous->saving_rate) {
            return "Kabar baik: persentase tabungan Anda bulan ini meningkat dibandingkan bulan lalu.";
        }

        if ($previous && $current->bocor_ratio < $previous->bocor_ratio) {
            return "Anda berhasil menekan angka pengeluaran tidak terencana dengan baik bulan ini.";
        }

        return "Cashflow Anda terlihat berjalan seperti biasa bulan ini.";
    }
}
