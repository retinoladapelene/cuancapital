<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;
use App\Models\Simulation;
use App\Models\SimulationStep;
use App\Models\SimulationOption;

class BusinessLaunchSimulationSeed extends Seeder
{
    public function run(): void
    {
        // ── 1. Buat / temukan course capstone ─────────────────────────────
        Course::where('title', 'Business Launch Simulation')->delete();

        $course = Course::create([
            'title'         => 'Business Launch Simulation',
            'description'   => 'Capstone simulation untuk menguji pemahamanmu dari 3 modul beginner: Dasar Bisnis Digital, Product Ideation, dan Branding. Bukan quiz hafalan — ini adalah Decision Training Module.',
            'level'         => 'beginner',
            'category'      => 'simulation',
            'xp_reward'     => 500,
            'lessons_count' => 0,
            'is_active'     => true,
        ]);

        // ── 2. Hapus simulation lama untuk course ini ─────────────────────
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
            'title'            => 'Business Launch Simulation',
            'intro_text'       => "Kamu baru saja memutuskan membuka bisnis online pertama. Modal terbatas dan kompetitor banyak. Tidak ada yang akan memandu langkah demi langkah—hanya keputusanmu sendiri yang menentukan nasib bisnis ini.\n\nSetiap pilihan yang kamu buat akan mempengaruhi tiga metrik utama: Profit (kesehatan finansial), Traffic (potensi pelanggan), dan Brand (reputasi di pasar).\n\nStat awal: Profit: 50, Traffic: 50, Brand: 50. Buat keputusan yang cerdas. Setiap tahap punya konsekuensi nyata.",
            'difficulty_level' => 'beginner',
            'xp_reward'        => 500,
        ]);

        // ── 4. Seed semua steps + options ─────────────────────────────────
        $steps = [

            // STAGE 1 — PRODUCT DECISION
            [
                'question' => "STAGE 1 — PRODUCT DECISION\n\nKamu harus memutuskan produk pertama yang akan dijual. Ingat: pilihan ini menentukan apakah traffic-mu organik atau kamu harus bersaing di laut merah kompetitor.\n\nProduk apa yang akan kamu jual?",
                'order'    => 1,
                'options'  => [
                    [
                        'label'         => 'Produk trending — ikut tren yang sedang viral',
                        'effect'        => ['traffic' => 20, 'brand' => -10],
                        'feedback'      => "Traffic melonjak karena demand pasar tinggi — tapi brand-mu tidak punya identitas yang kuat. Kamu berenang di lautan kompetitor yang menjual hal yang sama. Tren tidak akan selamanya ada. +Traffic 20, -Brand 10",
                    ],
                    [
                        'label'         => 'Produk niche — solusi spesifik untuk segmen kecil',
                        'effect'        => ['brand' => 15, 'traffic' => -5],
                        'feedback'      => "Brand-mu langsung terasa relevan dan unik di mata target market yang spesifik. Traffic lebih kecil di awal, tapi audiens yang datang jauh lebih qualified. +Brand 15, -Traffic 5",
                    ],
                    [
                        'label'         => 'Produk umum — dijual ke semua orang',
                        'effect'        => ['profit' => 10],
                        'feedback'      => "Margin sedikit lebih baik karena biaya produksi lebih rendah, tapi kamu tidak memiliki positioning yang jelas. Kamu berbicara ke semua orang — dan tidak ada yang benar-benar mendengar. +Profit 10",
                    ],
                ],
            ],

            // STAGE 2 — PRICING STRATEGY
            [
                'question' => "STAGE 2 — PRICING STRATEGY\n\nProduk sudah ditentukan. Sekarang: berapa harga yang akan kamu pasang?\n\nIngat dari modul Branding: harga bukan sekadar angka — harga adalah sinyal persepsi value di benak customer.\n\nStrategi harga apa yang kamu pilih?",
                'order'    => 2,
                'options'  => [
                    [
                        'label'         => 'Diskon besar — daya tarik harga murah untuk traction awal',
                        'effect'        => ['traffic' => 25, 'profit' => -20],
                        'feedback'      => "Banyak orang tertarik karena harga murah — tapi margin-mu hancur. Kamu melatih pasar untuk tidak membeli di harga normal. Ketika diskon berakhir, traffic akan menghilang bersamanya. +Traffic 25, -Profit 20",
                    ],
                    [
                        'label'         => 'Harga premium — mencerminkan kualitas dan brand value',
                        'effect'        => ['brand' => 20, 'traffic' => -15],
                        'feedback'      => "Brand-mu langsung dipersepsikan sebagai sesuatu yang bernilai. Traffic lebih sedikit, tapi setiap pembeli adalah pembeli yang menghargai value-mu — bukan pemburu diskon. +Brand 20, -Traffic 15",
                    ],
                    [
                        'label'         => 'Harga kompetitif — sesuai standar pasar',
                        'effect'        => ['profit' => 10],
                        'feedback'      => "Harga yang wajar menjaga margin tetap sehat tanpa mengorbankan positioning. Kamu tidak menonjol tapi juga tidak kalah. Pilihan aman yang tetap menjaga profit. +Profit 10",
                    ],
                ],
            ],

            // STAGE 3 — BRAND STYLE
            [
                'question' => "STAGE 3 — BRAND STYLE\n\nBisnis sudah berjalan. Saatnya menentukan identitas visual. Dari modul Branding kamu tahu: konsistensi visual adalah senjata utama membangun kepercayaan jangka panjang.\n\nTone visual brand yang kamu pilih?",
                'order'    => 3,
                'options'  => [
                    [
                        'label'         => 'Premium minimal — clean, elegan, konsisten',
                        'effect'        => ['brand' => 25],
                        'feedback'      => "Identitas visual yang kuat langsung memisahkan kamu dari kompetitor yang asal-asalan. Customer mempersepsikan kamu sebagai brand yang serius dan terpercaya bahkan sebelum mereka membaca satu kata pun tentang produkmu. +Brand 25",
                    ],
                    [
                        'label'         => 'Playful colorful — energik, fun, eye-catching',
                        'effect'        => ['traffic' => 20],
                        'feedback'      => "Konten-konten kamu mudah menarik perhatian di feed yang ramai. Traffic organik meningkat karena konten terasa segar dan mengundang engagement. Tapi tanpa konsistensi yang kuat, brand masih terasa seperti banyak brand lain. +Traffic 20",
                    ],
                    [
                        'label'         => 'Random style — ganti-ganti sesuai mood',
                        'effect'        => ['brand' => -20],
                        'feedback'      => "Inkonsistensi adalah pembunuh brand paling senyap. Setiap kali seseorang melihat kontentmu yang berbeda, otak mereka harus 'berkenalan' ulang denganmu. Kepercayaan tidak bisa dibangun dari pondasi yang terus bergeser. -Brand 20",
                    ],
                ],
            ],

            // STAGE 4 — MARKET VALIDATION
            [
                'question' => "STAGE 4 — MARKET VALIDATION\n\nBisnis sudah berjalan beberapa minggu. Kamu mulai bertanya-tanya: apakah strategi ini benar-benar tepat sasaran?\n\nDari modul Product Ideation kamu tahu: data selalu lebih jujur dari intuisi.\n\nCara validasi ide yang kamu pilih?",
                'order'    => 4,
                'options'  => [
                    [
                        'label'         => 'Survey target market — tanya langsung sebelum scaling',
                        'effect'        => ['profit' => 10, 'brand' => 10],
                        'feedback'      => "Kamu mendapatkan insight berharga dari calon pembeli nyata — bukan asumsimu. Bisnis disesuaikan lebih tepat sasaran, dan customer merasa dilibatkan yang secara organik meningkatkan kepercayaan pada brand-mu. +Profit 10, +Brand 10",
                    ],
                    [
                        'label'         => 'Langsung launch skala besar — move fast',
                        'effect'        => ['traffic' => 15, 'brand' => -15],
                        'feedback'      => "Traffic masuk cepat karena skala besar, tapi tanpa validasi kamu berisiko melayani pasar yang salah. Beberapa customer kecewa karena ekspektasi tidak terpenuhi, merusak reputasi yang sedang dibangun. +Traffic 15, -Brand 15",
                    ],
                    [
                        'label'         => 'Test pre-order — validasi dengan uang nyata',
                        'effect'        => ['profit' => 15],
                        'feedback'      => "Ini validasi paling kuat: orang membayar sebelum produk selesai. Kamu mendapat modal kerja di awal dan kepastian penuh bahwa ada demand nyata — bukan sekadar interest. +Profit 15",
                    ],
                ],
            ],

            // STAGE 5 — MARKETING CHANNEL
            [
                'question' => "STAGE 5 — MARKETING CHANNEL\n\nBisnis sudah tervalidasi. Sekarang saatnya gas.\n\nDari modul Dasar Bisnis Digital kamu tahu: ada tiga jenis traffic — Paid, Organic, dan Owned. Masing-masing punya trade-off yang berbeda.\n\nChannel marketing utama yang kamu pilih?",
                'order'    => 5,
                'options'  => [
                    [
                        'label'         => 'Ads — iklan berbayar untuk traffic instan',
                        'effect'        => ['traffic' => 30, 'profit' => -15],
                        'feedback'      => "Traffic meledak dalam hitungan jam — tapi setiap klik menggerus profit. Dan ketika budget habis, traffic berhenti. Kamu membangun traffic di atas pondasi yang disewa, bukan dimiliki. +Traffic 30, -Profit 15",
                    ],
                    [
                        'label'         => 'Organic content — bangun audience yang kamu miliki',
                        'effect'        => ['brand' => 20],
                        'feedback'      => "Setiap konten yang kamu buat adalah aset jangka panjang. Brand-mu tumbuh secara organik karena audiens mulai melihatmu sebagai otoritas di bidangnya. Hasilnya lambat — tapi tidak bisa diambil oleh siapapun. +Brand 20",
                    ],
                    [
                        'label'         => 'Influencer — leverage kepercayaan orang lain',
                        'effect'        => ['traffic' => 20, 'brand' => 10, 'profit' => -10],
                        'feedback'      => "Kombinasi yang cerdas: kamu mendapat traffic dari audiense influencer yang sudah terpercaya, sekaligus ada brand lift karena rekomendasi dari pihak ketiga yang kredibel. Ada biaya, tapi ROI-nya jelas terukur. +Traffic 20, +Brand 10, -Profit 10",
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

        $this->command->info("✅ Business Launch Simulation — 5 stages, 15 options berhasil di-seed.");
        $this->command->info("   Simulation ID: {$sim->id} | Module (Course) ID: {$course->id}");
        $this->command->info("   Score formula: (profit × 0.5) + (traffic × 0.3) + (brand × 0.2)");
        $this->command->info("   Ranks: <50 Struggling | 50-69 Beginner | 70-84 Smart Builder | 85+ Strategic Thinker");
        $this->command->info("   Perfect Strategist: profit≥70 AND traffic≥70 AND brand≥70 → Badge + 200 Bonus XP");
    }
}
