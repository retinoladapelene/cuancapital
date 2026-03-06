<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReverseGoalSessionResource extends JsonResource
{
    /**
     * Transform the ReverseGoalSession model output.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'business_model'    => $this->business_model,
            'traffic_strategy'  => $this->traffic_strategy,
            'inputs' => [
                'target_profit'     => $this->target_profit,
                'timeline_days'     => $this->timeline_days,
                'capital_available' => $this->capital_available,
                'hours_per_day'     => $this->hours_per_day,
            ],
            'outputs' => [
                'required_units'        => $this->required_units,
                'required_traffic'      => $this->required_traffic,
                'required_ad_budget'    => $this->required_ad_budget,
                'execution_load_ratio'  => $this->execution_load_ratio,
            ],
            'scores' => [
                'financial'  => $this->financial_score,
                'capital'    => $this->capital_score,
                'execution'  => $this->execution_score,
                'overall'    => $this->overall_score,
                'risk_level' => $this->risk_level,
            ],
            'is_stable_plan' => (bool) $this->is_stable_plan,
            'created_at'     => $this->created_at?->toIso8601String(),
        ];
    }
}
