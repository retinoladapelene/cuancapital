<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Course;
use App\Models\Simulation;
use App\Models\SimulationStep;
use App\Models\SimulationOption;

class MasterExecutiveCommandSeed extends Seeder
{
    public function run(): void
    {
        Course::where('title', 'Executive Command Program')->delete();

        $course = Course::create([
            'title'         => 'Executive Command Program',
            'description'   => 'Level MASTER — 5 Command Missions. Kamu tidak belajar bisnis. Kamu menjalankan perusahaan. Setiap keputusan mempengaruhi Company Valuation — metrik yang jauh lebih nyata dari sekadar XP.',
            'level'         => 'master',
            'category'      => 'master',
            'xp_reward'     => 3000,
            'lessons_count' => 0,
            'is_active'     => true,
        ]);

        $scoringFormula   = 'profitability*0.25+growth_rate*0.20+brand_equity*0.20+system_strength*0.20+investor_confidence*0.15';
        $initialState     = ['profitability' => 50, 'growth_rate' => 50, 'brand_equity' => 50, 'system_strength' => 50, 'investor_confidence' => 50];

        $missions = [

            // ── MISSION 1 — MARKET DOMINATION WAR ────────────────────────
            [
                'title'      => 'Mission 1 — Market Domination War',
                'intro'      => "BOARD BRIEFING — MARKET DOMINATION WAR\n\nKompetitor baru yang didanai VC masuk ke market-mu. Mereka punya runway 24 bulan dan tim 40 orang. Kamu punya cashflow terbatas dan tim 12 orang.\n\nMereka memulai price war. Customer mulai bertanya-tanya. Investor khawatir.\n\nObjective: Survive dan grow market share tanpa membakar bisnis.\n\nMetrics awal:\nProfitability: 55 | Growth Rate: 50 | Brand Equity: 60 | System: 50 | Investor Confidence: 50\n\nValuation = (Profitability×0.25) + (Growth×0.20) + (Brand×0.20) + (System×0.20) + (Investor×0.15)\nTarget: CEO tier (400+)",
                'difficulty' => 'master',
                'xp'         => 500,
                'init_state' => ['profitability' => 55, 'growth_rate' => 50, 'brand_equity' => 60, 'system_strength' => 50, 'investor_confidence' => 50],
                'steps'      => [
                    [
                        'q'    => "PHASE 1 — PRICING RESPONSE\n\nKompetitor memotong harga 35% di bawah hargamu. Dalam 72 jam, 3 customer besar kirim email menanyakan apakah kamu akan menyesuaikan harga.\n\nBagaimana responsmu?",
                        'type' => 'decision', 'irr' => false,
                        'opts' => [
                            ['label' => 'Match harga kompetitor — jangan biarkan customer pergi', 'effect' => ['growth_rate' => 15, 'profitability' => -25, 'investor_confidence' => -10], 'feedback' => "Price war yang kamu mulai sendiri. Volume mungkin stabil tapi margin hancur. Investor melihat profitability turun drastis dan mulai mempertanyakan sustainability model bisnismu. Growth naik karena akuisisi customer, tapi harga yang telah kamu potong sangat sulit dinaikkan kembali. -Profitability 25, +Growth 15, -Investor Confidence 10"],
                            ['label' => 'Pertahankan harga — justifikasi dengan value differentiation', 'effect' => ['brand_equity' => 20, 'investor_confidence' => 15, 'growth_rate' => -5], 'feedback' => "Keputusan yang menunjukkan positioning clarity. Kamu memilih tidak bermain di arena yang kompetitor tentukan. Beberapa customer pergi ke kompetitor — tapi yang bertahan adalah yang membeli karena value, bukan harga. Brand equity naik, investor confidence meningkat karena kamu tidak panic. Growth sedikit turun karena ada customer yang churn. +Brand 20, +Investor 15, -Growth 5"],
                            ['label' => 'Tambahkan paket premium baru di atas harga saat ini', 'effect' => ['brand_equity' => 10, 'profitability' => 10, 'growth_rate' => 5], 'feedback' => "Counter-positioning yang cerdas. Alih-alih turun ke price war, kamu naik ke tier yang lebih premium. Ini mengirim sinyal pasar bahwa kamu bergerak ke atas sementara kompetitor bergerak ke bawah. Brand naik, profitability terjaga, growth moderat dari segmen premium. +Brand 10, +Profitability 10, +Growth 5"],
                        ],
                    ],
                    [
                        'q'    => "PHASE 2 — POSITIONING BATTLE\n\nKompetitor mulai menjalankan ads yang secara langsung membandingkan diri mereka dengan brandmu — highlighting price difference. Awareness market sedang terbentuk.\n\nBagaimana kamu memposisikan diri?",
                        'type' => 'decision', 'irr' => false,
                        'opts' => [
                            ['label' => 'Counter-ads langsung — serang weakness kompetitor', 'effect' => ['growth_rate' => 10, 'brand_equity' => -10, 'profitability' => -10], 'feedback' => "Comparative advertising yang masuk ke arena mud-slinging. Kamu mendapat attention, tapi brand terdampak karena terlihat reaktif dan defensif. Customer skeptis ketika brand mulai saling serang. Growth sedikit naik dari awareness, profitability turun dari ads spend. +Growth 10, -Brand 10, -Profit 10"],
                            ['label' => 'Fokus content marketing — tunjukkan expertise bukan harga', 'effect' => ['brand_equity' => 25, 'investor_confidence' => 10, 'growth_rate' => 5], 'feedback' => "Thought leadership positioning. Kamu membiarkan kompetitor berteriak soal harga sementara kamu membangun reputasi sebagai yang paling tahu tentang domain ini. Efeknya lebih lambat tapi jauh lebih durable. Brand equity naik signifikan, investor melihat long-term moat yang sedang dibangun. +Brand 25, +Investor 10, +Growth 5"],
                            ['label' => 'Buat case study dari customer terbaikmu — social proof attack', 'effect' => ['brand_equity' => 15, 'growth_rate' => 15, 'investor_confidence' => 5], 'feedback' => "Evidence-based positioning. Alih-alih klaim vs klaim, kamu menunjukkan hasil nyata. Customer yang melihat orang seperti mereka berhasil jauh lebih mudah dikonversi. Growth naik dari referral effect, brand naik dari proof of value. +Brand 15, +Growth 15, +Investor 5"],
                        ],
                    ],
                    [
                        'q'    => "PHASE 3 — COMPETITIVE INTELLIGENCE\n\nDari beberapa customer yang berpindah ke kompetitor dan kembali, kamu mendapat insight: produk kompetitor punya kelemahan besar di fitur X yang justru kamu miliki. Kamu bisa eksploitasi ini.\n\nApa keputusanmu?",
                        'type' => 'decision', 'irr' => false,
                        'opts' => [
                            ['label' => 'Double down — invest besar di fitur X, jadikan ini key differentiator', 'effect' => ['brand_equity' => 20, 'system_strength' => 15, 'profitability' => -10], 'feedback' => "Strategic moat building. Kamu menemukan asymmetric advantage dan kamu menginvestasikannya. Ini bukan reaksi — ini adalah strategic attack di titik terlemah kompetitor. Brand dan system strength naik, tapi investment dibutuhkan. +Brand 20, +System 15, -Profit 10"],
                            ['label' => 'Buat perbandingan publik di website — fitur X vs kompetitor', 'effect' => ['brand_equity' => 10, 'growth_rate' => 20, 'profitability' => -5], 'feedback' => "Transparansi yang menguntungkan. Comparison page yang jujur mengkonversi high-intent buyer yang sedang evaluasi — mereka yang paling dekat dengan keputusan beli. Growth naik signifikan dari conversion, brand naik dari kepercayaan publik. +Brand 10, +Growth 20, -Profit 5"],
                            ['label' => 'Simpan untuk negosiasi — gunakan sebagai leverage dengan investor', 'effect' => ['investor_confidence' => 15, 'growth_rate' => -5], 'feedback' => "Conservative play. Insight berharga ini disimpan dan digunakan untuk memperkuat narrative kepada investor — menunjukkan kamu tahu persis kelemahan kompetitor. Investor confidence naik, tapi growth sedikit tertinggal karena competitive advantage tidak dieksploitasi ke pasar. +Investor 15, -Growth 5"],
                        ],
                    ],
                    [
                        'q'    => "PRESSURE EVENT — BERITA FUNDING KOMPETITOR\n\nKompetitor baru saja mengumumkan Series A $5 juta. Media menulis 'kompetitor dominasi pasar'. Tim internalmu panik. Tiga orang kirim pesan menanyakan apakah bisnis kamu aman.\n\nBagaimana kamu merespons sebagai CEO?",
                        'type' => 'decision', 'irr' => false,
                        'opts' => [
                            ['label' => 'All-hands meeting — berikan transparency penuh tentang kondisi bisnis', 'effect' => ['system_strength' => 20, 'investor_confidence' => 10], 'feedback' => "Leadership under pressure. Transparency di saat tidak nyaman membangun trust yang tidak bisa dibeli. Tim yang tahu truth lebih stabil dari tim yang menebak-nebak. System strength naik karena organisasi diperkuat, investor confidence naik karena kamu terbukti bisa memipin dengan tenang. +System 20, +Investor 10"],
                            ['label' => 'Diam dan fokus — jangan validasi kegelisahan tim', 'effect' => ['system_strength' => -15, 'brand_equity' => -5], 'feedback' => "Silence yang membayar mahal. Ketika leader diam di saat kritis, tim mengisi kekosongan informasi dengan skenario terburuk. Moral turun, produktivitas drop, dan beberapa talent mulai update CV mereka. -System 15, -Brand 5"],
                            ['label' => 'Post statement publik — tunjukkan differentiasi dan growth metrics', 'effect' => ['brand_equity' => 15, 'growth_rate' => 10, 'investor_confidence' => 10], 'feedback' => "Proactive narrative control. Daripada membiarkan media yang membentuk persepsi, kamu mengambil alih cerita. Angka growth yang solid membuktikan bahwa berita funding kompetitor tidak mendefinisikan posisimu. +Brand 15, +Growth 10, +Investor 10"],
                        ],
                    ],
                    [
                        'q'    => "PHASE FINAL — MARKET POSITION LOCK\n\nSetelah 6 bulan battle, posisi pasar mulai terbentuk. Investor dari angel network tertarik berbicara. Mereka ingin tahu: apa moat jangka panjangmu?\n\nJawaban mana yang paling kuat secara strategic?",
                        'type' => 'decision', 'irr' => true,
                        'opts' => [
                            ['label' => 'Network effect — semakin banyak user, semakin valuable platform kami', 'effect' => ['investor_confidence' => 25, 'growth_rate' => 15], 'feedback' => "Network effect adalah salah satu moat paling compelling bagi investor — karena ia self-reinforcing dan semakin sulit ditembus seiring waktu. Investor confidence melonjak, growth rate naik dari narrative yang menarik tambahan user. IRREVERSIBLE: commitment ini membentuk product roadmap jangka panjang. +Investor 25, +Growth 15"],
                            ['label' => 'Switching cost — customer yang sudah 12 bulan bersama kami sangat sulit pindah', 'effect' => ['investor_confidence' => 20, 'profitability' => 15], 'feedback' => "Switching cost moat adalah bukti product stickiness — customer yang tidak pergi menjamin revenue predicability yang investor sangat hargai. Profitability naik dari retention, investor confidence naik dari proven model. IRREVERSIBLE: arah ini membentuk onboarding dan product strategy. +Investor 20, +Profit 15"],
                            ['label' => 'Brand trust — 85% customer kami datang dari referral, bukan ads', 'effect' => ['brand_equity' => 25, 'investor_confidence' => 15, 'profitability' => 10], 'feedback' => "Referral-driven growth adalah moat yang paling cost-efficient dan paling sulit direplikasi. Kompetitor bisa copy fitur, tapi tidak bisa copy trust. Brand equity naik signifikan, investor melihat model yang sustainable karena CAC terus turun. IRREVERSIBLE: komitmen ini membentuk CS dan community strategy. +Brand 25, +Investor 15, +Profit 10"],
                        ],
                    ],
                ],
            ],

            // ── MISSION 2 — FUNDING NEGOTIATION ──────────────────────────
            [
                'title'      => 'Mission 2 — Funding Negotiation',
                'intro'      => "BOARD BRIEFING — FUNDING NEGOTIATION\n\nInvestor tertarik. Mereka menawarkan Rp 5 miliar — dengan syarat 30% equity dan board seat.\n\nValuasi mereka: Rp 16.7 miliar. Kamu yakin bisnis ini minimal senilai Rp 25 miliar.\n\nObjective: Raise capital, jaga kontrol, dan tutup deal yang fair.\n\nMetrics awal:\nProfitability: 60 | Growth Rate: 55 | Brand Equity: 55 | System: 50 | Investor Confidence: 65",
                'difficulty' => 'master',
                'xp'         => 600,
                'init_state' => ['profitability' => 60, 'growth_rate' => 55, 'brand_equity' => 55, 'system_strength' => 50, 'investor_confidence' => 65],
                'steps'      => [
                    [
                        'q'    => "PHASE 1 — VALUATION BATTLE\n\nInvestor membuka negosiasi dengan valuasi Rp 16.7 miliar untuk Rp 5M investasi (30% equity). Kamu menginginkan valuasi Rp 25 miliar (20% equity untuk dana yang sama).\n\nBagaimana kamu membuka counter?",
                        'type' => 'decision', 'irr' => false,
                        'opts' => [
                            ['label' => 'Langsung counter dengan angka Rp 25M — show your math', 'effect' => ['investor_confidence' => 15, 'growth_rate' => 10], 'feedback' => "Anchor yang kuat dengan justifikasi. Kamu membuka dengan angka yang signifikan di atas tawaran mereka — tapi dengan math yang solid. Investor menghormati founder yang tahu valuasi bisnisnya dan bisa mempertahankannya dengan data. +Investor 15, +Growth 10"],
                            ['label' => 'Minta waktu 2 minggu — cari term sheet pembanding dulu', 'effect' => ['investor_confidence' => 20, 'profitability' => 10], 'feedback' => "Leverage engineering terbaik dalam negosiasi: competing offers. Bahkan jika kamu tidak yakin mendapatkannya, sinyal bahwa kamu tidak desperate adalah posisi yang sangat kuat. +Investor 20, +Profit 10"],
                            ['label' => 'Setuju valuasi mereka tapi minta warrant atau anti-dilution clause', 'effect' => ['profitability' => -5, 'system_strength' => 10, 'investor_confidence' => 5], 'feedback' => "Kompromi dengan proteksi. Kamu menyerah di valuasi tapi mendapat proteksi struktural. Ini bisa jadi pilihan yang reasonable tergantung seberapa kritis dananya. +System 10, +Investor 5, -Profit 5"],
                        ],
                    ],
                    [
                        'q'    => "PHASE 2 — TERM SHEET DETAILS\n\nNegotiasi valuasi selesai di Rp 21 miliar (23.8% equity). Tapi mereka meminta: 1 board seat + right of first refusal untuk future rounds + pro-rata rights.\n\nIni adalah keputusan yang menentukan struktur governance jangka panjang.",
                        'type' => 'decision', 'irr' => true,
                        'opts' => [
                            ['label' => 'Setuju semua — deal lebih penting dari governance detail sekarang', 'effect' => ['profitability' => 15, 'investor_confidence' => 10, 'system_strength' => -10], 'feedback' => "Cash masuk tapi kontrol terdilusi signifikan. Board seat + ROFR + pro-rata adalah kombinasi yang akan terasa berat di round berikutnya. IRREVERSIBLE: governance structure ini akan mengikuti bisnis untuk selamanya. +Profit 15, +Investor 10, -System 10"],
                            ['label' => 'Setuju board seat, negosiasikan hapus ROFR dan batasi pro-rata', 'effect' => ['investor_confidence' => 15, 'system_strength' => 15, 'profitability' => 10], 'feedback' => "Balanced deal. Board seat reasonable untuk investor satu stage — tapi ROFR dan pro-rata tak terbatas adalah hal yang perlu dinegosikan. Founder yang tahu apa yang bisa diflexible dan apa yang tidak adalah founder yang dihormati. IRREVERSIBLE: foundation governance solid. +Investor 15, +System 15, +Profit 10"],
                            ['label' => 'Tolak board seat — observer seat saja maksimum', 'effect' => ['investor_confidence' => -15, 'system_strength' => 25], 'feedback' => "Tebak keras yang mungkin membunuh deal. Investor yang menaruh Rp 5 miliar biasanya mengharapkan representasi governance. Menolak board seat bisa diartikan sebagai tanda bahwa founder tidak mau ada oversight. System strength naik (kontrol terjaga) tapi investor confidence collapse. +System 25, -Investor 15"],
                        ],
                    ],
                    [
                        'q'    => "PHASE 3 — CLOSING CONDITIONS\n\nDeal hampir ditutup. Investor minta 2 hal terakhir: due diligence 3 minggu penuh akses ke semua data, dan performance milestone untuk tranche kedua dana (Rp 2.5M dari Rp 5M dicairkan setelah GMV tertentu tercapai).\n\nKamu punya leverage karena ada investor lain yang juga tertarik.",
                        'type' => 'decision', 'irr' => false,
                        'opts' => [
                            ['label' => 'Setuju due diligence penuh + milestone yang realistis untuk kamu', 'effect' => ['investor_confidence' => 20, 'profitability' => 10, 'growth_rate' => 10], 'feedback' => "Transparency yang membangun trust. Due diligence yang bersih adalah opportunity untuk showcase quality bisnis, bukan ancaman. Milestone yang realistis melindungi kamu dari tekanan yang tidak perlu. +Investor 20, +Profit 10, +Growth 10"],
                            ['label' => 'Negosiasikan full disbursement — milestone membatasi fleksibilitas operasional', 'effect' => ['investor_confidence' => 5, 'profitability' => 20, 'system_strength' => 5], 'feedback' => "Valid argument. Milestone disbursement menciptakan uncertainty dalam planning — kamu tidak bisa commit ke resource allocation jika tidak tahu kapan dana masuk. Jika kamu bisa defend ini dengan data yang kuat, investor seringkali fleksibel. +Profit 20, +Investor 5, +System 5"],
                            ['label' => 'Gunakan competing investor untuk pressure close tanpa kondisi tambahan', 'effect' => ['investor_confidence' => -5, 'profitability' => 15, 'growth_rate' => 5], 'feedback' => "Taktik yang memiliki dua sisi. Competing term sheet adalah leverage yang sah — tapi jika investor tahu kamu sedang bluffing, relasi rusak. Jika genuine, ini adalah posisi kuat yang mempercepat close dan mengurangi kondisi. ±Investor 5 (tergantung eksekusi), +Profit 15, +Growth 5"],
                        ],
                    ],
                ],
            ],

            // ── MISSION 3 — CRISIS MANAGEMENT ────────────────────────────
            [
                'title'      => 'Mission 3 — Crisis Management',
                'intro'      => "BOARD BRIEFING — CRISIS MANAGEMENT\n\nProduk terbarumu viral — tapi bukan karena bagus. Seorang customer influencer dengan 800K followers memposting unboxing yang menunjukkan produkmu rusak. Dalam 4 jam: 2.400 komentar, 340 share, dan hashtag negatif trending.\n\nObjective: Selamatkan brand dalam 48 jam.\n\nMetrics awal:\nProfitability: 55 | Growth Rate: 45 | Brand Equity: 70 | System: 55 | Investor Confidence: 60",
                'difficulty' => 'master',
                'xp'         => 600,
                'init_state' => ['profitability' => 55, 'growth_rate' => 45, 'brand_equity' => 70, 'system_strength' => 55, 'investor_confidence' => 60],
                'steps'      => [
                    [
                        'q'    => "PHASE 1 — JAM PERTAMA (0-2 jam)\n\nPost sudah berjalan 2 jam. Views: 180.000. Kamu baru mengetahuinya. Tim CS sudah respond defensif di kolom komentar. Media online mengambil screenshot.\n\nAksi pertamamu sebagai CEO?",
                        'type' => 'decision', 'irr' => false,
                        'opts' => [
                            ['label' => 'Hapus komentar defensif tim CS segera + posting public apology', 'effect' => ['brand_equity' => 15, 'investor_confidence' => 10, 'growth_rate' => -5], 'feedback' => "Damage control yang tepat dan cepat. Komentar defensif adalah accelerant — menghapusnya dan langsung ganti dengan apology yang genuine memotong spiral. Orang memperhatikan bagaimana brand merespons lebih dari masalah awalnya. +Brand 15, +Investor 10, -Growth 5 (jangka pendek)"],
                            ['label' => 'Hubungi influencer secara private — tawarkan solusi sebelum public statement', 'effect' => ['brand_equity' => 10, 'investor_confidence' => 5, 'growth_rate' => -10], 'feedback' => "Pendekatan relasional yang bisa backfire. Jika influencer mempublikasi DM-mu sebagai upaya 'suap by silence' — situasi memburuk. Tapi jika berhasil, ini adalah penyelesaian paling efisien. High risk, medium reward. +Brand 10, +Investor 5, -Growth 10"],
                            ['label' => 'Hubungi tim legal dulu — pastikan tidak ada liability sebelum statement', 'effect' => ['brand_equity' => -20, 'growth_rate' => -15], 'feedback' => "Fatal delay. Setiap jam tanpa respons di krisis media sosial sama dengan bahan bakar bagi yang mencari konten. Legal review penting tapi tidak boleh menggantikan respons empati yang cepat. Crisis yang bisa dikontrol di jam 2 menjadi tidak terkontrol di jam 8. -Brand 20, -Growth 15"],
                        ],
                    ],
                    [
                        'q'    => "PHASE 2 — 24 JAM PERTAMA\n\nPost public apology-mu mendapat respons mixed. Sebagian mengapresiasi, sebagian menganggap PR move. Sekarang ada dua tekanan: (1) media online ingin statement resmi, (2) customer yang sudah beli produk yang sama mulai cek mereka.\n\nFokus resource kamu ke mana?",
                        'type' => 'decision', 'irr' => false,
                        'opts' => [
                            ['label' => 'Proactive recall — announce semua customer bisa klaim penggantian gratis', 'effect' => ['brand_equity' => 25, 'profitability' => -20, 'investor_confidence' => 15], 'feedback' => "Service recovery yang bold dan mahal. Proactive recall sebelum ada yang minta adalah sinyal terkuat bahwa kamu memprioritaskan customer di atas margin. Sangat mahal jangka pendek, tapi brand equity melonjak dan investor melihat leader yang berani. +Brand 25, +Investor 15, -Profit 20"],
                            ['label' => 'Media interview CEO — berikan transparency penuh tentang apa yang terjadi', 'effect' => ['brand_equity' => 20, 'investor_confidence' => 20, 'growth_rate' => 5], 'feedback' => "Narrative leadership. CEO yang mau berbicara langsung kepada media di tengah krisis adalah langka dan sangat dihormati. Transparency yang genuine — termasuk mengakui kesalahan — membangun kembali trust lebih cepat dari statement yang dipoles. +Brand 20, +Investor 20, +Growth 5"],
                            ['label' => 'Fokus ke existing customer via email — skip media', 'effect' => ['brand_equity' => 10, 'investor_confidence' => 5, 'profitability' => 5], 'feedback' => "Prioritas yang defensible. Existing customer adalah aset terpenting — memastikan mereka tidak panic churn adalah prioritas yang benar. Tapi mengabaikan media berarti membiarkan narasi dikontrol orang lain. +Brand 10, +Investor 5, +Profit 5"],
                        ],
                    ],
                    [
                        'q'    => "PHASE 3 — 48 JAM — ROOT CAUSE & SYSTEMIC FIX\n\nKrisis mulai mereda. Tapi kamu harus memutuskan: apa respons sistemik untuk memastikan ini tidak terulang?\n\nIni adalah IRREVERSIBLE decision yang akan membentuk QC architecture bisnismu.",
                        'type' => 'decision', 'irr' => true,
                        'opts' => [
                            ['label' => 'Hire QC Manager + rancang ulang proses quality control seluruh produksi', 'effect' => ['system_strength' => 30, 'profitability' => -10, 'brand_equity' => 15], 'feedback' => "Systemic fix yang komprehensif. Ini bukan band-aid — ini adalah perubahan struktural yang memastikan batch error seperti ini tidak ada ruang untuk lolos. IRREVERSIBLE: QC architecture ini menjadi standar operasional permanen. +System 30, +Brand 15, -Profit 10"],
                            ['label' => 'Partnership dengan third-party lab testing — setiap batch wajib sertifikasi', 'effect' => ['system_strength' => 20, 'brand_equity' => 25, 'profitability' => -15], 'feedback' => "External credibility. Third-party certification memberikan trust yang tidak bisa diklaim sendiri oleh brand. IRREVERSIBLE: ini menjadi commitment publik yang harus dijalankan setiap batch. +System 20, +Brand 25, -Profit 15"],
                            ['label' => 'Implementasi customer feedback loop wajib di setiap batch launch', 'effect' => ['growth_rate' => 20, 'brand_equity' => 15, 'system_strength' => 10], 'feedback' => "Voice-of-customer sebagai early warning system. Setiap batch launch disertai beta group kecil yang memberikan feedback sebelum scale distribusi. IRREVERSIBLE: komitmen ini menjadi bagian dari launch protocol permanen. +Growth 20, +Brand 15, +System 10"],
                        ],
                    ],
                ],
            ],

            // ── MISSION 4 — HYPERGROWTH SCALING ──────────────────────────
            [
                'title'      => 'Mission 4 — Hypergrowth Scaling',
                'intro'      => "BOARD BRIEFING — HYPERGROWTH SCALING\n\nDalam 7 hari terakhir, traffic naik 10× karena konten viral dan partnership media besar. Revenue harian naik dari Rp 8 juta ke Rp 85 juta. Tapi sistem belum siap.\n\nObjective: Scale tanpa crash.\n\nMetrics awal:\nProfitability: 50 | Growth Rate: 85 | Brand Equity: 65 | System: 35 | Investor Confidence: 60",
                'difficulty' => 'master',
                'xp'         => 700,
                'init_state' => ['profitability' => 50, 'growth_rate' => 85, 'brand_equity' => 65, 'system_strength' => 35, 'investor_confidence' => 60],
                'steps'      => [
                    [
                        'q'    => "PHASE 1 — INFRASTRUCTURE EMERGENCY\n\nServer response time: 9 detik. Cart abandonment: 67%. Customer complaint queue: 840 tiket belum terjawab. Tim teknis bilang butuh 2 hari untuk upgrade infrastructure.\n\nKeputusan CEO dalam kondisi darurat ini?",
                        'type' => 'decision', 'irr' => false,
                        'opts' => [
                            ['label' => 'Emergency cloud upgrade sekarang — bayar premium rate untuk immediate capacity', 'effect' => ['system_strength' => 35, 'profitability' => -15, 'growth_rate' => 10], 'feedback' => "Triage yang tepat. Setiap jam dengan server lambat = ribuan konversi yang hilang. Emergency cloud cost adalah investasi yang realistis dibanding revenue yang bocor. System strength naik drastis, profitability berkurang dari premium cost. +System 35, +Growth 10, -Profit 15"],
                            ['label' => 'Aktifkan waitlist funnel + suppress ads — beli waktu untuk fix infra', 'effect' => ['brand_equity' => 15, 'system_strength' => 25, 'growth_rate' => -15], 'feedback' => "Triage yang cerdas. Daripada mengundang lebih banyak orang ke sistem yang broken, kamu mengarahkan traffic ke waitlist yang dikelola dengan baik. Brand terjaga karena orang merasa eksklusif, bukan frustrasi. +Brand 15, +System 25, -Growth 15"],
                            ['label' => 'Biarkan berjalan — beberapa user frustrated adalah acceptable di hypergrowth', 'effect' => ['brand_equity' => -25, 'system_strength' => -10, 'investor_confidence' => -15], 'feedback' => "Fatal misjudgement. Frustrated user di hypergrowth adalah bom waktu — mereka punya platform dan mereka akan menggunakanya. Review negatif yang viral di momen pertumbuhan bisa memotong momentum lebih keras dari masalah teknis apapun. -Brand 25, -System 10, -Investor 15"],
                        ],
                    ],
                    [
                        'q'    => "PHASE 2 — HIRING UNDER PRESSURE\n\nCS queue: 840 tiket. Estimasi waktu response: 36 jam. Viral moment kamu membutuhkan tim yang lebih besar segera. Ada dua opsi hiring yang bisa dieksekusi dalam 48 jam.\n\nIni adalah hiring decision di bawah tekanan maksimal.",
                        'type' => 'decision', 'irr' => false,
                        'opts' => [
                            ['label' => 'Outsource ke CS agency untuk handling emergency 2 minggu', 'effect' => ['system_strength' => 20, 'brand_equity' => -5, 'profitability' => -10], 'feedback' => "Speed optimization. Agency CS bisa onboard dalam jam, bukan minggu. Kualitas mungkin sedikit di bawah standar internal — tapi tiket yang terjawab dalam 2 jam selalu lebih baik dari tiket yang menunggu 36 jam. +System 20, -Brand 5, -Profit 10"],
                            ['label' => 'Aktifkan AI chatbot + self-serve FAQ untuk handle 70% tiket generik', 'effect' => ['system_strength' => 30, 'profitability' => -5, 'growth_rate' => 5], 'feedback' => "Scalable solution. Bot yang merespons dalam detik menyelesaikan pertanyaan FAQ — yang biasanya 60-80% dari total volume. Tim manusia bisa fokus ke 20-40% tiket kompleks yang benar-benar butuh empati. +System 30, +Growth 5, -Profit 5"],
                            ['label' => 'Hire freelancer cepat dari platform talent — mulai besok', 'effect' => ['system_strength' => 10, 'profitability' => -10, 'brand_equity' => -10], 'feedback' => "Terlalu lambat untuk ukuran krisis ini. Freelancer yang mulai besok masih butuh onboarding. 840 tiket terus bertambah sementara kamu menunggu. System sedikit naik, tapi brand terdampak dari delay yang tidak perlu. +System 10, -Profit 10, -Brand 10"],
                        ],
                    ],
                    [
                        'q'    => "PHASE 3 — GROWTH ARCHITECTURE\n\nSistem sudah stabil. Momentum masih ada. Sekarang keputusan arsitektural yang paling penting: bagaimana kamu menyusun sistem untuk sustainable hypergrowth?\n\nIni adalah IRREVERSIBLE decision yang akan menentukan DNA operasional bisnismu.",
                        'type' => 'decision', 'irr' => true,
                        'opts' => [
                            ['label' => 'Build horizontal — expand ke market baru dengan model yang sama', 'effect' => ['growth_rate' => 25, 'brand_equity' => 15, 'system_strength' => -10], 'feedback' => "Geographic atau demographic expansion. Mengambil model yang sudah terbukti ke market baru adalah cara tercepat untuk growth. IRREVERSIBLE: ini mengalokasikan resource dan fokus ke ekspansi — yang berarti depth di market core mungkin terkorbankan. +Growth 25, +Brand 15, -System 10"],
                            ['label' => 'Build vertical — jadikan platform lebih dalam dan lengkap untuk existing users', 'effect' => ['system_strength' => 25, 'profitability' => 20, 'investor_confidence' => 15], 'feedback' => "Depth over breadth. Platform yang makin dalam menciptakan switching cost yang kuat. IRREVERSIBLE: ini adalah komitmen untuk menjadi best-in-class di satu market sebelum expand. +System 25, +Profit 20, +Investor 15"],
                            ['label' => 'Build ecosystem — buka API + partner program untuk third-party integration', 'effect' => ['growth_rate' => 20, 'system_strength' => 15, 'investor_confidence' => 20], 'feedback' => "Platform play jangka panjang. Ecosystem yang kuat menciptakan network effect yang kompetitor tidak bisa replikasi secara mudah. IRREVERSIBLE: ini adalah commitment untuk menjadi platform company, bukan product company. +Growth 20, +System 15, +Investor 20"],
                        ],
                    ],
                ],
            ],

            // ── MISSION 5 — EXIT STRATEGY ─────────────────────────────────
            [
                'title'      => 'Mission 5 — Exit Strategy',
                'intro'      => "BOARD BRIEFING — EXIT STRATEGY\n\nKamu menerima acquisition offer yang tidak pernah kamu bayangkan: perusahaan publik menawarkan Rp 120 miliar untuk mengakuisisi bisnismu secara penuh.\n\nBisnis berjalan baik. Revenue Rp 1.2 miliar per bulan. Kamu masih passionate. Tapi 120 miliar adalah angka yang merubah hidup.\n\nObjective: Buat keputusan exit yang tidak akan kamu sesali dalam 10 tahun.\n\nMetrics awal:\nProfitability: 70 | Growth Rate: 65 | Brand Equity: 70 | System: 65 | Investor Confidence: 75",
                'difficulty' => 'master',
                'xp'         => 700,
                'init_state' => ['profitability' => 70, 'growth_rate' => 65, 'brand_equity' => 70, 'system_strength' => 65, 'investor_confidence' => 75],
                'steps'      => [
                    [
                        'q'    => "PHASE 1 — VALUATION ASSESSMENT\n\nRp 120 miliar vs revenue run rate Rp 14.4 miliar per tahun = 8.3× revenue multiple. Di industri ini, comparable exits terjadi di 12-15× revenue untuk bisnis dengan growth rate-mu.\n\nBagaimana kamu merespons initial offer?",
                        'type' => 'decision', 'irr' => false,
                        'opts' => [
                            ['label' => 'Counter dengan Rp 180 miliar — tunjukkan math dengan growth projection', 'effect' => ['investor_confidence' => 20, 'profitability' => 15], 'feedback' => "Principled negotiation. 8.3× di growth stage-mu adalah di bawah market. Counter dengan Rp 180M (12.5×) dengan justifikasi growth trajectory yang solid adalah anchoring yang defensible. Investor confidence naik karena kamu tahu nilaimu. +Investor 20, +Profit 15"],
                            ['label' => 'Minta 60 hari untuk engage investment bank — dapatkan competing bids', 'effect' => ['investor_confidence' => 25, 'growth_rate' => 10], 'feedback' => "Process leverage. Competing bids adalah cara paling efektif untuk memaksimalkan exit value. Investment bank yang berpengalaman dapat meningkatkan final price 20-40%. 60 hari adalah waktu yang reasonable. +Investor 25, +Growth 10"],
                            ['label' => 'Terima dan close cepat — bird in hand lebih baik dari dua di pohon', 'effect' => ['profitability' => 20, 'growth_rate' => -15], 'feedback' => "Certainty premium. Ada value nyata dalam done deal vs. hypothetical higher bid yang mungkin tidak materialisasi. Tapi untuk bisnis dengan growth profile seperti ini, meninggalkan 40-50% value di meja adalah keputusan yang sulit dijustifikasi secara objektif. +Profit 20, -Growth 15"],
                        ],
                    ],
                    [
                        'q'    => "PHASE 2 — DUE DILIGENCE REVEALS\n\nDue diligence berlangsung 45 hari. Acquirer menemukan 2 hal: (1) 3 customer besar (40% revenue) tidak memiliki kontrak jangka panjang dan bisa churn kapanpun, (2) Sistem IT bergantung pada satu contractor eksternal yang bisa pergi.\n\nMereka meminta pengurangan harga 25% ke Rp 90 miliar dari Rp 210 miliar yang sudah disepakati.",
                        'type' => 'decision', 'irr' => false,
                        'opts' => [
                            ['label' => 'Negosiasikan earn-out — harga dasar Rp 165M + bonus Rp 45M jika customer bertahan 18 bulan', 'effect' => ['investor_confidence' => 20, 'profitability' => 15, 'growth_rate' => 10], 'feedback' => "Elegant solution. Earn-out menyelesaikan information asymmetry — kamu yakin customer akan bertahan, mereka tidak. Earn-out memberi kamu upside jika keyakinanmu benar. +Investor 20, +Profit 15, +Growth 10"],
                            ['label' => 'Tolak penyesuaian — fix kedua masalah dalam 30 hari sebelum close', 'effect' => ['system_strength' => 25, 'profitability' => 10, 'growth_rate' => 5], 'feedback' => "Proactive risk remediation. Kontrak jangka panjang dengan 3 customer besar dan engagement langsung dengan alternative IT solution dalam 30 hari. Jika berhasil, due diligence issues hilang dan deal kembali ke harga penuh. +System 25, +Profit 10, +Growth 5"],
                            ['label' => 'Setuju Rp 90 miliar — certainty lebih penting dari 25%', 'effect' => ['profitability' => 5, 'investor_confidence' => -5, 'growth_rate' => -10], 'feedback' => "Value destruction yang signifikan. Rp 120 miliar sudah di bawah market. Rp 90 miliar adalah undersell yang akan terasa menyakitkan ketika kamu kemudian melihat apa yang acquirer lakukan dengan bisnis ini. +Profit 5, -Investor 5, -Growth 10"],
                        ],
                    ],
                    [
                        'q'    => "PHASE FINAL — THE ULTIMATE DECISION\n\nSetelah semua negosiasi, final offer: Rp 195 miliar. Deal bisa tutup dalam 30 hari.\n\nTapi kamu baru saja mendapat informasi: market yang kamu mainkan projected tumbuh 10× dalam 5 tahun ke depan. Jika kamu tetap independen dan berhasil, valuasi bisnismu bisa mencapai Rp 1 triliun.\n\nIni adalah IRREVERSIBLE — keputusan terbesar dalam karir entrepreneurshipmu.",
                        'type' => 'decision', 'irr' => true,
                        'opts' => [
                            ['label' => 'Jual — ambil Rp 195M, eliminasi risiko, dan mulai chapter berikutnya', 'effect' => ['profitability' => 30, 'investor_confidence' => 20, 'growth_rate' => -25], 'feedback' => "Liquidity event yang mengubah hidup. Rp 195M guaranteed vs. probabilistic Rp 1T in 5 years. Risk-adjusted, ini bisa jadi keputusan yang sangat rational. IRREVERSIBLE: chapter ini selesai. Kamu bebas untuk memulai yang baru dengan modal dan pengalaman. +Profit 30, +Investor 20, -Growth 25"],
                            ['label' => 'Tolak — bisnis ini belum mencapai potensi penuhnya', 'effect' => ['growth_rate' => 30, 'brand_equity' => 20, 'profitability' => -10], 'feedback' => "Founder conviction yang kuat. Menolak likuiditas besar membutuhkan keyakinan yang luar biasa terhadap vision dan kemampuan eksekusi. IRREVERSIBLE: kamu all-in untuk membuktikan bahwa Rp 1T dalam 5 tahun adalah achievable. +Growth 30, +Brand 20, -Profit 10"],
                            ['label' => 'Partial exit — jual 40%, tetap CEO, reneg untuk full exit di valuasi lebih tinggi nanti', 'effect' => ['profitability' => 15, 'growth_rate' => 15, 'investor_confidence' => 25, 'brand_equity' => 10], 'feedback' => "Sophisticated structuring. 40% exit memberikan liquidity nyata sambil mempertahankan upside dari growth story yang kamu yakini. IRREVERSIBLE: kamu sekarang punya partner besar yang membawa resource dan network, sambil tetap memimpin bisnis. +Profit 15, +Growth 15, +Investor 25, +Brand 10"],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($missions as $idx => $mission) {
            $old = Simulation::where('module_id', $course->id)
                             ->where('title', $mission['title'])->first();
            if ($old) {
                Schema::disableForeignKeyConstraints();
                SimulationOption::whereIn('step_id',
                    SimulationStep::where('simulation_id', $old->id)->pluck('id')
                )->delete();
                SimulationStep::where('simulation_id', $old->id)->delete();
                $old->delete();
                Schema::enableForeignKeyConstraints();
            }

            $sim = Simulation::create([
                'module_id'          => $course->id,
                'title'              => $mission['title'],
                'intro_text'         => $mission['intro'],
                'difficulty_level'   => $mission['difficulty'],
                'xp_reward'          => $mission['xp'],
                'scoring_formula'    => $scoringFormula,
                'initial_state_json' => $mission['init_state'],
            ]);

            foreach ($mission['steps'] as $order => $stepData) {
                $step = SimulationStep::create([
                    'simulation_id'  => $sim->id,
                    'question'       => $stepData['q'],
                    'order'          => $order + 1,
                    'step_type'      => $stepData['type'],
                    'is_irreversible'=> $stepData['irr'],
                ]);

                foreach ($stepData['opts'] as $opt) {
                    SimulationOption::create([
                        'step_id'       => $step->id,
                        'label'         => $opt['label'],
                        'effect_json'   => $opt['effect'],
                        'feedback_text' => $opt['feedback'],
                    ]);
                }
            }

            $this->command->info("   ✓ {$mission['title']} seeded ({$mission['xp']} XP)");
        }

        $this->command->info("\n✅ Executive Command Program (Master) — 5 missions berhasil di-seed.");
        $this->command->info("   Course ID: {$course->id}");
        $this->command->info("   Scoring: profitability×0.25 + growth×0.20 + brand×0.20 + system×0.20 + investor×0.15");
        $this->command->info("   Tiers: Operator(<200) | Founder(200-399) | CEO(400-599) | Builder(600-799) | Titan(800-999) | Legend(1000)");
    }
}
