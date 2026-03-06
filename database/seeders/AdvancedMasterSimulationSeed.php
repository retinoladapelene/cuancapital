<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;
use App\Models\Simulation;
use App\Models\SimulationStep;
use App\Models\SimulationOption;

class AdvancedMasterSimulationSeed extends Seeder
{
    public function run(): void
    {
        // ── 1. Course capstone advanced ───────────────────────────────────
        Course::where('title', 'Business Scaling War Room')->delete();

        $course = Course::create([
            'title'         => 'Business Scaling War Room',
            'description'   => 'Advanced capstone simulation — uji integrasi skill dari 3 modul advanced: Sales Funnel, Customer Retention, dan Operational Efficiency. 5 keputusan strategis nyata dalam skenario startup yang tiba-tiba viral.',
            'level'         => 'advanced',
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
            'title'            => 'Business Scaling War Room',
            'intro_text'       => "ALERT: Perusahaanmu baru saja viral.\n\nDalam 48 jam terakhir:\n→ Traffic naik 400%\n→ Server mulai lambat\n→ CS kewalahan menerima ribuan tiket\n→ Conversion rate terjun dari 5% ke 1.7%\n→ Refund rate mulai merangkak\n→ Tim panikdan menunggu arahanmu\n\nIni bukan latihan. Setiap keputusan yang kamu buat dalam 5 tahap berikutnya akan menentukan apakah momentum viral ini menjadi batu loncatan atau kubur bisnismu.\n\nKamu diuji dalam 5 dimensi:\n💰 Profit — kesehatan margin dan keuangan\n📣 Traffic — jangkauan dan volume\n🏆 Brand — persepsi dan reputasi\n⚙️ System — stabilitas operasional\n❤️ Loyalty — kepercayaan customer\n\nStat awal:\nProfit: 50 | Traffic: 50 | Brand: 50 | System: 50 | Loyalty: 50\n\nScore = (Profit×0.25) + (Traffic×0.15) + (Brand×0.20) + (System×0.25) + (Loyalty×0.15)\n\nTarget: Strategic Builder (65+) | Elite: 85+\n\nWaktu memutuskan. Tim menunggumu.",
            'difficulty_level' => 'advanced',
            'xp_reward'        => 1500,
        ]);

        // ── 4. 5 Decision Stages ──────────────────────────────────────────
        $steps = [

            // STEP 1 — TRAFFIC EXPLOSION RESPONSE
            [
                'question' => "STEP 1 — TRAFFIC EXPLOSION RESPONSE\n\nWebsite mulai lag. Halaman loading 8 detik padahal normalnya 1.2 detik. Setiap 10 detik, ratusan orang baru datang dari postingan yang viral. Conversion sudah mulai drop karena frustrasi user.\n\nCTO kamu memberikan 3 opsi. Ini keputusan pertama dan paling mendesak — apa yang akan kamu lakukan?",
                'order'    => 1,
                'options'  => [
                    [
                        'label'    => 'Upgrade server sekarang — bayar berapapun yang perlu',
                        'effect'   => ['traffic' => 20, 'profit' => -10, 'brand' => 5, 'system_stability' => 30],
                        'feedback' => "Keberanian yang mahal tapi terbayar. Server stabil, traffic tetap mengalir, dan brand impression terjaga. Profit terpotong untuk infrastruktur — tapi ini adalah investasi yang benar ketika momentum sedang tinggi. Bisnis yang tidak bisa menerima lonjakan traffic adalah bisnis yang tidak siap tumbuh. +Traffic 20, -Profit 10, +Brand 5, +System 30",
                    ],
                    [
                        'label'    => 'Biarkan dulu — hemat biaya, lihat apakah traffic bertahan',
                        'effect'   => ['traffic' => -25, 'brand' => -20, 'profit' => 5, 'system_stability' => -30],
                        'feedback' => "Keputusan yang akan kamu sesali. Server yang lambat adalah conversion killer terbesar. Di era dimana ekspektasi loading adalah < 3 detik, 8 detik berarti 53% pengunjung sudah pergi sebelum melihat produkmu. Kamu mengorbankan momen viral yang mungkin tidak akan datang lagi. -Traffic 25, -Brand 20, +Profit 5, -System 30",
                    ],
                    [
                        'label'    => 'Redirect ke waitlist funnel — tampung leads, beli waktu untuk fix server',
                        'effect'   => ['traffic' => -10, 'brand' => 15, 'profit' => 10, 'system_stability' => 10],
                        'feedback' => "Keputusan yang brilliant. Daripada kehilangan segalanya atau menghabiskan keuangan sekarang — kamu mengubah traffic yang tidak bisa ditangani menjadi leads yang bisa dikonversi nanti. \"Gabung waitlist eksklusif\" bahkan terasa lebih premium. Brand naik, leads dikumpulkan, server punya waktu untuk diperbaiki dengan tenang. -Traffic 10, +Brand 15, +Profit 10, +System 10",
                    ],
                ],
            ],

            // STEP 2 — CONVERSION DROP
            [
                'question' => "STEP 2 — CONVERSION DROP CRISIS\n\nServer sudah stabil. Tapi data masuk: conversion rate terjun dari 5% ke 1.7%. Artinya dari 100 orang yang mengunjungi halaman produk, hanya 1-2 yang membeli — padahal normalnya 5.\n\nAnalis kamu berkata ini bukan masalah traffic. Ini masalah landing page dan messaging. Tim marketing menunggu arahanmu untuk mengeksekusi solusi hari ini.\n\nFunnel thinking-mu diuji sekarang.",
                'order'    => 2,
                'options'  => [
                    [
                        'label'    => 'Tambah diskon besar — turunkan barrier harga untuk konversi cepat',
                        'effect'   => ['profit' => -25, 'traffic' => 10],
                        'feedback' => "Respons yang umum tapi berbahaya. Diskon memang mendorong konversi jangka pendek — tapi kamu baru saja melatih semua orang yang masuk dari viral moment ini bahwa harga normalmu tidak masuk akal. Ketika diskon berakhir, conversion akan drop bahkan lebih dalam. Dan margin-mu sudah hancur. -Profit 25, +Traffic 10",
                    ],
                    [
                        'label'    => 'Perbaiki landing page copy — audit dan rewrite headline, benefit, dan CTA',
                        'effect'   => ['brand' => 10, 'system_stability' => 0, 'profit' => 0, 'customer_loyalty' => 0, 'traffic' => 0, 'conversion_boost' => 30],
                        'feedback' => "Ini adalah diagnosis yang benar dan solusi yang tepat. Conversion drop 1.7% adalah gejala — messaging mismatch adalah penyakitnya. Audiens baru yang datang dari viral post memiliki konteks yang berbeda dari biasanya. Rewrite headline untuk menyesuaikan dengan kenapa mereka datang, perjelas benefit unikmu, dan perkuat CTA. +Brand 10, dan conversion engine-mu kembali optimal",
                    ],
                    [
                        'label'    => 'Tambah urgency timer — countdown \"Penawaran berakhir dalam 2 jam\"',
                        'effect'   => ['brand' => -5, 'customer_loyalty' => -10, 'traffic' => 0, 'conversion_boost' => 15],
                        'feedback' => "Urgency memang meningkatkan konversi — tapi hanya jika dipercaya. Audiens yang datang dari viral moment dan langsung melihat countdown timer akan merasa dimanipulasi. Ini adalah dark pattern yang merusak trust jangka panjang dan brand impression. Ada kenaikan konversi sesaat, tapi brand dan loyalty terkikis. -Brand 5, -Loyalty 10",
                    ],
                ],
            ],

            // STEP 3 — CS OVERLOAD
            [
                'question' => "STEP 3 — CUSTOMER SERVICE OVERLOAD\n\nTicket CS naik 600% dalam 24 jam. Tim CS yang biasanya menangani 80 tiket per hari sekarang dibanjiri 480+ tiket. Response time sudah di atas 12 jam. Customer mulai posting keluhan di media sosial tentang tidak ada yang merespons.\n\nRetention psychology dan CS strategy-mu diuji sekarang. Keputusan ini berdampak langsung pada loyalitas semua customer baru yang baru saja masuk dari viral moment.",
                'order'    => 3,
                'options'  => [
                    [
                        'label'    => 'Hire CS cepat — rekrut 5 orang baru hari ini',
                        'effect'   => ['customer_loyalty' => 20, 'profit' => -15],
                        'feedback' => "Inisiatif yang manusiawi tapi tidak realistis. Orang baru perlu training — dan training butuh waktu yang tidak kamu punya sekarang. CS yang tidak terlatih bisa merespons dengan informasi yang salah, memperburuk situasi. Profit berkurang untuk biaya rekrutmen dan gaji, tapi kapasitas efektif tidak naik signifikan dalam 48 jam kritis ini. +Loyalty 20, -Profit 15",
                    ],
                    [
                        'label'    => 'Aktifkan auto-reply + FAQ bot — kategorisasi tiket otomatis untuk 80% pertanyaan umum',
                        'effect'   => ['system_stability' => 25, 'customer_loyalty' => 10],
                        'feedback' => "Ini adalah solusi yang skalabel dan paling optimal untuk jangka pendek maupun panjang. 80% tiket CS adalah pertanyaan yang sama berulang — status order, cara penggunaan, kebijakan refund. Bot yang menjawab pertanyaan ini dalam detik lebih baik dari manusia yang menjawab dalam 12 jam. Tim CS bisa fokus ke 20% tiket kompleks yang membutuhkan empati manusia. +System 25, +Loyalty 10",
                    ],
                    [
                        'label'    => 'Abaikan dulu — fokus ke hal yang lebih penting, CS bisa tunggu',
                        'effect'   => ['brand' => -40],
                        'feedback' => "Keputusan yang menghancurkan brand dalam hitungan jam. Di era media sosial, satu keluhan yang di-share bisa mencapai jutaan orang lebih cepat dari iklan terbaikmu. Customer yang merasa diabaikan tidak hanya pergi — mereka menjadi musuh yang aktif. Momentum viral yang kamu miliki bisa berbalik menjadi viral cancelation. -Brand 40",
                    ],
                ],
            ],

            // STEP 4 — REFUND SPIKE
            [
                'question' => "STEP 4 — REFUND RATE SPIKE\n\nData 72 jam terakhir: refund rate naik dari rata-rata 3% ke 18%. Itu angka yang mengkhawatirkan. Kamu mulai kehilangan revenue dari order yang sudah masuk. Tim finance panik.\n\nInvestigasi awal menunjukkan dua kemungkinan: (1) customer baru yang datang dari viral moment memiliki ekspektasi yang tidak terpenuhi, (2) proses onboarding untuk memahami cara menggunakan produk tidak cukup jelas.\n\nRetention logic dan funnel thinking diuji bersamaan.",
                'order'    => 4,
                'options'  => [
                    [
                        'label'    => 'Perketat refund policy — tambahkan syarat dan ketentuan lebih ketat',
                        'effect'   => ['profit' => 5, 'brand' => -25, 'customer_loyalty' => -30],
                        'feedback' => "Ini adalah solusi yang menyerang gejala, bukan penyakit. Memperketat policy tidak menyelesaikan kenapa customer ingin refund — ia hanya menyulitkan mereka untuk mendapatkan hak mereka. Hasilnya: kemarahan yang terposting di media sosial, chargeback dispute yang jauh lebih mahal, dan brand damage yang persisten. Profit sedikit terjaga di atas kertas, tapi reputasimu hancur. +Profit 5, -Brand 25, -Loyalty 30",
                    ],
                    [
                        'label'    => 'Audit produk + perbaiki onboarding — cari tahu kenapa ekspektasi tidak terpenuhi',
                        'effect'   => ['customer_loyalty' => 25, 'brand' => 15, 'system_stability' => 10],
                        'feedback' => "Keputusan yang menunjukkan founder dengan growth mindset. Refund spike yang tiba-tiba adalah feedback pasar yang sangat berharga — gratis. Daripada menyumbatnya dengan policy, kamu menggunakannya untuk memperbaiki produk dan onboarding. Customer yang merasa didengar dan dibantu bahkan setelah masalah terjadi menjadi advocates terkuat brand-mu. +Loyalty 25, +Brand 15, +System 10",
                    ],
                    [
                        'label'    => 'Stop semua ads — kurangi exposure sampai situasi terkendali',
                        'effect'   => ['traffic' => -30, 'profit' => -10, 'system_stability' => 5],
                        'feedback' => "Respons yang over-reaktif. Menghentikan ads tidak menyelesaikan masalah onboarding atau ekspektasi yang tidak terpenuhi — ia hanya mengurangi kecepatan masalah baru masuk, sambil menghilangkan momentum traffic yang sudah sulit dibangun. Ketika ads dihidupkan lagi, kamu mulai dari nol dengan audience trust yang sudah terdampak. -Traffic 30, -Profit 10, +System 5",
                    ],
                ],
            ],

            // STEP 5 — SCALING DECISION
            [
                'question' => "STEP 5 — STRATEGIC SCALING DECISION\n\nSituasi sudah stabil. Server oke. CS terkendali. Refund kembali normal. Kamu berhasil melewati krisis viral moment.\n\nSekarang pertanyaan terpenting: apa langkah berikutnya?\n\nKamu punya 3 opsi strategis. Ini adalah keputusan yang membedakan founder yang berhasil scale dari yang stuck di level yang sama. Semua kapasitasmu yang telah terlatih diuji sekarang — funnel thinking + retention logic + systems thinking.",
                'order'    => 5,
                'options'  => [
                    [
                        'label'    => 'Tambah ads budget — gas iklan sekarang selagi momentum masih ada',
                        'effect'   => ['traffic' => 20, 'profit' => -20, 'system_stability' => -15],
                        'feedback' => "Ini adalah kesalahan scaling klasik. Kamu baru saja keluar dari krisis yang disebabkan oleh sistem yang tidak siap menangani lonjakan — dan keputusan pertamamu adalah mengundang lonjakan yang lebih besar? Tanpa funnel yang dioptimasi dan sistem yang diperkuat, budget tambahan hanya akan mempercepat pemborosan. Kamu membeli traffic ke funnel yang masih bocor. +Traffic 20, -Profit 20, -System 15",
                    ],
                    [
                        'label'    => 'Optimasi funnel dulu — perbaiki conversion sebelum tambah traffic',
                        'effect'   => ['profit' => 20, 'brand' => 10, 'customer_loyalty' => 10],
                        'feedback' => "Keputusan yang strategis. Sebelum menuangkan air ke dalam ember, pastikan ember tidak bocor. Dengan mengoptimasi funnel — landing page, checkout flow, onboarding — setiap pengunjung yang sudah ada menghasilkan lebih banyak. Ketika kamu akhirnya scale ads, setiap rupiah yang diinvestasikan memberikan return yang jauh lebih tinggi. +Profit 20, +Brand 10, +Loyalty 10",
                    ],
                    [
                        'label'    => 'Bangun automation system — systemisasi operasional sebelum scale apapun',
                        'effect'   => ['system_stability' => 35, 'profit' => 15, 'customer_loyalty' => 15, 'brand' => 10],
                        'feedback' => "Keputusan tertinggi dari seorang Strategic Builder. Kamu baru mengalami sendiri bagaimana sistem yang tidak siap bisa menghancurkan momen terbaik bisnismu. Sebelum scaling ads atau funnel, pastikan bisnis ini bisa beroperasi pada 10x volume saat ini tanpa collapse. Automation system adalah fondasi yang membuat semua scaling menjadi sustainable bukan sekedar bertahan. +System 35, +Profit 15, +Loyalty 15, +Brand 10",
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
                // Build clean effect array — strip internal keys not in schema
                $effect = collect($opt['effect'])->reject(fn($v) => $v === 0 && !in_array(array_search($v, $opt['effect']), [
                    'profit','traffic','brand','system_stability','customer_loyalty'
                ]))->toArray();

                // Map conversion_boost to a tag for frontend, keep only stat keys
                $cleanEffect = [];
                $statKeys = ['profit','traffic','brand','system_stability','customer_loyalty'];
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

        $this->command->info("✅ Business Scaling War Room — 5 stages, 15 options berhasil di-seed.");
        $this->command->info("   Simulation ID: {$sim->id} | Course ID: {$course->id}");
        $this->command->info("   5 Stats: profit, traffic, brand, system_stability, customer_loyalty");
        $this->command->info("   Score = (profit×0.25) + (traffic×0.15) + (brand×0.20) + (system×0.25) + (loyalty×0.15)");
        $this->command->info("   Tiers: <40 Chaos Founder | 40-64 Tactical Operator | 65-84 Strategic Builder | 85+ Elite Business Architect");
        $this->command->info("   Perfect: all stats ≥ 70 → bonus 500 XP");
    }
}
