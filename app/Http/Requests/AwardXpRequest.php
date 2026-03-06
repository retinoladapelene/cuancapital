<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AwardXpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Valid XP actions — must match the server-side reward map.
     */
    public function rules(): array
    {
        return [
            'action'         => 'required|string|in:reverse_calculate,feasibility_green,save_blueprint,profit_over_10m,mentor_evaluate,generate_roadmap,roadmap_toggle',
            'reference_type' => 'nullable|string|max:50',
            'reference_id'   => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Action wajib diisi.',
            'action.in'       => 'Action XP tidak dikenali.',
        ];
    }
}
