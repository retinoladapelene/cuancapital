<?php

namespace App\Http\Controllers;

use App\Http\Requests\AwardXpRequest;
use App\Http\Resources\UserProgressResource;
use App\Jobs\ProcessXpAward;
use App\Repositories\UserProgressRepository;
use App\Support\ApiResponse;
use App\Support\CacheKeys;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GamificationController extends Controller
{
    public function __construct(
        private readonly UserProgressRepository $progressRepo
    ) {}

    /**
     * GET /api/learning/progress
     * Returns the current user's XP progress (Redis-cached).
     */
    public function getProgress(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $data = Cache::remember(CacheKeys::xp($userId), CacheKeys::TTL_XP, function () use ($userId) {
            $progress = $this->progressRepo->findOrCreateForUser($userId);
            return (new UserProgressResource($progress))->resolve();
        });

        // Phase 18: Pop newly unlocked achievements for micro-feedback Toast
        $cacheKey = "unlocked_toast:user:{$userId}";
        $unlockedKeys = Cache::pull($cacheKey, []);
        $unlockedToasts = [];
        
        if (!empty($unlockedKeys)) {
            $config = \App\Models\Achievement::all()->keyBy('code');
            foreach (array_unique($unlockedKeys) as $key) {
                if ($ach = $config->get($key)) {
                    $unlockedToasts[] = [
                        'key' => $key,
                        'name' => $ach->title,
                        'icon' => $ach->icon,
                    ];
                }
            }
        }

        $data['unlocked_achievements'] = $unlockedToasts;

        return ApiResponse::success($data);
    }

    /**
     * POST /api/learning/xp
     * Award XP for a specific action.
     */
    public function awardXP(AwardXpRequest $request): JsonResponse
    {
        $user   = $request->user();
        $action = $request->validated('action');
        
        // SECURITY: Require reference_id to prevent XP farming (idempotency)
        // Without this, a user could spam POST /api/learning/xp and get infinite XP.
        $referenceId = $request->input('reference_id');
        
        if (!$referenceId && !in_array($action, ['roadmap_toggle'])) {
            // Some minor actions might not have a strict reference, but major ones must.
            return ApiResponse::error("Action {$action} requires a reference_id for anti-farming protection.", 400);
        }

        $xpMap = [
            'reverse_calculate' => 50,
            'feasibility_green' => 100,
            'save_blueprint'    => 150,
            'profit_over_10m'   => 200,
            'mentor_evaluate'   => 200,
            'generate_roadmap'  => 250,
            'roadmap_toggle'    => 20,
        ];

        $reward = $xpMap[$action] ?? null;

        if ($reward === null) {
            return ApiResponse::error("Unknown XP action: {$action}", 422);
        }

        // Apply XP synchronously so the response includes the updated state
        $progress = $this->progressRepo->findOrCreateForUser($user->id);
        $oldLevel = $progress->level ?? 1;
        $originalXp = $progress->xp_points;

        // Apply same level-based compounding multiplier as ProcessXpAward
        $multiplier = 1.0 + (($oldLevel - 1) * 0.05);
        $scaledReward = (int) ceil($reward * $multiplier);

        // Pass referenceId to repository to enforce idempotency
        $updatedProgress = $this->progressRepo->applyXp($progress, $scaledReward, $action, 'api_request', $referenceId);
        
        // If points didn't change, it means the idempotency check blocked it
        if ($updatedProgress->xp_points === $originalXp && $scaledReward > 0) {
            return ApiResponse::error("XP already awarded for this specific action and reference.", 409);
        }

        // Bust cache so next getProgress call is fresh
        Cache::forget(CacheKeys::xp($user->id));
        Cache::forget(CacheKeys::dashboard($user->id));

        // Refresh progress from DB
        $updatedProgress->refresh();
        $newLevel = $updatedProgress->level ?? 1;

        $responseData = (new UserProgressResource($updatedProgress))->resolve();
        $responseData['reward']    = $scaledReward;
        $responseData['level_up']  = $newLevel > $oldLevel;
        $responseData['old_level'] = $oldLevel;
        $responseData['new_level'] = $newLevel;

        return ApiResponse::success($responseData);
    }
}
