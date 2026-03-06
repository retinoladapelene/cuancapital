<?php

namespace App\Listeners;

use App\Events\LessonCompleted;
use App\Services\XPService;

class RewardLessonXP
{
    public function __construct(private XPService $xpService) {}

    public function handle(LessonCompleted $event): void
    {
        $this->xpService->addXP($event->user, $event->lesson->xp_reward);
    }
}
