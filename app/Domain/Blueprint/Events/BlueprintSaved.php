<?php

namespace App\Domain\Blueprint\Events;

use App\Models\Blueprint;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BlueprintSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Blueprint $blueprint,
        public readonly bool $isNew
    ) {}
}
