<?php

namespace App\Services\Roadmap;

use App\Models\BusinessProfile;
use App\Models\User;

class UserContextService
{
    /**
     * Build a unified context object from User Profile + Simulation Inputs.
     * 
     * @param User $user
     * @param array $simulationInputs Transient inputs (traffic, conversion, etc.)
     * @return array Unified Context
     */
    public function buildContext(User $user, array $simulationInputs = []): array
    {
        // 1. Fetch persistent profile
        $profile = BusinessProfile::where('user_id', $user->id)->first();

        // 2. Default values if profile doesn't exist
        $defaults = [
            'stage' => 'startup',
            'risk_tolerance' => 'medium',
            'team_size' => 'solo',
            'time_availability' => 'part_time',
            'primary_goal' => 'profit',
            'experience_level' => 'intermediate',
            'available_cash' => 0,
        ];

        // 3. Merge Persistent Data
        $context = [
            'stage' => $profile->stage ?? $defaults['stage'],
            'risk_tolerance' => $profile->risk_tolerance ?? $defaults['risk_tolerance'],
            'team_size' => $profile->team_size ?? $defaults['team_size'],
            'time_availability' => $profile->time_availability ?? $defaults['time_availability'],
            'primary_goal' => $profile->primary_goal ?? $defaults['primary_goal'],
            'experience_level' => $profile->experience_level ?? $defaults['experience_level'],
            'budget_level' => $this->classifyBudget($profile->available_cash ?? 0),
        ];

        // 4. Merge Simulation Inputs (Transient)
        // Simulation inputs override profile defaults for specific metrics
        if (!empty($simulationInputs)) {
             // Example: If user inputs specific traffic, we can infer stage? 
             // For now, we trust the profile's explicit stage, unless it's missing.
             if (isset($simulationInputs['traffic']) && $simulationInputs['traffic'] > 10000 && $context['stage'] === 'startup') {
                 $context['stage'] = 'growth'; // Auto-upgrade stage based on data
             }
        }

        return $context;
    }

    private function classifyBudget($amount)
    {
        if ($amount < 1000000) return 'low'; // < 1jt
        if ($amount < 10000000) return 'medium'; // < 10jt
        return 'high'; // > 10jt
    }
}
