<?php

namespace App\Services\Roadmap;

class BusinessDiagnosticEngine
{
    /**
     * Analyze business metrics to find the primary bottleneck.
     * 
     * @param array $metrics { traffic, conversion_rate, margin, cpa, etc. }
     * @return array breakdown of diagnosis
     */
    public function diagnose(array $metrics): array
    {
        $diagnosis = [
            'primary_problem' => null,
            'severity' => 0, // 0-100 score
            'details' => [],
            'bottlenecks' => []
        ];

        // 1. Gather Metrics
        $traffic = $metrics['traffic'] ?? 0;
        $conversion = $metrics['conversion_rate'] ?? 0; // percentage, e.g., 2.5
        $margin = $metrics['margin'] ?? 0; // percentage, e.g., 0.20
        $cpa = $metrics['cpa'] ?? 0;
        $profit = $metrics['profit'] ?? 0;

        // 2. Define Thresholds (Can be dynamic based on stage later)
        $trafficThreshold = 1000; // Minimum viable traffic
        $convThreshold = 1.0; // Minimum 1% conversion
        $marginThreshold = 0.15; // Minimum 15% margin

        // 3. Detect Bottlenecks & Calculate Severity

        // A. Traffic Check
        $trafficGap = max(0, $trafficThreshold - $traffic);
        $trafficSeverity = ($trafficGap / $trafficThreshold) * 100; // 0-100%
        if ($traffic < $trafficThreshold) {
            $diagnosis['bottlenecks']['traffic'] = [
                'severity' => $trafficSeverity,
                'reason' => 'Traffic di bawah batas minimal validasi (1000 user/mo).'
            ];
        }

        // B. Conversion Check
        // Only valid if traffic is decent (> 100) to be statistically significant
        if ($traffic > 100) {
            $convGap = max(0, $convThreshold - $conversion);
            $convSeverity = ($conversion > 0) 
                ? ($convGap / $convThreshold) * 100 
                : 100; // Max severity if 0 conversion
            
            if ($conversion < $convThreshold) {
                $diagnosis['bottlenecks']['conversion'] = [
                    'severity' => $convSeverity,
                    'reason' => 'Conversion Rate di bawah 1% menandakan offer belum valid.'
                ];
            }
        }

        // C. Margin Check
        $marginGap = max(0, $marginThreshold - $margin);
        $marginSeverity = ($marginGap / $marginThreshold) * 100;
        if ($margin < $marginThreshold) {
             $diagnosis['bottlenecks']['margin'] = [
                'severity' => $marginSeverity,
                'reason' => 'Margin terlalu tipis (< 15%), sulit scale up.'
            ];
        }

        // 4. Identify Primary Problem (Highest Severity)
        $maxSeverity = 0;
        $primary = 'unknown';

        foreach ($diagnosis['bottlenecks'] as $key => $data) {
            if ($data['severity'] > $maxSeverity) {
                $maxSeverity = $data['severity'];
                $primary = $key;
            }
        }

        // 5. Fallback if no explicit failure: find the "weakest link" mathematically
        if ($primary === 'unknown') {
            // How far are they from the "excellent" benchmarks?
            // Let's say excellent is: Traffic 50000, Conv 5%, Margin 50%
            $trafficScore = min(100, ($traffic / 50000) * 100);
            $convScore = min(100, ($conversion / 5.0) * 100);
            $marginScore = min(100, ($margin / 0.50) * 100);

            $scores = [
                'traffic' => $trafficScore,
                'conversion' => $convScore,
                'margin' => $marginScore
            ];

            // Primary problem is the one with the lowest score
            $primary = array_keys($scores, min($scores))[0];
            
            // Assign a mild severity for the "weakest link" so it still registers as an improvement area
            $maxSeverity = 30; // 30% severity means it's a bottleneck holding back scale, not a critical failure
            
            $diagnosis['bottlenecks'][$primary] = [
                'severity' => $maxSeverity,
                'reason' => 'Metrik ini adalah titik terlemah untuk dikembangkan saat ini (Optimization Phase).'
            ];
        }

        $diagnosis['primary_problem'] = $primary;
        $diagnosis['severity'] = round($maxSeverity, 2);
        
        return $diagnosis;
    }
}
