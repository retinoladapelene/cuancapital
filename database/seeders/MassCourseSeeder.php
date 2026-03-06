<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;

class MassCourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['title' => 'Product Ideation & Validasi Market', 'description' => 'Pelajari cara meracik ide produk yang relevan dan mengujinya di pasar sebelum keluar modal besar.', 'level' => 'beginner', 'category' => 'product', 'lessons' => ['Cara Brainstorming Ide Bisnis yang Relevan', 'Menganalisis Kebutuhan Market (Pain Points)', 'Memetakan Persona Customer Ideal', 'Competitor Analysis & Benchmark', 'Konsep Minimum Viable Product (MVP)', 'Membuat Prototipe Tanpa Coding', 'Validasi Ide Menggunakan Landing Page Sederhana', 'Menjalankan Pre-order untuk Proof of Concept', 'Mengumpulkan Feedback Pelanggan Pertama', 'Product-Market Fit: Kapan Harus Scale Up?']],
            ['title' => 'Branding & Identitas Visual', 'description' => 'Bangun merek yang kuat, mudah diingat, dan profesional.', 'level' => 'beginner', 'category' => 'marketing', 'lessons' => ['Psikologi Warna dalam Bisnis', 'Tips Memilih Nama Brand yang Tepat', 'Logo: Elemen Penting & Kesalahan Umum', 'Brand Voice: Cara Brand Kamu Berbicara', 'Menyusun Brand Guideline Sederhana', 'Visual Konsisten di Media Sosial', 'Packaging Experience untuk Produk Fisik', 'Positioning Brand di Mata Customer', 'Membangun Kepercayaan (Trust) via Desain', 'Evolusi Brand Seiring Pertumbuhan']],
            ['title' => 'Copywriting & Storytelling', 'description' => 'Kuasai seni merangkai kata yang mendorong konversi penjualan.', 'level' => 'intermediate', 'category' => 'marketing', 'lessons' => ['Anatomi Copywriting yang Menjual', 'Rahasia Headline yang Sulit Diabaikan', 'Formula AIDA dalam Praktek Nyata', 'Formula PAS (Problem-Agitate-Solve)', 'Storytelling untuk Membangun Emosi', 'Mendesain Call to Action (CTA) yang Efektif', 'Copywriting untuk Landing Page', 'Copywriting untuk Caption Media Sosial', 'Menulis Email Marketing yang Dibuka & Dibaca', 'Editing & A/B Testing Teks']],
            ['title' => 'Digital Marketing: Social Media Organic', 'description' => 'Gunakan TikTok, Instagram, dan X untuk mendapatkan trafik gratis.', 'level' => 'intermediate', 'category' => 'marketing', 'lessons' => ['Memahami Algoritma Platform Terbaru', 'Content Pillar & Kalender Editorial', 'Membuat Video Pendek (Shorts/Reels) yang Viral', 'Pentingnya Hook di 3 Detik Pertama', 'Community Management & Engagement', 'Strategi Hashtag & Optimasi SEO Profil', 'Kolaborasi dengan Micro-Influencer', 'User Generated Content (UGC)', 'Merawat Audience via Instagram Stories', 'Analisis Metrik Konten Organik']],
            ['title' => 'Meta & Google Ads Mastery', 'description' => 'Scale bisnis dengan trafik berbayar dan targeting yang presisi.', 'level' => 'intermediate', 'category' => 'marketing', 'lessons' => ['Perbedaan Fundamental Meta Ads vs Google Ads', 'Struktur Campaign, Ad Set, dan Ads', 'Riset Audience & Custom Targeting', 'Membuat Kreatif Iklan yang Stop-Scrolling', 'Mengatur Budget & Bidding Strategy', 'Retargeting: Mengejar Pengunjung yang Kabur', 'Lookalike Audience untuk Scale-Up', 'Membaca dan Menganalisis Metrik Iklan', 'A/B Testing Iklan Secara Sistematis', 'Scaling Campaign yang Sudah Profitable']],
            ['title' => 'Manajemen Keuangan Bisnis', 'description' => 'Kuasai cashflow, pricing, dan laporan keuangan sederhana.', 'level' => 'beginner', 'category' => 'finance', 'lessons' => ['Memisahkan Keuangan Pribadi & Bisnis', 'Menghitung HPP (Harga Pokok Produksi)', 'Strategi Pricing yang Menguntungkan', 'Memahami Cashflow & Arus Kas', 'Laporan Laba Rugi Sederhana', 'Mengelola Piutang & Hutang Bisnis', 'Break Even Point (BEP): Kapan Mulai Untung?', 'Budgeting untuk Pertumbuhan Bisnis', 'Pajak Dasar untuk Pemilik UMKM', 'Reinvestasi: Cara Membesarkan Bisnis dari Profit']],
            ['title' => 'Sales Funnel & Konversi', 'description' => 'Bangun sistem penjualan yang bekerja otomatis 24 jam sehari.', 'level' => 'advanced', 'category' => 'sales', 'lessons' => ['Anatomy of a High-Converting Funnel', 'Lead Magnet: Menarik Calon Pembeli Gratis', 'Landing Page yang Mengkonversi', 'Email Sequence untuk Nurture Lead', 'WhatsApp Funnel untuk Bisnis Lokal', 'Upsell, Cross-sell & Order Bump', 'Menghitung dan Mengoptimalkan Conversion Rate', 'Customer Journey Mapping', 'Mengurangi Friction di Proses Checkout', 'Mengukur ROI dari Setiap Channel Penjualan']],
            ['title' => 'Operasional & Skalabilitas Bisnis', 'description' => 'Bangun sistem yang membuat bisnis bisa berjalan tanpa kamu.', 'level' => 'advanced', 'category' => 'operations', 'lessons' => ['Membuat SOP (Standard Operating Procedure)', 'Rekrutmen & Onboarding Tim Pertama', 'Manajemen Supplier & Rantai Pasok', 'Inventory Management & Stock Control', 'Customer Service System yang Skalabel', 'Otomasi dengan Tools Digital', 'KPI dan OKR untuk Tim Kecil', 'Delegasi Efektif & Manajemen Waktu', 'Unit Economics: Pondasi Scaling', 'Dari Solopreneur ke Tim: Mindset Shift']],
            ['title' => 'E-commerce Mastery', 'description' => 'Dominasi marketplace dan toko online dengan strategi terbukti.', 'level' => 'advanced', 'category' => 'ecommerce', 'lessons' => ['Memilih Platform yang Tepat: Tokopedia vs Shopee vs Website', 'Optimasi Listing Produk untuk SEO Marketplace', 'Foto Produk yang Menaikkan Konversi', 'Strategi Review & Reputasi Toko', 'Manajemen Stok & Fulfillment', 'Flash Sale & Promosi Marketplace', 'Membuka Toko Online Sendiri (Website)', 'Integrasi Pembayaran & Logistik', 'Analitik Toko & Optimasi Kinerja', 'Ekspansi ke Multi-Channel Selling']],
            ['title' => 'Growth Hacking & Acquisition', 'description' => 'Strategi pertumbuhan eksponensial untuk startup dan UMKM modern.', 'level' => 'expert', 'category' => 'growth', 'lessons' => ['Mindset Growth Hacker vs Marketer Biasa', 'Viral Loop: Membuat Produk yang Menyebar Sendiri', 'Referral Program yang Menguntungkan', 'SEO untuk Trafik Organik Jangka Panjang', 'Content Marketing sebagai Growth Engine', 'Partnership & Co-Marketing Strategy', 'Influencer Marketing dengan ROI Terukur', 'Community-Led Growth', 'Product-Led Growth (PLG)', 'Membangun Dashboard Pertumbuhan']],
        ];

        $createdCount = 0;

        foreach ($courses as $cData) {
            $course = Course::firstOrCreate(
                ['title' => $cData['title']],
                [
                    'description'    => $cData['description'],
                    'level'          => $cData['level'],
                    'category'       => $cData['category'],
                    'xp_reward'      => rand(200, 400),
                    'lessons_count'  => count($cData['lessons']),
                    'is_active'      => true,
                ]
            );

            foreach ($cData['lessons'] as $order => $lTitle) {
                $xp  = rand(30, 80);
                $min = rand(6, 12);
                $content = $this->generateLessonContent($lTitle, $cData['title']);

                $lesson = Lesson::where('course_id', $course->id)->where('title', $lTitle)->first();
                if ($lesson) {
                    $lesson->update(['content' => $content, 'xp_reward' => $xp, 'estimated_minutes' => $min]);
                } else {
                    $course->lessons()->create([
                        'title'              => $lTitle,
                        'order'              => $order + 1,
                        'type'               => 'text',
                        'content'            => $content,
                        'xp_reward'          => $xp,
                        'estimated_minutes'  => $min,
                    ]);
                    $createdCount++;
                }
            }

            $course->update(['lessons_count' => $course->lessons()->count()]);
        }

        $this->command->info("MassCourseSeeder selesai: {$createdCount} pelajaran baru ditambahkan ke database.");
    }

    private function generateLessonContent(string $lessonTitle, string $courseTitle): string
    {
        $t = strtolower($lessonTitle);
        $caseStudy   = $this->pickCaseStudy($t, $lessonTitle);
        $infographic = $this->pickInfographic($t, $lessonTitle);
        $quiz        = $this->pickQuiz($t, $lessonTitle);
        $challenge   = $this->pickChallenge($t, $lessonTitle);

        $intros = [
            "**$lessonTitle** bukan teori basi dari buku kuliah. Ini adalah senjata operasional yang dipakai entrepreneur kelas dunia untuk menciptakan keunggulan kompetitif.",
            "Di balik setiap bisnis yang tumbuh pesat, selalu ada founder yang hafal luar kepala konsep **$lessonTitle**. Mari kita bongkar tuntas — dari konsep hingga eksekusi riilnya.",
            "Banyak pebisnis yang sudah berbulan-bulan belajar namun masih jalan di tempat karena melewatkan materi kritis **$lessonTitle**. Modul ini khusus membedah komponen ini dari akar hingga aplikasinya di lapangan.",
        ];
        $bridges1 = [
            "Teori tanpa kasus nyata itu hambar. Mari kita lihat bagaimana **$lessonTitle** memainkan peran menentukan di kisah bisnis berikut ini:",
            "Supaya insting bisnismu tertempa dengan benar, simak baik-baik skenario lapangan yang benar-benar terjadi ini:",
            "Sebelum kita masuk ke blueprint praktisnya, penting untuk kamu pahami gambaran nyata di lapangan terlebih dahulu:",
        ];
        $bridges2 = [
            "Dari studi kasus itu, kita bisa menarik kesimpulan penting. Berikut blueprint ringkasnya:",
            "Itulah pelajaran mahal yang bisa kamu ambil secara gratis. Internalisasi framework berikut ke dalam cara kerjamu:",
            "Kuncinya ada pada pola yang berulang. Jadikan visual berikut sebagai pegangan harianmu:",
        ];
        $bridges3 = [
            "Sebelum kita masuk ke tahap eksekusi, uji seberapa tajam instingmu terlebih dahulu:",
            "Framework sudah di tangan. Tapi apakah sudah masuk ke kepala? Buktikan dengan menjawab ini:",
            "Pastikan kamu benar-benar menguasai konsep ini sebelum melangkah ke tugas lapangan:",
        ];
        $closings = [
            "Pengetahuan yang kamu serap hari ini tidak ada artinya tanpa satu langkah nyata. Selesaikan Action Plan di bawah ini sebelum membuka lesson berikutnya.",
            "Sekarang saatnya eksekusi. Pengetahuan tanpa tindakan adalah kebohongan yang mahal. Kerjakan tugas ini detik ini juga:",
            "Selamat, kamu sudah selangkah lebih depan dari 90% pesaingmu yang hanya menonton. Buktikan dengan menyelesaikan challenge berikut:",
        ];

        $intro   = $intros[array_rand($intros)];
        $bridge1 = $bridges1[array_rand($bridges1)];
        $bridge2 = $bridges2[array_rand($bridges2)];
        $bridge3 = $bridges3[array_rand($bridges3)];
        $closing = $closings[array_rand($closings)];

        return <<<MD
{$intro}

{$bridge1}

[CASE_STUDY]
**Observasi Lapangan: {$lessonTitle}**
{$caseStudy}
[/CASE_STUDY]

{$bridge2}

{$infographic}

{$bridge3}

{$quiz}

{$closing}

[CHALLENGE]
**Action Plan Hari Ini:**
{$challenge}
[/CHALLENGE]
MD;
    }

    private function pickCaseStudy(string $t, string $title): string
    {
        if (str_contains($t, 'brainstorm') || str_contains($t, 'ide bisnis')) {
            return "Founder Tokopedia awalnya punya 37 ide bisnis di whiteboard. Filter brutalnya: 'ide mana yang bisa diuji dengan Rp 500.000 minggu ini?' 36 gugur. Satu bertahan. Pelajaran: bukan tentang ide terbanyak, tapi yang paling cepat bisa divalidasi.";
        }
        if (str_contains($t, 'persona') || str_contains($t, 'customer ideal')) {
            return "Sebuah brand tas lokal hampir tutup karena iklannya ditargetkan ke 'wanita 18-45 tahun'. Terlalu lebar. Setelah riset mendalam menemukan persona spesifik: 'Wanita 28-35 tahun, karyawan kantoran, gaji 8-15jt, sering meeting' — ROAS iklan mereka naik 8 kali lipat.";
        }
        if (str_contains($t, 'competitor') || str_contains($t, 'benchmark')) {
            return "Sebuah kafe di Bandung melakukan mystery shopping di 10 kafe kompetitor. Satu kelemahan bersama: tidak ada yang menyediakan colokan cukup untuk laptopan. Mereka pasang 40 colokan. Dalam 2 bulan, jadi 'kafe favorit freelancer' yang selalu penuh dari pagi sampai malam.";
        }
        if (str_contains($t, 'mvp') || str_contains($t, 'prototipe') || str_contains($t, 'validasi')) {
            return "Dropbox tidak langsung membangun software miliaran. Drew Houston membuat video 3 menit mendemonstrasikan bagaimana Dropbox *akan* bekerja — tanpa satu baris kode. Dalam satu malam, 75.000 orang mendaftar waitlist. MVP paling efektif adalah yang paling murah, bukan yang paling canggih.";
        }
        if (str_contains($t, 'pre-order') || str_contains($t, 'proof of concept')) {
            return "Seorang kreator kuliner buka pre-order buku resep ke 300 follower dengan harga Rp 150.000. Target: 50 pesanan. Hasilnya: 180 orang memesan dalam 48 jam — Rp 27 juta *sebelum* buku itu bahkan ditulis. Bukan keberuntungan — ini strategi proof of concept.";
        }
        if (str_contains($t, 'product-market fit') || str_contains($t, 'scale up')) {
            return "Spotify tahu sudah PMF ketika pengguna mulai *marah* saat layanan down. Founder Daniel Ek: 'Kamu tahu sudah PMF ketika kehilangan produkmu terasa seperti kehilangan anggota tubuh.' Itu barometer paling jujur.";
        }
        if (str_contains($t, 'warna') || str_contains($t, 'psikologi')) {
            return "Penelitian membuktikan warna biru membangun kepercayaan — kenapa hampir semua bank berwarna biru (Mandiri, BNI, PayPal). Warna merah menciptakan urgensi — kenapa tombol 'BELI SEKARANG' di marketplace hampir selalu merah. Brand kamu berbicara sebelum satu kata pun terbaca.";
        }
        if (str_contains($t, 'nama brand') || str_contains($t, 'naming')) {
            return "Dulu ada startup bernama 'BackRub'. Tidak ada yang takut. Lalu berganti menjadi 'Google'. Sejarah berubah. Nama brand wajib lolos 3 filter: mudah diucapkan, mudah dieja, mudah diingat bahkan oleh yang mendengarnya hanya sekali.";
        }
        if (str_contains($t, 'logo')) {
            return "Nike membeli logo Swoosh — yang dikenal 8 miliar orang — seharga 35 dolar dari mahasiswi desain tahun 1971. Bukan harga logo yang menentukan kekuatan brand. Yang menentukan adalah konsistensi penggunaan selama puluhan tahun di setiap titik kontak dengan pelanggan.";
        }
        if (str_contains($t, 'copywriting') || str_contains($t, 'headline')) {
            return "Gary Halbert mengubah satu kata dalam headline iklan dan meningkatkan respons 1.785%. Dari 'Gratis!' menjadi 'Gratis untuk Anda!'. Dua kata tambahan. Personalifikasi adalah senjata tersembunyi dalam setiap copy penjualan yang berhasil.";
        }
        if (str_contains($t, 'aida') || str_contains($t, 'formula')) {
            return "Penjual properti Surabaya menerapkan AIDA: Attention (foto drone golden hour), Interest (perbandingan harga per meter), Desire (video virtual tour), Action (tombol WA 'Jadwalkan Survei Hari Ini'). Hasilnya: 43 leads dalam 72 jam dari satu postingan.";
        }
        if (str_contains($t, 'storytelling') || str_contains($t, 'emosi')) {
            return "Iklan Dove 'Real Beauty Sketches' tidak menyebut sabun sekalipun. Selama 3 menit, hanya wanita nyata mendeskripsikan diri ke seniman forensik. Hasilnya: 114 juta penonton dalam sebulan, Cannes Grand Prix, penjualan naik 12% global. Cerita mengalahkan fakta produk.";
        }
        if (str_contains($t, 'tiktok') || str_contains($t, 'reels') || str_contains($t, 'video pendek')) {
            return "UMKM penjual keripik singkong Malang berhasil viral dengan video '3 Detik Tantangan Pedasannya' dan mendapat pesanan 10.000 pcs dari seluruh Indonesia dalam 48 jam — tanpa bayar influencer sepeser pun. Di TikTok, 3 detik pertama menentukan segalanya.";
        }
        if (str_contains($t, 'algoritma')) {
            return "Algoritma Instagram berubah 17 kali dalam 3 tahun terakhir. Brand yang bertahan punya satu prinsip yang tidak berubah: konten yang membuat orang berdiam lama selalu menang, apapun algoritmanya. Ukurlah Watch Time dan Save Rate, bukan sekadar likes.";
        }
        if (str_contains($t, 'influencer') || str_contains($t, 'ugc') || str_contains($t, 'kolaborasi')) {
            return "Brand skincare lokal mengalokasikan 100% budget ke 50 micro-influencer (10K-50K followers) alih-alih 1 mega. Hasilnya: engagement rate 8,4% vs 0,7%, biaya per konversi turun 74%. Audiens kecil yang terlibat personal selalu lebih mengkonversi.";
        }
        if (str_contains($t, 'meta ads') || str_contains($t, 'facebook ads') || str_contains($t, 'google ads') || str_contains($t, 'iklan')) {
            return "Toko online furnitur habiskan Rp 30 juta/bulan untuk Meta Ads dengan ROAS 1,2x. Setelah audit, 73% budget terbuang ke audience tak relevan. Restart dengan Lookalike dari 200 pembeli terbaik — ROAS melompat ke 6,8x dengan budget sama.";
        }
        if (str_contains($t, 'funnel') || str_contains($t, 'konversi') || str_contains($t, 'conversion')) {
            return "Amazon menemukan setiap 1 detik tambahan loading time menghasilkan kerugian 1,6 miliar dolar per tahun. Di level UMKM: setiap friction di funnel — form panjang, checkout membingungkan, CTA tidak ditemukan — adalah uang bocor diam-diam setiap menit.";
        }
        if (str_contains($t, 'cashflow') || str_contains($t, 'arus kas')) {
            return "FTX — valuasi 32 miliar dolar — bangkrut bukan karena kurang inovasi. Mereka gagal mengelola cashflow. Pelajaran terpenting: omzet besar tidak menjamin kelangsungan hidup. Hanya cashflow positif yang bisa membayar gaji dan menjaga lampu tetap menyala.";
        }
        if (str_contains($t, 'pricing') || str_contains($t, 'harga') || str_contains($t, 'hpp')) {
            return "Apple menaikkan harga iPhone dan berhasil menjual *lebih banyak*. Fenomena Veblen Good: harga premium menciptakan persepsi kualitas. Di Indonesia, '7up' diposisikan ulang lebih mahal dari Sprite — menghasilkan persepsi premium dan meningkatkan margin 40%.";
        }
        if (str_contains($t, 'seo') || str_contains($t, 'search engine')) {
            return "Blog kuliner kecil dari Yogyakarta membangun traffic 200.000 pengunjung/bulan tanpa satu rupiah iklan — semata konsisten mempublikasikan konten yang menjawab pertanyaan yang dicari orang di Google setiap hari.";
        }
        if (str_contains($t, 'email') || str_contains($t, 'whatsapp blast')) {
            return "Toko baju online naik omzet 35% semata dari membersihkan dan re-engaging database lama 5.000 email yang sudah 'mati' selama 8 bulan — tanpa satu rupiah untuk iklan baru. Email masih channel dengan ROI tertinggi: rata-rata Rp 420 kembali per Rp 1 diinvestasikan.";
        }
        if (str_contains($t, 'sop') || str_contains($t, 'operasional') || str_contains($t, 'sistem')) {
            return "McDonald's bukan tentang burger terenak di dunia. McDonald's tentang sistem yang bisa direplikasi oleh siapapun. Setiap gerai di 100+ negara menghasilkan produk yang persis sama karena SOP yang terdokumentasi dengan sangat ketat. Itulah kekuatan sistem.";
        }
        return "Seorang founder UMKM hampir menyerah setelah 6 bulan berjuang dengan **{$title}**. Begitu menemukan framework yang sesuai dan mengeksekusinya konsisten 30 hari, omzetnya naik 280% dalam satu kuartal. Kuncinya bukan strategi sempurna — melainkan mengeksekusi strategi 'cukup baik' dengan konsistensi brutal.";
    }

    private function pickInfographic(string $t, string $title): string
    {
        if (str_contains($t, 'brainstorm') || str_contains($t, 'ide bisnis')) {
            return "[INFOGRAPHIC:3 Filter Ide Bisnis yang Layak Dieksekusi]\nPain Point Nyata | Apakah ide ini menyelesaikan masalah yang orang MAU bayar?\nValidasi Cepat | Bisakah kamu uji dalam 7 hari dengan modal di bawah Rp 1.000.000?\nRepeatability | Akankah pelanggan membeli ulang, atau pembelian sekali seumur hidup?\n[/INFOGRAPHIC]";
        }
        if (str_contains($t, 'persona') || str_contains($t, 'customer')) {
            return "[INFOGRAPHIC:4 Dimensi Persona Customer]\nDemografi | Usia, gender, lokasi, pendapatan — angka yang bisa diukur.\nPsikografi | Gaya hidup, nilai, kekhawatiran, mimpi — tidak terlihat di KTP.\nPerilaku Digital | Platform favorit, jam aktif, konten yang di-save dan di-share.\nTitik Pemicu Beli | Apa emosi spesifik yang mendorong mereka merogoh dompet?\n[/INFOGRAPHIC]";
        }
        if (str_contains($t, 'pricing') || str_contains($t, 'harga') || str_contains($t, 'hpp')) {
            return "[INFOGRAPHIC:Formula Harga Anti Rugi]\nHPP Jujur | Totalkan SEMUA biaya: bahan, kemasan, ongkir, listrik, waktu kamu.\nMargin Minimum | Minimal 30% di atas HPP untuk bisnis yang sehat dan bisa scale-up.\nHarga Psikologis | Pakai angka ganjil: Rp 97.000 bukan Rp 100.000.\n[/INFOGRAPHIC]";
        }
        if (str_contains($t, 'copywriting') || str_contains($t, 'headline') || str_contains($t, 'aida')) {
            return "[INFOGRAPHIC:Struktur Copy yang Menjual]\nHook | Kalimat pertama yang meledakkan rasa ingin tahu atau sentuh pain point.\nBukti Sosial | Testimoni nyata — screenshot WA lebih dipercaya dari desain cantik.\nUrgensi | 'Stok tinggal 5' atau 'Harga naik Jumat' — ciptakan alasan bertindak SEKARANG.\n[/INFOGRAPHIC]";
        }
        if (str_contains($t, 'cashflow') || str_contains($t, 'arus kas') || str_contains($t, 'keuangan')) {
            return "[INFOGRAPHIC:3 Rekening Wajib Pemilik Bisnis]\nOperasional | Masuk dan keluarnya uang harian — JANGAN campur dengan lain.\nCadangan | Simpan minimum 3x biaya operasional bulanan sebagai safety net.\nPertumbuhan | Dana khusus reinvestasi: iklan, stok, atau pengembangan produk baru.\n[/INFOGRAPHIC]";
        }
        if (str_contains($t, 'konversi') || str_contains($t, 'funnel')) {
            return "[INFOGRAPHIC:Angka-Angka yang Harus Kamu Hafal]\nCTR | Berapa % yang klik dari semua yang melihat iklanmu? Avg: 1-3%.\nCVR | Berapa % yang beli dari yang masuk ke toko? E-commerce avg: 1-4%.\nAOV | Rata-rata nilai per transaksi. Naikkan AOV sebelum naikan budget iklan.\n[/INFOGRAPHIC]";
        }
        if (str_contains($t, 'seo') || str_contains($t, 'algoritma') || str_contains($t, 'organik')) {
            return "[INFOGRAPHIC:Pilar Konten Organik yang Menang]\nEdukasi | Ajarkan sesuatu berguna secara gratis — bangun kepercayaan dan otoritas.\nEntertain | Konten yang membuat tertawa = share rate tinggi = jangkauan melebar.\nInspirasi | Kisah transformasi nyata = save rate tinggi = sinyal positif ke algoritma.\n[/INFOGRAPHIC]";
        }
        if (str_contains($t, 'influencer') || str_contains($t, 'kolaborasi')) {
            return "[INFOGRAPHIC:Cara Memilih Influencer yang Tepat]\nEngagement Rate | (likes + komentar) / followers × 100. Di atas 3% sudah bagus.\nKualitas Komentar | Baca 20 komentar terakhir. Asli atau emoji spam beli komentar?\nAudience Match | Minta screenshot insight demografis followers sebelum deal.\n[/INFOGRAPHIC]";
        }
        return "[INFOGRAPHIC:3 Prinsip Eksekusi {$title}]\nFokus | Kuasai satu hal hingga mahir sebelum berpindah ke strategi berikutnya.\nUkur | Tetapkan KPI yang jelas — jika tidak bisa diukur, tidak bisa diperbaiki.\nIterasi | Eksekusi → Evaluasi → Perbaiki → Ulangi dengan lebih cerdas.\n[/INFOGRAPHIC]";
    }

    private function pickQuiz(string $t, string $title): string
    {
        if (str_contains($t, 'ide') || str_contains($t, 'brainstorm')) {
            return "[QUIZ:Apa cara tercepat memvalidasi apakah ide bisnismu diminati pasar?|Langsung produksi massal agar bisa jual banyak|Buat landing page sederhana dan jalankan iklan kecil|Tanya pendapat keluarga dan teman|1]";
        }
        if (str_contains($t, 'persona') || str_contains($t, 'customer')) {
            return "[QUIZ:Kamu punya budget iklan Rp 500 ribu. Mana yang lebih efektif?|Iklan ke audience seluas mungkin agar banyak yang lihat|Iklan ke persona spesifik yang sudah kamu riset mendalam|Posting organik saja agar gratis|1]";
        }
        if (str_contains($t, 'pricing') || str_contains($t, 'harga') || str_contains($t, 'hpp')) {
            return "[QUIZ:Pelanggan mengeluh harga terlalu mahal. Respons terbaik adalah?|Langsung turunkan harga agar tidak kalah saing|Jelaskan nilai dan transformasi yang mereka dapatkan, bukan sekadar fitur|Abaikan karena bukan target market kamu|1]";
        }
        if (str_contains($t, 'copywriting') || str_contains($t, 'headline')) {
            return "[QUIZ:Mana headline yang lebih kuat secara psikologi penjualan?|Produk Kami Berkualitas Tinggi dan Terpercaya|Raih Kulit Glowing dalam 14 Hari atau Uang Kembali 100%|Beli Sekarang Dapatkan Diskon 10%|1]";
        }
        if (str_contains($t, 'cashflow') || str_contains($t, 'keuangan') || str_contains($t, 'arus kas')) {
            return "[QUIZ:Bisnis omzet Rp 100 juta/bulan tapi selalu kekurangan kas. Penyebab paling umum?|Harga jual terlalu murah dari HPP|Piutang tidak tertagih dan stok tidak berputar cepat|Karyawan terlalu banyak|1]";
        }
        if (str_contains($t, 'konversi') || str_contains($t, 'funnel')) {
            return "[QUIZ:10.000 pengunjung/bulan tapi hanya 50 yang beli (CVR 0.5%). Prioritas perbaikan pertama?|Tambah budget iklan agar lebih banyak pengunjung masuk|Audit dan perbaiki friction di halaman produk dan checkout|Ganti semua foto produk dengan yang lebih mahal|1]";
        }
        if (str_contains($t, 'iklan') || str_contains($t, 'ads')) {
            return "[QUIZ:ROAS iklan kamu 2x. Apakah ini sudah menguntungkan?|Ya, sudah untung karena balik 2x lipat|Belum tentu! Hitung dulu apakah 2x sudah menutup HPP dan semua biaya operasional|Pasti rugi karena ROAS harus minimal 5x|1]";
        }
        return "[QUIZ:Dari semua strategi di lesson ini, mana yang harus dieksekusi PERTAMA?|Yang paling mudah dan paling cepat bisa diuji dengan sumber daya yang ada|Yang paling canggih agar hasilnya sempurna|Yang paling banyak disarankan motivator di media sosial|0]";
    }

    private function pickChallenge(string $t, string $title): string
    {
        if (str_contains($t, 'ide') || str_contains($t, 'brainstorm')) {
            return "Tulis 10 ide produk dalam 10 menit tanpa menyensor diri. Coret 9 yang paling sulit divalidasi. Yang tersisa: bagaimana kamu bisa mengujinya minggu ini dengan modal di bawah Rp 500.000?";
        }
        if (str_contains($t, 'persona') || str_contains($t, 'customer')) {
            return "Hubungi langsung 3 orang yang pernah membeli produkmu. Tanya 1 pertanyaan: 'Masalah apa yang sebenarnya ingin kamu selesaikan saat membeli ini?' Catat kata per kata. Gunakan kalimat mereka — bukan kata-katamu — di caption iklan berikutnya.";
        }
        if (str_contains($t, 'pricing') || str_contains($t, 'harga') || str_contains($t, 'hpp')) {
            return "Hitung HPP produk terlaris kamu secara jujur: bahan, kemasan, waktu kemas, admin marketplace, ongkir. Apakah margin sudah di atas 30%? Jika belum, putuskan hari ini: naikan harga atau potong ongkos.";
        }
        if (str_contains($t, 'copywriting') || str_contains($t, 'headline')) {
            return "Tulis 5 versi headline berbeda untuk produk terlaris kamu dalam 20 menit. Formula: [Hasil Spesifik] + [Dalam Berapa Lama] + [Dengan/Tanpa Apa]. Posting yang paling kuat hari ini dan ukur engagement-nya.";
        }
        if (str_contains($t, 'cashflow') || str_contains($t, 'keuangan') || str_contains($t, 'arus kas')) {
            return "Hitung total pengeluaran bisnismu bulan lalu. Bagi dengan 30 — itulah Burn Rate harianmu. Kas yang ada sekarang cukup untuk beroperasi berapa hari? Jika kurang dari 30 hari, ini situasi darurat yang harus diselesaikan sebelum tidur malam ini.";
        }
        if (str_contains($t, 'konversi') || str_contains($t, 'funnel')) {
            return "Buka tokomu sebagai 'orang asing' yang baru pertama datang. Hitung langkah dari landing hingga checkout. Setiap langkah berlebihan adalah kebocoran konversi. Target maksimal: 3 langkah. Eliminasi sisanya hari ini.";
        }
        if (str_contains($t, 'iklan') || str_contains($t, 'ads')) {
            return "Audit campaign iklanmu. Identifikasi 3 adset dengan CTR terendah — hentikan sekarang dan redirect budgetnya ke adset terbaik. Jangan sentimental pada iklan yang tidak perform. Data bicara lebih keras dari intuisi.";
        }
        if (str_contains($t, 'seo') || str_contains($t, 'konten') || str_contains($t, 'organik')) {
            return "Riset 3 pertanyaan yang paling sering dicari oleh target audiensmu di Google. Pilih satu paling relevan. Buat satu konten — artikel, video, atau carousel — yang menjawabnya secara tuntas. Publish hari ini.";
        }
        if (str_contains($t, 'influencer') || str_contains($t, 'kolaborasi')) {
            return "Cari 5 micro-influencer (10K-100K followers) yang audiensnya cocok dengan produkmu. Cek engagement rate — harus di atas 2%. DM mereka dengan pesan personal dan spesifik hari ini, bukan template copas.";
        }
        return "Pilih satu konsep terpenting dari lesson **{$title}** ini. Terapkan satu langkah konkret dalam bisnis kamu *hari ini* — tidak perlu sempurna, cukup mulai. Dokumentasikan hasilnya dan evaluasi dalam 7 hari ke depan.";
    }
}
