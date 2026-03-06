<?php

namespace App\Gamification\Listeners;

use App\Domain\Roadmap\Events\RoadmapStepCompleted;
use App\Jobs\ProcessXpAward;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AwardXpForRoadmapStep implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(RoadmapStepCompleted $event): void
    {
        ProcessXpAward::dispatch(
            $event->userId,
            'roadmap_toggle',
            'roadmap_action',
            $event->action->id
        );
    }
}
