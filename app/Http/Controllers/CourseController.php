<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CourseService;
use App\Services\LearningPathEngine;
use App\Support\ApiResponse;

class CourseController extends Controller
{
    public function index(Request $request, CourseService $service)
    {
        $courses = $service->list($request->user());
        return ApiResponse::success($courses);
    }

    public function show(Request $request, int $id, CourseService $service)
    {
        $course = $service->detail($request->user(), $id);
        
        if ($course->is_locked) {
            return ApiResponse::error('Modul ini masih terkunci. Silakan selesaikan modul sebelumnya terlebih dahulu.', 403);
        }
        
        return ApiResponse::success($course);
    }

    public function nextLesson(Request $request, int $courseId, LearningPathEngine $engine)
    {
        $next = $engine->nextLesson($request->user(), $courseId);
        return ApiResponse::success($next);
    }
}
