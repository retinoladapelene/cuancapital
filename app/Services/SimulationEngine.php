<?php

namespace App\Services;

use App\Models\Simulation;
use App\Models\SimulationSession;
use App\Models\UserSimulationResult;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SimulationEngine
{
    /**
     * Start or resume a simulation session for a user.
     */
    public function startSimulation(int $userId, int $simulationId)
    {
        $simulation = Simulation::with('steps.options')->findOrFail($simulationId);
        
        // Check if user has exceeded attempts (max 3), they can replay but for 0 XP
        $result = UserSimulationResult::where('user_id', $userId)
            ->where('simulation_id', $simulationId)
            ->first();
            
        // Reset or create active session
        $initialState = $simulation->initial_state_json ?? ['profit' => 0, 'traffic' => 0, 'brand' => 0];
        $session = SimulationSession::updateOrCreate(
            ['user_id' => $userId, 'simulation_id' => $simulationId],
            [
                'current_step_id' => $simulation->steps->first()->id ?? null,
                'state_json' => $initialState,
                'started_at' => now(),
            ]
        );

        return [
            'simulation' => $simulation,
            'session' => $session,
            'attempts' => $result ? $result->attempts : 0,
            'is_eligible_for_xp' => !$result || $result->attempts < 3
        ];
    }

    /**
     * Process an answer, apply effect to state, and advance to next step.
     */
    public function processAnswer(int $userId, int $simulationId, int $stepId, int $optionId)
    {
        $session = SimulationSession::where('user_id', $userId)
            ->where('simulation_id', $simulationId)
            ->firstOrFail();

        if ($session->current_step_id !== $stepId) {
            throw new \Exception("Invalid step sequence.");
        }

        $simulation = Simulation::with('steps.options')->findOrFail($simulationId);
        $currentStep = $simulation->steps->where('id', $stepId)->first();
        $selectedOption = $currentStep->options->where('id', $optionId)->first();

        // Apply stat effects
        $currentState = $session->state_json;
        $effects = $selectedOption->effect_json;

        $newState = $currentState;
        if (is_array($effects)) {
            foreach ($effects as $key => $val) {
                if ($key !== 'event') {
                    $newState[$key] = ($newState[$key] ?? 0) + $val;
                }
            }
        }

        // Determine next step
        $nextStep = $simulation->steps->where('order', '>', $currentStep->order)->sortBy('order')->first();
        
        $session->update([
            'current_step_id' => $nextStep ? $nextStep->id : null,
            'state_json' => $newState
        ]);

        return [
            'feedback' => $selectedOption->feedback_text,
            'effects' => $effects,
            'new_state' => $newState,
            'is_finished' => $nextStep === null,
            'next_step_id' => $nextStep ? $nextStep->id : null
        ];
    }

    /**
     * Calculate final rating based on Master Design rules.
     */
    public function finishSimulation(int $userId, int $simulationId)
    {
        $session = SimulationSession::where('user_id', $userId)
            ->where('simulation_id', $simulationId)
            ->firstOrFail();

        if ($session->current_step_id !== null) {
            throw new \Exception("Simulation is not finished yet.");
        }

        $simulation = Simulation::findOrFail($simulationId);
        $state = $session->state_json;

        if ($simulation->difficulty_level === 'master') {
            $score = ($state['profitability'] ?? 0) +
                     ($state['growth_rate'] ?? 0) +
                     ($state['brand_equity'] ?? 0) +
                     ($state['system_strength'] ?? 0) +
                     ($state['investor_confidence'] ?? 0);

            // Tiers: Operator(<200) | Founder(200-399) | CEO(400-599) | Builder(600-799) | Titan(800-999) | Legend(1000)
            if ($score >= 1000) {
                $rating = 'Legend';
            } elseif ($score >= 800) {
                $rating = 'Titan';
            } elseif ($score >= 600) {
                $rating = 'Builder';
            } elseif ($score >= 400) {
                $rating = 'CEO';
            } elseif ($score >= 200) {
                $rating = 'Founder';
            } else {
                $rating = 'Operator';
            }
        } else {
            $profit = $state['profit'] ?? 0;
            $traffic = $state['traffic'] ?? 0;
            $brand = $state['brand'] ?? 0;

            // Simple fallback formula for old simulations
            $score = ($profit * 0.5) + ($traffic * 0.3) + ($brand * 0.2);
            
            $rating = 'Good';
            if ($score < 0) {
                $rating = 'Failed';
            } elseif ($score <= 50) {
                $rating = 'Beginner';
            } elseif ($score <= 80) {
                $rating = 'Good';
            } else {
                $rating = 'Expert';
            }

            // Perfect Strategist check (all metrics > 70)
            if ($profit > 70 && $traffic > 70 && $brand > 70) {
                $rating = 'Perfect Strategist';
            }
        }

        // Store or update Result
        $result = UserSimulationResult::firstOrNew([
            'user_id' => $userId,
            'simulation_id' => $simulationId,
        ]);

        $isEligibleForXP = $result->attempts < 3; 
        
        $calculatedXp = 0;
        if ($isEligibleForXP) {
            $calculatedXp = $simulation->xp_reward + max(0, intval($score / ($simulation->difficulty_level === 'master' ? 10 : 2)));
        }

        // Save result
        $result->attempts = $result->exists ? $result->attempts + 1 : 1;
        $result->score = $score;
        $result->result_json = $state;
        $result->rating = $rating;
        $result->completed_at = now();
        $result->save();

        // Trigger Achievement Engine
        $isPerfect = ($rating === 'Perfect Strategist' || $rating === 'Legend');
        app(\App\Services\AchievementEngine::class)->check(User::find($userId), 'simulation_finished', [
            'simulation_id' => $simulationId,
            'is_perfect' => $isPerfect,
            'score' => $score
        ]);

        return [
            'metrics' => $state,
            'score' => $score,
            'rating' => $rating,
            'gained_xp' => $calculatedXp,
            'attempts' => $result->attempts
        ];
    }
}
