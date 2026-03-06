<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AuthController;



use App\Http\Controllers\BusinessController;

Route::get('/', [BusinessController::class, 'index'])->name('index');

// Phase 6: Dedicated Mini Course page
Route::get('/courses', function () {
    return view('courses');
})->name('courses');

// Feature Guides
Route::get('/guide/phase-1', function () { return view('dashboard.guide-phase1'); })->name('guide.phase1');
Route::get('/guide/phase-2', function () { return view('dashboard.guide-phase2'); })->name('guide.phase2');
Route::get('/guide/phase-3', function () { return view('dashboard.guide-phase3'); })->name('guide.phase3');
Route::get('/guide/phase-4', function () { return view('dashboard.guide-phase4'); })->name('guide.phase4');
Route::get('/guide/cashbook', function () { return view('dashboard.guide-cashbook'); })->name('guide.cashbook');

// Cashbook V2 Frontend (auth protected client-side via JS token, same as /admin)
Route::get('/cashbook', function () {
    return view('dashboard.cashbook');
})->name('cashbook');

// Business Manager — Local-First (all data in IndexedDB, server only serves shell)
Route::get('/business-manager', function () {
    return view('dashboard.business');
})->name('business');

Route::get('/login', function () {
    return redirect('/?auth_action=login');
})->name('login');

Route::get('/reset-password/{token}', function (Illuminate\Http\Request $request, $token) {
    return redirect('/?auth_action=reset_final&token=' . urlencode($token) . '&email=' . urlencode($request->query('email')));
})->name('password.reset');

Route::get('/admin', function () {
    return view('admin');
})->name('admin');

Route::get('/admin/strategic-analytics', [App\Http\Controllers\Admin\StrategicAnalyticsController::class, 'index'])
    ->name('admin.strategic.analytics');

Route::get('/settings', [SettingsController::class, 'index'])->name('settings');

Route::middleware('auth:sanctum')->group(function () {
    // API routes migrated to api.php
    Route::get('/api/blueprints', [\App\Http\Controllers\BlueprintStorageController::class, 'index']);
    Route::get('/api/blueprints/{id}', [\App\Http\Controllers\BlueprintStorageController::class, 'show']);
    Route::patch('/api/blueprints/{id}', [\App\Http\Controllers\BlueprintStorageController::class, 'update']);
    Route::delete('/api/blueprints/{id}', [\App\Http\Controllers\BlueprintStorageController::class, 'destroy']);
});

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']); // Fixed namespace


Route::middleware(['auth:sanctum'])->group(function () {
    // New Mentor Lab V2 Routes
    Route::post('/mentor/save', [App\Http\Controllers\MentorController::class, 'saveMentorSession']);
    Route::get('/mentor/blueprints', [App\Http\Controllers\MentorController::class, 'getBlueprints']);
    Route::get('/mentor/blueprints/{id}', [App\Http\Controllers\MentorController::class, 'getBlueprint']);
    Route::post('/mentor/{id}/generate-roadmap', [App\Http\Controllers\MentorRoadmapController::class, 'generateFromMentor']);
    
    Route::get('/mentor/{id}', [\App\Http\Controllers\MentorController::class, 'load'])->middleware('token.query');
});

// Mentor Lab Public/Session Routes (Allow Guest but start Session)
Route::get('/mentor/preset', [App\Http\Controllers\MentorController::class, 'plannerPreset']);
Route::post('/mentor/calculate', [App\Http\Controllers\MentorController::class, 'calculate']);
Route::post('/mentor/evaluate', [App\Http\Controllers\MentorController::class, 'evaluate']); // New V2 Endpoint
Route::post('/mentor/simulate', [App\Http\Controllers\MentorController::class, 'simulate']);
Route::post('/mentor/upsell', [App\Http\Controllers\MentorController::class, 'upsell']);
Route::get('/mentor/feasibility', [App\Http\Controllers\MentorController::class, 'checkFeasibility']);
Route::get('/mentor/simulation/latest', [App\Http\Controllers\MentorController::class, 'getLatestSimulation']);

// Reverse Goal Planner V2
Route::post('/reverse-planner/calculate', [App\Http\Controllers\ReverseGoalPlannerController::class, 'calculate']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/reverse-goal/{id}', [\App\Http\Controllers\ReverseGoalPlannerController::class, 'load'])->middleware('token.query');
});

// Profit Simulator (v2.0)
// Profit Simulator (v2.0)
Route::middleware(['auth:sanctum'])->prefix('profit-simulator')->group(function () {
    Route::post('/simulate', [App\Http\Controllers\ProfitSimulatorController::class, 'simulate']);
    
    // UI Loading route
    Route::get('/profit/{id}', [\App\Http\Controllers\ProfitSimulatorController::class, 'load'])->middleware('token.query');
    
    // Blueprint Management
    Route::get('/blueprints', [App\Http\Controllers\ProfitSimulatorController::class, 'index']);
    Route::post('/blueprints', [App\Http\Controllers\ProfitSimulatorController::class, 'store']);
    Route::get('/blueprints/{id}', [App\Http\Controllers\ProfitSimulatorController::class, 'show']);
    Route::patch('/blueprints/{id}', [App\Http\Controllers\ProfitSimulatorController::class, 'update']);
    Route::delete('/blueprints/{id}', [App\Http\Controllers\ProfitSimulatorController::class, 'destroy']);
    
    // Legacy support (redirect/alias if needed, but for now we keep strict)
    // The previous /store and /blueprint routes are replaced by the RESTful ones above.
    // However, to avoid immediate breaking if frontend hasn't updated, let's keep aliases or update frontend first.
    // For this plan, we update frontend simultaneously.
});

// Context-Aware Learning Engine
Route::match(['get', 'post'], '/api/context-evaluate', [App\Http\Controllers\EducationController::class, 'evaluateContext']);
Route::get('/api/education/{termKey}', [App\Http\Controllers\EducationController::class, 'getTerm']);
Route::post('/api/behavior-log', [App\Http\Controllers\EducationController::class, 'logBehavior']);

// Local Development: Serve storage files directly to bypass Windows symlink 403 errors
if (app()->environment('local')) {
    Route::get('/storage/{path}', function ($path) {
        // Strip duplicate 'avatars/' if it exists since the path already includes it
        // The error shows: 7jwPNcql8ecAJZJb1eg5v9kTB5EDgabUje3V9bKt.png
        // But the DB saves it as: /storage/avatars/7jw...png
        // So the requested URL should be /storage/avatars/7jw...png
        // Which means $path should be 'avatars/7jw...png'
        
        $filePath = storage_path('app/public/' . $path);
        
        if (!file_exists($filePath)) {
            // Check if it's missing the 'avatars' prefix
            $altFilePath = storage_path('app/public/avatars/' . $path);
            if (file_exists($altFilePath)) {
                $filePath = $altFilePath;
            } else {
                abort(404);
            }
        }
        
        $mime = mime_content_type($filePath);
        return response()->file($filePath, ['Content-Type' => $mime]);
    })->where('path', '.*');
}
