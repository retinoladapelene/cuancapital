<?php

namespace App\Repositories;

use App\Models\ReverseGoalSession;

class ReverseGoalSessionRepository
{
    /**
     * Persist a new session from the structured planner result array.
     */
    public function createFromResult(int $userId, array $result): ReverseGoalSession
    {
        return ReverseGoalSession::create([
            'user_id'             => $userId,
            'logic_version'       => $result['logic_version'],
            'constraint_snapshot' => json_encode([
                'capital_gap'            => $result['output']['capital_gap'],
                'execution_load_ratio'   => $result['output']['execution_load_ratio'],
                'difficulty_index'       => 1.0,
            ]),
            'is_stable_plan'      => ($result['scores']['ofs'] >= 75),

            // Inputs
            'business_model'      => $result['input']['business_model'],
            'traffic_strategy'    => $result['input']['traffic_strategy'],
            'target_profit'       => $result['input']['target_profit'],
            'timeline_days'       => $result['input']['timeline_days'],
            'capital_available'   => $result['input']['capital_available'],
            'hours_per_day'       => $result['input']['hours_per_day'],

            // Assumptions
            'assumed_margin'      => $result['input']['assumed_margin'],
            'assumed_conversion'  => $result['input']['assumed_conversion'],
            'assumed_cpc'         => $result['input']['assumed_cpc'],

            // Outputs
            'unit_net_profit'       => $result['output']['unit_profit'],
            'required_units'        => $result['output']['required_units'],
            'required_traffic'      => $result['output']['required_traffic'],
            'required_ad_budget'    => $result['output']['total_ad_spend'],
            'execution_load_ratio'  => $result['output']['execution_load_ratio'],

            // Scores
            'financial_score'  => $result['scores']['ffs'],
            'capital_score'    => $result['scores']['cas'],
            'execution_score'  => $result['scores']['efs'],
            'overall_score'    => $result['scores']['ofs'],
            'risk_level'       => $result['scores']['risk_level'],
        ]);
    }

    /**
     * Get the most recent session for a user (minimal columns for cache).
     */
    public function latestForUser(int $userId): ?ReverseGoalSession
    {
        return ReverseGoalSession::query()
            ->where('user_id', $userId)
            ->select([
                'id', 'user_id', 'business_model', 'traffic_strategy',
                'target_profit', 'timeline_days', 'capital_available',
                'overall_score', 'risk_level', 'is_stable_plan', 'created_at',
            ])
            ->latest()
            ->first();
    }
}
