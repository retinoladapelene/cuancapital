<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BorderFrame;

class BorderFrameSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BorderFrame::truncate();
        DB::statement("ALTER TABLE border_frames MODIFY COLUMN rarity ENUM('bronze', 'silver', 'gold', 'platinum', 'diamond', 'mythic', 'zodiac', 'element', 'universe') DEFAULT 'bronze'");
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $borders = [
            // Bronze
            [
                'name'             => 'Copper Ring',
                'rarity'           => 'bronze',
                'rarity_weight'    => 10,
                'css_class'        => 'border-badge-bronze',
                'icon'             => '🔩',
                'unlock_condition' => 'Selesaikan pelajaran pertama Anda.',
            ],
            // Silver
            [
                'name'             => 'Iron Sigil',
                'rarity'           => 'silver',
                'rarity_weight'    => 20,
                'css_class'        => 'border-badge-silver',
                'icon'             => '⚙️',
                'unlock_condition' => 'Selesaikan 10 sesi pelajaran untuk mengasah strategi.',
            ],
            // Gold
            [
                'name'             => 'Golden Aura',
                'rarity'           => 'gold',
                'rarity_weight'    => 30,
                'css_class'        => 'border-badge-gold',
                'icon'             => '✨',
                'unlock_condition' => 'Lulus dari modul Mini Course pertama Anda.',
            ],
            // Platinum
            [
                'name'             => 'Platinum Edge',
                'rarity'           => 'platinum',
                'rarity_weight'    => 40,
                'css_class'        => 'border-badge-platinum',
                'icon'             => '💠',
                'unlock_condition' => 'Raih peringkat Perfect 5 kali untuk membuktikan dominasi Anda.',
            ],
            // Diamond
            [
                'name'             => 'Diamond Halo',
                'rarity'           => 'diamond',
                'rarity_weight'    => 50,
                'css_class'        => 'border-badge-diamond',
                'icon'             => '💎',
                'unlock_condition' => 'Simpan 10 blueprint strategi bisnis.',
            ],
            // Mythic
            [
                'name'             => 'Supreme Crown',
                'rarity'           => 'mythic',
                'rarity_weight'    => 60,
                'css_class'        => 'border-badge-mythic',
                'icon'             => '👑',
                'unlock_condition' => 'Capai Level 50 untuk membuka wawasan strategis tingkat tinggi.',
            ],
            // ---------------- Zodiac ----------------
            ['name' => 'Aries Ring', 'rarity' => 'zodiac', 'rarity_weight' => 70, 'css_class' => 'zodiac-aries', 'icon' => '♈', 'unlock_condition' => 'Capai ranking top 10%.'],
            ['name' => 'Taurus Ring', 'rarity' => 'zodiac', 'rarity_weight' => 70, 'css_class' => 'zodiac-taurus', 'icon' => '♉', 'unlock_condition' => 'Miliki 5 aset produktif.'],
            ['name' => 'Gemini Ring', 'rarity' => 'zodiac', 'rarity_weight' => 70, 'css_class' => 'zodiac-gemini', 'icon' => '♊', 'unlock_condition' => 'Lakukan 50 simulasi profit.'],
            ['name' => 'Cancer Ring', 'rarity' => 'zodiac', 'rarity_weight' => 70, 'css_class' => 'zodiac-cancer', 'icon' => '♋', 'unlock_condition' => 'Konsisten login 30 hari.'],
            ['name' => 'Leo Ring', 'rarity' => 'zodiac', 'rarity_weight' => 70, 'css_class' => 'zodiac-leo', 'icon' => '♌', 'unlock_condition' => 'Capai net profit miliaran.'],
            ['name' => 'Virgo Ring', 'rarity' => 'zodiac', 'rarity_weight' => 70, 'css_class' => 'zodiac-virgo', 'icon' => '♍', 'unlock_condition' => 'Selesaikan seluruh modul edukasi.'],
            ['name' => 'Scorpio Ring', 'rarity' => 'zodiac', 'rarity_weight' => 70, 'css_class' => 'zodiac-scorpio', 'icon' => '♏', 'unlock_condition' => 'Selesaikan 10 blueprint.'],
            ['name' => 'Sagittarius Ring', 'rarity' => 'zodiac', 'rarity_weight' => 70, 'css_class' => 'zodiac-sagittarius', 'icon' => '♐', 'unlock_condition' => 'Eksplorasi seluruh fitur platform.'],
            ['name' => 'Capricorn Ring', 'rarity' => 'zodiac', 'rarity_weight' => 70, 'css_class' => 'zodiac-capricorn', 'icon' => '♑', 'unlock_condition' => 'Kumpulkan 10,000 XP.'],
            ['name' => 'Aquarius Ring', 'rarity' => 'zodiac', 'rarity_weight' => 70, 'css_class' => 'zodiac-aquarius', 'icon' => '♒', 'unlock_condition' => 'Bagikan blueprint Anda.'],
            
            // ---------------- Earth Elements ----------------
            ['name' => 'Api', 'rarity' => 'element', 'rarity_weight' => 80, 'css_class' => 'element-fire', 'icon' => '🔥', 'unlock_condition' => 'Simulasi profit berturut-turut.'],
            ['name' => 'Air', 'rarity' => 'element', 'rarity_weight' => 80, 'css_class' => 'element-water', 'icon' => '💧', 'unlock_condition' => 'Capai margin keuntungan stabil.'],
            ['name' => 'Tanah', 'rarity' => 'element', 'rarity_weight' => 80, 'css_class' => 'element-earth', 'icon' => '🌍', 'unlock_condition' => 'Miliki arus kas fundamental yang kuat.'],
            ['name' => 'Petir', 'rarity' => 'element', 'rarity_weight' => 80, 'css_class' => 'element-lightning', 'icon' => '⚡', 'unlock_condition' => 'Gunakan strategi pertumbuhan kilat.'],
            ['name' => 'Es', 'rarity' => 'element', 'rarity_weight' => 80, 'css_class' => 'element-ice', 'icon' => '❄️', 'unlock_condition' => 'Bertahan dari risiko simulasi.'],
            ['name' => 'Angin', 'rarity' => 'element', 'rarity_weight' => 80, 'css_class' => 'element-wind', 'icon' => '🌪️', 'unlock_condition' => 'Ciptakan strategi viral.'],
            ['name' => 'Hutan', 'rarity' => 'element', 'rarity_weight' => 80, 'css_class' => 'element-forest', 'icon' => '🌲', 'unlock_condition' => 'Tumbuhkan berbagai sumber cuan.'],
            ['name' => 'Badai', 'rarity' => 'element', 'rarity_weight' => 80, 'css_class' => 'element-storm', 'icon' => '⛈️', 'unlock_condition' => 'Melewati tantangan bisnis besar.'],
            ['name' => 'Laut Dalam', 'rarity' => 'element', 'rarity_weight' => 80, 'css_class' => 'element-sea', 'icon' => '🌊', 'unlock_condition' => 'Eksplorasi strategi mendalam.'],

            // ---------------- Universe ----------------
            ['name' => 'Solar System', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-solar', 'icon' => '☀️', 'unlock_condition' => 'Bangun ekosistem bisnis.'],
            ['name' => 'Black Hole', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-blackhole', 'icon' => '🕳️', 'unlock_condition' => 'Serap 100% market share di simulasi.'],
            ['name' => 'Nebula', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-nebula', 'icon' => '🌌', 'unlock_condition' => 'Ciptakan ide bisnis brilian.'],
            ['name' => 'Comet', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-comet', 'icon' => '☄️', 'unlock_condition' => 'Raih kenaikan profit super cepat.'],
            ['name' => 'Pulsar', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-pulsar', 'icon' => '💫', 'unlock_condition' => 'Pertahankan ritme cuan.'],
            ['name' => 'Galaxy', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-galaxy', 'icon' => '🌀', 'unlock_condition' => 'Miliki 10+ lini produk.'],
            ['name' => 'Asteroid', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-asteroid', 'icon' => '🪨', 'unlock_condition' => 'Atasi rintangan pasar.'],
            ['name' => 'Supernova', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-supernova', 'icon' => '💥', 'unlock_condition' => 'Exit plan fantastis.'],
            ['name' => 'Wormhole', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-wormhole', 'icon' => '🌀', 'unlock_condition' => 'Bypass kompetitor.'],
            ['name' => 'Magnetar', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-magnetar', 'icon' => '🧲', 'unlock_condition' => 'Tarik ribuan customer.'],
            ['name' => 'Dark Matter', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-darkmatter', 'icon' => '👁️', 'unlock_condition' => 'Kuasai hidden market.'],
            ['name' => 'Quasar', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-quasar', 'icon' => '✨', 'unlock_condition' => 'Menjadi pusat perputaran uang.'],
            ['name' => 'CMB', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-cmb', 'icon' => '📻', 'unlock_condition' => 'Pahami sejarah market.'],
            ['name' => 'Nursery', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-nursery', 'icon' => '🍼', 'unlock_condition' => 'Inkubasi ide startup.'],
            ['name' => 'Gravitational', 'rarity' => 'universe', 'rarity_weight' => 90, 'css_class' => 'universe-gravitational', 'icon' => '🌊', 'unlock_condition' => 'Beri impact besar pada industri.'],
        ];

        foreach ($borders as $border) {
            BorderFrame::create($border);
        }
    }
}
