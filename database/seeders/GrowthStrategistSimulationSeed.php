<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;
use App\Models\Simulation;
use App\Models\SimulationStep;
use App\Models\SimulationOption;

class GrowthStrategistSimulationSeed extends Seeder
{
    public function run(): void
    {
        // ── 1. Course capstone intermediate ───────────────────────────────
        Course::where('title', 'Growth Strategist Simulation')->delete();

        $course = Course::create([
            'title'         => 'Growth Strategist Simulation',
            'description'   => 'Intermediate capstone — uji integrasi skill dari 3 modul: Copywriting & Storytelling, Social Media Organic, dan Meta & Google Ads Mastery. Bukan kuis hafalan — ini Decision Intelligence Simulation.',
            'level'         => 'intermediate',
            'category'      => 'simulation',
            'xp_reward'     => 800,
            'lessons_count' => 0,
            'is_active'     => true,
        ]);

        // ── 2. Bersihkan simulation lama jika ada ─────────────────────────
        $old = Simulation::where('module_id', $course->id)->first();
        if ($old) {
            Schema::disableForeignKeyConstraints();
            SimulationOption::whereIn('step_id',
                SimulationStep::where('simulation_id', $old->id)->pluck('id')
            )->delete();
            SimulationStep::where('simulation_id', $old->id)->delete();
            $old->delete();
            Schema::enableForeignKeyConstraints();
        }

        // ── 3. Buat simulation ────────────────────────────────────────────
        $sim = Simulation::create([
            'module_id'        => $course->id,
            'title'            => 'Growth Strategist Simulation',
            'intro_text'       => "Selamat datang di Intermediate Capstone Simulation.\n\nKamu baru saja bergabung sebagai Growth Marketer untuk brand yang baru launch. Tidak ada blueprint — hanya data, intuisi yang terlatih, dan keputusan yang harus kamu buat sekarang.\n\nSetiap keputusan mempengaruhi 4 metrik:\n\n💰 Profit — margin dan kesehatan keuangan\n📣 Traffic — jangkauan dan volume audiens\n🏆 Brand — kekuatan persepsi di pasar\n💸 Budget — sumber daya yang tersisa\n\nStat awal:\nProfit: 50 | Traffic: 50 | Brand: 50 | Budget: 100\n\nScore akhir = (Profit × 0.4) + (Traffic × 0.3) + (Brand × 0.3)\n\nTarget: Growth Strategist (90+) | Perfect: profit≥80, traffic≥80, brand≥80\n\nBuat keputusan yang cerdas. Ini bukan teori.",
            'difficulty_level' => 'intermediate',
            'xp_reward'        => 800,
        ]);

        // ── 4. Semua stages ───────────────────────────────────────────────
        $steps = [

            // STAGE 1 — CAMPAIGN STRATEGY CHOICE
            [
                'question' => "STAGE 1 — CAMPAIGN STRATEGY CHOICE\n\nProduk baru appmu baru saja siap diluncurkan. Tim menunggu arahanmu. Budget tersedia tapi terbatas — setiap rupiah harus bekerja keras.\n\nDari semua yang kamu pelajari tentang traffic dan brand building, strategi launch mana yang akan kamu pilih?",
                'order'    => 1,
                'options'  => [
                    [
                        'label'    => 'Organic content — bangun audiens dengan konten dulu',
                        'effect'   => ['traffic' => 15, 'brand' => 15],
                        'feedback' => "Pilihan yang cerdas untuk brand jangka panjang. Konten organik membangun kepercayaan dan audiens yang loyal — mereka datang karena value, bukan karena iklan. Traffic tumbuh perlahan tapi brand equity-mu naik. Budget aman, tapi butuh sabar untuk melihat hasil yang signifikan. +Traffic 15, +Brand 15",
                    ],
                    [
                        'label'    => 'Paid ads langsung — beli traffic cepat dari hari pertama',
                        'effect'   => ['traffic' => 30, 'budget' => -40],
                        'feedback' => "Traffic melonjak instan — tapi budget terkuras 40 poin. Kamu membeli visibilitas, bukan membangunnya. Jika landing page dan offer belum dioptimasi, traffic ini bisa jadi sangat mahal per konversi. High risk, high speed. +Traffic 30, -Budget 40",
                    ],
                    [
                        'label'    => 'Influencer launch — leverage kepercayaan kreator yang sudah ada',
                        'effect'   => ['brand' => 25, 'budget' => -30],
                        'feedback' => "Brand langsung dapat social proof dari audiens yang sudah terpercaya. Orang membeli karena rekomendasi — dan rekomendasi influencer yang relevan adalah bentuk social proof terkuat. Budget berkurang tapi brand equity melonjak signifikan. +Brand 25, -Budget 30",
                    ],
                ],
            ],

            // STAGE 2 — COPYWRITING ANGLE
            [
                'question' => "STAGE 2 — COPYWRITING ANGLE\n\nSaatnya menentukan angle copy utama untuk semua materi marketing: iklan, caption, dan landing page.\n\nDari pelajaran tentang psychology of persuasion dan story selling — angle mana yang akan kamu pilih untuk brand barumu?",
                'order'    => 2,
                'options'  => [
                    [
                        'label'    => 'Emotional storytelling — cerita yang menyentuh dan membangun koneksi',
                        'effect'   => ['brand' => 20],
                        'feedback' => "Keputusan yang sophisticated. Storytelling membangun kepercayaan yang tidak bisa dibeli — dan brand yang dipercaya memiliki customer lifetime value yang jauh lebih tinggi. Conversion mungkin lebih lambat di awal, tapi loyalitas yang terbentuk bertahan lama. +Brand 20",
                    ],
                    [
                        'label'    => 'Discount driven — penawaran diskon untuk mendorong tindakan cepat',
                        'effect'   => ['traffic' => 25, 'profit' => -10],
                        'feedback' => "Traffic datang cepat karena diskon adalah motivator yang powerful. Tapi kamu baru melatih pasar untuk tidak membeli di harga normal. Setiap kali kamu berhenti diskon, traffic drop. Dan margin-mu sudah terpotong dari awal. +Traffic 25, -Profit 10",
                    ],
                    [
                        'label'    => 'Feature explanation — jelaskan spesifikasi dan keunggulan produk',
                        'effect'   => ['profit' => 15],
                        'feedback' => "Feature copy bekerja baik untuk audiens yang sudah aware dan sedang membandingkan produk — phase decision stage. Mereka yang membeli karena memahami feature biasanya memiliki ekspektasi yang realistic dan churn rate lebih rendah. Margin terjaga. +Profit 15",
                    ],
                ],
            ],

            // STAGE 3 — AUDIENCE TARGETING
            [
                'question' => "STAGE 3 — AUDIENCE TARGETING\n\nKampanye sudah berjalan. Sekarang saatnya setting audience untuk ads yang akan tumbuh.\n\nDari pelajaran targeting psychology dan retargeting funnel — audience mana yang akan kamu prioritaskan?",
                'order'    => 3,
                'options'  => [
                    [
                        'label'    => 'Broad audience — jangkau sebanyak mungkin orang',
                        'effect'   => ['traffic' => 20, 'profit' => -10],
                        'feedback' => "Reach naik cepat dan data berdatangan. Tapi kamu membuang budget untuk menjangkau orang yang tidak akan pernah beli. CPM mungkin murah, tapi CPA-mu membengkak karena conversion rate dari audience yang tidak targeted sangat rendah. +Traffic 20, -Profit 10",
                    ],
                    [
                        'label'    => 'Niche targeting — sangat spesifik ke persona yang sudah didefinisikan',
                        'effect'   => ['profit' => 20],
                        'feedback' => "Ini cara berpikir seorang performance marketer. Audiens yang spesifik artinya relevance score tinggi, CPM turun, dan conversion rate naik. Kamu membayar lebih sedikit untuk hasil yang lebih baik. Budget efisien, profit terjaga. +Profit 20",
                    ],
                    [
                        'label'    => 'Retargeting — fokus ke orang yang sudah mengenal brand',
                        'effect'   => ['profit' => 15, 'traffic' => 10],
                        'feedback' => "Warm audience adalah goldmine. Mereka sudah melewati tahap awareness — yang dibutuhkan hanya sedikit dorongan untuk convert. ROAS dari retargeting hampir selalu lebih tinggi dari cold audience. Keputusan yang cerdas dan efisien. +Profit 15, +Traffic 10",
                    ],
                ],
            ],

            // STAGE 4 — CONTENT OPTIMIZATION
            [
                'question' => "STAGE 4 — CONTENT OPTIMIZATION\n\nData masuk. Satu konten organik yang kamu publish perform jauh di bawah ekspektasi — watch time rendah, engagement minimal.\n\nDari pelajaran tentang hook visual, format konten, dan engagement loop — apa yang akan kamu lakukan?",
                'order'    => 4,
                'options'  => [
                    [
                        'label'    => 'Edit hook — ganti 3 detik pertama dengan hook yang lebih kuat',
                        'effect'   => ['traffic' => 15],
                        'feedback' => "Diagnosis yang tepat! Hook adalah gate keeper dari semua konten — jika 3 detik pertama gagal, tidak ada yang akan melihat konten seindah apapun isinya. Dengan memperbaiki hook, watch time membaik, algoritma membaca sinyal positif, dan distribusi meningkat. +Traffic 15",
                    ],
                    [
                        'label'    => 'Ganti format — ubah strukturnya dari narasi ke list atau tutorial',
                        'effect'   => ['brand' => 15],
                        'feedback' => "Format memang krusial. Konten yang sama dalam format berbeda bisa perform 2-5x lebih baik. Dengan beralih ke format yang lebih mudah dicerna, brand-mu terlihat lebih profesional dan konten lebih shareable — meningkatkan brand perception secara keseluruhan. +Brand 15",
                    ],
                    [
                        'label'    => 'Biarkan — mungkin algoritmanya sedang tidak berpihak',
                        'effect'   => ['brand' => -15],
                        'feedback' => "Ini adalah bias 'menyalahkan algoritma' yang berbahaya. Algoritma tidak bias — ia memberi distribusi kepada konten yang disukai audiens. Membiarkan konten underperform tanpa aksi berarti kamu melewatkan learning yang berharga dan audience perception terhadap brand-mu melemah. -Brand 15",
                    ],
                ],
            ],

            // STAGE 5 — BUDGET SCALING DECISION
            [
                'question' => "STAGE 5 — BUDGET SCALING DECISION\n\nSatu kampanye iklan perform sangat baik. ROAS-nya di atas target. Semua sinyal positif.\n\nDari pelajaran tentang budget scaling strategy — keputusan mana yang akan kamu buat sekarang?",
                'order'    => 5,
                'options'  => [
                    [
                        'label'    => 'Scale 20-30% — naikkan budget bertahap sesuai framework',
                        'effect'   => ['profit' => 20],
                        'feedback' => "Ini adalah keputusan seorang profesional. Scaling bertahap menjaga campaign tetap dalam fase learning yang stabil — platform tidak perlu mengeksplor audiens baru secara agresif. ROAS tetap terjaga, profit meningkat secara konsisten tanpa risiko kampanye collapse. +Profit 20",
                    ],
                    [
                        'label'    => 'Scale 200% — gas pol sekarang, manfaatkan momentum',
                        'effect'   => ['traffic' => 30, 'profit' => -20],
                        'feedback' => "Emosi menang atas strategi. Traffic memang naik karena budget besar — tapi kampanye keluar dari fase learning dan platform mengeksplor audiens yang tidak teroptimasi. ROAS hancur, profit turun drastis. Momentum itu nyata, tapi cara memanfaatkannya ada ilmunya. +Traffic 30, -Profit 20",
                    ],
                    [
                        'label'    => 'Tidak scale — pertahankan apa yang ada, jangan ambil risiko',
                        'effect'   => ['brand' => -10],
                        'feedback' => "Terlalu konservatif saat peluang ada. Campaign yang profitable adalah sinyal pasar yang jelas — tidak memanfaatkan momentum berarti kamu membiarkan kompetitor mengisi ruang yang seharusnya milikmu. Ketakutan berlebihan melemahkan brand positioning di pasar. -Brand 10",
                    ],
                ],
            ],

            // STAGE 6 — CRISIS SCENARIO
            [
                'question' => "STAGE 6 — CRISIS SCENARIO\n\nAlert masuk. CTR campaign utama drop drastis dalam 48 jam terakhir. ROAS mulai melemah. Tim panik.\n\nDari semua yang kamu pelajari tentang ads optimization loop dan creative testing — apa langkah pertama yang akan kamu ambil?",
                'order'    => 6,
                'options'  => [
                    [
                        'label'    => 'Refresh creative — ganti visual dan copy dengan variasi baru',
                        'effect'   => ['traffic' => 20],
                        'feedback' => "Diagnosis yang tepat. CTR drop yang tiba-tiba paling sering disebabkan oleh creative fatigue — audiens sudah terlalu sering melihat iklan yang sama dan tidak bereaksi lagi. Refresh creative memberikan stimulus baru kepada audiens dan algoritma. Traffic kembali sehat. +Traffic 20",
                    ],
                    [
                        'label'    => 'Ganti audience — mungkin audience lama sudah jenuh',
                        'effect'   => ['profit' => 15],
                        'feedback' => "Audience saturation bisa terjadi di niche yang sempit. Dengan mengekspand ke lookalike audience baru atau segmen yang belum tersentuh, kamu menemukan demand baru tanpa harus memulai dari nol. Profit terjaga karena kamu tidak buang budget ke audience yang sudah tidak responsif. +Profit 15",
                    ],
                    [
                        'label'    => 'Stop campaign — lebih aman matikan dulu daripada buang uang',
                        'effect'   => ['brand' => -20],
                        'feedback' => "Panic stop adalah respons emosional, bukan strategis. Mematikan campaign yang masih dalam fase pemulihan menghancurkan semua learning yang sudah dikumpulkan. Audience momentum hilang, brand presence di platform turun, dan ketika kamu re-launch, kamu mulai dari nol lagi dengan biaya lebih tinggi. -Brand 20",
                    ],
                ],
            ],

        ];

        foreach ($steps as $stepData) {
            $step = SimulationStep::create([
                'simulation_id' => $sim->id,
                'question'      => $stepData['question'],
                'order'         => $stepData['order'],
            ]);

            foreach ($stepData['options'] as $opt) {
                SimulationOption::create([
                    'step_id'       => $step->id,
                    'label'         => $opt['label'],
                    'effect_json'   => $opt['effect'],
                    'feedback_text' => $opt['feedback'],
                ]);
            }
        }

        $this->command->info("✅ Growth Strategist Simulation — 6 stages, 18 options berhasil di-seed.");
        $this->command->info("   Simulation ID: {$sim->id} | Course ID: {$course->id}");
        $this->command->info("   Score formula: (profit×0.4) + (traffic×0.3) + (brand×0.3)");
        $this->command->info("   Tiers: <60 Beginner | 60-74 Ads Operator | 75-89 Performance Marketer | 90+ Growth Strategist");
        $this->command->info("   Perfect: profit≥80 AND traffic≥80 AND brand≥80 → badge Performance Mastermind + 300 Bonus XP");
    }
}
