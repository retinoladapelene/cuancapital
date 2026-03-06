<?php

namespace App\Services;

use App\Models\Course;
use App\Models\UserCourseProgress;
use App\Models\UserLessonProgress;
use App\Models\User;

class CourseService
{
    /**
     * Returns all active courses, enriched with the user's progress.
     */
    public function list(User $user)
    {
        $courses = Course::where('is_active', true)->orderBy('id')->get();

        $progressMap = UserCourseProgress::where('user_id', $user->id)
            ->get()
            ->keyBy('course_id');

        $previousCourseCompleted = true; // First course is always unlocked

        return $courses->map(function ($course) use ($progressMap, &$previousCourseCompleted) {
            $prog = $progressMap->get($course->id);
            $course->user_completed_lessons = $prog?->completed_lessons ?? 0;
            $course->user_completed         = (bool) ($prog?->completed ?? false);
            
            // Lock logic
            $course->is_locked = !$previousCourseCompleted;
            
            // Carry over completion status for the NEXT course
            $previousCourseCompleted = $course->user_completed;

            return $course;
        });
    }

    /**
     * Returns a single course with lessons and each lesson's completion status for user.
     */
    public function detail(User $user, int $id)
    {
        // Calculate if this course should be locked
        $previousCourse = Course::where('is_active', true)->where('id', '<', $id)->orderBy('id', 'desc')->first();
        $isCourseLocked = false;
        
        if ($previousCourse) {
            $prevProg = UserCourseProgress::where('user_id', $user->id)
                ->where('course_id', $previousCourse->id)
                ->first();
            if (!$prevProg || !$prevProg->completed) {
                $isCourseLocked = true;
            }
        }

        $course = Course::with(['lessons', 'simulation', 'simulations'])->findOrFail($id);
        $course->is_locked = $isCourseLocked;

        $completedLessonIds = UserLessonProgress::where('user_id', $user->id)
            ->where('completed', true)
            ->pluck('lesson_id')
            ->flip();

        $previousLessonCompleted = true; // First lesson is always unlocked

        $course->lessons->each(function ($lesson) use ($completedLessonIds, &$previousLessonCompleted) {
            $lesson->is_completed = $completedLessonIds->has($lesson->id);
            
            // Lock logic for lesson
            $lesson->is_locked = !$previousLessonCompleted;
            
            // Carry over for next lesson
            $previousLessonCompleted = $lesson->is_completed;
        });

        $prog = UserCourseProgress::where('user_id', $user->id)
            ->where('course_id', $id)
            ->first();

        $course->user_completed_lessons = $prog?->completed_lessons ?? 0;
        $course->user_completed         = (bool) ($prog?->completed ?? false);

        return $course;
    }
}
