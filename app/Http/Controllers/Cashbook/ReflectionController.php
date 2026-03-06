<?php

namespace App\Http\Controllers\Cashbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CashbookReflection;

class ReflectionController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month', date('Y-m'));
        $reflection = CashbookReflection::firstOrCreate(
            ['user_id' => $request->user()->id, 'month' => $month],
            ['what_worked' => '', 'what_failed' => '', 'improvement_plan' => '']
        );
        
        return response()->json($reflection);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'what_worked' => 'nullable|string',
            'what_failed' => 'nullable|string',
            'improvement_plan' => 'nullable|string',
        ]);

        $reflection = CashbookReflection::updateOrCreate(
            ['user_id' => $request->user()->id, 'month' => $validated['month']],
            [
                'what_worked' => $validated['what_worked'],
                'what_failed' => $validated['what_failed'],
                'improvement_plan' => $validated['improvement_plan'],
            ]
        );

        return response()->json($reflection, 200);
    }
}
