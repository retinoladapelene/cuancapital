<?php

namespace App\DTO;

class StrategicInput
{
    public function __construct(
        // ── Original (Aspirational) Fields ──────────────────────────────────
        public readonly string $businessType,
        public readonly float  $capital,
        public readonly float  $grossMargin,       // 0.0–1.0
        public readonly float  $experienceLevel,   // 0.5, 1.0, 1.5, 2.0
        public readonly float  $targetRevenue,
        public readonly int    $timeframeMonths,   // 6, 12, 24
        public readonly string $riskIntent,        // stable_income | scale_fast | market_dominance
        public readonly string $stage    = 'running',
        public readonly string $channel  = 'marketplace',

        // ── NEW: Actual Business Data (Diagnostic Mode) ──────────────────────
        public readonly float  $actualRevenue  = 0.0,   // Revenue aktual bulan terakhir
        public readonly float  $actualExpenses = 0.0,   // Total biaya aktual bulan terakhir
        public readonly float  $cashBalance    = 0.0,   // Saldo kas saat ini
        public readonly int    $businessAge    = 0,     // Umur bisnis dalam bulan
        public readonly float  $avgOrderValue  = 0.0,   // Rata-rata nilai per transaksi
        public readonly float  $adSpend        = 0.0,   // Biaya iklan per bulan
        public readonly float  $repeatRate     = 0.0,   // % repeat buyer (0–100)

        /** @var string[] */
        public readonly array  $problemAreas   = [],    // Multi-select problem tags
    ) {}

    public function hasDiagnosticData(): bool
    {
        return $this->actualRevenue > 0 || $this->actualExpenses > 0 || $this->cashBalance > 0 || !empty($this->problemAreas);
    }

    /** Actual monthly profit — 0 if no diagnostic data */
    public function actualProfit(): float
    {
        return $this->actualRevenue - $this->actualExpenses;
    }

    /** Actual gross margin derived from real data, falls back to grossMargin field */
    public function effectiveGrossMargin(): float
    {
        if ($this->actualRevenue > 0 && $this->actualExpenses > 0) {
            $cogs = $this->actualExpenses - ($this->actualRevenue * 0.15); // strip ~OpEx
            return max(0.0, min(1.0, ($this->actualRevenue - max(0, $cogs)) / $this->actualRevenue));
        }
        return $this->grossMargin;
    }

    public static function fromArray(array $data): self
    {
        // 1. Map Capital
        $capital = $data['capital'] ?? 0;
        if (is_string($capital)) {
            $capital = match($capital) {
                'under_5'  => 3_000_000,
                '5_20'     => 12_500_000,
                '20_100'   => 60_000_000,
                'over_100' => 150_000_000,
                default    => (float) $capital,
            };
        }

        // 2. Map Gross Margin (% or decimal)
        $margin = (float) ($data['grossMargin'] ?? 0.3);
        if ($margin > 1) $margin = $margin / 100;

        // 3. Problem areas — accept comma string or array
        $problems = $data['problemAreas'] ?? [];
        if (is_string($problems)) {
            $problems = array_filter(explode(',', $problems));
        }

        return new self(
            businessType:   $data['businessType']   ?? 'general',
            capital:        (float) $capital,
            grossMargin:    (float) $margin,
            experienceLevel:(float) ($data['experienceLevel'] ?? 1.0),
            targetRevenue:  (float) ($data['targetRevenue']   ?? 0),
            timeframeMonths:(int)   ($data['timeframeMonths'] ?? 12),
            riskIntent:     $data['riskIntent']      ?? 'stable_income',
            stage:          $data['stage']           ?? 'running',
            channel:        $data['channel']         ?? 'marketplace',

            // Diagnostic fields
            actualRevenue:  (float) ($data['actualRevenue']  ?? 0),
            actualExpenses: (float) ($data['actualExpenses'] ?? 0),
            cashBalance:    (float) ($data['cashBalance']    ?? 0),
            businessAge:    (int)   ($data['businessAge']    ?? 0),
            avgOrderValue:  (float) ($data['avgOrderValue']  ?? 0),
            adSpend:        (float) ($data['adSpend']        ?? 0),
            repeatRate:     (float) ($data['repeatRate']     ?? 0),
            problemAreas:   array_values(array_filter((array) $problems)),
        );
    }

    public function toArray(): array
    {
        return [
            'businessType'   => $this->businessType,
            'capital'        => $this->capital,
            'grossMargin'    => $this->grossMargin,
            'experienceLevel'=> $this->experienceLevel,
            'targetRevenue'  => $this->targetRevenue,
            'timeframeMonths'=> $this->timeframeMonths,
            'riskIntent'     => $this->riskIntent,
            'stage'          => $this->stage,
            'channel'        => $this->channel,
            'actualRevenue'  => $this->actualRevenue,
            'actualExpenses' => $this->actualExpenses,
            'cashBalance'    => $this->cashBalance,
            'businessAge'    => $this->businessAge,
            'avgOrderValue'  => $this->avgOrderValue,
            'adSpend'        => $this->adSpend,
            'repeatRate'     => $this->repeatRate,
            'problemAreas'   => $this->problemAreas,
        ];
    }
}
