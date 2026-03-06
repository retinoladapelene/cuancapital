<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StrategicEvaluateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Original fields (required) ────────────────────────────────
            'businessType'    => 'required|string|max:100',
            'capital'         => 'required|string|max:50',
            'grossMargin'     => 'required|numeric|min:0|max:100',
            'experienceLevel' => 'required|numeric|min:0|max:10',
            'targetRevenue'   => 'required|numeric|min:0|max:100000000000', // max 100 Billion
            'timeframeMonths' => 'required|integer|min:1|max:120', // max 10 years
            'riskIntent'      => 'required|string|max:50',
            'stage'           => 'nullable|string|max:50',
            'channel'         => 'nullable|string|max:100',

            // ── Diagnostic fields (all nullable for backward compat) ───────
            'actualRevenue'   => 'nullable|numeric|min:0|max:100000000000',
            'actualExpenses'  => 'nullable|numeric|min:0|max:100000000000',
            'cashBalance'     => 'nullable|numeric|min:0|max:50000000000',
            'businessAge'     => 'nullable|integer|min:0|max:1200',
            'avgOrderValue'   => 'nullable|numeric|min:0|max:1000000000',
            'adSpend'         => 'nullable|numeric|min:0|max:10000000000', // max 10 Billion
            'repeatRate'      => 'nullable|numeric|min:0|max:100',
            'problemAreas'    => 'nullable|array|max:10', // Limit array size to prevent abuse
            'problemAreas.*'  => 'string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'businessType.required'    => 'Tipe bisnis wajib diisi.',
            'capital.required'         => 'Modal wajib diisi.',
            'grossMargin.required'     => 'Gross margin wajib diisi.',
            'experienceLevel.required' => 'Level pengalaman wajib diisi.',
            'targetRevenue.required'   => 'Target revenue wajib diisi.',
            'timeframeMonths.required' => 'Timeframe wajib diisi.',
            'riskIntent.required'      => 'Risk intent wajib diisi.',
        ];
    }
}
