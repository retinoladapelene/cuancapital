<?php

namespace App\Repositories;

use App\Models\Simulation;

class SimulationRepository
{
    /**
     * Get the latest simulation for a user.
     */
    public function latestForUser(int $userId): ?Simulation
    {
        return Simulation::query()
            ->where('user_id', $userId)
            ->select(['id', 'user_id', 'mode', 'input_data', 'result_data', 'health_score', 'created_at'])
            ->latest()
            ->first();
    }

    /**
     * Persist a new simulation record.
     */
    public function create(int $userId, string $mode, array $inputData, array $resultData, mixed $healthScore = null): Simulation
    {
        return Simulation::create([
            'user_id'      => $userId,
            'mode'         => $mode,
            'input_data'   => $inputData,
            'result_data'  => $resultData,
            'health_score' => $healthScore,
        ]);
    }
}
