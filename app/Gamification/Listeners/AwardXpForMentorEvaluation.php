<?php

namespace App\Gamification\Listeners;

use App\Domain\Mentor\Events\MentorSessionEvaluated;
use App\Jobs\ProcessXpAward;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AwardXpForMentorEvaluation implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(MentorSessionEvaluated $event): void
    {
        ProcessXpAward::dispatch(
            $event->userId,
            'mentor_evaluate',
            'mentor_session',
            $event->sessionId
        );
    }
}
