<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\Roadmap\RoadmapEngine;

class TestRoadmapEngine extends Command
{
    protected $signature   = 'roadmap:test';
    protected $description = 'Integration test for Roadmap Engine 2.0';

    public function handle(): void
    {
        $this->info('=== Integration Test: Roadmap Engine 2.0 ===');

        $user = User::first();
        if (!$user) {
            $this->error('No users found. Please seed the database first.');
            return;
        }

        $engine = app(RoadmapEngine::class);

        // ── Test 1: Low budget + low traffic ─────────────────────────────────────
        $this->newLine();
        $this->line('[1] LOW BUDGET + LOW TRAFFIC (Startup / Organic)');
        $r1 = $engine->generate($user, [
            'traffic'         => 120,
            'conversion_rate' => 2.5,
            'margin'          => 0.20,
            'channel'         => 'organic',
        ]);
        $this->table(
            ['Key', 'Value'],
            [
                ['Diagnosis',  $r1['diagnosis']['primary_problem'] . ' (' . $r1['diagnosis']['severity'] . '%)'],
                ['Strategy',   $r1['strategy']['primary_strategy']],
                ['Steps',      $r1['total_steps']],
                ['Confidence', $r1['confidence_score'] . '%'],
                ['Phases',     implode(', ', array_column($r1['phases'], 'name'))],
            ]
        );
        $this->assertEqual('organic_growth', $r1['strategy']['primary_strategy'], 'Test 1');

        // ── Test 2: High traffic + low conversion (Ads) ───────────────────────────
        $this->newLine();
        $this->line('[2] GOOD TRAFFIC + LOW CONVERSION (Growth / Ads)');
        $r2 = $engine->generate($user, [
            'traffic'         => 5000,
            'conversion_rate' => 0.3,
            'margin'          => 0.25,
            'channel'         => 'ads',
        ]);
        $this->table(
            ['Key', 'Value'],
            [
                ['Diagnosis',  $r2['diagnosis']['primary_problem'] . ' (' . $r2['diagnosis']['severity'] . '%)'],
                ['Strategy',   $r2['strategy']['primary_strategy']],
                ['Steps',      $r2['total_steps']],
                ['Confidence', $r2['confidence_score'] . '%'],
                ['Phases',     implode(', ', array_column($r2['phases'], 'name'))],
            ]
        );
        $this->assertEqual('conversion_optimization', $r2['strategy']['primary_strategy'], 'Test 2');

        // ── Sample first step ─────────────────────────────────────────────────────
        if (!empty($r2['phases'])) {
            $step = $r2['phases'][0]['steps'][0] ?? null;
            if ($step) {
                $this->newLine();
                $this->line('📋 First step in "' . $r2['phases'][0]['name'] . '":');
                $this->line('   Title:     ' . $step['title']);
                $this->line('   Reasoning: ' . $step['reasoning']);
                $this->line('   Score:     ' . $step['priority_score']);
            }
        }

        $this->newLine();
        $this->info('=== Done ===');
    }

    private function assertEqual(string $expected, string $actual, string $label): void
    {
        if ($actual === $expected) {
            $this->info("  ✅ PASS [{$label}]: Got {$actual}");
        } else {
            $this->error("  ❌ FAIL [{$label}]: Expected {$expected}, got {$actual}");
        }
    }
}
