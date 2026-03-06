<?php

namespace App\Events;

use App\DTO\StrategicInput;
use App\DTO\StrategicResult;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StrategicAnalysisEvaluated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly StrategicInput $input,
        public readonly StrategicResult $result,
        public readonly ?string $userId = null,
        public readonly ?string $sessionId = null,
        public readonly ?string $scenarioId = null
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
