<?php

namespace App\DTO;

class StrategicResult
{
    public function __construct(
        public readonly StrategicAnalysisAggregate $metrics,
        public readonly string  $strategyLabel,
        public readonly string  $strategyDescription,
        public readonly array   $recommendations,       // Now: [{label, text, severity}]
        public readonly array   $inputSummary,
        public readonly ?string $scenarioId    = null,
        public readonly array   $projections   = [],    // V2: month-by-month projections

        // ── V3: Diagnostic fields ──────────────────────────────────────
        public readonly array   $diagnoses     = [],    // [{code, label, severity, description}]
        public readonly array   $gapAnalysis   = [],    // {revenueGap, revenuePct, profitGap}
        public readonly array   $cashHealth    = [],    // {score, coverageMonths, level}
        public readonly bool    $isDiagnostic  = false, // true when actual data was provided
        public readonly string  $problemSummary = '',
    ) {}

    public function toArray(): array
    {
        return [
            'scenario_id'     => $this->scenarioId,
            'metrics'         => $this->metrics->toArray(),
            'strategy'        => [
                'label'       => $this->strategyLabel,
                'description' => $this->strategyDescription,
            ],
            'recommendations' => $this->recommendations,
            'input'           => $this->inputSummary,
            'projections'     => $this->projections,
            // V3 Diagnostic
            'diagnoses'       => $this->diagnoses,
            'gap_analysis'    => $this->gapAnalysis,
            'cash_health'     => $this->cashHealth,
            'is_diagnostic'   => $this->isDiagnostic,
            'problem_summary' => $this->problemSummary,
        ];
    }
}
