<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;
use App\Models\Simulation;
use App\Models\SimulationStep;
use App\Models\SimulationOption;

class ScalingCrisisSimulationSeed extends Seeder
{
    public function run(): void
    {
        // ── 1. Course capstone expert ─────────────────────────────────────
        Course::where('title', 'Scaling Crisis — CEO di Titik Kritis')->delete();

        $course = Course::create([
            'title'         => 'Scaling Crisis — CEO di Titik Kritis',
            'description'   => 'Expert capstone simulation — uji integrasi skill dari 2 modul expert: Membangun Tim & Sistem dan Strategic Decision Making. 6 keputusan kritis dari CEO yang bisnisnya baru scale dari 50 juta ke 400 juta/bulan.',
            'level'         => 'expert',
            'category'      => 'simulation',
            'xp_reward'     => 1500,
            'lessons_count' => 0,
            'is_active'     => true,
        ]);

        // ── 2. Bersihkan simulation lama ──────────────────────────────────
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
            'title'            => 'Scaling Crisis — CEO di Titik Kritis',
            'intro_text'       => "SELAMAT DATANG DI RUANG KEPUTUSAN.\n\nKamu adalah founder bisnis digital yang dalam 8 bulan terakhir berhasil menumbuhkan revenue dari Rp 50 juta ke Rp 400 juta per bulan. Growthnya nyata — tapi cepat. Mungkin terlalu cepat.\n\nSekarang, satu per satu, masalah mulai muncul:\n→ Tim mulai overload dan sinyal burnout terlihat jelas\n→ Customer complaint naik 40% dalam 6 minggu terakhir\n→ Growth mulai melambat karena kapasitas sistem tidak mengikuti\n→ Cashflow mulai ketat karena biaya operasional ikut scale\n→ Dan satu hal yang mengubah semua tekanan: investor serius tertarik masuk — tapi mereka ingin melihat sistemmu stabil dalam 90 hari.\n\nKamu harus membuat 6 keputusan eksekutif yang menentukan apakah momentum ini menjadi fondasi bisnis yang scalable — atau titik collapse yang mahal.\n\n4 metrik yang akan kamu kelola:\n💰 Profit — kesehatan finansial bisnis\n👥 Team Health — kondisi dan kapasitas tim\n⚡ Execution Speed — kecepatan dan efisiensi operasional\n🏆 Brand Strength — persepsi dan posisi di pasar\n\nStat awal:\nProfit: 60 | Team Health: 55 | Execution Speed: 70 | Brand: 65\n\nScore = (Profit×0.35) + (Team Health×0.25) + (Execution Speed×0.25) + (Brand×0.15)\n\nPassing: 70 | Perfect: 90\nTiers: <50 Overwhelmed Founder | 50-69 Tactical Operator | 70-84 Strategic Leader | 85-94 Elite Builder | 95+ Visionary Architect\n\nInvestor menunggu. Tim menunggu. Putuskan.",
            'difficulty_level' => 'expert',
            'xp_reward'        => 1500,
        ]);

        // ── 4. 6 Decision Stages ──────────────────────────────────────────
        $steps = [

            // STEP 1 — BOTTLENECK CRISIS
            [
                'question' => "STEP 1 — BOTTLENECK CRISIS\n\nTim operasional yang sebelumnya handle 60 order per hari sekarang menerima 280+ order per hari tanpa penambahan kapasitas. Error rate sudah naik 3x. Dua anggota tim minta bicara dan terlihat sangat kelelahan.\n\nIni adalah krisis pertama scaling — dan cara kamu menghadapinya akan mewarnai semua keputusan selanjutnya.\n\nSebagai CEO dengan mindset hiring framework dan bottleneck detection, apa yang akan kamu lakukan?",
                'order'    => 1,
                'options'  => [
                    [
                        'label'    => 'Hire cepat siapa saja yang tersedia — kita butuh tangan tambahan sekarang',
                        'effect'   => ['team_health' => 10, 'profit' => -10, 'execution_speed' => -5],
                        'feedback' => "Warm body hiring dalam kondisi panik. Tim memang sedikit lebih ringan karena ada orang baru — tapi orang yang belum terlatih di sistem yang sudah kewalahan justru menambah koordinasi overhead. Execution speed turun karena onboarding di tengah krisis. Dan profit tersedot biaya rekrutmen tanpa SOP yang jelas. Tim health sedikit naik karena setidaknya ada sinyal bahwa kamu peduli. +Team Health 10, -Profit 10, -Execution Speed 5",
                    ],
                    [
                        'label'    => 'Audit workflow dulu sebelum hire — temukan dan eliminasi bottleneck nyata',
                        'effect'   => ['execution_speed' => 15, 'profit' => -5],
                        'feedback' => "Keputusan yang menunjukkan strategic maturity. Sebelum menambah orang ke dalam sistem yang broken, kamu memperbaiki sistemnya. Audit sering mengungkap bahwa 60% workload berasal dari 3-4 proses yang bisa diotomasi atau di-streamline. Execution speed naik signifikan — profit sedikit berkurang untuk waktu dan tool yang digunakan untuk audit. Ini adalah investasi yang benar di titik kritis ini. +Execution Speed 15, -Profit 5",
                    ],
                    [
                        'label'    => 'Handle sendiri sementara — founder terjun langsung agar tim sedikit lega',
                        'effect'   => ['execution_speed' => 5, 'team_health' => -15],
                        'feedback' => "Founder Trap dalam skenario paling klasiknya. Kamu kembali ke mode operator ketika seharusnya berada di mode strategist. Tim memang sedikit terbantu operasional — tapi mereka melihat sinyalnya: pemimpin kita tidak bisa memimpin keluar dari krisis ini, ia malah terjun ke dalamnya. Team health turun karena kepercayaan pada sistem dan kepemimpinan melemah. +Execution Speed 5, -Team Health 15",
                    ],
                ],
            ],

            // STEP 2 — LEADERSHIP STRUCTURE
            [
                'question' => "STEP 2 — LEADERSHIP STRUCTURE BREAKDOWN\n\nDengan tim yang berkembang dari 5 ke 18 orang dalam 6 bulan, struktur reporting sudah tidak jelas. 12 orang masih langsung report ke kamu. Setiap hari ada 30-40 pesan di WhatsApp yang menunggu responmu sebelum tim bisa bergerak. Bottleneck-nya adalah kamu sendiri.\n\nIni adalah tantangan Role Clarity Architecture dan Delegation Engineering yang pernah kamu pelajari — tapi sekarang dieksekusi dalam kondisi tekanan tinggi.\n\nApa keputusanmu?",
                'order'    => 2,
                'options'  => [
                    [
                        'label'    => 'Tambahkan manager layer — angkat 2 senior jadi team lead dengan authority yang jelas',
                        'effect'   => ['execution_speed' => 10, 'profit' => -10, 'team_health' => 10],
                        'feedback' => "Keputusan yang menunjukkan pemahaman Role Clarity Architecture yang matang. Dengan manager layer yang jelas, bottleneck informasi dari kamu terputus. Tim punya pemimpin yang bisa diakses tanpa menunggu CEO. Profit berkurang untuk kompensasi manager — tapi ini adalah leverage investment terbaik yang bisa kamu buat sekarang. Execution speed naik karena keputusan bisa dibuat lebih cepat. Team health naik karena tim merasa ada struktur yang jelas. +Execution Speed 10, -Profit 10, +Team Health 10",
                    ],
                    [
                        'label'    => 'Semua tetap report ke kamu — centralised control memastikan kualitas terjaga',
                        'effect'   => ['profit' => 5, 'team_health' => -15],
                        'feedback' => "Ini adalah rasionalisasi control freak yang sangat umum di kalangan founder. Ya, kualitas terjaga di satu titik — tapi kamu baru saja menjadikan dirimu bottleneck permanen. Tim yang tidak bisa bergerak tanpa persetujuanmu akan berhenti berinisiatif. Talent terbaik yang tidak diberi kepercayaan akan mencari tempat lain. Profit sedikit naik karena tidak ada biaya manager — tapi team health kolaps karena micromanagement. +Profit 5, -Team Health 15",
                    ],
                    [
                        'label'    => 'Biarkan fleksibel dulu — structure terlalu formal akan mematikan kreativitas',
                        'effect'   => ['execution_speed' => -10],
                        'feedback' => "Slippery slope reasoning. Fleksibilitas yang bekerja di tim 5 orang menciptakan chaos di tim 18 orang. Tanpa clarity siapa yang memutuskan apa, setiap keputusan menjadi negosiasi informal yang membuang waktu. Execution speed turun drastis bukan karena kamu tidak bekerja keras — tapi karena sistem tidak ada. -Execution Speed 10",
                    ],
                ],
            ],

            // STEP 3 — OPPORTUNITY DECISION
            [
                'question' => "STEP 3 — OPPORTUNITY DECISION\n\nDi tengah menata internal, ada yang menghubungi: influencer dengan 4.2 juta followers menawarkan kolaborasi eksklusif. Tawaran ini hanya berlaku 72 jam — tim nya sedang mempertimbangkan beberapa brand lain. Ini peluang besar untuk brand exposure, tapi membutuhkan Rp 80 juta dan alokasi tim yang sudah overloaded.\n\nOpportunity Scoring System dan Second Order Thinking-mu diuji di sini — dalam kondisi tekanan waktu dan FOMO yang nyata.",
                'order'    => 3,
                'options'  => [
                    [
                        'label'    => 'Gas langsung — momentum dan brand exposure seperti ini jarang datang dua kali',
                        'effect'   => ['brand_strength' => 20, 'profit' => -15],
                        'feedback' => "FOMO-driven decision — dan kadang berhasil. Brand strength naik signifikan dari exposure besar. Tapi kamu baru saja mengeluarkan Rp 80 juta di saat cashflow sedang ketat dan tim sudah overloaded. Second order: tim yang sudah overloaded sekarang harus memproduksi konten kolaborasi, handle lonjakan inquiry, dan maintain kualitas setelah exposure. Eksekusi yang tidak siap bisa mengubah brand boost menjadi brand damage. +Brand Strength 20, -Profit 15",
                    ],
                    [
                        'label'    => 'Hitung ROI dulu dalam 24 jam — minta proposal detail dan simulasikan dampak revenue',
                        'effect'   => ['profit' => 10, 'execution_speed' => -5],
                        'feedback' => "Keputusan yang menunjukkan Data vs Intuisi mastery. 72 jam bukan terlalu sempit untuk due diligence singkat — kamu meminta angka: berapa average CPM influencer ini? Berapa konversi rate campaign serupa? Berapa cashflow yang dibutuhkan? Dari kalkulasi ini, kamu membuat keputusan berbasis data, bukan excitement. Execution speed sedikit turun karena tim dialihkan untuk 24 jam analisis. Tapi profit naik karena beberapa biaya dapat dinegosikan atau dihindari berdasarkan data. +Profit 10, -Execution Speed 5",
                    ],
                    [
                        'label'    => 'Tolak — fokus internal dulu, brand building bisa nanti setelah sistem stabil',
                        'effect'   => ['execution_speed' => 10, 'brand_strength' => -5],
                        'feedback' => "Pilihan yang konservatif tapi defensible. Kamu memilih tidak menambah beban di saat sistem sedang dirapikan. Execution speed naik karena tidak ada distraksi. Brand strength sedikit turun — bukan karena kamu menjadi lebih buruk, tapi karena kompetitor yang mengambil slot itu akan mendapat visibility lebih. Pilihan ini benar jika internal chaos-mu benar-benar parah — tapi bisa jadi terlalu konservatif jika sistemmu sudah 70% stabil. +Execution Speed 10, -Brand Strength 5",
                    ],
                ],
            ],

            // STEP 4 — UNDERPERFORM EMPLOYEE
            [
                'question' => "STEP 4 — UNDERPERFORMING EMPLOYEE\n\nSalah satu anggota tim yang sudah 8 bulan bersama kamu menunjukkan penurunan performa signifikan dalam 6 minggu terakhir: KPI tidak tercapai 3 bulan berturut-turut, response rate ke klien melambat, dan beberapa kesalahan yang mempengaruhi customer experience.\n\nTim lain sudah menyadari ini — dan cara kamu menanganinya akan menjadi signal tentang culture dan leadership yang kamu bangun.\n\nKultur engineering dan leadership maturity-mu diuji sekarang.",
                'order'    => 4,
                'options'  => [
                    [
                        'label'    => 'Terminate segera — performa buruk yang dibiarkan meracuni standar seluruh tim',
                        'effect'   => ['execution_speed' => 10, 'team_health' => -10],
                        'feedback' => "Ada kebenaran dalam logika ini — tapi eksekusinya terlalu terburu-buru dan berbahaya jika tidak ada proses yang proper. Execution speed naik karena kapasitas tim tidak lagi terbebani underperformer. Tapi team health turun karena tim melihat bahwa kamu terminate tanpa coaching, tanpa PIP, tanpa proses yang fair — dan mereka bertanya-tanya: apakah hal yang sama akan terjadi kepada saya? Culture 'execute or exit' tanpa safety net menciptakan ketakutan, bukan high performance. +Execution Speed 10, -Team Health 10",
                    ],
                    [
                        'label'    => 'Buat coaching plan 2 minggu — identifikasi root cause dan beri kesempatan yang fair',
                        'effect'   => ['team_health' => 15, 'execution_speed' => -5],
                        'feedback' => "Keputusan yang menunjukkan Culture Engineering yang matang. Sebelum terminate, kamu mencari tahu: apakah ini masalah skill, will, atau circumstance? Coaching plan yang terstruktur memberikan clarity — jika performa tidak membaik setelah 2 minggu dengan support yang jelas, keputusan terminate menjadi jauh lebih defensible secara etis dan legal. Tim melihat bahwa leader mereka fair — dan itu menciptakan psychological safety yang menghasilkan high performance. +Team Health 15, -Execution Speed 5",
                    ],
                    [
                        'label'    => 'Biarkan saja dulu — mungkin sedang ada masalah personal dan akan membaik sendiri',
                        'effect'   => ['team_health' => -15],
                        'feedback' => "Penghindaran yang mahal. Biarkan saja bukan empati — ini adalah ketidaktegasan yang mengorbankan seluruh tim demi kenyamanan satu interaksi awkward. Tim yang lain melihat: KPI tidak tercapai tidak ada konsekuensinya. Standar yang sebelumnya ada sekarang terasa fleksibel. Yang paling capable justru akan yang pertama frustrasi dan pergi — karena mereka bisa. -Team Health 15",
                    ],
                ],
            ],

            // STEP 5 — SCALING DECISION
            [
                'question' => "STEP 5 — SCALING DECISION\n\nTrafik organik tiba-tiba naik drastis — konten yang diposting 2 minggu lalu viral di komunitas bisnis online. Dalam 48 jam, kamu mendapat 3.000 pengunjung baru per hari dibanding biasanya 400. Conversion rate masih solid. Ini adalah momen yang bisa dikapitalisasi.\n\nSemua skill dari Strategic Decision Making berkonvergensi di sini: pattern recognition, decision speed, risk mapping, dan second order thinking.\n\nApa keputusanmu CEO?",
                'order'    => 5,
                'options'  => [
                    [
                        'label'    => 'Scale ads sekarang — gunakan momentum, gas marketing budget semaksimal mungkin',
                        'effect'   => ['profit' => 20, 'brand_strength' => -5],
                        'feedback' => "High-risk high-reward play. Profit naik karena volume konversi melonjak ketika traffic diperbesar. Tapi second order thinking harus kamu terapkan: jika sistem fulfillment, CS, dan kualitas belum diperkuat dari step sebelumnya — lonjakan ini bisa menciptakan experience buruk yang viral negatif. Kamu sedang membeli traffic ke dalam sistem yang mungkin belum sepenuhnya siap. Profit naik tapi brand strength sedikit terkikis dari risiko execution. +Profit 20, -Brand Strength 5",
                    ],
                    [
                        'label'    => 'Scale system dulu dalam 1 minggu — perkuat kapasitas sebelum amplifikasi traffic',
                        'effect'   => ['execution_speed' => 15, 'profit' => -5],
                        'feedback' => "Keputusan yang paling systems-thinking. Kamu memilih untuk tidak terbawa euforia viral moment dan menyadari bahwa scale tanpa sistem hanya memperbesar chaos. Dalam 1 minggu, kamu memperkuat CS capacity, memastikan fulfillment siap, dan memverifikasi bahwa kualitas bisa maintain di 3x volume. Profit sedikit berkurang karena ada delay dalam kapitalisasi — tapi execution speed naik karena sistemmu kini siap menerima scale yang sesungguhnya. +Execution Speed 15, -Profit 5",
                    ],
                    [
                        'label'    => 'Pause growth — jaga kualitas dulu, traffic organik ini tidak membutuhkan intervensi kita',
                        'effect'   => ['profit' => -10, 'team_health' => 10],
                        'feedback' => "Terlalu pasif untuk momen yang langka ini. Viral moment memiliki half-life yang sangat pendek — algoritma dan perhatian audiens tidak menunggu. Dengan tidak mengkapitalisasi momentum, kamu meninggalkan konversi di atas meja sementara kompetitor yang lebih agresif mungkin sedang mengiklan ke audience yang sama. Team health naik karena tidak ada tekanan tambahan — tapi profit turun karena opportunity cost yang sangat nyata. -Profit 10, +Team Health 10",
                    ],
                ],
            ],

            // STEP 6 — FINAL EXECUTIVE DECISION
            [
                'question' => "STEP 6 — FINAL EXECUTIVE DECISION\n\nTiga bulan telah berlalu. Sistem lebih stabil. Tim lebih terstruktur. Investor masih tertarik.\n\nSaat meeting final, investor meminta hal yang jarang: bukan pitch deck — tapi keputusan. Mereka ingin tahu di mana kamu akan memfokuskan semua resources selama 90 hari ke depan sebagai komitmen strategis yang akan kamu pertanggungjawabkan:\n\nA — Profitabilitas\nB — Market domination\nC — Infrastructure\n\nIni adalah keputusan yang menguji semua yang telah kamu pelajari tentang strategic thinking, leveraged decisions, dan second order consequences. Tidak ada jawaban universal — hanya jawaban yang paling aligned dengan kondisi bisnis nyatamu.",
                'order'    => 6,
                'options'  => [
                    [
                        'label'    => 'Profitabilitas — perkuat margin, kurangi waste, pastikan bisnis menghasilkan profit bersih',
                        'effect'   => ['profit' => 20, 'brand_strength' => -10],
                        'feedback' => "Keputusan yang menunjukkan financial maturity. Investor dari sektor PE dan debt financing biasanya menyukai ini — profitabilitas yang terbukti adalah kredensial yang berbicara sendiri. Profit naik signifikan dari efisiensi dan margin improvement. Brand strength sedikit turun karena dalam pursuit profitabilitas, marketing dan inovasi kadang dikurangi. Pilihan terbaik jika cashflow-mu sedang tight dan kamu butuh proof of financial health untuk term sheet yang baik. +Profit 20, -Brand Strength 10",
                    ],
                    [
                        'label'    => 'Market domination — agresif ekspansi, kuasai mindshare sebelum kompetitor mengisi ruang',
                        'effect'   => ['brand_strength' => 20, 'profit' => -15],
                        'feedback' => "Bold play yang disegani oleh investor growth-oriented. Brand strength melonjak ketika kamu agresif mengisi pasar dengan konten, partnership, dan presence. Tapi profit berkurang karena market domination butuh investasi. Second order: brand yang kuat akan memudahkan acquisition customer berikutnya dengan biaya lebih rendah. Pilihan terbaik jika pasar-mu masih open dan window of opportunity belum tertutup oleh kompetitor yang lebih besar. +Brand Strength 20, -Profit 15",
                    ],
                    [
                        'label'    => 'Infrastructure — bangun sistem, SOP, tim, dan teknologi yang siap untuk scale 10x',
                        'effect'   => ['execution_speed' => 20, 'profit' => -10],
                        'feedback' => "Keputusan yang paling sistem-oriented dan yang paling sulit dijual kepada investor yang mengukur nilai berdasarkan GMV atau revenue growth saat ini. Namun ini adalah pilihan yang membangun bisnis yang benar-benar tahan lama. Execution speed melonjak ketika sistem siap. Investor strategic jangka panjang akan sangat menghargai ini — karena bisnis dengan infrastruktur kuat adalah yang paling aman untuk diinvestasikan. +Execution Speed 20, -Profit 10",
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
                // Only keep known stat keys
                $statKeys   = ['profit', 'team_health', 'execution_speed', 'brand_strength'];
                $cleanEffect = [];
                foreach ($statKeys as $key) {
                    if (isset($opt['effect'][$key])) {
                        $cleanEffect[$key] = $opt['effect'][$key];
                    }
                }

                SimulationOption::create([
                    'step_id'       => $step->id,
                    'label'         => $opt['label'],
                    'effect_json'   => $cleanEffect,
                    'feedback_text' => $opt['feedback'],
                ]);
            }
        }

        $this->command->info("✅ Scaling Crisis — CEO di Titik Kritis — 6 stages, 18 options berhasil di-seed.");
        $this->command->info("   Simulation ID: {$sim->id} | Course ID: {$course->id}");
        $this->command->info("   4 Stats: profit(×0.35), team_health(×0.25), execution_speed(×0.25), brand_strength(×0.15)");
        $this->command->info("   Starting: Profit 60 | Team Health 55 | Exec Speed 70 | Brand 65");
        $this->command->info("   Tiers: <50 Overwhelmed | 50-69 Tactical | 70-84 Strategic Leader | 85-94 Elite Builder | 95+ Visionary Architect");
    }
}
