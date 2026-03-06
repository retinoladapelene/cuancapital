<?php

namespace App\Http\Controllers\Cashbook;

use App\Http\Controllers\Controller;
use App\Services\Cashbook\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $debts = \App\Models\CashbookDebt::where('user_id', $userId)
            ->with(['installments' => function($q) {
                $q->orderBy('payment_date', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $debts]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'debt_name' => 'required|string|max:255',
            'debt_type' => 'required|in:payable,receivable',
            'total_amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $debt = \App\Models\CashbookDebt::create([
            'user_id' => $request->user()->id,
            'debt_name' => $validated['debt_name'],
            'debt_type' => $validated['debt_type'],
            'total_amount' => $validated['total_amount'],
            'remaining_amount' => $validated['total_amount'],
            'due_date' => $validated['due_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'active',
        ]);

        return response()->json(['message' => 'Utang berhasil ditambahkan.', 'data' => $debt], 201);
    }

    public function destroy(Request $request, $id)
    {
        $debt = \App\Models\CashbookDebt::where('user_id', $request->user()->id)->findOrFail($id);
        $debt->delete();
        return response()->json(['message' => 'Utang berhasil dihapus.']);
    }

    public function addInstallment(Request $request, $id)
    {
        $validated = $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|before_or_equal:today',
            'account_id'   => 'required|exists:cashbook_accounts,id',
            'notes'        => 'nullable|string|max:500',
        ]);

        $userId = $request->user()->id;
        $debt   = \App\Models\CashbookDebt::where('user_id', $userId)->findOrFail($id);

        if ($debt->status === 'paid_off' || $debt->remaining_amount <= 0) {
            return response()->json(['message' => 'Utang ini sudah lunas.'], 400);
        }

        // Cap payment to the remaining amount so we don't overpay
        $amountToPay = min((float) $validated['amount'], (float) $debt->remaining_amount);

        // Determine transaction type based on debt type:
        // payable (kita yg berutang) → bayar = expense (saldo berkurang)
        // receivable (orang lain yg berutang ke kita) → terima = income (saldo bertambah)
        $isPayable       = ($debt->debt_type === 'payable');
        $txType          = $isPayable ? 'expense' : 'income';

        // ── Balance sufficiency check (only for payable, income doesn't reduce balance) ──
        $account = \App\Models\CashbookAccount::where('id', $validated['account_id'])
            ->where('user_id', $userId)
            ->firstOrFail();

        if ($isPayable && (float) $account->balance_cached < $amountToPay) {
            return response()->json([
                'message' => 'Saldo akun "' . $account->name . '" tidak mencukupi. ' .
                             'Saldo saat ini: Rp ' . number_format($account->balance_cached, 0, ',', '.') .
                             ', dibutuhkan: Rp ' . number_format($amountToPay, 0, ',', '.') . '.',
            ], 422);
        }

        try {
            $installment = DB::transaction(function () use ($userId, $debt, $amountToPay, $validated, $txType) {
                // 1. Create a cashbook transaction (expense for payable, income for receivable)
                $prefix = $txType === 'expense' ? 'Cicilan dibayar' : 'Piutang diterima';
                $note   = $prefix . ': ' . $debt->debt_name . ($validated['notes'] ? ' – ' . $validated['notes'] : '');
                $cashbookTx = $this->transactionService->createTransaction($userId, [
                    'account_id'       => $validated['account_id'],
                    'category_id'      => null,
                    'type'             => $txType,
                    'amount'           => $amountToPay,
                    'note'             => $note,
                    'transaction_date' => $validated['payment_date'],
                ]);

                // 2. Record installment and link to the cashbook transaction
                $installment = \App\Models\CashbookDebtInstallment::create([
                    'debt_id'        => $debt->id,
                    'amount'         => $amountToPay,
                    'payment_date'   => $validated['payment_date'],
                    'notes'          => $validated['notes'] ?? null,
                    'transaction_id' => $cashbookTx->id,
                ]);

                // 3. Update debt remaining amount and status
                $debt->remaining_amount -= $amountToPay;
                if ($debt->remaining_amount <= 0) {
                    $debt->remaining_amount = 0;
                    $debt->status = 'paid_off';
                }
                $debt->save();

                return $installment;
            });

            $successMsg = $isPayable
                ? 'Cicilan berhasil dibayar. Saldo akun ' . $account->name . ' terpotong.'
                : 'Pembayaran piutang berhasil dicatat. Saldo akun ' . $account->name . ' bertambah.';
            return response()->json(['message' => $successMsg, 'data' => $installment]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal memproses pembayaran: ' . $e->getMessage()], 400);
        }
    }
}
