<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\AdArsenalController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GlossaryController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\AchievementV2Controller;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Middleware\EnsureUserIsAdmin;

// ──────────────────────── Public Routes ──────────────────────────────────────
Route::post('/check-username', [AuthController::class, 'checkUsername']);
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/arsenal', [AdArsenalController::class, 'index']);
Route::get('/system/settings', [App\Http\Controllers\SettingsController::class, 'systemSettings']);

// Learning Layer (public)
Route::prefix('learning')->group(function () {
    Route::get('/glossary/{key}', [GlossaryController::class, 'show']);
});

// ──────────────────────── Protected Routes ───────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth & User
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Settings API
    Route::get('/settings/activity-logs', [App\Http\Controllers\SettingsController::class, 'activityLogs']);
    Route::post('/settings/profile', [App\Http\Controllers\SettingsController::class, 'updateProfile']);
    Route::post('/settings/password', [App\Http\Controllers\SettingsController::class, 'updatePassword']);

    // Heavy Engine Async Polling (Strict Throttle: 60/min)
    Route::get('/jobs/{id}/status', [App\Http\Controllers\API\AsyncJobController::class, 'status'])
        ->middleware('throttle:60,1');

    // Business Profile
    Route::get('/business', [BusinessController::class, 'index']);
    Route::post('/business', [BusinessController::class, 'update']);

    // Roadmap
    Route::get('/roadmap', [RoadmapController::class, 'index']);
    Route::post('/roadmap/update', [RoadmapController::class, 'update']);
    Route::post('/roadmap/generate', [RoadmapController::class, 'generate'])
        ->middleware('throttle:10,1');    // Roadmap AI generation: 10 per minute max

    // ── Gamification (with rate limiting) ────────────────────────────────────
    Route::get('/experience/progress', [GamificationController::class, 'getProgress']);
    Route::get('/learning/progress', [GamificationController::class, 'getProgress']);
    Route::post('/learning/xp', [GamificationController::class, 'awardXP'])
        ->middleware('throttle:xp-award');

    // ── Roleplay Simulation Engine ────────────────────────────────────────────
    Route::post('/simulation/start', [App\Http\Controllers\SimulationController::class, 'start'])
        ->middleware('throttle:10,1');   // 10 starts per minute max
    Route::post('/simulation/answer', [App\Http\Controllers\SimulationController::class, 'answer'])
        ->middleware('throttle:30,1');   // 30 answers per minute
    Route::get('/simulation/result', [App\Http\Controllers\SimulationController::class, 'result']);

    // ── Achievement System V2 ─────────────────────────────────────────────────
    Route::get('/me/achievements', [AchievementV2Controller::class, 'getAchievements']);
    Route::get('/me/badges', [AchievementV2Controller::class, 'getBadges']);
    Route::post('/me/badge/equip', [AchievementV2Controller::class, 'equipBadge']);
    // Border Frame routes (independent from badge)
    Route::get('/me/borders', [AchievementV2Controller::class, 'getBorders']);
    Route::post('/me/border/equip', [AchievementV2Controller::class, 'equipBorder']);
    Route::post('/me/border/unequip', [AchievementV2Controller::class, 'unequipBorder']);
    Route::post('/me/track-time', [AchievementV2Controller::class, 'trackTime']);


    // ── Mini Course System ────────────────────────────────────────────────────
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::get('/courses/{id}/next', [CourseController::class, 'nextLesson']);
    Route::post('/lesson/complete', [LessonController::class, 'complete']);

    // ── Heavy Compute (named throttles) ───────────────────────────────────────
    // Mentor Lab calculate: 10 req/min
    Route::post('/mentor/calculate', [App\Http\Controllers\MentorController::class, 'calculate'])
        ->middleware('throttle:30,1');

    // Strategic Engine evaluate: 30 req/min
    Route::post('/mentor/evaluate', [App\Http\Controllers\MentorController::class, 'evaluate'])
        ->middleware('throttle:30,1');
        
    Route::get('/mentor/evaluation/latest', [App\Http\Controllers\MentorController::class, 'getLatestEvaluation']);

    // ── Mentor Roadmap Generation ──────────────────────────────────────────
    Route::post('/mentor/roadmap/generate', [App\Http\Controllers\MentorController::class, 'generateRoadmap']);
    Route::get('/mentor/roadmap/v2', [App\Http\Controllers\MentorController::class, 'getLatestRoadmapV2']);
    Route::get('/mentor/roadmap', [App\Http\Controllers\MentorController::class, 'getRoadmap']);
    Route::post('/mentor/roadmap/action/{id}/toggle', [App\Http\Controllers\MentorController::class, 'toggleAction']);

    // ── Cuan Cashbook V2 ───────────────────────────────────────────────────
    Route::prefix('cashbook')->middleware('throttle:60,1')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Cashbook\DashboardController::class, 'index']);
        Route::get('/financial-direction', [App\Http\Controllers\Cashbook\FinancialDirectionController::class, 'getDirection']);
        
        Route::get('/settings', [App\Http\Controllers\Cashbook\SettingsController::class, 'show']);
        Route::put('/settings', [App\Http\Controllers\Cashbook\SettingsController::class, 'update']);
        
        Route::apiResource('accounts', App\Http\Controllers\Cashbook\AccountController::class);
        Route::apiResource('categories', App\Http\Controllers\Cashbook\CategoryController::class);
        Route::apiResource('transactions', App\Http\Controllers\Cashbook\TransactionController::class);
        
        Route::get('/budgets', [App\Http\Controllers\Cashbook\BudgetController::class, 'index']);
        Route::post('/budgets', [App\Http\Controllers\Cashbook\BudgetController::class, 'store']);
        
        Route::get('/discipline/status', [App\Http\Controllers\Cashbook\DisciplineController::class, 'status']);
        Route::post('/discipline/checkin', [App\Http\Controllers\Cashbook\DisciplineController::class, 'checkin']);
        
        Route::get('/reflections', [App\Http\Controllers\Cashbook\ReflectionController::class, 'index']);
        Route::post('/reflections', [App\Http\Controllers\Cashbook\ReflectionController::class, 'store']);
        
        Route::post('/export', [App\Http\Controllers\Cashbook\ExportController::class, 'download']);
        Route::post('/import', [App\Http\Controllers\Cashbook\ExportController::class, 'import']);
        
        Route::apiResource('debts', App\Http\Controllers\Cashbook\DebtController::class);
        Route::post('debts/{id}/installments', [App\Http\Controllers\Cashbook\DebtController::class, 'addInstallment']);
    });

    // ── Admin Routes ──────────────────────────────────────────────────────────
    Route::middleware(EnsureUserIsAdmin::class)->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'getDashboardData']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/users/{id}', [AdminController::class, 'show']);
        Route::post('/users/{user}/ban', [AdminController::class, 'ban']);
        Route::post('/users/{user}/unban', [AdminController::class, 'unban']);
        Route::post('/users/{user}/promote', [AdminController::class, 'promote']);

        // System Settings & Maintanance
        Route::get('/settings', [AdminController::class, 'getSettings']);
        Route::post('/settings', [AdminController::class, 'updateSetting']);
        
        // Gamification Manager
        Route::get('/gamification', [AdminController::class, 'getGamificationData']);
        Route::get('/gamification/badges', [AdminController::class, 'getBadgesList']);
        Route::post('/gamification/award', [AdminController::class, 'awardBadge']);

        // Ad Arsenal Management
        Route::get('/arsenal', [AdArsenalController::class, 'adminIndex']);
        Route::post('/arsenal', [AdArsenalController::class, 'store']);
        Route::post('/arsenal/{adArsenal}/update', [AdArsenalController::class, 'update']);
        Route::delete('/arsenal/{adArsenal}', [AdArsenalController::class, 'destroy']);

        // User Management
        Route::post('/users/{user}/password', [AdminController::class, 'updateUserPassword']);
    });

});

