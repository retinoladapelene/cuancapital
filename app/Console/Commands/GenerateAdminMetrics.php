<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateAdminMetrics extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'admin:generate-metrics {--days=30 : Number of days to backfill}';

    /**
     * The console command description.
     */
    protected $description = 'Aggregates daily metrics for the founder-grade admin dashboard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        
        $this->info("Starting metrics generation for the last {$days} day(s)...");

        for ($i = $days - 1; $i >= 0; $i--) {
            $targetDate = Carbon::today()->subDays($i);
            $dateString = $targetDate->toDateString();
            
            $this->info("Generating metrics for {$dateString}...");
            $this->generateForDate($targetDate, $dateString);
        }

        $this->info("Done generating admin metrics.");
    }

    private function generateForDate(Carbon $date, string $dateString)
    {
        $endOfDay = $date->copy()->endOfDay();

        // 1. Optimized Combined User Metrics
        $userStats = DB::table('users')
            ->selectRaw('
                COUNT(*) as total_users,
                SUM(IF(DATE(created_at) = ?, 1, 0)) as new_users,
                SUM(IF(DATE(last_login_at) = ?, 1, 0)) as active_users
            ', [$dateString, $dateString])
            ->where('created_at', '<=', $endOfDay)
            ->first();

        // 2. Blueprint Metrics
        $blueprintStats = DB::table('blueprints')
            ->selectRaw('
                COUNT(*) as total_blueprints,
                SUM(IF(DATE(created_at) = ?, 1, 0)) as new_blueprints,
                AVG(target_profit) as avg_target_profit
            ', [$dateString])
            ->where('created_at', '<=', $endOfDay)
            ->first();

        // 3. Roadmap Metrics
        $roadmapStats = DB::table('roadmaps')
            ->selectRaw('
                COUNT(*) as total_roadmaps,
                SUM(IF(DATE(created_at) = ?, 1, 0)) as new_roadmaps
            ', [$dateString])
            ->where('created_at', '<=', $endOfDay)
            ->first();

        // 4. Strategic DNA Averages
        $dnaStats = DB::table('blueprint_metrics')
            ->selectRaw('
                AVG(feasibility_score) as avg_feasibility,
                AVG(probability_score) as avg_probability_score
            ')
            ->where('created_at', '<=', $endOfDay)
            ->first();

        // 5. High Intent Detection
        $highIntentUsers = DB::table('users')
            ->join('blueprints', 'users.id', '=', 'blueprints.user_id')
            ->join('roadmaps', 'users.id', '=', 'roadmaps.user_id')
            ->where('users.created_at', '<=', $endOfDay)
            ->where('blueprints.target_profit', '>=', 25000000)
            ->distinct('users.id')
            ->count('users.id');

        // Calculate Rates Safely
        $roadmapConversionRate = $userStats->total_users > 0 ? ($roadmapStats->total_roadmaps / $userStats->total_users) * 100 : 0;

        // Persist Daily Core Metrics
        DB::table('admin_daily_metrics')->updateOrInsert(
            ['date' => $dateString],
            [
                'total_users' => (int) $userStats->total_users,
                'new_users' => (int) $userStats->new_users,
                'active_users' => (int) $userStats->active_users,
                'total_blueprints' => (int) $blueprintStats->total_blueprints,
                'new_blueprints' => (int) $blueprintStats->new_blueprints,
                'total_roadmaps' => (int) $roadmapStats->total_roadmaps,
                'new_roadmaps' => (int) $roadmapStats->new_roadmaps,
                'avg_feasibility' => (float) $dnaStats->avg_feasibility,
                'avg_probability_score' => (float) $dnaStats->avg_probability_score,
                'avg_target_profit' => (int) $blueprintStats->avg_target_profit,
                'roadmap_conversion_rate' => $roadmapConversionRate,
                'high_intent_users' => $highIntentUsers,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 6. Funnel Snapshot Aggregation
        $rgpUsers = DB::table('blueprints')->where('type', 'rgp')->where('created_at', '<=', $endOfDay)->distinct('user_id')->count('user_id');
        $simUsers = DB::table('blueprints')->where('type', 'simulator')->where('created_at', '<=', $endOfDay)->distinct('user_id')->count('user_id');
        $mentorUsers = DB::table('blueprints')->where('type', 'mentor_lab')->where('created_at', '<=', $endOfDay)->distinct('user_id')->count('user_id');
        $roadmapFunnelUsers = DB::table('roadmaps')->where('created_at', '<=', $endOfDay)->distinct('user_id')->count('user_id');

        // Extrapolate total visitors roughly from users until visitor tracking is native.
        $totalVisitors = $userStats->total_users * 3;

        DB::table('admin_funnel_snapshot')->updateOrInsert(
            ['date' => $dateString],
            [
                'total_visitors' => $totalVisitors,
                'rgp_users' => $rgpUsers,
                'simulator_users' => $simUsers,
                'mentor_users' => $mentorUsers,
                'roadmap_users' => $roadmapFunnelUsers,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 7. Founder-Grade AI Insight Summary
        $activeText = $userStats->active_users > 0 ? "You have {$userStats->active_users} active users today." : "No active users today.";
        $trendStr = $userStats->new_users > 0 ? "📈 Growth is moving up with {$userStats->new_users} new signups" : "📉 Growth is static today";
        $highIntentStr = $highIntentUsers > 0 ? "🔥 {$highIntentUsers} High-Intent prospects are currently active." : "";
        $roadmapNote = $roadmapConversionRate < 20 ? "⚠️ Roadmap conversion is low (" . number_format($roadmapConversionRate, 1) . "%)." : "✅ Strong roadmap engagement.";

        $summaryText = "{$activeText} {$trendStr}. Average Target Profit identified across blueprints stands at IDR " . number_format((float) $blueprintStats->avg_target_profit, 0, ',', '.') . ".\n{$roadmapNote} {$highIntentStr}";

        DB::table('admin_ai_summary')->updateOrInsert(
            ['date' => $dateString],
            [
                'summary_text' => trim($summaryText),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
