<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blueprint;
use App\Models\Roadmap;
use App\Models\RoadmapStep;
use App\Models\RoadmapAction;
use App\Services\Roadmap\Adapters\MentorToRoadmapAdapter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MentorRoadmapController extends Controller
{
    public function generateFromMentor($id)
    {
        $blueprint = Blueprint::findOrFail($id);

        $user = Auth::user();
        if (!$user && Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
        }

        if (!$user || $blueprint->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $mentorData = $blueprint->data;

        // Idempotency Check: Returns existing roadmap if already generated.
        if (!empty($mentorData['roadmap_v2']) && !empty($blueprint->linked_source_id)) {
            return response()->json([
                'success' => true,
                'data' => $mentorData['roadmap_v2']
            ]);
        }

        // Map Mentor Output to V2 Roadmap Structure
        $mentorOutput = $mentorData['mentor_output'] ?? [];
        $recommendations = $mentorOutput['recommendations'] ?? [];
        $strategyLabel = $mentorOutput['strategy']['label'] ?? 'Custom AI Strategy';
        
        DB::beginTransaction();
        try {
            // 1. Create Master Roadmap Record
            $dbRoadmap = Roadmap::create([
                'user_id' => $user->id,
                'status' => 'active'
            ]);

            $v2Steps = [];
            foreach ($recommendations as $index => $rec) {
                $title = is_string($rec) ? $rec : ($rec['title'] ?? 'Strategic Action');
                $desc = is_string($rec) ? 'Terapkan langkah ini berdasarkan saran Mentor AI.' : ($rec['description'] ?? '');
                
                // 2. Create Roadmap Step Record
                $dbStep = RoadmapStep::create([
                    'roadmap_id' => $dbRoadmap->id,
                    'title' => 'Tindakan ' . ($index + 1) . ': ' . $title,
                    'description' => $desc,
                    'strategy_tag' => 'Mentor AI',
                    'order' => $index + 1,
                    'status' => $index === 0 ? 'unlocked' : 'locked' // Follow standard unlock logic
                ]);

                // 3. Create Roadmap Action Records
                $actionTexts = [
                    $title,
                    'Tinjau kembali saran ini setiap minggu.',
                    'Gunakan tools yang relevan dengan action ini.'
                ];
                
                $v2Actions = [];
                foreach ($actionTexts as $aIndex => $aText) {
                    $dbAction = RoadmapAction::create([
                        'step_id' => $dbStep->id,
                        'action_text' => $aText,
                        'is_completed' => false,
                        'order' => $aIndex + 1
                    ]);
                    
                    // Map back for V2 UI payload (needs ID!)
                    $v2Actions[] = [
                        'id' => $dbAction->id, // This is crucial for the toggle endpoint
                        'action_text' => $aText,
                        'is_completed' => false
                    ];
                }

                $v2Steps[] = [
                    'id' => $dbStep->id, // Use DB id
                    'title' => $dbStep->title,
                    'description' => $dbStep->description,
                    'category' => 'strategy',
                    'difficulty_score' => 5,
                    'impact_score' => 8,
                    'priority_score' => 85,
                    'reasoning' => 'Rekomendasi langsung berdasarkan hasil Business Mentor Lab.',
                    'estimated_time' => '1-2 Minggu',
                    'outcome_type' => 'efficiency',
                    'actions' => $v2Actions // Pass the associative array with IDs instead of plain strings
                ];
            }

            $roadmap = [
                'strategy' => [
                    'primary_strategy' => $strategyLabel,
                    'secondary_strategy' => 'Execution Focus'
                ],
                'confidence_score' => 95,
                'reliability' => 'High',
                'diagnosis' => [
                    'primary_problem' => 'Fokus pada Eksekusi Taktis',
                    'severity' => 70
                ],
                'phases' => [
                    [
                        'name' => 'Fase 1: Implementasi Prioritas mentor',
                        'duration' => 'Bulan 1-3',
                        'steps' => $v2Steps
                    ]
                ],
                'summary' => 'Roadmap V2 custom yang diciptakan berdasarkan analisis AI Mentor khusus untuk bisnis Anda.'
            ];

            // 4. Update the Blueprint
            $mentorData['roadmap_generated'] = true;
            $mentorData['roadmap_v2'] = $roadmap;
            
            $blueprint->update([
                'data' => $mentorData,
                'linked_source_type' => 'roadmap_v2',
                'linked_source_id' => $dbRoadmap->id // Link to real DB
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $roadmap 
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
