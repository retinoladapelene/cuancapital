<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProgressResource extends JsonResource
{
    /**
     * Expose the XP progress in a clean, frontend-friendly shape.
     * Used by the nav bar XP widget and achievement modal.
     */
    public function toArray(Request $request): array
    {
        $xp     = $this->xp_points ?? 0;
        $level  = $this->level ?? 1;
        $userId = $this->user_id;
        $tierMax = $level < 20 ? 500 : 1000;
        $currentTierBase = $level <= 20 ? ($level - 1) * 500 : 9500 + ($level - 20) * 1000;
        $nextLevelXp = $currentTierBase + $tierMax;
        $progressPct = max(0, min(100, (int) ((($xp - $currentTierBase) / $tierMax) * 100)));

        return [
            'xp'            => $xp,
            'level'         => $level,
            'level_title'   => $this->getLevelTitle($level),
            'next_level_xp' => $nextLevelXp,
            'progress_pct'  => $progressPct,
            'achievements'  => $this->achievements ?? [],
            'milestones'    => [
                'rgp_completed'      => (bool) $this->is_rgp_completed,
                'simulator_used'     => (bool) $this->is_simulator_used,
                'mentor_completed'   => (bool) $this->is_mentor_completed,
                'roadmap_generated'  => (bool) $this->is_roadmap_generated,
            ],
        ];
    }

    private function getLevelTitle(int $level): string
    {
        if ($level >= 100) return 'Business Titan';
        if ($level >= 50)  return 'Cuan Architect';
        if ($level >= 20)  return 'Strategic Builder';
        if ($level >= 10)  return 'Growth Hacker';
        if ($level >= 5)   return 'Smart Executor';
        return 'Rookie Planner';
    }
}
