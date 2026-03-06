<?php

/**
 * Achievement Metrics Configuration
 *
 * Maps achievement keys to:
 * - metric: The method/column to measure progress
 * - target: The count needed to unlock
 * - hint: Human-readable hint with {remaining} placeholder
 *
 * All metrics are resolved from real DB relations — no new tables needed.
 */
return [

    // ── Core Simulations ────────────────────────────────────────────────────────

    'first_simulation' => [
        'metric' => 'profit_simulations_count',
        'target' => 1,
        'hint'   => 'Run your first Profit Simulation',
    ],

    'power_simulator' => [
        'metric' => 'profit_simulations_count',
        'target' => 10,
        'hint'   => 'Run {remaining} more simulations to become a Power Simulator',
    ],

    // ── Reverse Goal Planning ───────────────────────────────────────────────────

    'first_goal' => [
        'metric' => 'reverse_goal_sessions_count',
        'target' => 1,
        'hint'   => 'Calculate your first Reverse Goal plan',
    ],

    'regular_planner' => [
        'metric' => 'reverse_goal_sessions_count',
        'target' => 5,
        'hint'   => 'Create {remaining} more goal plans to become a Regular Planner',
    ],

    // ── Blueprint Management ────────────────────────────────────────────────────

    'blueprint_saver' => [
        'metric' => 'blueprints_count',
        'target' => 1,
        'hint'   => 'Save your first strategy blueprint',
    ],

    'blueprint_hoarder' => [
        'metric' => 'blueprints_count',
        'target' => 5,
        'hint'   => 'Save {remaining} more blueprints to become a Blueprint Hoarder',
    ],

    // ── Mentor Lab ──────────────────────────────────────────────────────────────

    'mentor_graduate' => [
        'metric' => 'blueprints_mentor_count',
        'target' => 1,
        'hint'   => 'Complete your first Business Mentor Lab session and save it',
    ],

    'mentor_master' => [
        'metric' => 'blueprints_mentor_count',
        'target' => 3,
        'hint'   => 'Complete {remaining} more Mentor Lab sessions',
    ],

    // ── Roadmap Generation ──────────────────────────────────────────────────────

    'roadmap_builder' => [
        'metric' => 'roadmaps_count',
        'target' => 1,
        'hint'   => 'Generate your first Execution Roadmap',
    ],

    // ── XP Milestones ───────────────────────────────────────────────────────────

    'first_1000_xp' => [
        'metric' => 'xp_points',
        'target' => 1000,
        'hint'   => 'Earn {remaining} more XP to reach 1,000!',
    ],

    'xp_5k' => [
        'metric' => 'xp_points',
        'target' => 5000,
        'hint'   => 'Earn {remaining} more XP to reach 5,000!',
    ],

    // ── Level Milestones ────────────────────────────────────────────────────────

    'level_5' => [
        'metric' => 'level',
        'target' => 5,
        'hint'   => '{remaining} more levels to reach the Smart Executor rank',
    ],

    'level_10' => [
        'metric' => 'level',
        'target' => 10,
        'hint'   => '{remaining} more levels to become a Growth Hacker',
    ],

];
