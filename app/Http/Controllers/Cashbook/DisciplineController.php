<?php

namespace App\Http\Controllers\Cashbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Cashbook\DisciplineService;
use App\Models\CashbookDisciplineLog;
use Carbon\Carbon;

class DisciplineController extends Controller
{
    protected DisciplineService $disciplineService;

    public function __construct(DisciplineService $disciplineService)
    {
        $this->disciplineService = $disciplineService;
    }

    public function status(Request $request)
    {
        $userId = $request->user()->id;
        $today = Carbon::today()->toDateString();
        $month = $request->query('month', date('Y-m'));
        
        $todayLog = CashbookDisciplineLog::where('user_id', $userId)->where('date', $today)->first();
        
        // Let's trigger health score calc on status check for convenience of the demo, 
        // though normally this might be a cron or hooked to transaction creation.
        $healthScore = $this->disciplineService->calculateAndSnapshotHealthScore($userId, $month);

        return response()->json([
            'did_input_today' => $todayLog ? $todayLog->did_input_today : false,
            'health_score' => $healthScore,
        ]);
    }

    public function checkin(Request $request)
    {
        $log = $this->disciplineService->recordDailyInput($request->user()->id);
        return response()->json($log, 201);
    }
}
