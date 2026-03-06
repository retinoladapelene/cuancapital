<?php

namespace App\Gamification\Listeners;

use App\Domain\Blueprint\Events\BlueprintSaved;
use App\Jobs\ProcessXpAward;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AwardXpForBlueprint implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(BlueprintSaved $event): void
    {
        // Only award XP if it's a new blueprint being saved for the first time
        if ($event->isNew) {
            ProcessXpAward::dispatch(
                $event->blueprint->user_id,
                'save_blueprint',
                'blueprint',
                $event->blueprint->id
            );
        }
    }
}
