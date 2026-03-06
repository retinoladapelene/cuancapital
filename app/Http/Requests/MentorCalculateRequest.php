<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MentorCalculateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'traffic'        => 'required|numeric|min:0|max:1000000000', // max 1 billion
            'conversion'     => 'required|numeric|min:0|max:100',
            'price'          => 'required|numeric|min:0|max:1000000000000', // max 1 trillion
            'cost'           => 'required|numeric|min:0|max:1000000000000',
            'fixed_cost'     => 'required|numeric|min:0|max:1000000000000',
            'target_revenue' => 'nullable|numeric|min:0|max:1000000000000',
            'mode'           => 'nullable|string|in:optimizer,planner',
        ];
    }

    public function messages(): array
    {
        return [
            'traffic.required'    => 'Jumlah traffic wajib diisi.',
            'conversion.max'      => 'Conversion rate tidak boleh lebih dari 100%.',
            'price.required'      => 'Harga jual wajib diisi.',
            'cost.required'       => 'Modal per unit wajib diisi.',
            'fixed_cost.required' => 'Biaya tetap wajib diisi.',
            'mode.in'             => 'Mode tidak valid. Pilih: optimizer atau planner.',
        ];
    }
}
