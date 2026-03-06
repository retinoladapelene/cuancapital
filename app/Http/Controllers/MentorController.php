<?php

namespace App\Http\Controllers;

use App\DTO\BusinessInputDTO;
use App\Http\Requests\MentorCalculateRequest;
use App\Http\Requests\StrategicEvaluateRequest;
use App\Http\Resources\BlueprintResource;
use App\Jobs\InvalidateDashboardCache;
use App\Jobs\ProcessXpAward;
use App\Repositories\BlueprintRepository;
use App\Repositories\SimulationRepository;
use App\Services\DiagnosticEngine;
use App\Services\FinancialEngine;
use App\Services\PlannerPresetService;
use App\Services\RoadmapGeneratorService;
use App\Services\SimulationEngine;
use App\Services\Strategic\StrategicEngine;
use App\Support\ApiResponse;
use App\Support\CacheKeys;
use App\Support\CircuitBreaker;
use App\Models\MentorSession;
use App\Models\Roadmap;
use App\Models\RoadmapAction;
use App\Models\RoadmapStep;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MentorController extends Controller
{
    public function __construct(
        private readonly BlueprintRepository $blueprintRepo,
        private readonly SimulationRepository $simulationRepo,
    ) {}

    // ─────────────────────────────── Blueprint CRUD ───────────────────────────

    public function saveMentorSession(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $payload = $request->all();
        
        // Include Roadmap if it exists and is active
        $activeRoadmap = Roadmap::where('user_id', $user->id)
            ->whereIn('status', ['active', 'completed'])
            ->with(['steps.actions'])
            ->latest()
            ->first();
            
        if ($activeRoadmap) {
            // Inject roadmap into the snapshot
            if (isset($payload['snapshot'])) {
                $payload['snapshot']['roadmap_data'] = $activeRoadmap->toArray();
            }
        }

        // Limit check: Max 10 blueprints per user if creating a NEW blueprint
        if (! $request->input('blueprint_id')) {
            $currentBlueprintCount = $this->blueprintRepo->findByUser($user->id)
                ->count();
                
            if ($currentBlueprintCount >= 10) {
                return ApiResponse::error(
                    'Anda telah mencapai batas maksimal 10 Blueprint. Silakan hapus beberapa Blueprint lama untuk membuat yang baru.',
                    403
                );
            }
        }

        $blueprint = $this->blueprintRepo->upsert(
            $user->id,
            $payload,
            $request->input('blueprint_id')
        );

        Cache::forget(CacheKeys::blueprints($user->id));

        if (! $request->input('blueprint_id')) {
            event(new \App\Domain\Blueprint\Events\BlueprintSaved($blueprint, true));
            app(\App\Services\AchievementEngine::class)->check($user, 'blueprint_saved');
        } else {
            event(new \App\Domain\Blueprint\Events\BlueprintSaved($blueprint, false));
        }

        return ApiResponse::success(new BlueprintResource($blueprint));
    }

    public function getBlueprints(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $data = Cache::remember(CacheKeys::blueprints($userId), CacheKeys::TTL_BLUEPRINTS, function () use ($userId) {
            return BlueprintResource::collection(
                $this->blueprintRepo->findByUser($userId)
            )->resolve();
        });

        return ApiResponse::success($data);
    }

    public function getBlueprint(Request $request, int $id): JsonResponse
    {
        $blueprint = $this->blueprintRepo->findByIdForUser($id, $request->user()->id);

        if (! $blueprint) {
            return ApiResponse::notFound('Blueprint tidak ditemukan.');
        }

        return ApiResponse::success([
            'id' => $blueprint->id,
            'title' => $blueprint->title,
            'persona' => $blueprint->persona,
            'type' => $blueprint->type,
            'data' => $blueprint->data, // Include data attribute
            'created_at' => $blueprint->created_at,
            'updated_at' => $blueprint->updated_at
        ]);
    }

    // ─────────────────────────────── Calculate ────────────────────────────────

    public function calculate(MentorCalculateRequest $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::error('Silakan login gratis untuk menyimpan hasil kalkulasi ini secara permanen.', 401);
        }

        $job = \App\Models\AsyncJob::create([
            'user_id' => $user->id,
            'type' => 'mentor_calculation',
            'status' => 'pending',
            'input_parameters' => $request->validated()
        ]);
        
        \App\Jobs\ProcessMentorCalculation::dispatch($job->id);
        
        return ApiResponse::success([
            'job_id' => $job->id,
            'status' => 'pending'
        ], 202);
    }

    public function simulate(Request $request): JsonResponse
    {
        $input      = BusinessInputDTO::fromRequest($request);
        $baseline   = FinancialEngine::calculateBaseline($input);
        $changes    = $request->input('changes', []);
        $simulation = SimulationEngine::simulateScenario($baseline, $changes);

        return ApiResponse::success(['simulation' => $simulation]);
    }

    public function getLatestSimulation(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $data = Cache::remember(CacheKeys::latestSimulation($userId), CacheKeys::TTL_BLUEPRINTS, function () use ($userId) {
            $sim = $this->simulationRepo->latestForUser($userId);
            if (! $sim) return null;

            $dto = new BusinessInputDTO(
                (float) ($sim->input_data['traffic']        ?? 0),
                (float) ($sim->input_data['conversion']     ?? 0),
                (float) ($sim->input_data['price']          ?? 0),
                (float) ($sim->input_data['cost']           ?? 0),
                (float) ($sim->input_data['fixed_cost']     ?? 0),
                (float) ($sim->input_data['target_revenue'] ?? 0),
            );

            return [
                'baseline'    => $sim->result_data,
                'diagnostic'  => $sim->health_score,
                'input'       => $sim->input_data,
                'mode'        => $sim->mode,
                'sensitivity' => SimulationEngine::sensitivity($dto),
                'break_even'  => SimulationEngine::breakEven($dto),
            ];
        });

        if (! $data) {
            return ApiResponse::notFound('Tidak ada simulasi yang ditemukan.');
        }

        return ApiResponse::success($data);
    }

    public function getLatestEvaluation(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $session = \App\Models\MentorSession::where('user_id', $userId)
            ->where('mode', 'planner') // Used by ProcessStrategicEvaluation
            ->latest()
            ->first();

        if (! $session || empty($session->baseline_json)) {
            return ApiResponse::notFound('Tidak ada hasil evaluasi yang ditemukan.');
        }

        // Return the exact same JSON structure expected by the frontend Strategic Evaluate logic
        return ApiResponse::success($session->baseline_json);
    }

    // ─────────────────────────────── Preset / Feasibility ────────────────────

    public function plannerPreset(Request $request): JsonResponse
    {
        $preset = PlannerPresetService::getPreset($request->input('type', 'digital'));
        return ApiResponse::success($preset);
    }

    public function checkFeasibility(Request $request): JsonResponse
    {
        $result = PlannerPresetService::calculateFeasibility(
            (float) $request->input('modal', 0),
            (float) $request->input('estimated_ad_cost', 0),
            (float) $request->input('time', 0),
            $request->input('type', 'digital')
        );
        return ApiResponse::success($result);
    }

    public function upsell(Request $request): JsonResponse
    {
        $input      = BusinessInputDTO::fromRequest($request);
        $result     = SimulationEngine::upsell($input, (float) $request->input('upsell_price'), (float) $request->input('take_rate'));
        return ApiResponse::success(['upsell' => $result]);
    }

    // ─────────────────────────────── Roadmap ─────────────────────────────────

    public function generateRoadmap(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::error('Login sekarang untuk men-generate Action Plan eksklusifmu.', 401);
        }

        $job = \App\Models\AsyncJob::create([
            'user_id' => $user->id,
            'type' => 'roadmap_generation',
            'status' => 'pending',
            'input_parameters' => [] // Context is verified within the job using the latest MentorSession
        ]);

        \App\Jobs\ProcessRoadmapGeneration::dispatch($job->id);

        return ApiResponse::success([
            'job_id' => $job->id,
            'status' => 'pending'
        ], 202);
    }

    public function getRoadmap(Request $request): JsonResponse
    {
        $roadmap = \App\Models\Roadmap::where('user_id', $request->user()->id)
            ->whereIn('status', ['active', 'completed'])
            ->with(['steps.actions'])
            ->latest()
            ->first();

        return ApiResponse::success(['roadmap' => $roadmap]);
    }

    public function getLatestRoadmapV2(Request $request): JsonResponse
    {
        $roadmap = \App\Models\Roadmap::where('user_id', $request->user()->id)
            ->whereIn('status', ['active', 'completed'])
            ->with(['steps.actions'])
            ->latest()
            ->first();

        if (!$roadmap) {
            return ApiResponse::error('Tidak ada roadmap aktif.', 404);
        }

        $steps = $roadmap->steps ?? collect();

        // Build phases by grouping steps (3 per phase)
        $phases = [];
        $phaseNames = ['Foundation Phase', 'Growth Phase', 'Scale Phase', 'Mastery Phase'];
        $phaseDurations = ['Minggu 1–4', 'Minggu 5–8', 'Bulan 3–4', 'Bulan 5–6'];
        $chunks = $steps->chunk(3);
        $i = 0;
        foreach ($chunks as $chunk) {
            $phases[] = [
                'name'     => $phaseNames[$i] ?? 'Phase ' . ($i + 1),
                'duration' => $phaseDurations[$i] ?? '',
                'steps'    => $chunk->map(fn($step) => [
                    'id'             => $step->id,
                    'title'          => $step->title,
                    'description'    => $step->description ?? '',
                    'category'       => $step->strategy_tag ?? 'General',
                    'impact_score'   => 8,
                    'difficulty_score' => 5,
                    'reasoning'      => $step->description ?? '',
                    'estimated_time' => ($i === 0) ? '2–4 minggu' : (($i === 1) ? '4–6 minggu' : '8–12 minggu'),
                    'outcome_type'   => 'revenue_growth',
                    'priority_score' => 10 - $i,
                    'actions'        => $step->actions->map(fn($a) => [
                        'id'           => $a->id,
                        'action_text'  => $a->action_text,
                        'is_completed' => (bool) $a->is_completed,
                    ])->toArray(),
                ])->values()->toArray(),
            ];
            $i++;
        }

        $primaryTag  = optional($steps->first())->strategy_tag ?? 'Growth Strategy';
        $secondaryTag = optional($steps->skip(1)->first())->strategy_tag ?? 'Traffic Scaling';

        $roadmapPayload = [
            'id'               => $roadmap->id,
            'strategy'         => [
                'primary_strategy'   => str_replace(' ', '_', strtolower($primaryTag)),
                'secondary_strategy' => str_replace(' ', '_', strtolower($secondaryTag)),
            ],
            'confidence_score' => 78,
            'reliability'      => 'High',
            'diagnosis'        => null,
            'phases'           => $phases,
            'summary'          => 'Execution roadmap berhasil dibuat berdasarkan analisis strategi Mentor Lab.',
        ];

        return ApiResponse::success($roadmapPayload);
    }

    public function toggleAction(Request $request, int $actionId): JsonResponse
    {
        $user   = $request->user();
        $action = RoadmapAction::findOrFail($actionId);

        if ($action->step->roadmap->user_id !== $user->id) {
            return ApiResponse::forbidden('Akses ditolak.');
        }

        $wasCompleted = $action->is_completed;
        $action->is_completed = ! $action->is_completed;
        $action->save();

        $step              = $action->step;
        $incompleteActions = $step->actions()->where('is_completed', false)->count();
        $stepCompleted     = false;
        $nextStepUnlocked  = false;

        if ($incompleteActions === 0 && $step->status !== 'completed') {
            $step->status = 'completed';
            $step->save();
            $stepCompleted = true;

            $nextStep = RoadmapStep::where('roadmap_id', $step->roadmap_id)
                ->where('order', $step->order + 1)
                ->first();

            if ($nextStep) {
                $nextStep->status = 'unlocked';
                $nextStep->save();
                $nextStepUnlocked = true;
            } else {
                $step->roadmap->update(['status' => 'completed']);
            }
        } elseif ($incompleteActions > 0 && $step->status === 'completed') {
            $step->status = 'unlocked';
            $step->save();
        }

        // Fire Domain Event instead of gamification logic directly
        if (! $wasCompleted && $action->is_completed) {
            event(new \App\Domain\Roadmap\Events\RoadmapStepCompleted($user->id, $action));
            app(\App\Services\AchievementEngine::class)->check($user, 'roadmap_step_completed');
        }

        return ApiResponse::success([
            'action'            => $action,
            'step_completed'    => $stepCompleted,
            'next_step_unlocked' => $nextStepUnlocked,
        ]);
    }

    // ─────────────────────────────── Strategic Engine ────────────────────────

    public function evaluate(StrategicEvaluateRequest $request, StrategicEngine $engine): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return ApiResponse::error('Hampir selesai! Kamu perlu login dulu untuk melihat hasil evaluasi detailnya.', 401);
        }

        $job = \App\Models\AsyncJob::create([
            'user_id' => $user->id,
            'type' => 'strategic_evaluation',
            'status' => 'pending',
            'input_parameters' => $request->validated()
        ]);
        
        \App\Jobs\ProcessStrategicEvaluation::dispatch($job->id);

        return ApiResponse::success([
            'job_id' => $job->id,
            'status' => 'pending'
        ], 202);
    }

    // ─────────────────────────────── Redirect ────────────────────────────────

    public function load(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $blueprint = \App\Models\Blueprint::where('user_id', $request->user()->id)
            ->where('type', 'mentor_lab')
            ->findOrFail($id);

        return redirect()->route('index')->with('blueprintData', $blueprint);
    }
}
