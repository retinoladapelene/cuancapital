<?php

namespace App\Http\Controllers;

use App\Models\GlossaryTerm;
use Illuminate\Http\Request;

class GlossaryController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Request $request, $key)
    {
        $term = GlossaryTerm::where('key', $key)->first();

        if (!$term) {
            return response()->json([
                'status' => 'error',
                'message' => 'Term not found'
            ], 404);
        }

        $experienceLevel = $request->header('X-Experience-Level', 'beginner');

        $explanation = $experienceLevel === 'advanced'
            ? ($term->advanced_explanation ?? $term->simple_explanation)
            : $term->simple_explanation;

        return response()->json([
            'status' => 'success',
            'data' => [
                'title' => $term->title,
                'explanation' => $explanation,
                'advanced_explanation' => $term->advanced_explanation,
                'formula' => $term->formula,
            ]
        ]);
    }
}
