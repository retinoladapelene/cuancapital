<?php

namespace App\Gamification\Listeners;

use App\Domain\Roadmap\Events\RoadmapGenerated;
use App\Jobs\ProcessXpAward;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AwardXpForRoadmapGeneration implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(RoadmapGenerated $event): void
    {
        ProcessXpAward::dispatch(
            $event->roadmap->user_id,
            'generate_roadmap',
            'roadmap',
            $event->roadmap->id
        );
    }
}
