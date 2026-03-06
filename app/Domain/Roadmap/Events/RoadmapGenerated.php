<?php

namespace App\Domain\Roadmap\Events;

use App\Models\Roadmap;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoadmapGenerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Roadmap $roadmap
    ) {}
}
