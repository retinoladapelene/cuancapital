<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Services\LessonService;
use App\Events\LessonCompleted;

class LessonController extends Controller
{
    public function complete(Request $request, LessonService $service)
    {
        $request->validate(['lesson_id' => 'required|integer|exists:lessons,id']);

        // Check if the lesson is locked before allowing completion
        $lesson = Lesson::findOrFail($request->lesson_id);
        $courseDetail = app(\App\Services\CourseService::class)->detail($request->user(), $lesson->course_id);
        $lockedCheck = $courseDetail->lessons->where('id', $lesson->id)->first();
        
        if ($courseDetail->is_locked || ($lockedCheck && $lockedCheck->is_locked)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Lesson ini masih terkunci. Silakan selesaikan urutan sebelumnya.',
                'xp'      => 0,
            ], 403);
        }

        $result = $service->complete($request->user(), $request->lesson_id);

        // Guard — lesson was already done, no XP
        if ($result['already_completed']) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Already completed.',
                'xp'      => 0,
            ]);
        }

        $lesson = $result['lesson'];

        // Dispatch event → RewardLessonXP listener handles XP
        event(new LessonCompleted($request->user(), $lesson));

        return response()->json([
            'status'          => 'success',
            'xp'              => $lesson->xp_reward,
            'lesson_title'    => $lesson->title,
            'course_progress' => $result['course_progress'] ?? null,
        ]);
    }
}
