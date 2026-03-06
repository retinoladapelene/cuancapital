<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlueprintResource extends JsonResource
{
    /**
     * Transform Blueprint model to a safe, controlled API output.
     * Hides: user_id, internal data payload (use separate endpoint for full data)
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'persona'    => $this->persona,
            'type'       => $this->type,
            'status'     => $this->status,
            'version'    => $this->version,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
