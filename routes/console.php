<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\MentorSession;
use App\Services\Strategic\StrategicEngine;
use App\DTO\StrategicInput;
use App\Services\RoadmapGeneratorService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
Schedule::command('jobs:prune-async')->daily();

Artisan::command('test:roadmap', function() {
    $user = User::first();
    Auth::login($user); // login

    $inputData = [
        'businessType' => 'general',
        'riskIntent' => 'stable_income',
        'capital' => 5000000,
        'grossMargin' => 30,
        'experienceLevel' => 2.0,
        'targetRevenue' => 20000000,
        'timeframeMonths' => 6,
        'channel' => 'marketplace',
        'stage' => 'running',
        'actualRevenue' => 8000000,
        'actualExpenses' => 5000000,
        'cashBalance' => 2000000,
        'adSpend' => 0,
        'avgOrderValue' => 100000,
        'repeatRate' => 10,
        'businessAge' => 12,
        'problemAreas' => ['sales_conversion', 'traffic'],
        'mode' => 'optimizer'
    ];
    $inputDTO = StrategicInput::fromArray($inputData);
    $engine = app(StrategicEngine::class);
    $result = $engine->evaluate($inputDTO);

    // After evaluate, session should be created (as per Event Listener or Controller logic).
    // Wait, MentorSession is created in MentorController->evaluate() directly, not StrategicEngine.
    // So let's create MentorSession manually here as MentorController does.
    $session = MentorSession::create([
        'user_id' => $user->id,
        'mode' => 'optimizer',
        'input_json' => $inputData,
        'baseline_json' => $result->toArray()
    ]);

    $this->info("Session created ID: " . $session->id);

    $roadmap = RoadmapGeneratorService::generateForUser($user->id, $session);
    
    $this->info("Roadmap ID: " . $roadmap->id);
    $this->info("Steps count: " . $roadmap->steps->count());
    foreach ($roadmap->steps as $step) {
        $this->line("- " . $step->title);
    }
});
