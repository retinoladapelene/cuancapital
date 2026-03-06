<?php

namespace App\Services\Admin;

use App\Models\StrategicAnalysis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StrategicAnalyticsService
{
    /**
     * Get distribution of strategy labels.
     * Cached for 60 seconds.
     */
    public function getLabelDistribution(): array
    {
        return Cache::remember('strategic.label_distribution', 60, function () {
            return StrategicAnalysis::select('strategy_label', DB::raw('count(*) as total'))
                ->groupBy('strategy_label')
                ->get()
                ->map(fn($item) => [
                    'label' => $item->strategy_label,
                    'count' => $item->total
                ])
                ->toArray();
        });
    }

    /**
     * Get daily trend of strategy creation.
     * Cached for 60 seconds.
     */
    public function getTrendByDate(): array
    {
        return Cache::remember('strategic.trend_by_date', 60, function () {
            return StrategicAnalysis::select(
                    DB::raw('DATE(created_at) as date'),
                    'strategy_label',
                    DB::raw('count(*) as total')
                )
                ->groupBy('date', 'strategy_label')
                ->orderBy('date', 'asc')
                ->get()
                ->groupBy('date') // Group by date explicitly for frontend ease
                ->toArray();
        });
    }

    /**
     * Get average scores for Radar Chart.
     * Cached for 60 seconds.
     */
    public function getAverageScores(): array
    {
        return Cache::remember('strategic.average_scores', 60, function () {
            $averages = StrategicAnalysis::select(
                DB::raw('AVG(feasibility_score) as avg_feasibility'),
                DB::raw('AVG(risk_score) as avg_risk'),
                DB::raw('AVG(profit_score) as avg_profit'),
                DB::raw('AVG(efficiency_score) as avg_efficiency')
            )->first();

            return [
                'feasibility' => round($averages->avg_feasibility ?? 0, 1),
                'risk' => round($averages->avg_risk ?? 0, 1),
                'profit' => round($averages->avg_profit ?? 0, 1),
                'efficiency' => round($averages->avg_efficiency ?? 0, 1),
            ];
        });
    }

    /**
     * Get high-level KPI metrics.
     */
    public function getKpiMetrics(): array
    {
        return Cache::remember('strategic.kpi_metrics', 60, function () {
            $total = StrategicAnalysis::count();
            $unrealistic = StrategicAnalysis::where('strategy_label', 'Unrealistic Strategy')->count(); // Use constant if possible
            
            // Calculate Risk Appetite (Avg Risk Score)
            $avgRisk = StrategicAnalysis::avg('risk_score');

            // Find Most Common Strategy
            $mostCommon = StrategicAnalysis::select('strategy_label', DB::raw('count(*) as total'))
                ->groupBy('strategy_label')
                ->orderByDesc('total')
                ->first();

            return [
                'total_analyses' => $total,
                'unrealistic_percentage' => $total > 0 ? round(($unrealistic / $total) * 100, 1) : 0,
                'avg_risk_score' => round($avgRisk ?? 0, 1),
                'most_common_strategy' => $mostCommon?->strategy_label ?? 'N/A'
            ];
        });
    }
}
