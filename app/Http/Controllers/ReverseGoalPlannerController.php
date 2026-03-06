<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReverseGoalPlannerRequest;
use App\Http\Resources\ReverseGoalSessionResource;
use App\Jobs\InvalidateDashboardCache;
use App\Jobs\SaveReverseGoalSession;
use App\Services\ReverseGoalPlannerService;
use App\Support\ApiResponse;
use App\Support\CacheKeys;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReverseGoalPlannerController extends Controller
{
    public function __construct(
        private readonly ReverseGoalPlannerService $plannerService
    ) {}

    /**
     * POST /reverse-planner/calculate
     * Calculates the reverse goal plan and asynchronously saves the session.
     */
    public function calculate(ReverseGoalPlannerRequest $request): JsonResponse
    {
        $result = $this->plannerService->process($request->validated());

        if ($request->user()) {
            $userId = $request->user()->id;

            // Async save to repository + cache invalidation
            // The job now handles XP awarding internally because it needs the new Session ID
            SaveReverseGoalSession::dispatch($userId, $result);

            // Invalidate the latest-session cache so the next read is fresh
            InvalidateDashboardCache::dispatch($userId, [
                CacheKeys::rgpLatest($userId),
            ]);
            
            // Trigger Achievement V2
            try {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    app(\App\Services\AchievementEngine::class)->check($user, 'goal_planner_used');
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to trigger achievement for RGP: ' . $e->getMessage());
            }
        }

        return ApiResponse::success($result);
    }

    /**
     * Load a blueprint into the UI via redirect (web route).
     */
    public function load(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $blueprint = \App\Models\Blueprint::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return redirect()->route('index')->with('blueprintData', $blueprint);
    }
}
