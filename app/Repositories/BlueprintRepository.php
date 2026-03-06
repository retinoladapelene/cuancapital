<?php

namespace App\Repositories;

use App\Models\Blueprint;
use Illuminate\Database\Eloquent\Collection;

class BlueprintRepository
{
    /**
     * Get all blueprints for a user, filtered by type.
     * Selects only columns needed for list views.
     */
    public function findByUser(int $userId, string $type = 'mentor_lab'): Collection
    {
        return Blueprint::query()
            ->select('id', 'title', 'persona', 'version', 'status', 'created_at', 'updated_at')
            ->where('user_id', $userId)
            ->where('type', $type)
            ->latest()
            ->get();
    }

    /**
     * Find a single blueprint belonging to the user.
     */
    public function findByIdForUser(int $id, int $userId, string $type = 'mentor_lab'): ?Blueprint
    {
        return Blueprint::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->where('type', $type)
            ->first();
    }

    /**
     * Create or update a blueprint.
     * If blueprintId is given and belongs to user, it increments the version.
     */
    public function upsert(int $userId, array $data, ?int $blueprintId = null): Blueprint
    {
        if ($blueprintId) {
            $blueprint = Blueprint::query()
                ->where('id', $blueprintId)
                ->where('user_id', $userId)
                ->first();

            if ($blueprint) {
                $blueprint->update([
                    'title'   => $data['title'] ?? 'Business Mentor Snapshot',
                    'persona' => $data['persona'] ?? 'general',
                    'data'    => $data['snapshot'] ?? $data['data'] ?? null,
                    'version' => $blueprint->version + 1,
                    'status'  => 'updated',
                ]);

                return $blueprint->fresh();
            }
        }

        return Blueprint::create([
            'user_id' => $userId,
            'type'    => $data['type'] ?? 'mentor_lab',
            'title'   => $data['title'] ?? 'Business Mentor Snapshot',
            'persona' => $data['persona'] ?? 'general',
            'data'    => $data['snapshot'] ?? $data['data'] ?? null,
            'status'  => 'completed',
            'version' => 1,
        ]);
    }

    /**
     * Delete a blueprint if it belongs to the user.
     */
    public function deleteForUser(int $id, int $userId): bool
    {
        return Blueprint::where('id', $id)
            ->where('user_id', $userId)
            ->delete() > 0;
    }
}
