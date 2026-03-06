<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class SalesFunnelAdvancedSeed extends Seeder
{
    public function run(): void
    {
        Course::where('title', 'Sales Funnel & Conversion Optimization')->delete();

        $course = Course::create([
            'title'         => 'Sales Funnel & Conversion Optimization',
            'description'   => 'Level advanced — kuasai funnel strategy, conversion science, dan revenue optimization. Dari anatomy funnel profesional, lead magnet, CRO, hingga scaling yang profitable.',
            'level'         => 'advanced',
            'category'      => 'sales',
            'xp_reward'     => 650,
            'lessons_count' => 10,
            'is_active'     => true,
        ]);

        $lessons = [

            [
                'title' => 'Anatomy of a Sales Funnel',
                'order' => 1, 'xp' => 55, 'min' => 10,
                'content' => <<<MD
**Struktur funnel profesional** — kebanyakan bisnis berpikir dalam dua tahap: iklan → penjualan. Bisnis yang skala dan profitabel berpikir dalam lima tahap: **Awareness → Interest → Consideration → Conversion → Retention**. Setiap tahap membutuhkan pesan, media, dan strategi yang berbeda — menggabungkan semuanya dalam satu pendekatan adalah alasan terbesar mengapa funnel gagal.

Funnel bukan corong pasif — ia adalah **mesin yang dirancang untuk memandu keputusan**.

[CASE_STUDY]
**Observasi Lapangan: Brand E-Commerce yang Tidak Langsung Menjual**
Salah satu brand fashion lokal terbesar tidak pernah menjalankan iklan yang langsung meminta orang membeli. Strategi mereka: awareness melalui konten edukasi gaya berpakaian, interest melalui lookbook yang bisa disimpan, consideration melalui try-on virtual dan review detail, baru conversion dengan penawaran yang tepat waktu. Hasilnya: conversion rate 8x lebih tinggi dari iklan direct-to-buy, dan customer acquisition cost 60% lebih rendah — karena customer yang datang sudah "dipanaskan" dengan benar.
[/CASE_STUDY]

[INFOGRAPHIC:5 Tahap Sales Funnel Profesional]
Awareness | Traffic — belum kenal brand, butuh konten yang menginterupsi dan menarik perhatian
Interest | Lead — mulai tertarik, butuh value dan informasi yang relevan untuk hold perhatian
Consideration | Prospect — membandingkan opsi, butuh social proof dan diferensiasi yang jelas
Conversion | Customer — siap beli, butuh offer yang tepat dan proses checkout yang frictionless
Retention | Loyalist — sudah beli, butuh nurturing untuk repeat order dan referral
[/INFOGRAPHIC]

[QUIZ:Tahap apa yang harus dioptimasi sebelum tahap conversion dalam sebuah sales funnel?|Tahap awareness agar lebih banyak orang masuk ke funnel dari awal|Tahap consideration di mana prospect sedang membandingkan dan membutuhkan alasan kuat untuk memilihmu|Tahap retention agar customer lama mendorong referral yang membantu conversion|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Mapping **funnel bisnis kamu saat ini** — untuk setiap tahap (Awareness, Interest, Consideration, Conversion, Retention), identifikasi: strategi yang sedang digunakan, metrik yang diukur, dan gap terbesar yang ada. Di mana funnel-mu paling "bocor"?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Traffic Quality vs Quantity',
                'order' => 2, 'xp' => 50, 'min' => 9,
                'content' => <<<MD
**Kualitas trafik menentukan profitabilitas funnel** — 10.000 pengunjung yang salah adalah beban, bukan aset. Mereka mengkonsumsi server resources, mengaburkan data analytics, dan memberikan signal negatif ke algoritma bahwa konten atau penawaran tidak relevan. Traffic yang tepat — meski 10x lebih sedikit — menghasilkan conversion yang jauh lebih baik dengan biaya yang lebih rendah.

**Prinsip: Right Traffic > More Traffic.**

[CASE_STUDY]
**Observasi Lapangan: Potong Traffic, Naikkan Revenue**
Sebuah SaaS startup Indonesia melakukan audit dan menemukan bahwa 65% traffic mereka datang dari keyword informasional yang tidak memiliki intent pembelian. Mereka memotong budget untuk keyword ini dan mengalokasikan ulang ke 12 keyword transaksional dengan volume lebih kecil. Traffic turun 40%. Revenue naik 87%. Cost per acquisition turun dari Rp 450.000 ke Rp 180.000. Lebih sedikit traffic yang lebih tepat = funnel yang jauh lebih sehat.
[/CASE_CASE]

[INFOGRAPHIC:Dimensi Kualitas Traffic]
Intent Match | Seberapa aligned intent visitor dengan apa yang kamu tawarkan?
Source Quality | Traffic organik branded biasanya konversi 3-5x lebih tinggi dari cold display ads
Funnel Stage | Top-funnel traffic butuh nurturing lebih panjang sebelum siap konversi
Behavioral Signal | Time on site, pages visited, scroll depth — indikator kualitas yang lebih akurat dari volume
[/INFOGRAPHIC]

[QUIZ:Mengapa 10.000 traffic yang tidak tertarget lebih merugikan dibanding 1.000 traffic yang qualified?|Karena platform penalti akun dengan traffic tinggi tapi conversion rendah|Karena traffic tidak targeted membuang budget, mengaburkan data, dan tidak menghasilkan revenue|Karena terlalu banyak traffic memperlambat loading website dan menurunkan user experience|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buka analytics akun kamu. Evaluasi **3 sumber traffic terbesarmu**: untuk masing-masing, catat conversion rate dan revenue yang dihasilkan — bukan hanya volume. Sumber mana yang paling profitable? Alokasikan lebih banyak resource ke sana mulai minggu ini.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Landing Page Psychology',
                'order' => 3, 'xp' => 55, 'min' => 10,
                'content' => <<<MD
**Struktur halaman yang mengubah visitor menjadi pembeli** — landing page bukan sekadar halaman produk atau website. Landing page yang efektif adalah **argumen yang tersusun secara psikologis** — setiap elemen ditempatkan untuk menjawab pertanyaan tertentu dalam urutan yang sesuai dengan cara otak mengambil keputusan.

Kejelasan pesan adalah satu-satunya faktor yang paling sering menentukan conversion rate.

[CASE_STUDY]
**Observasi Lapangan: Satu Perubahan Headline, Revenue Naik 214%**
Landing page kursus online dengan conversion rate 1.2%. Headline awal: "Pelajari Digital Marketing Sekarang." Setelah audit, headline diubah menjadi: "Raih Rp 10 Juta Pertama dari Digital Marketing Dalam 90 Hari atau Uangmu Kembali Penuh." Conversion rate naik ke 3.8% — 214% improvement. Tanpa mengubah traffic, harga, atau produk. Headline yang baru menjawab pertanyaan implisit visitor: "Berapa lama?", "Apakah ini untuk saya?", dan "Apa risikonya?" — semuanya dalam satu kalimat.
[/CASE_STUDY]

[INFOGRAPHIC:Anatomi Landing Page yang Mengkonversi]
Headline | Klaim utama yang menjawab: "Apa yang saya dapatkan dan mengapa saya harus peduli?"
Subheadline | Konteks singkat yang memvalidasi klaim — spesifikasi atau timeframe
Social Proof | Testimoni + angka + logo klien — eliminasi keraguan sesegera mungkin
Benefit Section | Apa yang berubah dalam hidup mereka setelah menggunakan produk?
CTA | Satu tombol dengan satu tindakan — tidak ada pilihan lain untuk mengurangi friction
[/INFOGRAPHIC]

[QUIZ:Elemen apa yang paling kritis dalam sebuah landing page untuk memaksimalkan conversion rate?|Animasi dan efek visual yang membuat halaman terlihat premium dan menarik|Kejelasan pesan — visitor harus langsung mengerti apa yang ditawarkan dan mengapa itu relevan bagi mereka|Jumlah konten yang panjang agar semua informasi produk tersedia di satu halaman|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Lakukan **landing page audit** produkmu: tunjukkan halaman ke 5 orang yang belum pernah melihatnya. Tanya satu pertanyaan: "Dalam 5 detik, apa yang halaman ini minta kamu lakukan?" Jika jawaban mereka keliru atau tidak yakin, headline dan CTA-mu perlu direvisi segera.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Lead Magnet Strategy',
                'order' => 4, 'xp' => 55, 'min' => 9,
                'content' => <<<MD
**Mengubah visitor menjadi lead sebelum meminta mereka membeli** — di era informasi yang berlimpah, orang semakin skeptis terhadap iklan langsung. Lead magnet adalah strategi yang memberikan **value nyata terlebih dahulu** — ebook, checklist, template, mini-course, atau tool gratis — sebagai pertukaran untuk data kontak yang kemudian digunakan untuk nurturing.

Prinsipnya: **Value First → Trust → Sale.** Kamu tidak meminta komitmen besar dari orang yang belum kamu buktikan nilaimu.

[CASE_STUDY]
**Observasi Lapangan: Ebook Gratis yang Menghasilkan Rp 800 Juta**
Konsultan bisnis membuat ebook "17 Kesalahan Fatal UMKM dan Cara Menghindarinya" dan menawarkannya gratis via halaman opt-in. 3.200 orang mendownload dalam 30 hari. Dari 3.200 lead, 280 bergabung dalam webinar lanjutan (8.75%), dan dari 280 peserta webinar, 43 membeli program konsultasi seharga Rp 18.5 juta (15.4%). Total revenue dari satu ebook: Rp 795 juta. Biaya produksi ebook: Rp 500.000 dan waktu 2 hari.
[/CASE_STUDY]

[INFOGRAPHIC:Kriteria Lead Magnet yang Efektif]
Spesifik | Menyelesaikan satu masalah spesifik — bukan panduan umum yang mencakup segalanya
Instan | Nilai langsung terasa — bukan sesuatu yang hasilnya baru terlihat dalam 6 bulan
Relevan | Sangat terhubung dengan produk berbayar yang akan ditawarkan selanjutnya
Mudah Dikonsumsi | Selesai dalam 5-30 menit — friction rendah untuk mulai
[/INFOGRAPHIC]

[QUIZ:Fungsi utama lead magnet dalam sebuah sales funnel adalah?|Membuat brand terlihat viral dan mendapatkan banyak followers di media sosial|Mengumpulkan data kontak dengan memberikan value terlebih dahulu sebagai pondasi trust|Meningkatkan SEO website dengan konten gratis yang bisa diindex oleh mesin pencari|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Rancang **ide lead magnet** untuk produk atau jasamu: tentukan format (ebook, checklist, template, atau mini-course), topik spesifik yang diselesaikan, dan bagaimana ia terhubung langsung dengan produk berbayarmu. Bisakah kamu membuatnya dalam kurang dari satu minggu?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Conversion Rate Optimization (CRO)',
                'order' => 5, 'xp' => 60, 'min' => 10,
                'content' => <<<MD
**Meningkatkan revenue tanpa menambah satu rupiah pun ke advertising budget** — inilah kekuatan CRO. Sementara hampir semua bisnis fokus pada cara mendapatkan lebih banyak traffic, CRO fokus pada pertanyaan yang lebih menguntungkan: **bagaimana cara membuat traffic yang sudah ada menghasilkan lebih banyak?**

Formula dasar yang harus selalu ada di dashboard bisnismu: **Revenue = Traffic × Conversion Rate × AOV (Average Order Value)**.

[CASE_STUDY]
**Observasi Lapangan: Dari 1% ke 3.2% Tanpa Tambah Budget Iklan**
Platform e-learning dengan 15.000 pengunjung per bulan dan conversion rate 1% → 150 pembeli. Revenue per bulan: Rp 225 juta (harga rata-rata Rp 1.5 juta). Tim CRO mengidentifikasi 3 bottleneck: checkout terlalu panjang (4 langkah), tidak ada garansi uang kembali yang jelas, dan halaman pricing membingungkan. Setelah 3 perubahan itu diimplementasikan, conversion naik ke 3.2% → 480 pembeli. Revenue naik ke Rp 720 juta. Traffic sama persis. Budget iklan sama. Revenue naik 220%.
[/CASE_STUDY]

[INFOGRAPHIC:Lever CRO yang Paling Berdampak]
Checkout Friction | Setiap langkah tambahan di checkout menghilangkan 15-30% potential buyer
Trust Signals | Garansi, SSL, testimoni di halaman checkout — eliminasi keraguan last-minute
Page Speed | Setiap 1 detik tambahan load time menurunkan conversion 7%
Offer Clarity | Apakah visitor 100% jelas apa yang mereka beli dan apa yang mereka dapatkan?
[/INFOGRAPHIC]

[QUIZ:Apa keuntungan utama dari pendekatan Conversion Rate Optimization dibandingkan hanya meningkatkan traffic?|CRO lebih mudah diimplementasikan karena tidak membutuhkan keahlian teknis apapun|CRO meningkatkan revenue dari traffic yang sudah ada tanpa perlu menambah biaya akuisisi pelanggan baru|CRO secara otomatis meningkatkan traffic organik melalui perbaikan user experience|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Hitung **3 skenario revenue** menggunakan formula Traffic × Conversion Rate × AOV: (1) kondisi saat ini, (2) jika conversion rate naik 50%, (3) jika AOV naik 30%. Berapa perbedaan revenue antara skenario 1 dan 3? Angka itu adalah potensi CRO yang sedang kamu tinggalkan setiap bulan.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Trust Builder Elements',
                'order' => 6, 'xp' => 50, 'min' => 9,
                'content' => <<<MD
**Elemen kepercayaan yang mengeliminasi keraguan** — setiap calon pembeli yang tidak jadi membeli meninggalkan karena alasan yang sama: **risiko yang dirasakan lebih besar dari manfaat yang dijanjikan**. Trust builder adalah senjata untuk membalikkan persamaan itu — menurunkan perceived risk sampai keputusan membeli terasa seperti pilihan yang logis dan aman.

Empat elemen trust yang paling kuat: **testimoni spesifik, garansi yang jelas, review terverifikasi, dan badge keamanan**.

[CASE_STUDY]
**Observasi Lapangan: Garansi yang Melipattigakan Penjualan**
Toko online produk kesehatan menambahkan satu elemen ke halaman produk: banner garansi 30 hari uang kembali tanpa pertanyaan, ditempatkan langsung di bawah tombol "Beli Sekarang". Tidak ada perubahan lain. Penjualan meningkat 216% dalam 2 minggu. Ironisnya: tingkat klaim garansi hanya 2.3%. Artinya garansi jauh lebih digunakan sebagai conversion tool daripada sebagai biaya aktual. Trust menurunkan hambatan beli — bukan meningkatkan return rate.
[/CASE_STUDY]

[INFOGRAPHIC:4 Lapisan Trust Builder]
Testimoni Spesifik | Nama + foto + masalah sebelumnya + hasil terukur — bukan "produknya bagus"
Garansi | Semakin berani garansinya, semakin tinggi conversion — karena risiko terasa minimal
Review Terverifikasi | Platform pihak ketiga: Google, Tokopedia, Shopee — lebih dipercaya dari review internal
Badge & Certification | SSL, payment security, awards, media mention — sinyal kredibilitas pihak ketiga
[/INFOGRAPHIC]

[QUIZ:Mengapa kurangnya trust menjadi hambatan utama conversion meskipun produk dan harga sudah kompetitif?|Karena customer selalu mencari alasan untuk tidak membeli agar bisa menghemat uang|Karena ketika perceived risk melebihi perceived benefit, otak secara otomatis memilih untuk tidak bertindak|Karena competitor selalu menawarkan trust yang lebih baik sehingga customer beralih|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buka halaman jualan produkmu sekarang. Identifikasi **2 elemen trust yang hilang atau bisa diperkuat** — spesifiknya: apakah ada testimoni dengan hasil terukur? Apakah garansi ditampilkan dengan jelas di dekat CTA? Tambahkan keduanya hari ini sebelum lanjut ke aktivitas lain.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Offer Stack Optimization',
                'order' => 7, 'xp' => 55, 'min' => 9,
                'content' => <<<MD
**Membangun penawaran yang nilainya jauh melebihi harganya** — diskon besar adalah strategi optimasi offer yang paling malas dan paling merusak margin. Offer stack yang benar tidak memotong harga — ia **menambah value** sampai orang merasa akan sangat rugi jika tidak mengambil penawaran ini.

Struktur offer stack yang powerful: **Core Product + Bonus yang Relevan + Urgency + Guarantee**.

[CASE_STUDY]
**Observasi Lapangan: AOV Naik 340% Tanpa Diskon**
Kursus online seharga Rp 500.000 mengalami stagnasi penjualan. Tanpa mengubah harga atau memberi diskon, tim membuat offer stack: Core: Akses lifetime kursus (Rp 500.000) + Bonus 1: Template swipe file yang biasanya dijual terpisah Rp 150.000 + Bonus 2: Sesi review bulanan Rp 200.000 + Bonus 3: Komunitas eksklusif 12 bulan Rp 300.000 + Garansi 60 hari uang kembali. Total perceived value: Rp 1.150.000 — harga tetap Rp 500.000. Konversi naik 340%. Karena orang membeli ketika value yang mereka terima jauh melebihi harga yang mereka bayar.
[/CASE_STUDY]

[INFOGRAPHIC:Komponen Offer Stack yang Tidak Bisa Ditolak]
Core Product | Nilai utama — apa yang menjadi solusi inti dari masalah customer?
Bonus Relevan | Tambahan yang mempercepat hasil dari core product — bukan barang random
Urgency | Tenggat atau stok terbatas yang nyata — beri alasan untuk bertindak sekarang bukan nanti
Guarantee | Hilangkan risiko terbesar — semakin bold garansinya, semakin tinggi perceived value
[/INFOGRAPHIC]

[QUIZ:Mengapa offer stack lebih efektif dalam meningkatkan conversion dibandingkan sekadar memberikan diskon besar?|Karena diskon dilarang oleh regulasi platform e-commerce di Indonesia|Karena offer stack meningkatkan perceived value sehingga membeli terasa sebagai investasi, bukan pengeluaran|Karena diskon membutuhkan persetujuan dari supplier sedangkan offer stack tidak|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Bangun **offer stack lengkap** untuk produk utamamu: tentukan core product, 2-3 bonus yang relevan dan spesifik (dengan nilai yang bisa dikuantifikasi), mekanisme urgency yang nyata, dan garansi yang berani. Hitung total perceived value vs harga yang diminta — idealnya perceived value ≥ 3x harga.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Funnel Leak Detection',
                'order' => 8, 'xp' => 55, 'min' => 9,
                'content' => <<<MD
**Mendeteksi dan menambal kebocoran funnel** — setiap funnel pasti punya titik bocor. Titik di mana sejumlah besar potential customer keluar tanpa melanjutkan ke tahap berikutnya. Bisnis yang tumbuh profitable adalah bisnis yang paling sistematis dalam **mengidentifikasi dan memperbaiki drop point satu per satu**, dimulai dari yang paling mahal.

Tiga drop point paling umum: CTR rendah (hook salah), add-to-cart rendah (trust kurang), checkout abandonment (friction tinggi).

[CASE_STUDY]
**Observasi Lapangan: Satu Leak yang Menyebabkan 73% Revenue Hilang**
E-commerce dengan 20.000 pengunjung per bulan: 15% add to cart (3.000), 8% reach checkout (240), tetapi hanya 1.2% complete purchase (29 transaksi). Audit menemukan: 91% cart abandonment. Investigasi: form checkout meminta 12 field data termasuk nomor telepon, tanggal lahir, dan kode referral yang tidak mandatory. Setelah disederhanakan ke 5 field wajib dan ditambahkan opsi checkout sebagai guest, purchase completion naik ke 31% (dari 240 → 74 transaksi). Revenue 2.5x dari perubahan form saja.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Funnel Leak Detection]
CTR Rendah | Hook atau targeting salah → perbaiki creative dan audience sebelum mengoptimasi tahap lain
Add-to-Cart Rendah | Trust kurang atau harga tidak aligned → tambahkan social proof dan perjelas value
Cart Abandonment | Friction di checkout → kurangi field, tambahkan payment option, tampilkan security badge
Post-Purchase Drop | Onboarding buruk → perbaiki email sequence dan user experience awal
[/INFOGRAPHIC]

[QUIZ:Ketika checkout abandonment rate sangat tinggi, apa yang biasanya menjadi penyebab utamanya?|Hook iklan yang tidak cukup kuat untuk menarik kualitas traffic yang tepat|Friction yang terlalu tinggi di proses checkout — terlalu banyak langkah atau informasi yang diminta|Harga produk yang tidak kompetitif dibandingkan dengan competitor di pasar|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Ukur **conversion rate di setiap tahap funnel-mu**: berapa % yang klik ke landing page? Berapa % yang add to cart? Berapa % yang checkout? Berapa % yang selesai bayar? Temukan tahap dengan drop rate terbesar — itulah satu-satunya prioritas optimasimu minggu ini.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'A/B Testing Mastery',
                'order' => 9, 'xp' => 55, 'min' => 9,
                'content' => <<<MD
**Testing keputusan bisnis dengan data, bukan opini** — A/B testing bukan eksperimen random. A/B testing yang benar dimulai dengan **hipotesis yang jelas**: "Saya percaya bahwa mengubah X akan meningkatkan Y karena Z." Tanpa hipotesis, kamu tidak sedang testing — kamu sedang menebak.

Aturan fundamental: **satu variabel, satu test, cukup data, baru keputusan**.

[CASE_STUDY]
**Observasi Lapangan: Hipotesis yang Menghasilkan Rp 2 Miliar Tambahan**
Tim produk sebuah marketplace memiliki hipotesis: "Menambahkan counter real-time '247 orang melihat produk ini' akan meningkatkan purchase rate karena menciptakan social proof dan urgency." Mereka menjalankan A/B test: control (tanpa counter) vs treatment (dengan counter). Setelah 14 hari dan 50.000 session per variasi: treatment menghasilkan purchase rate 11.3% vs 8.7% control — 29.9% improvement. Pada scale mereka, 2.9 poin peningkatan conversion = Rp 2 miliar revenue tambahan per tahun dari satu eksperimen.
[/CASE_STUDY]

[INFOGRAPHIC:Framework A/B Testing yang Valid]
Hipotesis | "Mengubah [X] akan meningkatkan [metric] karena [reasoning]" — mandatory sebelum test
Isolasi | Hanya satu variabel yang berbeda antara control dan treatment
Sample Size | Minimum 1.000 visitor per variasi dan minimum 7-14 hari sebelum interpretasi
Significance | Minimum 95% statistical significance sebelum mengambil keputusan
[/INFOGRAPHIC]

[QUIZ:Dalam A/B testing yang valid, berapa variabel yang boleh berbeda antara versi A dan versi B?|Sebanyak mungkin variabel agar eksperimen lebih komprehensif dan efisien|Hanya satu variabel — agar bisa diidentifikasi dengan pasti elemen apa yang menyebabkan perbedaan hasil|Dua atau tiga variabel yang dikelompokkan berdasarkan kategori yang sama|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Formulasikan **satu hipotesis A/B test** untuk funnel-mu menggunakan format: "Saya percaya bahwa mengubah [elemen spesifik] pada [halaman/tahap spesifik] akan meningkatkan [metrik] karena [reasoning berbasis data]." Buat testing plan: variasi apa yang akan dites, berapa durasi minimum, dan berapa sample size yang dibutuhkan.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Scaling Funnel Profitably',
                'order' => 10, 'xp' => 70, 'min' => 11,
                'content' => <<<MD
**Scaling hanya boleh dilakukan setelah funnel terbukti profitable** — ini adalah aturan yang sering dilanggar oleh entrepreneur yang antusias. Scaling funnel yang belum profitable hanya memperbesar kerugian. Setiap rupiah yang kamu tambahkan ke campaign yang ROAS-nya di bawah break-even adalah rupiah yang terbakar lebih cepat.

**Profitable first, scale later.** Bukan sebaliknya.

[CASE_STUDY]
**Observasi Lapangan: Scaling yang Memperbesar Kerugian 5x**
Startup fashion melihat traffic yang bagus dari campaign pertama mereka dan langsung menaikkan budget 10x. Tidak ada yang memeriksa ROAS. Setelah sebulan dengan budget besar: revenue Rp 180 juta, total biaya (ads + COGS + ongkir) Rp 280 juta. Kerugian Rp 100 juta. Jika mereka tidak scaling, kerugian hanya Rp 10 juta — cukup untuk dipelajari dan diperbaiki. Dengan scaling, mereka membutuhkan injeksi modal untuk survival. Pelajaran: audit profitabilitas funnel sebelum satu rupiah pun ditambahkan untuk scaling.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Profitable Scaling]
Break-Even ROAS | Hitung dulu: (1 / gross margin) = minimum ROAS agar tidak rugi. Margin 40% → ROAS min 2.5x
Pre-Scale Checklist | Funnel sudah profitable ≥ 30 hari? Conversion rate stabil? LTV sudah diketahui?
Scaling Speed | Maksimum 20-30% budget increase setiap 3-5 hari untuk menjaga stability
Reinvestment Rule | Scale hanya dari profit yang dihasilkan, bukan dari modal tambahan yang dipinjam
[/INFOGRAPHIC]

[QUIZ:Kapan kondisi yang tepat untuk mulai melakukan scaling pada sebuah funnel?|Ketika brand sudah viral dan mendapat banyak perhatian di media sosial|Ketika funnel terbukti profitable dengan ROAS di atas break-even secara konsisten minimal 30 hari|Ketika kompetitor mulai memasuki pasar dan kamu harus bergerak cepat|1]

Selamat menyelesaikan modul **Sales Funnel & Conversion Optimization** — level Advanced! Kamu kini memiliki framework lengkap dari anatomy funnel hingga profitable scaling. Satu langkah terakhir:

[CHALLENGE]
**Action Plan Hari Ini:**
Hitung **break-even ROAS funnel-mu**: berapa gross margin per transaksi? Berapa ROAS minimum yang dibutuhkan untuk tidak rugi? Berapa ROAS aktual saat ini? Jika di atas break-even dan sudah stabil 30+ hari — buat scaling plan. Jika di bawah — audit funnel menggunakan semua framework dari modul ini sebelum menambah satu rupiah pun ke ads.
[/CHALLENGE]
MD,
            ],

        ];

        foreach ($lessons as $data) {
            $course->lessons()->create([
                'title'             => $data['title'],
                'order'             => $data['order'],
                'type'              => 'text',
                'content'           => $data['content'],
                'xp_reward'         => $data['xp'],
                'estimated_minutes' => $data['min'],
            ]);
        }

        $this->command->info('✅ Sales Funnel & Conversion Optimization (Advanced) — 10 lessons berhasil dimasukkan ke database.');
    }
}
