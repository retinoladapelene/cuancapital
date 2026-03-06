<?php

namespace App\Services\Roadmap;

use Illuminate\Support\Collection;

class RoadmapScoringEngine
{
    /**
     * Score and sort steps, then compute an overall confidence score.
     *
     * @param Collection $steps    Steps from RoadmapGenerator
     * @param array      $context  User context
     * @param array      $diagnosis Business diagnosis
     * @return array { ranked_steps: Collection, confidence_score: float }
     */
    public function score(Collection $steps, array $context, array $diagnosis): array
    {
        $problemSeverity = $diagnosis['severity'] ?? 50;
        $experienceLevel = $context['experience_level'] ?? 'intermediate';

        $ranked = $steps->map(function ($step) use ($problemSeverity, $experienceLevel) {
            $impact    = $step->impact    ?? 5;
            $difficulty = $step->difficulty ?? 5;
            $weight    = $step->priority_weight ?? 1;

            $ease      = (10 - $difficulty) + 1;
            $urgency   = $problemSeverity / 10;

            $contextMatch = match ($experienceLevel) {
                'beginner'     => $difficulty <= 4 ? 1.2 : ($difficulty <= 7 ? 0.9 : 0.6),
                'intermediate' => 1.0,
                'expert'       => $difficulty >= 7 ? 1.2 : 1.0,
                default        => 1.0,
            };

            $score = round($impact * $urgency * $ease * $contextMatch * $weight, 2);

            // Return a DTO-style array so we don't mutate the Eloquent model
            return [
                'model'          => $step,
                'priority_score' => $score,
            ];
        })->sortByDesc('priority_score')->values();

        // ── Confidence Score ───────────────────────────────────────────────────────
        // data_completeness: how many useful metrics we actually have (0-1)
        $hasTraffic  = isset($context['traffic'])          && $context['traffic'] > 0   ? 1 : 0;
        $hasConv     = isset($context['conversion_rate'])  && $context['conversion_rate'] > 0 ? 1 : 0;
        $hasMargin   = isset($context['margin'])           && $context['margin'] > 0   ? 1 : 0;
        $hasGoal     = isset($context['primary_goal'])                                  ? 1 : 0;
        $dataComplete = ($hasTraffic + $hasConv + $hasMargin + $hasGoal) / 4;

        // realism: penalise if severity is extreme (>90%) — the plan might be too ambitious
        $realism = $problemSeverity > 90 ? 0.7 : ($problemSeverity > 70 ? 0.85 : 1.0);

        // feasibility: higher if we have enough steps to cover the problem
        $stepCount    = $ranked->count();
        $feasibility  = min(1.0, $stepCount / 5); // Need at least 5 steps for full score

        $confidence = round(($dataComplete * $realism * $feasibility) * 100, 1);

        return [
            'ranked_steps'     => $ranked,
            'confidence_score' => $confidence,
        ];
    }
}
