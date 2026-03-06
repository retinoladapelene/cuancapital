<?php

namespace App\Services\Roadmap;

use App\Models\User;

/**
 * RoadmapEngine — Main orchestration facade for Phase 2.
 *
 * Flow:
 *   UserContextService
 *     → BusinessDiagnosticEngine
 *     → StrategyEngine
 *     → RoadmapGenerator
 *     → RoadmapScoringEngine
 *     → RoadmapComposer
 *     → Final Payload
 */
class RoadmapEngine
{
    public function __construct(
        private UserContextService     $contextService,
        private BusinessDiagnosticEngine $diagnosticEngine,
        private StrategyEngine         $strategyEngine,
        private RoadmapGenerator       $generator,
        private RoadmapScoringEngine   $scoringEngine,
        private RoadmapComposer        $composer,
    ) {}

    /**
     * Generate a full smart roadmap for a user.
     *
     * @param User  $user
     * @param array $inputs  Raw simulation inputs (traffic, conversion_rate, margin, …)
     * @return array  Final roadmap payload
     */
    public function generate(User $user, array $inputs): array
    {
        // 1. Build Context
        $context = $this->contextService->buildContext($user, $inputs);

        // Merge input metrics into context so downstream can use them
        $context = array_merge($context, [
            'traffic'         => $inputs['traffic']         ?? 0,
            'conversion_rate' => $inputs['conversion_rate'] ?? 0,
            'margin'          => $inputs['margin']          ?? 0,
            'channel'         => $inputs['channel']         ?? null,
        ]);

        // 2. Diagnose
        $diagnosis = $this->diagnosticEngine->diagnose($context);

        // 3. Select Strategy
        $strategy = $this->strategyEngine->determineStrategy($context, $diagnosis);

        // 4. Generate Steps from Library
        $steps = $this->generator->generate($context, $strategy, $context);

        // 5. Score + Rank
        $scored = $this->scoringEngine->score($steps, $context, $diagnosis);

        // 6. Compose Final Roadmap
        $roadmap = $this->composer->compose(
            $scored['ranked_steps'],
            $strategy,
            $scored['confidence_score']
        );

        // Attach diagnosis summary for transparency
        $roadmap['diagnosis'] = [
            'primary_problem' => $diagnosis['primary_problem'],
            'severity'        => $diagnosis['severity'],
        ];

        return $roadmap;
    }
}
