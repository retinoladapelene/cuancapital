<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\StrategicAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StrategicAnalyticsController extends Controller
{
    public function __construct(
        protected StrategicAnalyticsService $analytics
    ) {}

    /**
     * Display the Strategic Intelligence Dashboard.
     */
    public function index(): View
    {
        // Fetch all metrics via Service
        $data = [
            'kpi' => $this->analytics->getKpiMetrics(),
            'distribution' => $this->analytics->getLabelDistribution(),
            'trend' => $this->analytics->getTrendByDate(),
            'averages' => $this->analytics->getAverageScores(),
        ];

        return view('admin.strategic.dashboard', compact('data'));
    }
}
