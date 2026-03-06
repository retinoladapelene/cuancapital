<?php

namespace App\Http\Controllers\Cashbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Cashbook\AccountService;
use App\Models\CashbookAccount;

class AccountController extends Controller
{
    protected AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function index(Request $request)
    {
        $accounts = CashbookAccount::where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->get();
        return response()->json($accounts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'type' => 'required|in:cash,bank,ewallet',
            'initial_balance' => 'numeric|min:0|max:9999999999.99',
        ]);

        $account = $this->accountService->createAccount($request->user()->id, $validated);
        return response()->json($account, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'type' => 'in:cash,bank,ewallet',
        ]);

        $account = $this->accountService->updateAccount($id, $request->user()->id, $validated);
        return response()->json($account);
    }

    public function destroy(Request $request, $id)
    {
        $this->accountService->deactivateAccount($id, $request->user()->id);
        return response()->json(['message' => 'Account deactivated.']);
    }
}
