<?php

namespace App\Listeners;

use App\Events\StrategicAnalysisEvaluated;
use App\Models\StrategicAnalysis;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogStrategicAnalysis implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StrategicAnalysisEvaluated $event): void
    {
        try {
            // Use forceCreate to bypass guarded 'id' since we are setting it manually
            StrategicAnalysis::forceCreate([
                'id' => $event->scenarioId, // Use pre-generated UUID
                'user_id' => $event->userId,
                'session_id' => $event->sessionId,
                'business_type' => $event->input->businessType,
                'risk_intent' => $event->input->riskIntent,
                
                // Optimized Columns
                'feasibility_score' => $event->result->metrics->feasibilityScore,
                'risk_score' => $event->result->metrics->riskScore,
                'profit_score' => $event->result->metrics->profitScore,
                'efficiency_score' => $event->result->metrics->efficiencyScore,
                
                'strategy_label' => $event->result->strategyLabel,
                'strategy_version' => '1.0',
                
                // Full Snapshots
                'input_snapshot' => (array) $event->input,
                'metrics_snapshot' => $event->result->metrics->toArray(),
            ]);
        } catch (\Throwable $e) {
            // Non-blocking failure logging
            Log::error('Failed to log strategic analysis: ' . $e->getMessage());
        }
    }
}
