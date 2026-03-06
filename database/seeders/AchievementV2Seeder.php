<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Badge;
use App\Models\Achievement;

class AchievementV2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Re-truncate tables safely
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Achievement::truncate();
        Badge::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Rarity weight mapping for Badges (no css_class — borders handled by border_frames)
        $rarityConfigs = [
            'bronze'   => ['weight' => 10],
            'silver'   => ['weight' => 20],
            'gold'     => ['weight' => 30],
            'platinum' => ['weight' => 40],
            'diamond'  => ['weight' => 50],
            'mythic'   => ['weight' => 60],
        ];

        // 2. Comprehensive 1-to-1 Achievement to Unique Badge Mapping
        $achievementsData = [
            // ================= LEARNING CATEGORY =================
            [
                'code' => 'first_lesson', 'title' => 'First Step', 'icon' => '🎯', 'category' => 'learning',
                'description' => 'Selesaikan pelajaran pertama Anda.',
                'trigger_event' => 'lesson_completed', 'condition_type' => 'lessons_completed', 'condition_value' => 1,
                'badge_name' => 'Bronze Scholar Crest', 'rarity' => 'bronze', 'xp_reward' => 100, 'is_hidden' => false,
                'border_rarity' => 'bronze'
            ],
            [
                'code' => 'lesson_10', 'title' => 'Growth Hacker', 'icon' => '📖', 'category' => 'learning',
                'description' => 'Selesaikan 10 sesi pelajaran untuk mengasah strategi.',
                'trigger_event' => 'lesson_completed', 'condition_type' => 'lessons_completed', 'condition_value' => 10,
                'badge_name' => 'Growth Hacker', 'rarity' => 'silver', 'xp_reward' => 250, 'is_hidden' => false,
                'border_rarity' => 'silver'
            ],
            [
                'code' => 'course_1', 'title' => 'The Graduate', 'icon' => '🎓', 'category' => 'learning',
                'description' => 'Lulus dari modul Mini Course pertama Anda.',
                'trigger_event' => 'course_completed', 'condition_type' => 'courses_completed', 'condition_value' => 1,
                'badge_name' => 'The Graduate', 'rarity' => 'silver', 'xp_reward' => 500, 'is_hidden' => false,
                'border_rarity' => 'gold'
            ],
            [
                'code' => 'course_5', 'title' => 'Curriculum Master', 'icon' => '🧠', 'category' => 'learning',
                'description' => 'Selesaikan 5 modul Mini Course.',
                'trigger_event' => 'course_completed', 'condition_type' => 'courses_completed', 'condition_value' => 5,
                'badge_name' => 'Gold Wisdom Aura', 'rarity' => 'gold', 'xp_reward' => 1500, 'is_hidden' => false
            ],
            [
                'code' => 'course_10', 'title' => 'Library Keeper', 'icon' => '🏛️', 'category' => 'learning',
                'description' => 'Selesaikan 10 modul Mini Course.',
                'trigger_event' => 'course_completed', 'condition_type' => 'courses_completed', 'condition_value' => 10,
                'badge_name' => 'Platinum Archive Edge', 'rarity' => 'platinum', 'xp_reward' => 3000, 'is_hidden' => false
            ],
            [
                'code' => 'course_all', 'title' => 'Omniscient Student', 'icon' => '👁️', 'category' => 'learning',
                'description' => 'Selesaikan seluruh materi pembelajaran.',
                'trigger_event' => 'course_completed', 'condition_type' => 'courses_completed', 'condition_value' => 999,
                'badge_name' => 'Mythic Omniscience', 'rarity' => 'mythic', 'xp_reward' => 10000, 'is_hidden' => true
            ],

            // ================= TOOLS: BLUEPRINTS (MENTOR LAB SAVES) =================
            [
                'code' => 'blueprint_1', 'title' => 'Market Analyst', 'icon' => '📏', 'category' => 'tools',
                'description' => 'Simpan blueprint mentor pertama Anda untuk validasi pasar.',
                'trigger_event' => 'blueprint_saved', 'condition_type' => 'blueprints_saved', 'condition_value' => 1,
                'badge_name' => 'Market Analyst', 'rarity' => 'bronze', 'xp_reward' => 100, 'is_hidden' => false
            ],
            [
                'code' => 'blueprint_5', 'title' => 'Strategic Architect', 'icon' => '🏗️', 'category' => 'tools',
                'description' => 'Simpan 5 blueprint berbeda untuk diversifikasi strategi.',
                'trigger_event' => 'blueprint_saved', 'condition_type' => 'blueprints_saved', 'condition_value' => 5,
                'badge_name' => 'Strategic Architect', 'rarity' => 'silver', 'xp_reward' => 300, 'is_hidden' => false
            ],
            [
                'code' => 'blueprint_10', 'title' => 'Urban Planner', 'icon' => '🏙️', 'category' => 'tools',
                'description' => 'Simpan 10 blueprint strategi bisnis.',
                'trigger_event' => 'blueprint_saved', 'condition_type' => 'blueprints_saved', 'condition_value' => 10,
                'badge_name' => 'Gold Foundation', 'rarity' => 'gold', 'xp_reward' => 750, 'is_hidden' => false,
                'border_rarity' => 'diamond'
            ],
            [
                'code' => 'blueprint_25', 'title' => 'Empire Architect', 'icon' => '🏛️', 'category' => 'tools',
                'description' => 'Simpan 25 blueprint strategi bisnis.',
                'trigger_event' => 'blueprint_saved', 'condition_type' => 'blueprints_saved', 'condition_value' => 25,
                'badge_name' => 'Platinum Monument', 'rarity' => 'platinum', 'xp_reward' => 2000, 'is_hidden' => true
            ],

            // ================= ROADMAP PROGRESS =================
            [
                'code' => 'roadmap_1', 'title' => 'First Milestone', 'icon' => '🚩', 'category' => 'tools',
                'description' => 'Selesaikan item roadmap pertama Anda.',
                'trigger_event' => 'roadmap_step_completed', 'condition_type' => 'roadmap_items_completed', 'condition_value' => 1,
                'badge_name' => 'Bronze Banner', 'rarity' => 'bronze', 'xp_reward' => 100, 'is_hidden' => false
            ],
            [
                'code' => 'roadmap_10', 'title' => 'Persistent Builder', 'icon' => '🛠️', 'category' => 'tools',
                'description' => 'Selesaikan 10 item roadmap.',
                'trigger_event' => 'roadmap_step_completed', 'condition_type' => 'roadmap_items_completed', 'condition_value' => 10,
                'badge_name' => 'Silver Hammer', 'rarity' => 'silver', 'xp_reward' => 500, 'is_hidden' => false
            ],
            [
                'code' => 'roadmap_50', 'title' => 'Empire Tactician', 'icon' => '⛓️', 'category' => 'tools',
                'description' => 'Selesaikan 50 item roadmap untuk menguasai pasar.',
                'trigger_event' => 'roadmap_step_completed', 'condition_type' => 'roadmap_items_completed', 'condition_value' => 50,
                'badge_name' => 'Empire Tactician', 'rarity' => 'gold', 'xp_reward' => 1500, 'is_hidden' => false
            ],
            [
                'code' => 'roadmap_100', 'title' => 'Strategic Legendary', 'icon' => '⚔️', 'category' => 'tools',
                'description' => 'Selesaikan 100 item roadmap.',
                'trigger_event' => 'roadmap_step_completed', 'condition_type' => 'roadmap_items_completed', 'condition_value' => 100,
                'badge_name' => 'Mythic Sword of Order', 'rarity' => 'mythic', 'xp_reward' => 5000, 'is_hidden' => true
            ],

            // ================= TOOLS: GOAL PLANNER =================
            [
                'code' => 'planner_1', 'title' => 'Future Architect', 'icon' => '📐', 'category' => 'tools',
                'description' => 'Gunakan Goal Planner pertama kali.',
                'trigger_event' => 'goal_planner_used', 'condition_type' => 'goal_planner_used', 'condition_value' => 1,
                'badge_name' => 'Bronze Blueprint Frame', 'rarity' => 'bronze', 'xp_reward' => 150, 'is_hidden' => false
            ],
            [
                'code' => 'planner_10', 'title' => 'Master Planner', 'icon' => 'MAP', 'category' => 'tools',
                'description' => 'Gunakan Goal Planner 10 kali.',
                'trigger_event' => 'goal_planner_used', 'condition_type' => 'goal_planner_used', 'condition_value' => 10,
                'badge_name' => 'Silver Compass Ring', 'rarity' => 'silver', 'xp_reward' => 500, 'is_hidden' => false
            ],
            [
                'code' => 'planner_50', 'title' => 'Visionary Forecaster', 'icon' => '🔭', 'category' => 'tools',
                'description' => 'Gunakan Goal Planner 50 kali.',
                'trigger_event' => 'goal_planner_used', 'condition_type' => 'goal_planner_used', 'condition_value' => 50,
                'badge_name' => 'Gold Astrolabe Edge', 'rarity' => 'gold', 'xp_reward' => 2000, 'is_hidden' => false
            ],

            // ================= SKILLS: SIMULATION =================
            [
                'code' => 'sim_1', 'title' => 'Founder\'s Ignition', 'icon' => '🚀', 'category' => 'skill',
                'description' => 'Lulus dari Business Roleplay Simulation pertama Anda.',
                'trigger_event' => 'simulation_finished', 'condition_type' => 'simulations_passed', 'condition_value' => 1,
                'badge_name' => 'Founder\'s Ignition', 'rarity' => 'bronze', 'xp_reward' => 200, 'is_hidden' => false
            ],
            [
                'code' => 'sim_5', 'title' => 'Corporate Manager', 'icon' => '🏢', 'category' => 'skill',
                'description' => 'Selesaikan 5 simulasi bisnis.',
                'trigger_event' => 'simulation_finished', 'condition_type' => 'simulations_passed', 'condition_value' => 5,
                'badge_name' => 'Silver Syndicate Frame', 'rarity' => 'silver', 'xp_reward' => 1000, 'is_hidden' => false
            ],
            [
                'code' => 'sim_perfect_1', 'title' => 'Flawless Strategist', 'icon' => '🎯', 'category' => 'skill',
                'description' => 'Raih peringkat Perfect manapun.',
                'trigger_event' => 'simulation_finished', 'condition_type' => 'perfect_runs', 'condition_value' => 1,
                'badge_name' => 'Gold Precision Aura', 'rarity' => 'gold', 'xp_reward' => 1500, 'is_hidden' => false
            ],
            [
                'code' => 'sim_perfect_5', 'title' => 'Corporate Titan', 'icon' => '♟️', 'category' => 'skill',
                'description' => 'Raih peringkat Perfect 5 kali untuk membuktikan dominasi Anda.',
                'trigger_event' => 'simulation_finished', 'condition_type' => 'perfect_runs', 'condition_value' => 5,
                'badge_name' => 'Corporate Titan', 'rarity' => 'platinum', 'xp_reward' => 4500, 'is_hidden' => true,
                'border_rarity' => 'platinum'
            ],
            [
                'code' => 'sim_perfect_10', 'title' => 'Business Tycoon', 'icon' => '🏦', 'category' => 'skill',
                'description' => 'Raih peringkat Perfect 10 kali.',
                'trigger_event' => 'simulation_finished', 'condition_type' => 'perfect_runs', 'condition_value' => 10,
                'badge_name' => 'Diamond Executive Halo', 'rarity' => 'diamond', 'xp_reward' => 8000, 'is_hidden' => true
            ],

            // ================= ENGAGEMENT =================
            [
                'code' => 'time_spent_1h', 'title' => 'Focused Session', 'icon' => '⏳', 'category' => 'engagement',
                'description' => 'Habiskan total 1 jam aktif.',
                'trigger_event' => 'time_spent', 'condition_type' => 'minutes', 'condition_value' => 60,
                'badge_name' => 'Bronze Hourglass Emblem', 'rarity' => 'bronze', 'xp_reward' => 100, 'is_hidden' => false
            ],
            [
                'code' => 'time_spent_10h', 'title' => 'Dedicated Analyst', 'icon' => '🕰️', 'category' => 'engagement',
                'description' => 'Habiskan 10 jam di platform.',
                'trigger_event' => 'time_spent', 'condition_type' => 'minutes', 'condition_value' => 600,
                'badge_name' => 'Silver Chronos Border', 'rarity' => 'silver', 'xp_reward' => 800, 'is_hidden' => false
            ],
            [
                'code' => 'time_spent_50h', 'title' => 'Deep Immersion', 'icon' => '📅', 'category' => 'engagement',
                'description' => 'Habiskan 50 jam di platform.',
                'trigger_event' => 'time_spent', 'condition_type' => 'minutes', 'condition_value' => 3000,
                'badge_name' => 'Gold Epoch Aura', 'rarity' => 'gold', 'xp_reward' => 2500, 'is_hidden' => false
            ],

            // ================= MASTERY & LEVELING =================
            [
                'code' => 'level_2', 'title' => 'Newbie', 'icon' => '🌱', 'category' => 'mastery',
                'description' => 'Mencapai Level 2.',
                'trigger_event' => 'xp_earned', 'condition_type' => 'level', 'condition_value' => 2,
                'badge_name' => 'Bronze Sprout', 'rarity' => 'bronze', 'xp_reward' => 0, 'is_hidden' => false
            ],
            [
                'code' => 'level_5', 'title' => 'Novice Initiate', 'icon' => '🍀', 'category' => 'mastery',
                'description' => 'Mencapai Level 5.',
                'trigger_event' => 'xp_earned', 'condition_type' => 'level', 'condition_value' => 5,
                'badge_name' => 'Bronze Seed Ring', 'rarity' => 'bronze', 'xp_reward' => 0, 'is_hidden' => false
            ],
            [
                'code' => 'level_10', 'title' => 'Rising Star', 'icon' => '⭐', 'category' => 'mastery',
                'description' => 'Capai Level 10.',
                'trigger_event' => 'xp_earned', 'condition_type' => 'level', 'condition_value' => 10,
                'badge_name' => 'Silver Star Frame', 'rarity' => 'silver', 'xp_reward' => 0, 'is_hidden' => false
            ],
            [
                'code' => 'level_25', 'title' => 'Seasoned Pro', 'icon' => '🎖️', 'category' => 'mastery',
                'description' => 'Capai Level 25.',
                'trigger_event' => 'xp_earned', 'condition_type' => 'level', 'condition_value' => 25,
                'badge_name' => 'Gold Professional Aura', 'rarity' => 'gold', 'xp_reward' => 0, 'is_hidden' => false
            ],
            [
                'code' => 'level_50', 'title' => 'Strategic Visionary', 'icon' => '💎', 'category' => 'mastery',
                'description' => 'Capai Level 50 untuk membuka wawasan strategis tingkat tinggi.',
                'trigger_event' => 'xp_earned', 'condition_type' => 'level', 'condition_value' => 50,
                'badge_name' => 'Strategic Visionary', 'rarity' => 'platinum', 'xp_reward' => 0, 'is_hidden' => true,
                'border_rarity' => 'mythic'
            ],
            [
                'code' => 'level_100', 'title' => 'Supreme Cuan', 'icon' => '🪐', 'category' => 'mastery',
                'description' => 'Penguasa mutlak ekonomi CuanCapital.',
                'trigger_event' => 'xp_earned', 'condition_type' => 'level', 'condition_value' => 100,
                'badge_name' => 'Supreme Cuan', 'rarity' => 'mythic', 'xp_reward' => 0, 'is_hidden' => true
            ],


        ];

        // 3. Process and Insert
        foreach ($achievementsData as $data) {
            $rarityParams = $rarityConfigs[$data['rarity']];
            
            $badge = Badge::create([
                'name'          => $data['badge_name'],
                'rarity'        => $data['rarity'],
                'rarity_weight' => $rarityParams['weight'],
            ]);

            $borderId = null;
            if (isset($data['border_rarity'])) {
                $borderId = \App\Models\BorderFrame::where('rarity', $data['border_rarity'])->value('id');
            }

            Achievement::create([
                'code'            => $data['code'],
                'title'           => $data['title'],
                'description'     => $data['description'],
                'icon'            => $data['icon'],
                'category'        => $data['category'],
                'trigger_event'   => $data['trigger_event'],
                'condition_type'  => $data['condition_type'],
                'condition_value' => $data['condition_value'],
                'badge_id'        => $badge->id,
                'border_frame_id' => $borderId,
                'xp_reward'       => $data['xp_reward'],
                'is_hidden'       => $data['is_hidden'],
            ]);
        }

        // 4. Seed Standalone Premium Badges (Do not tie to achievements)
        // These are reserved exclusively for future gamification/point systems

        // Expand Enum dynamically if necessary
        DB::statement("ALTER TABLE badges MODIFY COLUMN rarity ENUM('bronze','silver','gold','platinum','diamond','mythic','education','businessmen') NOT NULL DEFAULT 'bronze'");

        $premiumBadges = [
            // Education
            ['Scholar Elite', 'education', 70],
            ['Grand Master', 'education', 75],
            ['Summa Cum Laude', 'education', 80],
            ['Prodigy', 'education', 85],
            ['Valedictorian', 'education', 90],
            ['Magna Scholar', 'education', 100],
            // Businessmen
            ['The Chairman', 'businessmen', 41],
            ['Wolf of Wall St.', 'businessmen', 42],
            ['Tycoon', 'businessmen', 43],
            ['Board Member', 'businessmen', 44],
            ['The Closer', 'businessmen', 45],
            ['Venture Founder', 'businessmen', 46],
            ['Mogul Status', 'businessmen', 47]
        ];

        foreach ($premiumBadges as $pb) {
            try {
                Badge::create([
                    'name'          => $pb[0],
                    'rarity'        => $pb[1],
                    'rarity_weight' => $pb[2],
                ]);
            } catch (\Exception $e) {
                // Ignore silent duplicate errors
                continue;
            }
        }
    }
}
