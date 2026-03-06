<?php

namespace Database\Seeders;

use App\Models\RoadmapLibraryStep;
use Illuminate\Database\Seeder;

class RoadmapLibraryStepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Traffic Steps (Organic & Ads)
        RoadmapLibraryStep::create([
            'title' => 'Setup Meta Ads Foundation',
            'description' => 'Install Pixel, verify domain, and create standard events.',
            'category' => 'traffic',
            'stage' => 'startup',
            'channel' => 'ads',
            'difficulty' => 4,
            'impact' => 9,
            'priority_weight' => 10,
            'conditions' => json_encode(['requires_budget_min' => 1000000]),
            'outcome_type' => 'tracking_ready',
            'required_budget' => 0,
            'required_time' => '1 day',
            'tags' => json_encode(['technical', 'foundation']),
        ]);

        RoadmapLibraryStep::create([
            'title' => 'Organic Content Strategy',
            'description' => 'Create 30-day content calendar for TikTok/IG Reels.',
            'category' => 'traffic',
            'stage' => 'startup',
            'channel' => 'organic',
            'difficulty' => 6,
            'impact' => 7,
            'priority_weight' => 5,
            'conditions' => json_encode(['requires_budget_max' => 500000]), // Low budget condition
            'outcome_type' => 'content_plan',
            'required_budget' => 0,
            'required_time' => '3 days',
            'tags' => json_encode(['creative', 'consistency']),
        ]);

        // 2. Conversion Steps
        RoadmapLibraryStep::create([
            'title' => 'Landing Page Headline Optimization',
            'description' => 'Test 3 different headlines emphasizing benefit over features.',
            'category' => 'conversion',
            'stage' => 'growth',
            'channel' => 'all',
            'difficulty' => 3,
            'impact' => 8,
            'priority_weight' => 8,
            'conditions' => json_encode(['requires_traffic_min' => 500]), // Needs traffic to test
            'outcome_type' => 'conversion_boost',
            'required_budget' => 0,
            'required_time' => '1 day',
            'tags' => json_encode(['copywriting', 'quick_win']),
        ]);

        RoadmapLibraryStep::create([
            'title' => 'Offer Irresistibility Audit',
            'description' => 'Add bonuses or guarantees to increase perceived value.',
            'category' => 'conversion',
            'stage' => 'startup',
            'channel' => 'all',
            'difficulty' => 5,
            'impact' => 10,
            'priority_weight' => 10,
            'conditions' => null,
            'outcome_type' => 'offer_fix',
            'required_budget' => 0,
            'required_time' => '2 days',
            'tags' => json_encode(['offer', 'core']),
        ]);

        // 3. Finance/Margin Steps
        RoadmapLibraryStep::create([
            'title' => 'Negotiate COGS',
            'description' => 'Ask supplier for volume discount or find alternative supplier.',
            'category' => 'finance',
            'stage' => 'growth',
            'channel' => 'all',
            'difficulty' => 7,
            'impact' => 8,
            'priority_weight' => 6,
            'conditions' => json_encode(['requires_sales_min' => 50]), // Leverage volume
            'outcome_type' => 'margin_boost',
            'required_budget' => 0,
            'required_time' => '1 week',
            'tags' => json_encode(['negotiation', 'profit']),
        ]);
        
        // 4. Advanced/Scaling Steps
        RoadmapLibraryStep::create([
            'title' => 'Automate Customer Support',
            'description' => 'Implement chatbot or hire VA for CS.',
            'category' => 'operations',
            'stage' => 'scaling',
            'channel' => 'all',
            'difficulty' => 6,
            'impact' => 7,
            'priority_weight' => 4,
            'conditions' => json_encode(['requires_sales_min' => 300]),
            'outcome_type' => 'time_save',
            'required_budget' => 3000000,
            'required_time' => '1 week',
            'tags' => json_encode(['automation', 'hiring']),
        ]);
    }
}
