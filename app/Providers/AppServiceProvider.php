<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            \App\Events\StrategicAnalysisEvaluated::class,
            \App\Listeners\LogStrategicAnalysis::class,
        );

        // Phase 6: Mini Course System
        Event::listen(
            \App\Events\LessonCompleted::class,
            \App\Listeners\RewardLessonXP::class,
        );

        // Phase 14: Gamification Domain Events
        Event::listen(
            \App\Domain\ReverseGoal\Events\ReverseGoalSessionCreated::class,
            \App\Gamification\Listeners\AwardXpForReverseGoal::class,
        );
        Event::listen(
            \App\Domain\Mentor\Events\MentorSessionEvaluated::class,
            \App\Gamification\Listeners\AwardXpForMentorEvaluation::class,
        );
        Event::listen(
            \App\Domain\Roadmap\Events\RoadmapGenerated::class,
            \App\Gamification\Listeners\AwardXpForRoadmapGeneration::class,
        );
        Event::listen(
            \App\Domain\Roadmap\Events\RoadmapStepCompleted::class,
            \App\Gamification\Listeners\AwardXpForRoadmapStep::class,
        );
        Event::listen(
            \App\Domain\Blueprint\Events\BlueprintSaved::class,
            \App\Gamification\Listeners\AwardXpForBlueprint::class,
        );

        // Phase 15: Gamification Streak Engine (Login-based)
        Event::listen(
            \Illuminate\Auth\Events\Login::class,
            // [DELETED UpdateUserStreak Binding]
        );
        // Phase 18: Achievement Dashboard System
        Event::listen(
            \App\Domain\ReverseGoal\Events\ReverseGoalSessionCreated::class,
            \App\Listeners\EvaluateAchievements::class,
        );
        Event::listen(
            \App\Domain\Roadmap\Events\RoadmapStepCompleted::class,
            \App\Listeners\EvaluateAchievements::class,
        );
        Event::listen(
            \App\Domain\Blueprint\Events\BlueprintSaved::class,
            \App\Listeners\EvaluateAchievements::class,
        );

        // ── Rate Limiters ──────────────────────────────────────────────────────

        // Heavy compute: Mentor Lab calculate (15 req/min per user or IP)
        RateLimiter::for('mentor-calculate', function (Request $request) {
            return Limit::perMinute(15)
                ->by($request->user()?->id ?? $request->ip());
        });

        // Very heavy: Strategic AI evaluation (5 req/min per user or IP)
        RateLimiter::for('strategic-evaluate', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->user()?->id ?? $request->ip());
        });

        // XP awards (30 req/min per user — prevent farming)
        RateLimiter::for('xp-award', function (Request $request) {
            return Limit::perMinute(30)
                ->by($request->user()?->id ?? $request->ip());
        });
    }
}

