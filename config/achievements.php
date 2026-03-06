<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Achievement Registry
    |--------------------------------------------------------------------------
    |
    | Centralized definition for all unlockable achievements.
    | Adding a new badge simply requires adding a new key here.
    | Evaluators and UIs read from this single source of truth.
    |
    */

    'ach_first_reverse' => [
        'name' => 'First Calculation',
        'description' => 'Completed your first Reverse Goal Planner session',
        'icon' => '🎯',
        'xp_bonus' => 50,
    ],

    'ach_profit_10m' => [
        'name' => '10M Breaker',
        'description' => 'Reached 10M profit milestone',
        'icon' => '💰',
        'xp_bonus' => 100,
    ],
    
    'ach_roadmap_starter' => [
        'name' => 'Execution Action',
        'description' => 'Started your first Execution Roadmap step',
        'icon' => '📍',
        'xp_bonus' => 50,
    ],
    
    'ach_first_blueprint' => [
        'name' => 'Architecture Locked',
        'description' => 'Saved your first Mentor DNA Blueprint',
        'icon' => '🧬',
        'xp_bonus' => 75,
    ]

];
