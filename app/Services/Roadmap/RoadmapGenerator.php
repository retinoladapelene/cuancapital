<?php

namespace App\Services\Roadmap;

use App\Models\RoadmapLibraryStep;

class RoadmapGenerator
{
    /**
     * Fetch and filter library steps based on context + strategy.
     *
     * @param array $context   { stage, budget_level, channel, risk_tolerance, ... }
     * @param array $strategy  { primary_strategy, focus_area }
     * @param array $metrics   { traffic, conversion_rate, margin, ... }
     * @return \Illuminate\Support\Collection
     */
    public function generate(array $context, array $strategy, array $metrics = []): \Illuminate\Support\Collection
    {
        $stage   = $context['stage']        ?? 'startup';
        $channel = $context['channel']      ?? null;
        $budget  = $context['budget_level'] ?? 'low';
        $focus   = $strategy['focus_area']  ?? null;

        // 1. Base query: match stage and optionally channel
        $query = RoadmapLibraryStep::query()
            ->where(function ($q) use ($stage) {
                $q->where('stage', $stage)
                  ->orWhere('stage', 'all')
                  ->orWhereNull('stage');
            });

        // 2. Filter by focus area / category (primary strategy drives this)
        if ($focus) {
            $query->where(function ($q) use ($focus) {
                $q->where('category', $focus)
                  ->orWhere('category', 'all')
                  ->orWhereNull('category');
            });
        }

        // 3. Filter by channel compatibility
        if ($channel) {
            $query->where(function ($q) use ($channel) {
                $q->where('channel', $channel)
                  ->orWhere('channel', 'all')
                  ->orWhereNull('channel');
            });
        }

        $steps = $query->get();

        // 4. Apply condition checks (JSON conditions)
        $steps = $steps->filter(function ($step) use ($metrics, $budget) {
            $conditions = is_array($step->conditions) ? $step->conditions : [];
            return $this->checkConditions($conditions, $metrics, $budget);
        });

        return $steps->values();
    }

    /**
     * Check if a step's conditions are met by the current metrics.
     */
    private function checkConditions(array $conditions, array $metrics, string $budget): bool
    {
        if (empty($conditions)) {
            return true;
        }

        $traffic  = $metrics['traffic'] ?? 0;
        $budgetAmt = match ($budget) {
            'low'    => 500000,
            'medium' => 5000000,
            'high'   => 50000000,
            default  => 500000,
        };

        // requires_traffic_min: step needs at least this traffic
        if (isset($conditions['requires_traffic_min']) && $traffic < $conditions['requires_traffic_min']) {
            return false;
        }

        // requires_budget_min: step needs at least this budget
        if (isset($conditions['requires_budget_min']) && $budgetAmt < $conditions['requires_budget_min']) {
            return false;
        }

        // requires_budget_max: step is only for low-budget users
        if (isset($conditions['requires_budget_max']) && $budgetAmt > $conditions['requires_budget_max']) {
            return false;
        }

        return true;
    }
}
