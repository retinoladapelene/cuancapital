<?php

namespace App\Http\Controllers\Cashbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\CashbookMonthlySummary;
use App\Models\CashbookTransaction;
use App\Services\Cashbook\BudgetService;
use App\Services\Cashbook\ProjectionService;
use App\Services\Cashbook\DisciplineService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected BudgetService $budgetService;
    protected ProjectionService $projectionService;
    protected DisciplineService $disciplineService;

    public function __construct(BudgetService $budgetService, ProjectionService $projectionService, DisciplineService $disciplineService)
    {
        $this->budgetService = $budgetService;
        $this->projectionService = $projectionService;
        $this->disciplineService = $disciplineService;
    }

    public function index(Request $request)
    {
        $month  = $request->query('month', date('Y-m'));
        $userId = $request->user()->id;

        // ── Current month summary ────────────────────────────────────────────
        $summary = CashbookMonthlySummary::firstOrCreate(
            ['user_id' => $userId, 'month' => $month],
            ['total_income' => 0, 'total_expense' => 0, 'net_cashflow' => 0]
        );

        if (empty($summary->health_score)) {
            $this->disciplineService->calculateAndSnapshotHealthScore($userId, $month);
            $summary->refresh();
        }

        // ── Budget status ────────────────────────────────────────────────────
        $budgets = $this->budgetService->getBudgetStatus($userId, $month);

        // ── Total balance ────────────────────────────────────────────────────
        $totalBalance = \App\Models\CashbookAccount::where('user_id', $userId)
            ->where('is_active', true)
            ->sum('balance_cached');

        // ── Projection ───────────────────────────────────────────────────────
        $projection = $this->projectionService->getProjection($userId);

        // ── Chart 1: Income vs Expense Trend (last 6 months) ─────────────────
        $trend = $this->getMonthlyTrend($userId, 6);

        // ── Chart 2: Expense Distribution by Pillar (current month) ──────────
        $pillarDistribution = $this->getPillarDistribution($userId, $month);

        // ── Reflection Summary (Auto-generated 3 bullets) ────────────────────
        $reflectionSummary = $this->generateReflectionSummary($userId, $month, $summary, $projection, $pillarDistribution, $trend);

        return response()->json([
            'month'               => $month,
            'summary'             => $summary,
            'budgets'             => $budgets,
            'total_balance'       => (float) $totalBalance,
            'projection'          => $projection,
            'trend'               => $trend,
            'pillar_distribution' => $pillarDistribution,
            'reflection_summary'  => $reflectionSummary,
        ]);
    }

    /**
     * Monthly income vs expense for the last N months.
     */
    private function getMonthlyTrend(int $userId, int $months): array
    {
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i)->format('Y-m');

            $summary = CashbookMonthlySummary::where('user_id', $userId)
                ->where('month', $m)
                ->first();

            $result[] = [
                'month'   => $m,
                'income'  => $summary ? (float) $summary->total_income   : 0,
                'expense' => $summary ? (float) $summary->total_expense  : 0,
                'net'     => $summary ? (float) $summary->net_cashflow   : 0,
            ];
        }
        return $result;
    }

    /**
     * Expense breakdown by pillar for a given month.
     */
    private function getPillarDistribution(int $userId, string $month): array
    {
        $rows = CashbookTransaction::where('cashbook_transactions.user_id', $userId)
            ->where('cashbook_transactions.type', 'expense')
            ->where('cashbook_transactions.transaction_date', 'like', $month . '%')
            ->join('cashbook_categories', 'cashbook_transactions.category_id', '=', 'cashbook_categories.id')
            ->select('cashbook_categories.pillar', DB::raw('SUM(cashbook_transactions.amount) as total'))
            ->groupBy('cashbook_categories.pillar')
            ->get();

        $pillars = ['wajib' => 0, 'growth' => 0, 'lifestyle' => 0, 'bocor' => 0];
        foreach ($rows as $row) {
            if (array_key_exists($row->pillar, $pillars)) {
                $pillars[$row->pillar] = (float) $row->total;
            }
        }

        // Also count uncategorised expenses
        $uncategorised = CashbookTransaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('transaction_date', 'like', $month . '%')
            ->whereNull('category_id')
            ->sum('amount');

        if ($uncategorised > 0) {
            $pillars['lainnya'] = (float) $uncategorised;
        }

        return $pillars;
    }

    /**
     * Generate 3-bullet reflection summary for the psychological UX.
     */
    private function generateReflectionSummary(int $userId, string $month, $summary, array $projection, array $pillarDistribution, array $trend): array
    {
        $bullets = [];

        // 1. Saving Rate vs Last Month
        if (count($trend) >= 2) {
            $lastMonth = $trend[count($trend) - 2];
            $lastIncome = $lastMonth['income'];
            $lastExpense = $lastMonth['expense'];
            $lastSr = $lastIncome > 0 ? max(0, (($lastIncome - $lastExpense) / $lastIncome) * 100) : 0;
            $currSr = $summary->saving_rate ?? 0;
            
            $diff = $currSr - $lastSr;
            if ($diff > 1) {
                $bullets[] = "Saving rate naik " . number_format($diff, 1) . "% dibanding bulan lalu.";
            } elseif ($diff < -1) {
                $bullets[] = "Saving rate turun " . number_format(abs($diff), 1) . "% dibanding bulan lalu.";
            } else {
                $bullets[] = "Saving rate cenderung stabil di angka " . number_format($currSr, 1) . "%.";
            }
        } else {
             $bullets[] = "Saving rate bulan ini: " . number_format($summary->saving_rate ?? 0, 1) . "%.";
        }

        // 2. Biggest Category / Pillar
        if (array_sum($pillarDistribution) > 0) {
            arsort($pillarDistribution);
            $biggestPillar = array_key_first($pillarDistribution);
            $pillarsIndo = [
                'wajib'     => 'Kebutuhan Wajib',
                'growth'    => 'Growth / Tabungan',
                'lifestyle' => 'Gaya Hidup',
                'bocor'     => 'Pengeluaran Bocor',
                'lainnya'   => 'Lain-lain'
            ];
            $bullets[] = "Porsi pengeluaran terbesar ada di area " . ($pillarsIndo[$biggestPillar] ?? ucfirst($biggestPillar)) . ".";
        } else {
            $bullets[] = "Belum ada pengeluaran signifikan bulan ini.";
        }

        // 3. Runway status
        $runway = $projection['runway_months'] ?? 0;
        if ($runway > 6) {
            $bullets[] = "Ketahanan kas (Runway) sangat aman (> 6 bulan).";
        } elseif ($runway >= 3) {
            $bullets[] = "Ketahanan kas cukup sehat (" . number_format($runway, 1) . " bulan).";
        } else {
            $bullets[] = "Ketahanan kas minim (" . number_format($runway, 1) . " bulan), butuh perhatian ekstra.";
        }

        return $bullets;
    }
}
