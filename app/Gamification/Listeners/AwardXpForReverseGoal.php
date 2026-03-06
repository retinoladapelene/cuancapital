<?php

namespace App\Gamification\Listeners;

use App\Domain\ReverseGoal\Events\ReverseGoalSessionCreated;
use App\Jobs\ProcessXpAward;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AwardXpForReverseGoal implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ReverseGoalSessionCreated $event): void
    {
        // Always award base XP for calculating
        ProcessXpAward::dispatch(
            $event->userId,
            'reverse_calculate',
            'reverse_goal_session',
            $event->sessionId
        );

        // Conditional bonus XP for feasible plan
        if ($event->isFeasible) {
            ProcessXpAward::dispatch(
                $event->userId,
                'feasibility_green',
                'reverse_goal_session',
                $event->sessionId
            );
        }
    }
}
