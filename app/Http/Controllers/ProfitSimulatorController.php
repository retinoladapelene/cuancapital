<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProfitSimulatorService;
use Illuminate\Support\Facades\Log;

class ProfitSimulatorController extends Controller
{
    protected $simulator;

    public function __construct(ProfitSimulatorService $simulator)
    {
        $this->simulator = $simulator;
    }

    /**
     * Run Profit Simulation
     * Endpoint: POST /profit-simulator/simulate
     */
    public function simulate(Request $request) 
    {
        try {
            // 1. Validation
            $validated = $request->validate([
                'session_id' => 'nullable|exists:reverse_goal_sessions,id',
                'manual_baseline' => 'nullable|array', 
                'zone' => 'required|string|in:traffic,conversion,pricing,cost',
                'level' => 'required|integer|in:1,2,3', // New Input: Level instead of pct
            ]);

            $userId = auth()->id();

            // 2. Resolve Baseline
            $sessionId = $validated['session_id'] ?? null;
            $baseline = $this->simulator->resolveBaseline(
                $userId, 
                null, 
                $sessionId,
                $validated['manual_baseline'] ?? []
            );

            if (!$baseline) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unable to resolve baseline data. Please run Reverse Goal Planner first.'
                ], 400);
            }

            // 3. Determine Goal Status (Server-Side Security)
            $goalStatus = 'Adjustable'; // Default
            if ($sessionId) {
                $session = \App\Models\ReverseGoalSession::find($sessionId);
                if ($session) {
                    // Map risk_level to goal_status
                    // Realistic -> Ready
                    // Challenging -> Adjustable
                    // High Risk -> Heavy
                    switch ($session->risk_level) {
                        case 'Realistic': $goalStatus = 'Ready'; break;
                        case 'Challenging': $goalStatus = 'Adjustable'; break;
                        case 'High Risk': $goalStatus = 'Heavy'; break;
                        default: $goalStatus = 'Adjustable';
                    }
                }
            }

            // 4. Run Simulation for Selected Zone (Single Focus)
            $result = $this->simulator->simulate(
                $baseline, 
                $validated['zone'], 
                $validated['level'],
                $goalStatus
            );

            // 4.5 Trigger Achievement Progress (V2 Engine)
            if ($userId) {
                try {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        app(\App\Services\AchievementEngine::class)->check($user, 'simulation_finished', [
                            'zone' => $validated['zone']
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to trigger achievement for profit simulator: ' . $e->getMessage());
                }
            }

            // 5. Return Response
            return response()->json([
                'status' => 'success',
                'baseline' => $baseline,
                'result' => $result,
                'goal_status' => $goalStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Profit Simulator Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Simulation failed: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * List User Blueprints
     * Endpoint: GET /profit-simulator/blueprints
     */
    public function index()
    {
        $blueprints = \App\Models\Blueprint::where('user_id', auth()->id())
            ->where(function($q) {
                $q->whereNull('type')->orWhere('type', 'profit_simulator');
            })
            ->orderBy('created_at', 'desc')
            ->select('id', 'name', 'created_at', 'updated_at') // Lightweight fetch
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $blueprints
        ]);
    }

    /**
     * Store Simulation Plan (Aggregated Blueprint)
     * Endpoint: POST /profit-simulator/blueprints
     */
    public function store(Request $request)
    {
        try {
            // 1. Validation for centralized state aggregator
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'reverseGoal' => 'nullable|array',
                'simulation' => 'nullable|array',
                'sessionId' => 'nullable',
            ]);

            $userId = \Illuminate\Support\Facades\Auth::id();
            
            // Limit check: Max 10 blueprints per user
            $currentBlueprintCount = \App\Models\Blueprint::where('user_id', $userId)->count();
            if ($currentBlueprintCount >= 10) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda telah mencapai batas maksimal 10 Blueprint. Silakan hapus beberapa Blueprint lama untuk membuat yang baru.'
                ], 403);
            }

            $sim = $validated['simulation'] ?? null;

            // 2. Create new record (Multi-storage)
            $blueprint = \App\Models\Blueprint::create([
                'user_id'           => $userId,
                'name'              => $validated['name'],
                'title'             => $validated['name'], // Ensure title is also populated for compatibility with BlueprintStorageController
                'reverse_goal_data' => $validated['reverseGoal'] ?? null,
                'simulation_data'   => $sim ? [
                    'zone'   => $sim['zone']   ?? null,
                    'level'  => $sim['level']  ?? null,
                    'result' => $sim['result'] ?? $sim, // fallback: whole sim if no nested result
                ] : null,
                'session_id'        => $validated['sessionId'] ?? null,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Blueprint saved successfully',
                'data' => $blueprint
            ]);

        } catch (\Exception $e) {
            Log::error('Blueprint Save Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save blueprint: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve Specific Blueprint
     * Endpoint: GET /profit-simulator/blueprints/{id}
     */
    public function show($id)
    {
        // Auth handled by middleware + Scoped query
        $blueprint = \App\Models\Blueprint::where('user_id', auth()->id())
            ->where(function($q) {
                $q->whereNull('type')->orWhere('type', 'profit_simulator');
            })
            ->where('id', $id)
            ->first();

        if (!$blueprint) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blueprint not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $blueprint
        ]);
    }

    /**
     * Update Blueprint (Rename)
     * Endpoint: PATCH /profit-simulator/blueprints/{id}
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $blueprint = \App\Models\Blueprint::where('user_id', auth()->id())
            ->where(function($q) {
                $q->whereNull('type')->orWhere('type', 'profit_simulator');
            })
            ->where('id', $id)
            ->first();

        if (!$blueprint) {
            return response()->json(['message' => 'Blueprint not found'], 404);
        }

        $blueprint->update([
            'name' => $validated['name'],
            'title' => $validated['name']
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Blueprint renamed',
            'data' => $blueprint
        ]);
    }

    /**
     * Delete Blueprint
     * Endpoint: DELETE /profit-simulator/blueprints/{id}
     */
    public function destroy($id)
    {
        $blueprint = \App\Models\Blueprint::where('user_id', auth()->id())
            ->where(function($q) {
                $q->whereNull('type')->orWhere('type', 'profit_simulator');
            })
            ->where('id', $id)
            ->first();

        if (!$blueprint) {
            return response()->json(['message' => 'Blueprint not found'], 404);
        }

        $blueprint->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Blueprint deleted'
        ]);
    }

    /**
     * Load a blueprint into the UI via redirect.
     */
    public function load(Request $request, $id)
    {
        $blueprint = \App\Models\Blueprint::where('user_id', auth()->id())
            ->where(function($q) {
                $q->whereNull('type')->orWhere('type', 'profit_simulator');
            })
            ->findOrFail($id);

        return redirect()->route('index')->with('blueprintData', $blueprint);
    }
}
