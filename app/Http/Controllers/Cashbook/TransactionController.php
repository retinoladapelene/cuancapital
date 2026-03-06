<?php

namespace App\Http\Controllers\Cashbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Cashbook\TransactionService;
use App\Models\CashbookTransaction;
use Exception;

class TransactionController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $query = CashbookTransaction::where('user_id', $request->user()->id)
            ->with(['account', 'category'])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->has('month')) {
            $month = $request->input('month'); // YYYY-MM
            $query->where('transaction_date', 'like', $month . '%');
        }

        return response()->json($query->paginate(50));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required_unless:type,transfer|exists:cashbook_accounts,id',
            'category_id' => 'nullable|exists:cashbook_categories,id',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|gt:0|max:9999999999.99',
            'note' => 'nullable|string|max:500',
            'transaction_date' => 'required|date|before_or_equal:today',
            // Transfer specific
            'from_account_id' => 'required_if:type,transfer|exists:cashbook_accounts,id',
            'to_account_id' => 'required_if:type,transfer|exists:cashbook_accounts,id',
        ]);

        try {
            if ($validated['type'] === 'transfer') {
                $result = $this->transactionService->transferBetweenAccounts($request->user()->id, $validated);
                return response()->json($result, 201);
            }

            $transaction = $this->transactionService->createTransaction($request->user()->id, $validated);
            return response()->json($transaction, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id)
    {
        return response()->json(['error' => 'Updates restricted for audit integrity. Please delete and recreate.'], 403);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $this->transactionService->deleteTransaction($id, $request->user()->id);
            return response()->json(['message' => 'Transaction deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
