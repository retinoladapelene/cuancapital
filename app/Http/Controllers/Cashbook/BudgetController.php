<?php

namespace App\Http\Controllers\Cashbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Cashbook\BudgetService;

class BudgetController extends Controller
{
    protected BudgetService $budgetService;

    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function index(Request $request)
    {
        $month = $request->query('month', date('Y-m'));
        $status = $this->budgetService->getBudgetStatus($request->user()->id, $month);
        
        return response()->json($status);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:cashbook_categories,id',
            'month' => 'required|date_format:Y-m',
            'limit_amount' => 'required|numeric|min:0',
        ]);

        $budget = $this->budgetService->setMonthlyBudget(
            $request->user()->id,
            $validated['category_id'],
            $validated['month'],
            $validated['limit_amount']
        );

        return response()->json($budget, 201);
    }
}
