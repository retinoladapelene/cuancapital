<?php

namespace App\Services;

use App\Models\Roadmap;
use App\Models\MentorSession;
use App\Models\StrategyBlueprint;
use App\Models\RoadmapStep;
use App\Models\RoadmapAction;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RoadmapGeneratorService
{
    /**
     * Main entry point: generate a roadmap for a user.
     * Uses MentorSession for personalized tags if available, otherwise uses standard tags.
     */
    public static function generateForUser(int $userId, ?MentorSession $session = null): ?Roadmap
    {
        return DB::transaction(function () use ($userId, $session) {
            // Check if we have diagnostic data in the session
            $diagnoses = [];
            $mappedDiagnoses = [];
            
            if ($session && !empty($session->baseline_json['diagnoses'])) {
                $diagnoses = $session->baseline_json['diagnoses'];
                $recommendations = $session->baseline_json['recommendations'] ?? [];

                // Group recommendations by their diagnosis label
                $recsByLabel = [];
                foreach ($recommendations as $rec) {
                    if (is_array($rec) && isset($rec['label']) && isset($rec['text'])) {
                        $recsByLabel[$rec['label']][] = $rec['text'];
                    }
                }

                // Map raw diagnoses into the format expected by the roadmap generator
                foreach ($diagnoses as $d) {
                    $mappedDiagnoses[] = [
                        'title' => $d['label'] ?? 'Strategic Priority',
                        'description' => $d['description'] ?? 'Fokus resolusi untuk masalah operasional utama.',
                        'actions' => $recsByLabel[$d['label']] ?? []
                    ];
                }
            }

            // Derive tags from session baseline data, or use defaults
            $tags = $session
                ? self::generateTagsFromSession($session)
                : ['Margin Improvement', 'Traffic Scaling', 'Monetization Expansion'];

            // Deactivate any previous active roadmaps for the user
            Roadmap::where('user_id', $userId)
                ->where('status', 'active')
                ->update(['status' => 'archived']);

            // Create new roadmap
            $roadmap = Roadmap::create([
                'user_id'          => $userId,
                'simulation_id'    => null,
                'status'           => 'active',
                'total_steps'      => 0,
                'completed_steps'  => 0,
            ]);

            // Build steps either from diagnostic data or fallback blueprints
            if (!empty($mappedDiagnoses)) {
                self::buildStepsFromDiagnoses($roadmap, $mappedDiagnoses);
            } else {
                self::buildSteps($roadmap, $tags);
            }

            return $roadmap->load('steps.actions');
        });
    }

    /** @deprecated Use generateForUser() instead */
    public static function generate(MentorSession $session)
    {
        return DB::transaction(function () use ($session) {
            // 1. Analyze & Generate Tags
            $tags = self::generateTagsFromSession($session);

            // 2. Create Roadmap Record
            $roadmap = Roadmap::create([
                'user_id' => $session->user_id,
                'simulation_id' => null,
                'status' => 'active',
                'total_steps' => 0,
                'completed_steps' => 0
            ]);

            // 3. Blueprint Merging & Step Creation
            self::buildSteps($roadmap, $tags);

            return $roadmap->load('steps.actions');
        });
    }

    public static function generateFromAdapter(array $roadmapInput)
    {
        return DB::transaction(function () use ($roadmapInput) {
            $roadmap = Roadmap::create([
                'user_id' => auth()->id(),
                'simulation_id' => null,
                'status' => 'active',
                'total_steps' => count($roadmapInput['priorities'] ?? []),
                'completed_steps' => 0
            ]);

            $order = 1;
            foreach ($roadmapInput['priorities'] as $priority) {
                $title = is_string($priority) ? $priority : ($priority['title'] ?? 'Strategic Step');
                $desc = is_string($priority) ? 'Execute this priority to reach the main goal.' : ($priority['description'] ?? '');

                $category = self::guessCategoryFromTitle($title);

                $step = RoadmapStep::create([
                    'roadmap_id' => $roadmap->id,
                    'title' => $title,
                    'description' => $desc,
                    'order' => $order,
                    'status' => ($order === 1) ? 'unlocked' : 'locked',
                    'strategy_tag' => $category
                ]);

                $actions = self::generateRichActions($category, $title);
                foreach ($actions as $act) {
                    RoadmapAction::create([
                        'step_id' => $step->id,
                        'action_text' => $act,
                        'is_completed' => false
                    ]);
                }

                $order++;
            }

            return $roadmap;
        });
    }

    private static function guessCategoryFromTitle(string $title): string
    {
        $lowercase = strtolower($title);
        if (str_contains($lowercase, 'traffic') || str_contains($lowercase, 'marketing') || str_contains($lowercase, 'iklan') || str_contains($lowercase, 'sosmed') || str_contains($lowercase, 'konten')) {
            return 'Traffic Scaling';
        }
        if (str_contains($lowercase, 'margin') || str_contains($lowercase, 'biaya') || str_contains($lowercase, 'harga') || str_contains($lowercase, 'cogs') || str_contains($lowercase, 'supplier')) {
            return 'Margin Improvement';
        }
        if (str_contains($lowercase, 'kas') || str_contains($lowercase, 'cashflow') || str_contains($lowercase, 'likuiditas') || str_contains($lowercase, 'uang')) {
            return 'Cashflow Acceleration';
        }
        if (str_contains($lowercase, 'retensi') || str_contains($lowercase, 'repeat') || str_contains($lowercase, 'loyal') || str_contains($lowercase, 'pelanggan')) {
            return 'Customer Retention';
        }
        if (str_contains($lowercase, 'stagnan') || str_contains($lowercase, 'jelas') || str_contains($lowercase, 'fokus')) {
            return 'Strategic Repositioning';
        }
        if (str_contains($lowercase, 'konversi') || str_contains($lowercase, 'sales') || str_contains($lowercase, 'closing') || str_contains($lowercase, 'offer') || str_contains($lowercase, 'upsell')) {
            return 'Monetization Expansion';
        }
        if (str_contains($lowercase, 'operasional') || str_contains($lowercase, 'sistem') || str_contains($lowercase, 'tim') || str_contains($lowercase, 'rekrut')) {
            return 'Operations';
        }
        return 'General Strategy';
    }

    private static function generateRichActions(string $category, string $title): array
    {
        return match ($category) {
            'Traffic Scaling' => [
                Arr::random([
                    'Buat Content Calendar 1 Bulan penuh (30 ide konten)',
                    'Susun jadwal publikasi konten harian selama 30 hari ke depan',
                    'Siapkan kalender editorial media sosial untuk sebulan penuh'
                ]),
                Arr::random([
                    'Setup & Verifikasi Meta Business Manager / TikTok Ads',
                    'Konfigurasi dan pastikan akun pengiklan Business Manager aktif',
                    'Verifikasi kelengkapan akun iklan di platform Meta atau TikTok'
                ]),
                Arr::random([
                    'Re-purpose materi konten ke format Reels, TikTok, dan Shorts',
                    'Daur ulang satu materi video panjang menjadi potongan pendek untuk berbagai platform',
                    'Ubah aset konten tulisan/gambar menjadi video vertikal singkat'
                ]),
                Arr::random([
                    'Test Campaign Budget Optimization dengan budget kecil',
                    'Lakukan pengujian iklan menggunakan CBO dengan alokasi harian minimum',
                    'Mulai kampanye uji coba beranggaran rendah untuk menemukan audiens pemenang'
                ]),
                Arr::random([
                    'Pantau Cost Per Click (CPC) dan Click Through Rate (CTR)',
                    'Evaluasi efektivitas iklan dengan melihat rasio klik banding tayangan',
                    'Cek metrik biaya per klik untuk memastikan efisiensi kampanye iklan berjalan'
                ]),
                Arr::random([
                    'Optimaliasi Bio profile & lengkapi link contact',
                    'Rapihkan deskripsi bio media sosial agar memiliki Call to Action jelas',
                    'Pastikan URL kontak di semua profil akun bisnis dapat diakses dengan mudah'
                ])
            ],
            'Margin Improvement' => [
                Arr::random([
                    'List semua Fixed Cost bulanan secara mendetail',
                    'Buat rincian daftar biaya tetap operasional setiap bulan',
                    'Audit ulang semua pos pengeluaran bulanan yang tidak bisa dihindari'
                ]),
                Arr::random([
                    'Cek harga modal (COGS) vs Harga Jual saat ini',
                    'Bandingkan ulang Harga Pokok Penjualan terhadap harga retail terbaru',
                    'Kalkulasi ulang persentase COGS terhadap nilai jual produk utama'
                ]),
                Arr::random([
                    'Negosiasi ulang term pembayaran dengan supplier',
                    'Minta keringanan tempo pelunasan kepada mitra pemasok barang',
                    'Ajak diskusi supplier untuk menurunkan harga beli partai besar'
                ]),
                Arr::random([
                    'Eliminasi biaya operasional software/layanan yang tidak krusial',
                    'Hentikan langganan aplikasi atau perangkat lunak b-to-b yang jarang dipakai',
                    'Batalkan biaya langganan layanan otomatisasi yang tidak memberikan nilai tambah'
                ]),
                Arr::random([
                    'Buat target efisiensi pengeluaran minimum 10%',
                    'Pangkas pengeluaran departemen sebesar sekurangnya 10% bulan ini',
                    'Tetapkan KPI untuk penghematan belanja operasional rutin setiap kuartal'
                ]),
                Arr::random([
                    'Rekapitulasi rasio Profit Margin setiap akhir minggu',
                    'Lakukan pencatatan profitabilitas setiap hari Jumat sore',
                    'Cek kesehatan margin kotor secara mingguan tanpa menunggu tutup buku'
                ])
            ],
            'Monetization Expansion' => [
                Arr::random([
                    'Kaji penawaran spesifik untuk memperbesar profit',
                    'Tinjau ulang paket dan penawaran agar margin nilai lebih tebal',
                    'Evaluasi struktur harga paket hemat vs paket eceran'
                ]),
                Arr::random([
                    'Tentukan produk komplementer untuk Upsell atau bundling',
                    'Pilih item pendamping yang murah untuk mendongkrak omset via paket',
                    'Identifikasi barang pelengkap yang bisa ditawarkan pasca-checkout'
                ]),
                Arr::random([
                    'Sederhanakan proses checkout atau pembayaran pelanggan',
                    'Kurangi langkah klik yang diperlukan pembeli hingga ke tahap transfer',
                    'Perbaiki halaman keranjang belanja untuk menurunkan rasio batal beli'
                ]),
                Arr::random([
                    'Berikan garansi eksklusif untuk meningkatkan rasio closing',
                    'Tambahkan kebijakan pengembalian tanpa syarat untuk menghancurkan keraguan',
                    'Sediakan jaminan layanan memuaskan guna mempercepat pengambilan keputusan klien'
                ]),
                Arr::random([
                    'Follow-up prospek atau database customer lama',
                    'Kirimkan pesan sapaan ke kontak WhatsApp dari pelanggan bulan lalu',
                    'Jadwalkan peneleponan kembali daftar leads yang menunda transaksi'
                ]),
                Arr::random([
                    'Review kembali penawaran ' . $title,
                    'Tinjau tingkat efektivitas pada aspek ' . $title . ' secara berkala',
                    'Audit daya tarik penawaran di sektor ' . $title
                ])
            ],
            'Operations' => [
                Arr::random([
                    'Automatisasi flow Customer Service FAQ dengan bot/template',
                    'Pasang balasan otomatis WhatsApp untuk pertanyaan harga dan ongkir',
                    'Gunakan bot atau quick replies untuk mempercepat layanan pelanggan'
                ]),
                Arr::random([
                    'Delegasikan tugas rutin berulang ke staf internal',
                    'Pindahkan tanggung jawab input data administratif kepada asisten virtual',
                    'Serahkan kegiatan pelaporan harian ke penanggung jawab divisi'
                ]),
                Arr::random([
                    'Audit SOP workflow tim internal secara berkala',
                    'Lakukan inspeksi terhadap kepatuhan standar operasi bisnis',
                    'Perbaharui dokumen langkah kerja yang sudah kedaluwarsa penggunaannya'
                ]),
                Arr::random([
                    'Integrasi sistem notifikasi pesanan otomatis',
                    'Sambungkan toko online dengan sistem resi dan pengingat ke konsumen',
                    'Pastikan sistem pesanan memberikan email update secara instan'
                ]),
                Arr::random([
                    'Perbarui database kontak supplier & vendor',
                    'Mutakhirkan daftar nomor pemasok beserta harga beli materialnya',
                    'Rapihkan spreadsheet mitra bisnis eksternal untuk pemesanan cepat'
                ]),
                Arr::random([
                    'Review bottleneck atau masalah pada prosedur operasi aktif',
                    'Identifikasi langkah mana yang paling sering macet setiap akhir minggu',
                    'Cari akar masalah keterlambatan proses pada lini bisnis Anda'
                ])
            ],
            'Cashflow Acceleration' => [
                Arr::random([
                    'Tagih piutang yang tertunda dengan memberikan opsi diskon pelunasan awal',
                    'Hubungi pelanggan yang belum lunas dan dorong pembayaran hari ini',
                    'Cairkan invoice yang tertunda secepat mungkin untuk menyambung nafas kas'
                ]),
                Arr::random([
                    'Liquidasi stok mati dengan diskon besar atau bundling ke produk laku',
                    'Obraal inventory yang mandek di gudang agar berubah menjadi uang tunai',
                    'Ubah barang lambat laku menjadi paket bonus berbayar murah'
                ]),
                Arr::random([
                    'Negosiasi perpanjangan tempo pembayaran dengan dua supplier terbesar',
                    'Minta keringanan waktu jatuh tempo hutang ke vendor utama',
                    'Tahan pengeluaran kas dengan memundurkan jadwal bayar bahan baku'
                ])
            ],
            'Customer Retention' => [
                Arr::random([
                    'Kirimkan broadcast apresiasi kepada pelanggan yang pernah membeli bulan lalu',
                    'Tanya kabar pembeli lama tanpa menyisipkan jargon jualan apa pun',
                    'Sapa kembali konsumen setia untuk menjaga top-of-mind brand Anda'
                ]),
                Arr::random([
                    'Buat program loyalty sederhana, misalnya diskon 10% untuk pembelian kedua',
                    'Tawarkan voucher khusus untuk pelanggan yang belum order dalam 30 hari terakhir',
                    'Sediakan kartu member digital berisi poin tiap kali mereka bertransaksi'
                ]),
                Arr::random([
                    'Kumpulkan feedback/review dari 10 pembeli pertama bulan ini',
                    'Minta ulasan jujur tentang layanan Anda kepada pelanggan terdekat',
                    'Lakukan survei singkat kepuasan pelanggan dengan hadiah e-book gratis'
                ])
            ],
            'Strategic Repositioning' => [
                Arr::random([
                    'Riset ulang satu masalah terbesar pasar yang belum diselesaikan kompetitor',
                    'Temukan celah kebutuhan pelanggan yang tidak dilirik oleh merek pesaing',
                    'Wawancarai prospek untuk memvalidasi asusmsi baru Anda mengenai produk'
                ]),
                Arr::random([
                    'Pilih satu segmen niche terkecil yang paling menguntungkan untuk difokuskan',
                    'Buang target pasar yang luas dan fokus melayani satu grup spesifik saja',
                    'Kecilkan kolam target audiens Anda demi pesan pemasaran yang lebih tajam'
                ]),
                Arr::random([
                    'Tulis ulang Headline penawaran utama agar lebih relevan dengan pain point audiens',
                    'Revisi kalimat pertama di landing page agar menghentak masalah psikologis pembaca',
                    'Perbaiki copywriting promosi dengan fokus pada manfaat ketimbang fitur produk'
                ])
            ],
            default => [
                Arr::random([
                    'Riset lebih lanjut dan susun rencana kerja mengenai ' . $title,
                    'Perdalam pemahaman tentang ' . $title . ' dan buat kerangka kerjanya',
                    'Mulai riset pasar seputar topik ' . $title . ' dalam minggu ini'
                ]),
                Arr::random([
                    'Tentukan penanggung jawab (PIC) eksekusi step ini',
                    'Delegasikan tanggung jawab pengawasan untuk langkah taktis tersebut',
                    'Tunjuk satu orang khusus sebagai eksekutor utama tugas terkait'
                ]),
                Arr::random([
                    'Buat timeline & tenggat waktu penyelesaian jelas',
                    'Pastikan rincian ini memiliki tenggat waktu eksekusi yang tegas',
                    'Gunakan kalender proyek untuk menentukan target selesai'
                ]),
                Arr::random([
                    'Cari tahu best practice di industri/kompetitor',
                    'Jelajahi contoh implementasi sukses dari pesaing terdekat',
                    'Teliti tolok ukur (benchmark) standar dari pemain industri serupa'
                ]),
                Arr::random([
                    'Monitor hasil metrik setiap akhir pekan',
                    'Analisis performa data secara periodik seminggu sekali',
                    'Luangkan waktu tiap Jumat/Sabtu mengevaluasi angka statistik pelaporan'
                ]),
                Arr::random([
                    'Lakukan penyesuaian strategi (iteratif) mingguan',
                    'Segera lakukan modifikasi jika metode yang ada tidak membawa kemajuan',
                    'Sesuaikan arah haluan dengan cepat manakala iterasi perdana gagal'
                ])
            ]
        };
    }

    private static function generateTagsFromSession(MentorSession $session)
    {
        $tags = [];
        // MentorSession stores baseline_json with financial results from FinancialEngine
        $baseline = $session->baseline_json ?? [];
        $margin = (float) ($baseline['margin'] ?? $baseline['net_margin'] ?? 0);
        // Let's use simple logic based on what we have.
        
        // A. Survival / Efficiency
        if ($margin < 0.15) {
            $tags[] = 'Margin Improvement';
        }

        // B. Traffic / Growth
        // If traffic < break even traffic -> Traffic Scaling (Urgent)
        // If traffic > break even but low growth -> Traffic Scaling
        $tags[] = 'Traffic Scaling'; // Almost always needed for growth

        // C. Monetization / Upsell
        // Unless margin is terrible, Upsell is good.
        if ($margin > 0.05) {
            $tags[] = 'Monetization Expansion';
        }
        
        return array_unique($tags);
    }

    private static function buildStepsFromDiagnoses(Roadmap $roadmap, array $diagnoses)
    {
        $order = 1;

        foreach ($diagnoses as $diagnosis) {
            $title = $diagnosis['title'] ?? 'Strategic Priority';
            $category = self::guessCategoryFromTitle($title);

            // Create Step corresponding to the diagnosis
            $step = RoadmapStep::create([
                'roadmap_id' => $roadmap->id,
                'title' => $title,
                'description' => $diagnosis['description'] ?? 'Fokus resolusi untuk masalah operasional utama.',
                'order' => $order,
                'status' => $order === 1 ? 'unlocked' : 'locked',
                'strategy_tag' => $category,
                'recommended_tools' => []
            ]);

            // Create Actions for this diagnosis
            $actions = $diagnosis['actions'] ?? [];

            if (is_array($actions) && count($actions) > 0) {
                foreach ($actions as $actionText) {
                    RoadmapAction::create([
                        'step_id' => $step->id,
                        'action_text' => $actionText,
                        'is_completed' => false
                    ]);
                }
            } else {
                // Fallback action if empty
                RoadmapAction::create([
                    'step_id' => $step->id,
                    'action_text' => 'Analisis mendalam dan jadwalkan perbaikan untuk area ini',
                    'is_completed' => false
                ]);
            }

            $order++;
        }

        $roadmap->update(['total_steps' => $order - 1]);
    }

    private static function buildSteps(Roadmap $roadmap, array $tags)
    {
        // Fetch Blueprints
        $blueprints = StrategyBlueprint::whereIn('strategy_tag', $tags)
            ->orderBy('priority_level', 'asc') // 1 (Survival) First
            ->get();

        // If no DB blueprints (e.g. first run), fallback to code defaults
        if ($blueprints->isEmpty()) {
            $blueprints = self::getFallbackBlueprints($tags);
        }

        $order = 1;

        foreach ($blueprints as $bp) {
            // Create Step
            $step = RoadmapStep::create([
                'roadmap_id' => $roadmap->id,
                'title' => $bp->step_title, // Use attribute access
                'description' => $bp->step_description,
                'order' => $order,
                'status' => $order === 1 ? 'unlocked' : 'locked',
                'strategy_tag' => $bp->strategy_tag,
                'recommended_tools' => $bp->recommended_tools ?? []
            ]);

            // Create Actions
            $actions = is_string($bp->default_actions) ? json_decode($bp->default_actions, true) : $bp->default_actions;
            
            if (is_array($actions)) {
                foreach ($actions as $actionText) {
                    RoadmapAction::create([
                        'step_id' => $step->id,
                        'action_text' => $actionText,
                        'is_completed' => false
                    ]);
                }
            }
            
            $order++;
        }

        $roadmap->update(['total_steps' => $order - 1]);
    }

    private static function getFallbackBlueprints(array $tags)
    {
        $collection = collect();
        
        if (in_array('Margin Improvement', $tags)) {
            $collection->push((object)[
                'strategy_tag' => 'Margin Improvement',
                'step_title' => Arr::random(['Audit Structure Biaya & COGS', 'Tinjau Rincian Harga Pokok Penjualan', 'Analisis Harga Modal Terhadap Jual']),
                'step_description' => Arr::random([
                    'Margin Anda di bawah target. Identifikasi pemborosan dan optimalisasi biaya untuk mengamankan profitabilitas dasar dari bisnis.',
                    'Persentase laba terlalu tipis. Lakukan penguraian biaya dan pangkas hal-hal tidak krusial sesegera mungkin.',
                    'Keuntungan bersih belum ideal. Cermati harga beli barang Anda dan optimalkan dari sisi fundamental operasional.'
                ]),
                'default_actions' => [
                    Arr::random([
                        'List kembali (audit) semua Fixed Cost dan Variable Cost secara rinci bulanan.',
                        'Rekap ulang pengeluaran tetap bulanan dan catat rincian variabel biaya tambahan.',
                        'Buat daftar audit pengeluaran mendetil demi mengungkap kebocoran pengeluaran yang tersembunyi.'
                    ]),
                    Arr::random([
                        'Dapatkan rincian terbaru harga modal (COGS) dari supplier dan hitung persentase terhadap harga jual.',
                        'Mintakan pembaruan daftar harga ke pemasok utama untuk memeriksa selisih dengan harga ritel Anda.',
                        'Lakukan analisis perbandingan antara modal asal dengan harga eceran demi menemukan celah margin.'
                    ]),
                    Arr::random([
                        'Prioritaskan 3 supplier terbesar dan jadwalkan negosiasi ulang untuk mendapatkan potongan harga massal atau term pembayaran yang lebih longgar.',
                        'Hubungi vendor utama guna merundingkan kelonggaran tempo pembayaran kredit usaha.',
                        'Diskusikan opsi pembelian kuantitas lebih besar kepada distributor untuk memenangkan diskon persentase tambahan.'
                    ]),
                    Arr::random([
                        'Identifikasi dan eliminasi minimal 3 biaya operasional/software opsional yang kurang produktif dalam 30 hari.',
                        'Hapus sekurangnya 3 beban tetap (langganan layanan atau sewa) yang return on investment-nya minim.',
                        'Babat seluruh pengeluaran lunak sekunder demi mempertahankan nafas kas perusahaan bulan ini.'
                    ]),
                    Arr::random([
                        'Analisis skenario kenaikan harga jual sebesar 5% hingga 10% tanpa mengurangi nilai produk.',
                        'Bandingkan efek terhadap minat konsumen bila terjadi kenaikan harga hingga sepuluh persen.',
                        'Buat perhitungan matematis untuk melihat seberapa jauh laba melonjak apabila harga jual dinaikkan perlahan.'
                    ]),
                    Arr::random([
                        'Sistemasi perekapan Laporan Laba Rugi untuk dilakukan evaluasi setiap minggu (bukan akhir bulan).',
                        'Berhentilah mengecek laporan untung rugi secara bulanan; ubah menjadi siklus periksa mingguan yang ketat.',
                        'Perketat kontrol dengan mengevaluasi neraca pencapaian margin tiap hari Jumat sore.'
                    ])
                ],
                'recommended_tools' => ['Trello untuk task audit', 'Google Sheets / Excel Template Cashflow', 'Jurnal.id atau Accurate'],
                'priority_level' => 1
            ]);
            $collection->push((object)[
                'strategy_tag' => 'Margin Improvement',
                'step_title' => Arr::random(['Optimalisasi Nilai & Pricing', 'Rekayasa Paket Penawaran', 'Peningkatan Persepsi Harga']),
                'step_description' => Arr::random([
                    'Meningkatkan harga tanpa mengurangi konversi adalah kunci margin sehat. Evaluasi "Perceived Value" produk Anda.',
                    'Pendongkrakan harga jual yang dibarengi kualitas adalah cara tercepat melipatgandakan keuntungan bersih tanpa menambah pembeli.',
                    'Alih-alih bersaing harga murah, perkuat layanan premium di sekitar penawaran agar pembeli tidak sensitif pada biaya.'
                ]),
                'default_actions' => [
                    Arr::random([
                        'Lakukan riset kompetitor langsung: petakan harga, keunggulan, dan kelemahan 5 produk pesaing terdekat.',
                        'Teliti lima merek kompetitor: cari tahu letak celah di penawaran mereka yang bisa Anda manfaatkan.',
                        'Lakukan perbandingan harga rival selevel dan klasifikasikan mengapa audiens tetap mau membeli dari mereka.'
                    ]),
                    Arr::random([
                        'Cari setidaknya satu fitur atau layanan ekstra yang dapat dilampirkan ke produk untuk mendongkrak persepsi nilainya.',
                        'Pasangkan produk dengan bonus berbiaya murah tapi bernilai persepsi tinggi, misal e-book eksklusif.',
                        'Sisipkan garansi khusus yang sulit ditiru kompetitor guna membangun kepercayaan dan membenarkan harga premium.'
                    ]),
                    Arr::random([
                        'Uji coba kenaikan harga pada satu produk Best-Seller ke sebagian kecil audiens (A/B testing harga).',
                        'Aplikasikan rekayasa harga ke barang yang paling laris untuk melihat elastisitas daya beli pelanggan.',
                        'Naikkan banderol harga secara hati-hati (micro-testing) dan monitor reaksinya terhadap angka konversi total.'
                    ]),
                    Arr::random([
                        'Siapkan Bundling produk (Beli 2 Lebih Murah) untuk mendongkrak Average Order Value (AOV).',
                        'Tawarkan pembelian keranjang kombo agar nilai total belanja pembeli bertambah signifikan.',
                        'Dorong angka AOV melalui promo paket berjejer yang mengesankan pembelian grosir terasa lebih hemat.'
                    ]),
                    Arr::random([
                        'Rancang strategi diskon yang cerdas: batasi diskon hanya untuk clearance barang lambat rilis, jangan mendiskon produk Tier 1.',
                        'Haramkan potongan harga brutal pada barang utama. Gunakan promo flash sale murni untuk obral stok menumpuk.',
                        'Jangan merombak struktur margin demi diskon; berusahalah mempertahankan integritas harga produk pemenang (winning product).'
                    ])
                ],
                'recommended_tools' => ['Canva untuk grafis bundling', 'Form Google (Survey Pelanggan)'],
                'priority_level' => 2
            ]);
        }
        
        if (in_array('Traffic Scaling', $tags)) {
            $collection->push((object)[
                'strategy_tag' => 'Traffic Scaling',
                'step_title' => Arr::random(['Akselerasi Konten Organik', 'Eskalasi Jangkauan Non-Berbayar', 'Sistemasi Publikasi Media Sosial']),
                'step_description' => Arr::random([
                    'Membangun pondasi trafik jangka panjang melalui penyebaran konten massif di multi-platform tanpa biaya iklan ekstra.',
                    'Penciptaan audiens tanpa henti lewat distribusi konten vertikal gratis adalah jaminan ketahanan bisnis.',
                    'Merajut strategi arus lalu lintas stabil bermodal edukasi profil media sosial yang berkesinambungan.'
                ]),
                'default_actions' => [
                    Arr::random([
                        'Buat Content Calendar 1 bulan (Minimal 20 ide konten yang menjawab Pain Points target market).',
                        'Kembangkan kalender pemuatan pos harian yang didedikasikan sepenuhnya memecahkan masalah klien Anda.',
                        'Tulis sekurang-kurangnya daftar dua puluh topik diskusi yang kerap ditanyakan pengikut (FAQ konten).'
                    ]),
                    Arr::random([
                        'Sistemasi pembuatan konten dengan metode Batch Creation (Rekam 10 video sekaligus dalam 1 hari).',
                        'Satukan hari khusus syuting: produksi puluhan draf video dalam sekali tempo rekaman.',
                        'Gunakan teknik perekaman maraton guna meraih efisiensi waktu penyusunan konten grafis mingguan.'
                    ]),
                    Arr::random([
                        'Implementasi strategi Repurpose: Ubah 1 Video TikTok menjadi Reels Instagram, YouTube Shorts, dan Carousel berdasar inti videonya.',
                        'Format ulang satu video pilar panjang menjadi bentuk Reels, tulisan utas di LinkedIn, maupun status interaktif WhatsApp.',
                        'Ekstrak materi dari sebuah rekaman utama ke berbagai tipe media guna mendominasi beragam jejaring sosial.'
                    ]),
                    Arr::random([
                        'Optimasi profil media sosial: pastikan Bio menjelaskan value proposition dalam 10 detik pertama, dan LinkTree/Contact berfungsi jelas.',
                        'Rapihkan biografi media sosial supaya pengunjung profil tahu dengan persis apa yang bisnis Anda jual.',
                        'Uji kecepatan akses URL di biodata usaha; pastikan orang dapat terhubung dengan nomor kasir tanpa bingung.'
                    ]),
                    Arr::random([
                        'Bangun rutinitas interaksi: Balas setiap komentar 30 menit setelah publish, kunjungi akun target audiens teratas dan tinggalkan jejak.',
                        'Lakukan tur balas komentar pada setengah jam awal penayangan publikasi guna mengerek algoritma jangkauan.',
                        'Libatkan diri pada perbincangan akun-akun komunitas sepemikiran demi numpang bersinar di atas pangsa pasar yang sudah kumpul.'
                    ])
                ],
                'recommended_tools' => ['Notion / Asana untuk Content Calendar', 'CapCut untuk editing', 'Hootsuite / Metricool'],
                'priority_level' => 2
            ]);
             $collection->push((object)[
                'strategy_tag' => 'Traffic Scaling',
                'step_title' => Arr::random(['Eksperimen Paid Traffic (Ads)', 'Eksekusi Trafik Berbayar', 'Kampanye Akuisisi Cepat Melalui Iklan']),
                'step_description' => Arr::random([
                    'Menguji coba kanal trafik berbayar untuk mendapatkan volume visitor yang cepat, terukur, dan tertarget.',
                    'Menginjeksi dorongan traksi pengunjung melalui alokasi dana kampanye iklan bertarget yang terkontrol presisi.',
                    'Pusaran tes peruntungan materi komersil menggunakan dashboard digital ads guna menilai respons instan dari market.'
                ]),
                'default_actions' => [
                    Arr::random([
                        'Setup & verifikasi aset iklan (Meta Business Manager, Tiktok Ads, Pixel tertanam aktif di Landing Page).',
                        'Cek kematangan pemasangan kode Pixel/sistem perekaman di halaman situs Anda sebelum uang dibakar.',
                        'Lengkapi struktur pendaftaran izin periklanan agar materi promosi tidak ditangguhkan.'
                    ]),
                    Arr::random([
                        'Buat 3 angle kreatif terpisah untuk materi iklan: Pain Point (fokus masalah), Benefit (fokus solusi), Testimonial (Social Proof).',
                        'Pasok pengujian awal kampanye dengan tiga orientasi beda: edukasi kegunaan, pamer pengakuan pembeli, dan intimidasi ketakutan prospek.',
                        'Siapkan portofolio materi gambar/video beragam sudut pandang, mulai dari unboxing sampai komparasi head-to-head.'
                    ]),
                    Arr::random([
                        'Lakukan testing Campaign Budget Optimization (CBO) dengan budget kecil selama 3-4 hari berturut-turut tanpa jeda.',
                        'Aktivasi sistem optimalisasi pendanaan otomatis dan evaluasi ketahanannya secara utuh selama tiga hari.',
                        'Gunakan model angaran harian minim tanpa campur tangan editing selama tiga hari pertama penayangan beruntun.'
                    ]),
                    Arr::random([
                        'Lakukan audit metrik kunci pada hari ke-3: Cost Per Click (CPC) maksimal 2000, Click Through Rate (CTR) minimal 1.5%.',
                        'Analisa temuan statistik pasca pengetesan 72 jam untuk menyingkirkan grup iklan bermasalah.',
                        'Simpulkan tingkat biaya per aksi/konversi di malam hari ketiga, cari mana yang paling bisa diandalkan.'
                    ]),
                    Arr::random([
                        'Matikan (Kill) aset iklan yang underperforming, dan duplikat (Scale) dengan kenaikan budget 20% pada aset yang terkonfirmasi profit.',
                        'Singkirkan dengan sigap semua gambar promosi yang boncos, dan lipatgandakan pasokan modal pada setoran kemenangan.',
                        'Naikkan anggaran penyajian sebesar dua puluh persen hari per hari hanya pada versi iklan berrasio untung tinggi.'
                    ])
                ],
                'recommended_tools' => ['Meta Ads Library untuk riset', 'TikTok Creative Center', 'Google Analytics'],
                'priority_level' => 3
            ]);
        }

        if (in_array('Monetization Expansion', $tags)) {
             $collection->push((object)[
                'strategy_tag' => 'Monetization Expansion',
                'step_title' => Arr::random(['Ekspansi Upsell & Relasi Pelanggan', 'Pelebaran Model Upsell Keranjang Kelancaran', 'Panen Profit Transaksi Lampau']),
                'step_description' => Arr::random([
                    'Maksimalkan Revenue dari setiap customer yang masuk, karena biaya akuisisi (CAC) sudah Anda bayarkan di awal.',
                    'Suntikkan pengadaan sistem pelengkap penjualan berjejer yang menjaring pendapatan berlebih dari audiens gratis.',
                    'Eksploitasi kumpulan prospek masa kuno untuk menghasilkan putaran pembelian peredaran tertunda bernilai premium.'
                ]),
                'default_actions' => [
                    Arr::random([
                        'Identifikasi 2 produk komplementer (murah tapi margin tinggi) untuk teknik Cross-Sell di halaman checkout.',
                        'Munculkan pop up tambahan barang receh ketika orang akan mentransfer; ini mendorong margin murni bertambah.',
                        'Gunakan tawaran aksesoris murah (bump offer) bertepat sebelah kanan kotak tombol konfirmasi pesanan akhir.'
                    ]),
                    Arr::random([
                        'Buat penawaran Premium (Upsell) yang disodorkan SETELAH pelanggan melakukan pembelian pertama (One-Click Upsell).',
                        'Berikan diskon satu kesempatan untuk versi produk paripurna segera usai kartu tergesek sukses.',
                        'Rancang urutan penjualan beranak tangga, di mana tawaran maha-untung ditebar di halaman pasca terima kasih transaksinya.'
                    ]),
                    Arr::random([
                        'Siapkan sistem Follow-Up via WhatsApp/Email untuk prospek yang Abandoned Cart dengan penawaran terbatas 24 Jam.',
                        'Bangun keraton retensi dari konsumen keranjang tercecer melalui penyapaan hangat via pesan pendek manual.',
                        'Eksekusi jalur tembakan surel pemulihan otomatis bilamana pembeli mengapung di tengah penyelesaian pembayaran.'
                    ]),
                    Arr::random([
                        'Manfaatkan database pembeli lama: Blast penawaran Flash Sale eksklusif hanya untuk Repeat Customer.',
                        'Berikan kejutan hadiah undian dan privilese pembelanjaan untuk mantan pembeli yang tak dilirik konsumen baru rata rata.',
                        'Sapa nomor database pelanggan Anda dengan kalimat non-jualan sebelum menyarankan program hadiah spesial akhir pecan.'
                    ]),
                    Arr::random([
                        'Siapkan Garansi Uang Kembali / Kepuasan Ekstrem yang berani untuk menaikkan tingkat Trust dan Closing Rate akhir.',
                        'Sematkan jaminan tukar menukar dengan syarat termudah sedunia untuk menurunkan risiko kekhawatiran pembelian awal secara empiris.',
                        'Berikan jaminan hasil (misal: sukses dalam 30 hari atau diganti baru) tanpa kompromi guna meledakkan jumlah pesanan terwujud seketika.'
                    ])
                ],
                'recommended_tools' => ['OrderOnline / Plugin WooCommerce', 'Watzap / Kirim.Email', 'Klaviyo'],
                'priority_level' => 3
            ]);
        }

        if (in_array('Cashflow Acceleration', $tags)) {
             $collection->push((object)[
                'strategy_tag' => 'Cashflow Acceleration',
                'step_title' => Arr::random(['Amankan Uang Tunai Segera', 'Injeksi Likuiditas Darurat', 'Putar Cepat Piutang dan Stok']),
                'step_description' => Arr::random([
                    'Meski laporan mencatat untung, kas menipis. Prioritaskan konversi aset mengendap menjadi dana likuid secepatnya.',
                    'Darah bisnis adalah kas. Jangan biarkan uang Anda terjebak dalam barang tidak terjual atau janji bayar pelanggan.',
                    'Lakukan taktik penyelamatan saldo rekening dengan melikuidasi apapun yang kurang produktif di bulan ini.'
                ]),
                'default_actions' => [
                    Arr::random([
                        'Rekap semua piutang pelanggan dan kerahkan tim khusus penagihan dengan target tuntas minggu ini.',
                        'Kirimkan pesan pengingat tagihan ke seluruh klien yang jatuh tempo dengan janji prioritas layanan.',
                        'Tagih segala bentuk hutang yang tertunda walau harus memberi diskon kecil demi asupan kas instan.'
                    ]),
                    Arr::random([
                        'Sortir barang di gudang yang belum laku selama 90 hari, lalu jual modal dalam format flash sale akhir pekan.',
                        'Adakan promosi cuci gudang brutal untuk dead stock demi mengonversinya menjadi uang tunai.',
                        'Hancurkan harga produk sisa pakai teknik bundling untuk menguras habis inventori lambat putar.'
                    ]),
                    Arr::random([
                        'Telepon langsung supplier bahan baku untuk negosiasi perpanjangan batas pembayaran 14 hari lagi.',
                        'Undur jadwal pelunasan kepada mitra pemasok guna meregangkan beban arus kas minggu ini.',
                        'Restrukturisasi hutang berjangka pendek Anda kepada vendor agar nafas perusahaan tidak sesak.'
                    ])
                ],
                'recommended_tools' => ['BukuKas / Jurnal.id', 'Excel Laporan Piutang', 'Template Pesan Penagihan'],
                'priority_level' => 1
            ]);
        }
        
        if (in_array('Customer Retention', $tags)) {
             $collection->push((object)[
                'strategy_tag' => 'Customer Retention',
                'step_title' => Arr::random(['Bangun Kolam Pembeli Setia', 'Sistemasi Repeat Order', 'Maksimalkan LTV Pelanggan']),
                'step_description' => Arr::random([
                    'Akuisisi baru selalu mahal. Tingkatkan retensi untuk meraup keuntungan dari pelanggan yang sama berulang kali.',
                    'Pelanggan yang tidak kembali adalah kebocoran terbesar. Ciptakan aliran belanja berulang dari kontak yang ada.',
                    'Jangan pusing mencari pembeli baru saat pelanggan lama diabaikan. Pasang strategi kunci agar daftar kontak Anda senantiasa panas.'
                ]),
                'default_actions' => [
                    Arr::random([
                        'Seting pengingat otomatis untuk menyapa pembeli H+3 dan H+14 setelah pesanan pertama diterima.',
                        'Rancang draf pesan WhatsApp yang ramah untuk mengecek kepuasan pengguna di minggu pertama.',
                        'Standarisasi obrolan purna-jual untuk membuat konsumen terkesan pada kecepatan tanggap bisnis Anda.'
                    ]),
                    Arr::random([
                        'Tawarkan insentif diskon rahasia khusus kepada kalangan kontak pembeli yang sudah 3 bulan tidak order.',
                        'Sebarkan voucher kembalikan kenangan manis untuk menarik mantan pembeli bersilaturahmi transaksi lagi.',
                        'Ciptakan paket pelengkap pembelian pertama dengan potongan harga istimewa khusus pelanggan ulangan.'
                    ]),
                    Arr::random([
                        'Mintakan testimoni atau review jujur dari pembeli terpilih dengan kompensasi suvenir atau ebook kecil.',
                        'Pancing partisipasi pelanggan bercerita tentang keunggulan produk Anda guna mendongkrak status loyalitasnya.',
                        'Tampung curhatan pembeli prioritas untuk memperbaiki lobang-lobang tak terlihat pada pengalaman berbelanja.'
                    ])
                ],
                'recommended_tools' => ['Mailchimp / Kirim.Email', 'Watzap / WA Gateway', 'Google Forms untuk Survei'],
                'priority_level' => 3
            ]);
        }
        
        if (in_array('Strategic Repositioning', $tags)) {
             $collection->push((object)[
                'strategy_tag' => 'Strategic Repositioning',
                'step_title' => Arr::random(['Revisi Tajam Model Bisnis', 'Pertajam Sudut Penawaran', 'Fokus Ulang Produk Pemenang']),
                'step_description' => Arr::random([
                    'Banyak arah namun tanpa fokus akan menghasilkan kebuntuan omzet. Tajamkan pesan dan singkirkan penawaran lemah.',
                    'Produk stagnan butuh posisi pembanding baru. Kemas kembali sudut pandang penjualan agar memicu rasa butuh segera.',
                    'Perbaiki fondasi penawaran Anda manakala pasar merespon dengan keengganan. Anda hanya perlu satu angle pemenang.'
                ]),
                'default_actions' => [
                    Arr::random([
                        'Susun ulang kalimat penawaran utama di halaman website dan pastikan menyentuh titik nyeri spesifik pembaca.',
                        'Ganti janji manis produk dengan jaminan mematikan atas masalah terbesar satu segmen konsumen.',
                        'Poles judul promosi agar tidak berasa jualan fitur, melainkan jualan masa depan yang lebih baik.'
                    ]),
                    Arr::random([
                        'Buang sementara promosi 80% daftar produk Anda, fokus seluruh energi pada 20% item penopang omzet terbesar.',
                        'Hentikan penawaran beragam tipe layanan yang bikin pusing; konsentrasi menjuarai satu bidang tersembunyi.',
                        'Kurangi distraksi sku barang; dorong kampanye besar besaran pada produk paling untung dan paling gampang laku.'
                    ]),
                    Arr::random([
                        'Wawancarai tiga sampai lima pelanggan favorit secara mendalam untuk menangkap alasan sejati mereka memilih brand Anda.',
                        'Gunakan bahasa pelanggan setia untuk mendikte pembuatan skrip iklan perburuan prospek bulan ini.',
                        'Petakan anatomi buyer persona pemenang dan sasar khusus audiens tipe ini pakai metode penargetan mikro.'
                    ])
                ],
                'recommended_tools' => ['Google Docs untuk Copywriting', 'Meta Ads Library', 'Laporan Penjualan internal'],
                'priority_level' => 2
            ]);
        }
        
        // Sort by Priority
        return $collection->sortBy('priority_level');
    }
}
