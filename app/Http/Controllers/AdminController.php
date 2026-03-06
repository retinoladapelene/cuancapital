<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Roadmap;
use App\Models\Blueprint;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;
use App\Support\ApiResponse;
use Carbon\Carbon;
use App\Models\UserProgress;
use App\Models\UserBadge;

class AdminController extends Controller
{
    /**
     * Get Analytics and System Metrics for the Admin Dashboard.
     */
    public function getDashboardData(Request $request)
    {
        try {
            // Metrics
            $totalUsers = User::count();
            $newUsers = User::where('created_at', '>=', Carbon::now()->subDays(7))->count();
            $activeUsersToday = User::where('last_login_at', '>=', Carbon::today())->count();
            
            $totalBlueprints = Blueprint::count();
            $newBlueprints = Blueprint::where('created_at', '>=', Carbon::now()->subDays(7))->count();
            
            // Mocking Average Target Profit (from blueprints JSON)
            $blueprints = Blueprint::pluck('data');
            $totalTarget = 0;
            $bpCount = 0;
            foreach($blueprints as $bpData) {
                $bp = is_string($bpData) ? json_decode($bpData, true) : $bpData;
                if(isset($bp['goal']['target_profit'])) {
                    $totalTarget += floatval($bp['goal']['target_profit']);
                    $bpCount++;
                }
            }
            $avgTargetProfit = $bpCount > 0 ? $totalTarget / $bpCount : 0;
            
            $totalRoadmaps = collect(Roadmap::all())->count(); // Handle safely
            $newRoadmaps = Roadmap::where('created_at', '>=', Carbon::now()->subDays(7))->count();
            
            $metrics = [
                'total_users' => $totalUsers,
                'active_users' => $activeUsersToday,
                'new_users' => $newUsers,
                'total_blueprints' => $totalBlueprints,
                'new_blueprints' => $newBlueprints,
                'avg_target_profit' => $avgTargetProfit,
                'total_roadmaps' => $totalRoadmaps,
                'new_roadmaps' => $newRoadmaps,
                'roadmap_conversion_rate' => $totalUsers > 0 ? ($totalRoadmaps / $totalUsers) * 100 : 0,
                'high_intent_users' => User::whereHas('blueprints', function($q) {
                    $q->where('data', 'LIKE', '%"target_profit":%');
                })->count(),
                'avg_feasibility' => 75, // Mock for now
                'avg_probability_score' => 60 // Mock for now
            ];

            // Funnel
            $funnel = [
                'total_visitors' => $totalUsers * 1.5, // Mock anonymous dropoff
                'rgp_users' => $totalUsers,
                'simulator_users' => $totalUsers * 0.8,
                'mentor_users' => $totalUsers * 0.6,
                'roadmap_users' => $totalRoadmaps
            ];

            // AI Insight
            $aiSummary = [
                'summary_text' => "Engagement is stable. <span class='text-emerald-400'>+".number_format($newUsers)."</span> new users this week. Roadmap generation conversion is at ".number_format($metrics['roadmap_conversion_rate'], 1)."%. Focus on increasing Mentor Lab retention."
            ];

            // System Health (Real Data from ApiLogs)
            $systemHealth = \App\Models\ApiLog::selectRaw('
                CONCAT(method, " ", endpoint) as endpoint,
                COUNT(*) as total_requests,
                SUM(CASE WHEN status_code >= 400 THEN 1 ELSE 0 END) as error_count,
                AVG(latency_ms) as avg_latency
            ')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->groupBy('method', 'endpoint')
            ->orderByDesc('total_requests')
            ->limit(8)
            ->get()
            ->toArray();

            return response()->json([
                'success' => true,
                'metrics' => $metrics,
                'funnel' => $funnel,
                'ai_summary' => $aiSummary,
                'system_health' => $systemHealth
            ]);

        } catch(\Exception $e) {
            return ApiResponse::error('Failed to load dashboard metrics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get paginated users.
     */
    public function users(Request $request)
    {
        $users = User::orderBy('created_at', 'desc')->paginate(50);
        return response()->json($users); 
    }

    /**
     * Get detail user info and logs
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        $latestBlueprint = $user->blueprints()->latest()->first();
        $business = [];
        if ($latestBlueprint) {
            $data = is_string($latestBlueprint->data) ? json_decode($latestBlueprint->data, true) : $latestBlueprint->data;
            $business = [
                'business_name' => $data['industry_vertical'] ?? 'Unknown Vertical',
                'target_revenue' => $data['goal']['target_profit'] ?? 0,
                'selling_price' => $data['goal']['selling_price'] ?? 0,
                'ad_spend' => $data['inputs']['budget'] ?? 0,
                'conversion_rate' => $data['inputs']['conversion_rate'] ?? 2,
                'traffic' => $data['inputs']['traffic'] ?? 0
            ];
        }

        // --- Gamification Data ---
        $progress = UserProgress::where('user_id', $user->id)->first();
        $xp = $progress ? $progress->xp : 0;
        $level = $progress ? $progress->level : 1;
        $equippedUserBadge = UserBadge::where('user_id', $user->id)
            ->where('is_equipped', true)
            ->with('badge')
            ->first();

        $gamification = [
            'level' => $level,
            'xp' => $xp,
            'badges_count' => UserBadge::where('user_id', $user->id)->count(),
            'equipped_badge' => $equippedUserBadge && $equippedUserBadge->badge ? $equippedUserBadge->badge->name : 'None',
            'equipped_badge_icon' => $equippedUserBadge && $equippedUserBadge->badge ? $equippedUserBadge->badge->icon_url : null
        ];

        // --- Engagement Data ---
        $engagement = [
            'total_blueprints' => $user->blueprints()->count(),
            'total_roadmaps' => \App\Models\Roadmap::where('user_id', $user->id)->count(),
            'unlocked_milestones' => $progress ? count($progress->unlocked_milestones ?? []) : 0
        ];

        $userArray = $user->toArray();
        $userArray['business_profile'] = $business;
        $userArray['gamification'] = $gamification;
        $userArray['engagement'] = $engagement;

        return response()->json([
            'success' => true,
            'user' => $userArray
        ]);
    }

    /**
     * Ban User
     */
    public function ban(User $user)
    {
        $user->update(['is_banned' => true]);
        // Ideally revoke tokens too
        $user->tokens()->delete();
        return ApiResponse::success('User banned.');
    }

    /**
     * Unban User
     */
    public function unban(User $user)
    {
         $user->update(['is_banned' => false]);
         return ApiResponse::success('User unbanned.');
    }

    /**
     * Promote to Admin
     */
    public function promote(User $user)
    {
         $user->update(['role' => 'admin']);
         return ApiResponse::success('User promoted to Admin.');
    }

    /**
     * Change Password
     */
    public function updateUserPassword(Request $request, User $user)
    {
        $request->validate(['password' => 'required|min:6']);
        $user->update([
            'password' => Hash::make($request->password)
        ]);
        return ApiResponse::success('Password updated.');
    }

    /**
     * Get System Settings Configuration
     */
    public function getSettings()
    {
        $settings = SystemSetting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    /**
     * Update a System Setting
     */
    public function updateSetting(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|string'
        ]);

        SystemSetting::setSetting($request->key, $request->value);
        return response()->json(['success' => true, 'message' => 'Setting updated']);
    }

    // =========================================================================
    // GAMIFICATION MANAGEMENT
    // =========================================================================
    
    /**
     * Get Leaderboard Data & Gamification Overview
     */
    public function getGamificationData(Request $request)
    {
        try {
            // Top 50 Users by XP (from user_metrics)
            // Removed max constraint so 0 XP users show up at bottom
            $topUsers = \App\Models\UserMetric::with('user:id,name,email')
                ->orderByDesc('level')
                ->orderByDesc('total_xp')
                ->limit(50)
                ->get()
                ->map(function($metric) {
                    return [
                        'user_id' => $metric->user_id,
                        'name' => $metric->user ? $metric->user->name : 'Unknown User',
                        'email' => $metric->user ? $metric->user->email : '-',
                        'level' => $metric->level,
                        'total_xp' => $metric->total_xp,
                        'minutes_spent' => $metric->minutes_spent,
                        'simulations_passed' => $metric->simulations_passed,
                        'perfect_runs' => $metric->perfect_runs
                    ];
                });

            // Summary Stats
            $totalXpGranted = \App\Models\UserMetric::sum('total_xp');
            $totalBadgesAwarded = UserBadge::count();

            return ApiResponse::success([
                'leaderboard' => $topUsers,
                'stats' => [
                    'total_xp_economy' => $totalXpGranted,
                    'total_badges_circulation' => $totalBadgesAwarded
                ]
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to load gamification data.', 500);
        }
    }

    /**
     * Get catalog of all available Badges & Borders
     */
    public function getBadgesList()
    {
        $badges = \App\Models\Badge::orderBy('rarity_weight', 'asc')->get();
        $borders = \App\Models\BorderFrame::orderBy('rarity_weight', 'asc')->get();
        return ApiResponse::success(['badges' => $badges, 'borders' => $borders]);
    }

    /**
     * Manually Award a Gamification Item to a specific User
     */
    public function awardBadge(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'item_id' => 'required',
            'item_type' => 'required|in:badge,border',
            'give_xp' => 'nullable|integer|max:100000'
        ]);

        if ($validator->fails()) {
            \Illuminate\Support\Facades\Log::error('Validation Failed on Award: ' . json_encode($validator->errors()));
            return response()->json([
                'success' => false, 
                'message' => 'Validation error: ' . json_encode($validator->errors()), 
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $itemName = '';

            // Award Badge or Border
            if ($request->item_type === 'badge') {
                UserBadge::updateOrCreate(
                    ['user_id' => $request->user_id, 'badge_id' => $request->item_id],
                    ['unlocked_at' => now()]
                );
                
                $badge = \App\Models\Badge::find($request->item_id);
                $itemTheme = 'badge';
                $itemName = $badge ? $badge->name : "Premium Badge";
                $itemIcon = $badge ? $badge->icon_url : null;

                // V1 Toast Achievement System compatibility
                $achievement = \App\Models\Achievement::where('badge_id', $request->item_id)->first();
                if ($achievement) {
                    $cacheKey = "unlocked_toast:user:{$request->user_id}";
                    $toasts = \Illuminate\Support\Facades\Cache::get($cacheKey, []);
                    $toasts[] = $achievement->code;
                    \Illuminate\Support\Facades\Cache::put($cacheKey, $toasts, 300);
                }
            } else {
                \App\Models\UserBorderFrame::updateOrCreate(
                    ['user_id' => $request->user_id, 'border_frame_id' => $request->item_id],
                    ['unlocked_at' => now()]
                );

                $border = \App\Models\BorderFrame::find($request->item_id);
                $itemTheme = 'border';
                $itemName = $border ? $border->name : "Premium Border";
                $itemIcon = $border ? $border->image_url : null;
            }

            // Optional XP Bonus
            $xpBonus = '';
            if ($request->has('give_xp') && $request->give_xp > 0) {
                // Update V2 Metrik
                $metric = \App\Models\UserMetric::firstOrCreate(['user_id' => $request->user_id]);
                $metric->total_xp += $request->give_xp;
                $metric->level = (int) floor($metric->total_xp / 500) + 1;
                $metric->save();

                // DUAL SYNC: Update V1 Progress (Supaya HUD bar di atas ikut gerak)
                $progressRepo = app(\App\Repositories\UserProgressRepository::class);
                $progress = $progressRepo->findOrCreateForUser($request->user_id);
                // Kita force langsung saja tanpa applyXp yang terstruktur ketat, sebab ini Admin Intervention
                $progress->xp_points += $request->give_xp;
                $progress->level = $progressRepo->calculateLevel($progress->xp_points);
                $progress->save();

                // Bersihkan Cached Data agar ditarik ulang dari database
                \Illuminate\Support\Facades\Cache::forget(\App\Support\CacheKeys::xp($request->user_id));
                \Illuminate\Support\Facades\Cache::forget(\App\Support\CacheKeys::dashboard($request->user_id));

                $xpBonus = " + " . number_format($request->give_xp, 0, ',', '.') . " XP";
            }

            // Phase 8: Ping Notifikasi Realtime untuk User yang direward
            $pingKey = "admin_reward_notify:user:{$request->user_id}";
            $pingData = [
                'title' => 'HADIAH SPESIAL',
                'message' => 'Anda mendapatkan Apresiasi Khusus dari Admin!',
                'item_name' => $itemName,
                'item_type' => $itemTheme,
                'item_icon' => $itemIcon,
                'xp_bonus' => $request->has('give_xp') ? (int) $request->give_xp : 0
            ];
            \Illuminate\Support\Facades\Cache::forever($pingKey, json_encode($pingData));

            return ApiResponse::success(null, 'Item awarded successfully!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Manual award error: ' . $e->getMessage());
            return ApiResponse::error('Failed to award item.', 500);
        }
    }
}
