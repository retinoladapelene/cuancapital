<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReverseGoalPlannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth check handled by route middleware
    }

    public function rules(): array
    {
        return [
            'target_profit'      => 'required|numeric|min:0',
            'timeline_days'      => 'required|integer|min:1',
            'capital_available'  => 'required|numeric|min:0',
            'hours_per_day'      => 'required|integer|min:1|max:24',
            'business_model'     => 'required|string|in:dropship,digital,service,stock,affiliate',
            'traffic_strategy'   => 'required|string|in:organic,ads,hybrid',
            'selling_price'      => 'nullable|numeric|min:0',

            // V3 model-specific inputs
            'custom_margin'      => 'nullable|numeric|min:0|max:100',
            'fixed_costs'        => 'nullable|numeric|min:0',
            'return_rate'        => 'nullable|numeric|min:0|max:100',
            'warehouse_cost'     => 'nullable|numeric|min:0',
            'affiliate_rate'     => 'nullable|numeric|min:0|max:100',
            'capacity'           => 'nullable|integer|min:1|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'target_profit.required'     => 'Target profit wajib diisi.',
            'target_profit.numeric'      => 'Target profit harus berupa angka.',
            'timeline_days.required'     => 'Timeline wajib diisi.',
            'timeline_days.integer'      => 'Timeline harus berupa bilangan bulat.',
            'capital_available.required' => 'Modal tersedia wajib diisi.',
            'hours_per_day.max'          => 'Jam kerja per hari tidak boleh lebih dari 24.',
            'business_model.in'          => 'Model bisnis tidak valid.',
            'traffic_strategy.in'        => 'Strategi traffic tidak valid.',
        ];
    }
}
