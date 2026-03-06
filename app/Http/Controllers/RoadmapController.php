<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Roadmap\RoadmapEngine;

class RoadmapController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->roadmapProgress;
    }

    public function update(Request $request)
    {
        $request->validate([
            'step_id' => 'required',
            'status' => 'required|in:completed,unlocked',
        ]);

        $progress = $request->user()->roadmapProgress()->updateOrCreate(
            ['step_id' => $request->step_id],
            ['status' => $request->status, 'completed_at' => now()]
        );

        return response()->json($progress);
    }

    /**
     * POST /api/roadmap/generate
     * Full smart roadmap generation via RoadmapEngine 2.0
     */
    public function generate(Request $request)
    {
        $request->validate([
            'traffic'         => 'nullable|numeric|min:0',
            'conversion_rate' => 'nullable|numeric|min:0',
            'margin'          => 'nullable|numeric|min:0|max:1',
            'channel'         => 'nullable|string|in:ads,organic,marketplace,sosmed',
        ]);

        $user = $request->user();
        
        $job = \App\Models\AsyncJob::create([
            'user_id' => $user->id,
            'type' => 'roadmap_generation',
            'status' => 'pending',
            'input_parameters' => $request->only(['traffic', 'conversion_rate', 'margin', 'channel'])
        ]);

        \App\Jobs\ProcessRoadmapGeneration::dispatch($job->id);

        return response()->json([
            'success' => true,
            'data'    => [
                'job_id' => $job->id,
                'status' => 'pending'
            ]
        ], 202);
    }
}
