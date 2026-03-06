<?php

namespace App\Services;

use App\Models\User;
use App\Models\Lesson;
use App\Models\Course;
use App\Models\UserLessonProgress;
use App\Models\UserCourseProgress;
use Illuminate\Support\Facades\DB;

class LessonService
{
    public function __construct(
        private LearningPathEngine $engine
    ) {}

    /**
     * Mark a lesson as complete for a user.
     * [Improvement 3] Wrapped in DB::transaction to prevent race condition double-XP.
     * Returns: ['already_completed' => bool, 'progress' => UserLessonProgress]
     */
    public function complete(User $user, int $lessonId): array
    {
        return DB::transaction(function () use ($user, $lessonId) {
            $lesson = Lesson::lockForUpdate()->findOrFail($lessonId);

            // firstOrCreate is idempotent — won't overwrite existing
            $progress = UserLessonProgress::firstOrCreate([
                'user_id'   => $user->id,
                'lesson_id' => $lessonId,
            ]);

            // Guard: only reward XP once
            if ($progress->completed) {
                return ['already_completed' => true, 'progress' => $progress, 'lesson' => $lesson];
            }

            $progress->update([
                'completed'    => true,
                'completed_at' => now(),
            ]);

            // Trigger Achievement Engine for Lesson
            app(\App\Services\AchievementEngine::class)->check($user, 'lesson_completed', ['lesson_id' => $lessonId]);

            // Increment course progress
            $courseProgress = UserCourseProgress::firstOrCreate([
                'user_id'   => $user->id,
                'course_id' => $lesson->course_id,
            ], ['completed_lessons' => 0]);

            $courseProgress->increment('completed_lessons');
            $courseProgress->refresh();

            // Check if the full course is now done
            $course = Course::find($lesson->course_id);
            if ($course && $this->engine->isCourseCompleted($user, $course->id, $course->lessons_count)) {
                $courseProgress->update([
                    'completed'    => true,
                    'completed_at' => now(),
                ]);

                // Trigger Achievement Engine for Course
                app(\App\Services\AchievementEngine::class)->check($user, 'course_completed', ['course_id' => $course->id]);
            }

            return [
                'already_completed' => false,
                'progress'          => $progress,
                'lesson'            => $lesson,
                'course_progress'   => $courseProgress,
            ];
        });
    }
}
