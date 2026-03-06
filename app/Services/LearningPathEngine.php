<?php

namespace App\Services;

use App\Models\User;
use App\Models\Lesson;
use App\Models\UserLessonProgress;

class LearningPathEngine
{
    /**
     * Returns the next uncompleted lesson for a user in a given course.
     * Uses a centralized query to avoid logic scatter in controllers.
     */
    public function nextLesson(User $user, int $courseId): ?Lesson
    {
        $completed = UserLessonProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->pluck('lesson_id');

        return Lesson::where('course_id', $courseId)
            ->whereNotIn('id', $completed)
            ->orderBy('order')
            ->first();
    }

    /**
     * Returns true if the user has completed all lessons in a course.
     */
    public function isCourseCompleted(User $user, int $courseId, int $totalLessons): bool
    {
        if ($totalLessons <= 0) return false;

        $completedCount = UserLessonProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->whereIn('lesson_id', function ($q) use ($courseId) {
                $q->select('id')->from('lessons')->where('course_id', $courseId);
            })
            ->count();

        return $completedCount >= $totalLessons;
    }
}
