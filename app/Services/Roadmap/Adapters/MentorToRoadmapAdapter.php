<?php

namespace App\Services\Roadmap\Adapters;

class MentorToRoadmapAdapter
{
    /**
     * Transform Mentor Lab API JSON output into Roadmap Engine input format
     */
    public static function transform(array $mentorOutput)
    {
        return [
            'goal' => $mentorOutput['strategy']['label'] ?? 'Strategic Execution',
            'priorities' => $mentorOutput['recommendations'] ?? [],
            'risks' => [],
            'resources' => [],
            'timeframe' => '6 Months'
        ];
    }
}
