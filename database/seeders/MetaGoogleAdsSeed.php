<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class MetaGoogleAdsSeed extends Seeder
{
    public function run(): void
    {
        Course::where('title', 'Meta & Google Ads Mastery')->delete();

        $course = Course::create([
            'title'         => 'Meta & Google Ads Mastery',
            'description'   => 'Kuasai paid traffic dari fundamental lelang iklan, audience targeting, creative testing, hingga optimization loop yang mengubah budget menjadi profit konsisten.',
            'level'         => 'intermediate',
            'category'      => 'marketing',
            'xp_reward'     => 500,
            'lessons_count' => 10,
            'is_active'     => true,
        ]);

        $lessons = [

            [
                'title' => 'Cara Kerja Paid Ads Ecosystem',
                'order' => 1, 'xp' => 45, 'min' => 9,
                'content' => <<<MD
**Fundamental iklan digital** — banyak orang mengira iklan digital adalah permainan siapa yang paling besar budgetnya. Ini kesalahan fatal yang menghabiskan uang tanpa hasil. Platform ads seperti Meta dan Google bekerja dengan **sistem lelang real-time** — dan pemenang lelang bukan yang bayar paling mahal, tapi yang menawarkan kombinasi terbaik antara bid, relevansi konten, dan perkiraan CTR.

Ini berarti kamu bisa mengalahkan kompetitor berbudget 10x lebih besar — jika iklanmu lebih relevan dan lebih menarik bagi audiens yang ditargetkan.

[CASE_STUDY]
**Observasi Lapangan: UMKM Mengalahkan Brand Besar**
Sebuah toko online pakaian lokal bersaing di niche yang sama dengan brand nasional yang budgetnya 50x lebih besar. Tapi iklan mereka menggunakan foto produk yang autentik, caption dalam bahasa yang sangat spesifik ke target market, dan landing page yang perfectly aligned dengan pesan iklan. Hasil: CPM mereka 40% lebih murah dan CTR 3x lebih tinggi dari brand besar yang menggunakan creative template generik. Relevance score yang tinggi menurunkan biaya lelang secara signifikan.
[/CASE_STUDY]

[INFOGRAPHIC:Formula Pemenang Lelang Iklan]
Bid | Berapa maksimum yang kamu bersedia bayar per hasil yang diinginkan
CTR Estimasi | Seberapa yakin platform bahwa audiens akan klik iklanmu berdasarkan data historis
Relevance Score | Seberapa relevan iklanmu dengan audiens yang ditarget — dinilai dari feedback awal
Ad Rank | Bid × CTR Estimasi × Relevance = posisi dan biaya aktual yang kamu bayar
[/INFOGRAPHIC]

[QUIZ:Faktor apa yang sebenarnya menentukan pemenang dalam lelang iklan digital?|Budget terbesar yang bersedia diinvestasikan untuk kampanye|Kombinasi bid, perkiraan CTR, dan relevansi iklan terhadap audiens|Kualitas logo dan desain visual identitas brand|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Sebelum membuat satu kampanye pun, **identifikasi objective utama campaign pertamamu**: apakah tujuannya awareness (reach), traffic (klik ke website), atau conversion (transaksi)? Tuliskan juga: metrik apa yang akan kamu gunakan untuk mengukur sukses kampanye ini?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Objective Campaign Strategy',
                'order' => 2, 'xp' => 45, 'min' => 8,
                'content' => <<<MD
**Tujuan iklan menentukan segalanya** — kesalahan paling mahal dalam paid ads adalah memilih objective yang salah. Platform mengoptimasi pengiriman iklan berdasarkan objective yang kamu pilih — jika kamu memilih Traffic tapi tujuan sesungguhnya adalah penjualan, platform akan mencari orang yang suka mengklik, bukan orang yang suka membeli.

Setiap objective menciptakan machine learning yang berbeda dan menargetkan persona perilaku yang berbeda.

[CASE_STUDY]
**Observasi Lapangan: Objective Salah = Budget Terbuang**
Seorang pemilik toko online menggunakan objective "Traffic" karena ingin lebih banyak orang mengunjungi toko-nya. Setelah Rp 3 juta habis, ia mendapat 2.000 klik tapi hanya 3 penjualan. Ia beralih ke objective "Conversion" dengan pixel terpasang — budget yang sama menghasilkan 47 penjualan. Perbedaannya: dengan Traffic, platform mengirim orang yang suka klik. Dengan Conversion, platform mengirim orang yang memiliki pola perilaku serupa dengan pembeli sebelumnya.
[/CASE_STUDY]

[INFOGRAPHIC:Panduan Memilih Objective]
Awareness | Baru mulai, ingin dikenal — optimalkan untuk reach dan impressions
Traffic | Ingin orang mengunjungi website atau profil — optimalkan untuk klik
Engagement | Ingin interaksi konten tinggi — optimalkan untuk like, komentar, share
Conversion | Ingin transaksi langsung — butuh pixel terpasang, optimalkan untuk pembeli
Lead Generation | Ingin data calon pembeli — cocok untuk produk high-ticket
[/INFOGRAPHIC]

[QUIZ:Objective Conversion pada platform ads mengoptimasi pengiriman iklan untuk menemukan?|Orang yang paling sering mengklik iklan apapun yang muncul|Orang yang memiliki pola perilaku serupa dengan pembeli yang sudah ada|Orang dengan jumlah followers terbanyak di platform|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Evaluasi campaign yang sedang berjalan atau yang akan kamu buat: apakah objective-nya aligned dengan tujuan bisnis sesungguhnya? Jika belum, **pilih objective yang tepat** dan pastikan pixel atau tracking sudah terpasang before kamu launch campaign baru.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Audience Targeting Psychology',
                'order' => 3, 'xp' => 50, 'min' => 9,
                'content' => <<<MD
**Menentukan target iklan yang tepat** — targeting yang efektif bukan tentang menjangkau sebanyak mungkin orang, tapi tentang menjangkau **orang yang tepat** dengan biaya serendah mungkin. Targeting terlalu luas membuang budget pada audiens yang tidak akan pernah membeli. Targeting terlalu sempit membatasi scale dan menaikkan biaya.

Kombinasi tiga dimensi targeting: **interest** (ketertarikan), **behavior** (perilaku pembelian), dan **demographic** (profil demografis).

[CASE_STUDY]
**Observasi Lapangan: Spesifisitas yang Menurunkan CPM 60%**
Brand suplemen olahraga awalnya menargetkan semua orang berusia 18-45 yang tertarik "fitness" — CPM Rp 85.000. Setelah diubah ke: wanita 25-35, tertarik "yoga" DAN "clean eating" DAN memiliki perilaku "frequently online buyer" — CPM turun ke Rp 34.000 dengan conversion rate 4x lebih tinggi. Audiens yang lebih spesifik artinya iklan lebih relevan, relevansi lebih tinggi artinya biaya lelang lebih rendah.
[/CASE_STUDY]

[INFOGRAPHIC:3 Dimensi Audience Targeting]
Interest | Topik, halaman, akun yang diikuti audiens — menunjukkan ketertarikan tapi bukan niat beli
Behavior | Pola tindakan nyata — frekuensi belanja online, device, perjalanan, engagement dengan brand sejenis
Demographic | Usia, gender, lokasi, status, pendidikan — filter dasar yang mempersempit relevansi
Lookalike | Audiens mirip pembeli yang sudah ada — paling powerful untuk scale yang efisien
[/INFOGRAPHIC]

[QUIZ:Mengapa audience targeting yang terlalu luas justru merugikan efisiensi kampanye iklan?|Karena platform tidak menyukai iklan yang ditargetkan ke terlalu banyak orang|Karena budget terbuang untuk menjangkau orang yang tidak relevan sehingga CPM dan CPA naik|Karena jumlah kompetitor yang bersaing di lelang iklan menjadi terlalu banyak|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **1 profil audience detail** untuk produkmu: tentukan usia, gender, lokasi, 3 interest spesifik, 1 behavior, dan apakah kamu memiliki custom audience (pembeli lama, website visitor) yang bisa dijadikan base Lookalike. Ini adalah audience pertama yang akan kamu test.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Creative Testing Method',
                'order' => 4, 'xp' => 50, 'min' => 9,
                'content' => <<<MD
**Testing iklan yang menghasilkan data valid** — sebagian besar advertiser membuang uang karena mereka mengubah terlalu banyak variabel sekaligus, lalu tidak bisa mengidentifikasi apa yang sebenarnya bekerja atau tidak. Testing yang baik mengikuti satu prinsip dasar: **satu variabel, satu waktu**.

Jika kamu mengubah targeting dan creative bersamaan lalu hasilnya membaik, kamu tidak tahu mana yang menyebabkan perbaikan itu.

[CASE_STUDY]
**Observasi Lapangan: Testing yang Menemukan Winner 312%**
Sebuah agency menjalankan campaign untuk brand fashion dengan 3 variasi headline (satu variabel, targeting identik): A) "Koleksi Terbaru Tiba", B) "Tampil Beda dengan Budget Terjangkau", C) "27 Wanita Sudah Pakai Ini Hari Ini". Creative C menghasilkan CTR 312% lebih tinggi dari A dan 187% lebih tinggi dari B. Tanpa isolasi variabel yang benar, winner ini tidak akan pernah teridentifikasi — semua terlihat biasa saja dalam noise data.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Creative Testing yang Benar]
Isolasi Variabel | Ubah satu elemen saja: headline, visual, CTA, atau format — tidak boleh kombinasi
Durasi Test | Minimum 3-7 hari dengan budget cukup untuk data statistik yang valid
Sample Size | Setiap variasi butuh minimal 50-100 konversi sebelum keputusan dibuat
Kill & Scale | Creative dengan performa terendah dimatikan — budget dialokasikan ke winner
[/INFOGRAPHIC]

[QUIZ:Mengapa testing iklan harus dilakukan dengan mengubah satu variabel saja pada satu waktu?|Agar kampanye lebih mudah dikelola dan tidak terlalu kompleks|Agar data yang dihasilkan valid sehingga bisa diidentifikasi elemen mana yang menyebabkan perbedaan performa|Karena platform iklan membatasi jumlah variasi yang bisa dibuat dalam satu kampanye|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **3 variasi creative** untuk campaign berikutmu — ubah hanya satu elemen: coba 3 headline berbeda dengan visual dan targeting yang sama persis. Jalankan ketiganya dengan budget identik selama minimal 5 hari sebelum mengambil keputusan.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'CTR Optimization',
                'order' => 5, 'xp' => 45, 'min' => 8,
                'content' => <<<MD
**Meningkatkan click-through rate untuk menurunkan biaya** — CTR adalah metrik yang paling langsung berdampak pada biaya iklanmu. Ketika iklanmu di-klik lebih banyak orang dari yang melihatnya, platform menilai iklan itu **relevan** — dan sebagai reward, harga per klik diturunkan. Platform iklan secara harfiah memberimu diskon untuk membuat iklan yang bagus.

Rata-rata CTR di Meta Ads: 0.9%. CTR di atas 3% dianggap excellent dan biasanya berkorelasi dengan CPC yang jauh lebih rendah.

[CASE_STUDY]
**Observasi Lapangan: CTR Naik 180%, Biaya Turun 55%**
Sebuah campaign e-commerce memiliki CTR 0.8% dengan biaya per klik Rp 2.400. Tim mengoptimasi creative: mengganti foto produk dengan video unboxing 15 detik yang menunjukkan ekspresi penerima hadiah. CTR naik ke 2.9%. Biaya per klik turun ke Rp 1.080 — tanpa mengubah bid, tanpa mengubah targeting. Creative yang lebih relevan = CTR lebih tinggi = biaya lebih rendah. Itulah cara kerja sistem reward algoritma ads.
[/CASE_STUDY]

[INFOGRAPHIC:Elemen CTR yang Bisa Dioptimasi]
Visual Hook | 0.3 detik pertama menentukan apakah mata berhenti atau terus scroll
Headline | Klaim yang spesifik, benefit yang jelas, atau angka yang mengejutkan
CTA Button | Kata kerja yang aktif dan spesifik — "Dapatkan Gratis" lebih baik dari "Klik Di Sini"
Social Proof | Angka pengguna, rating, atau tanda popularity — menurunkan barrier click
[/INFOGRAPHIC]

[QUIZ:Apa hubungan langsung antara CTR yang tinggi dengan biaya iklan yang harus dibayar?|CTR tinggi meningkatkan persaingan sehingga biaya lelang iklan naik|CTR tinggi memberi sinyal relevansi ke platform sehingga biaya per klik diturunkan sebagai reward|CTR tinggi tidak memiliki hubungan langsung dengan biaya iklan yang dibayarkan|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Ambil iklan yang sedang berjalan dengan CTR terendah. **Optimalkan headline-nya** menggunakan salah satu dari: tambahkan angka spesifik, tambahkan kata "gratis" atau "terbatas", atau ganti dengan format pertanyaan langsung yang membuat audiens merasa iklan itu berbicara khusus kepada mereka.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Landing Page Alignment',
                'order' => 6, 'xp' => 45, 'min' => 8,
                'content' => <<<MD
**Sinkronisasi iklan dengan halaman tujuan** — kamu bisa memiliki iklan terbaik di dunia, tapi jika landing page-nya tidak aligned dengan pesan iklan, semua klik yang mahal itu terbuang sia-sia. Ini disebut **message mismatch** — dan ini adalah penyebab number one conversion rate yang rendah meskipun traffic sudah tinggi.

Orang yang mengklik iklanmu sudah menyetujui "kontrak implisit": "Saya klik karena iklan menjanjikan X." Jika landing page tidak segera memvalidasi X, mereka pergi.

[CASE_STUDY]
**Observasi Lapangan: Satu Perubahan yang Tripling Conversion**
Campaign dengan CTR 4% tapi conversion rate hanya 0.8% — angka yang menunjukkan iklan baik tapi landing page gagal. Audit mengungkap masalah: iklan menjanjikan "Diskon 40% untuk pemesan hari ini" tapi headline landing page hanya bertuliskan nama brand tanpa menyebut diskon. Visitor bingung dan bounce. Setelah headline landing page diubah menjadi "Klaim Diskon 40% Kamu — Berakhir Malam Ini", conversion rate naik ke 2.7%. Satu perubahan kalimat — 237% peningkatan konversi.
[/CASE_STUDY]

[INFOGRAPHIC:Checklist Message Alignment]
Headline Match | Kata kunci utama di iklan harus muncul di headline landing page — confirmasi segera
Visual Continuity | Foto atau warna di iklan sebaiknya muncul di landing page — mengurangi cognitive dissonance
Offer Clarity | Penawaran di iklan harus lebih jelas di landing page, bukan lebih samar
CTA Consistency | Tombol di landing page harus merepetisi CTA di iklan — tidak boleh mengejutkan
[/INFOGRAPHIC]

[QUIZ:Apa yang terjadi ketika pesan iklan tidak sinkron dengan konten landing page yang dituju?|CTR iklan akan naik karena audiens lebih penasaran dengan perbedaannya|Conversion rate turun drastis karena visitor merasa ditipu dan langsung bounce|Biaya iklan akan turun karena platform menganggap kampanye kurang kompetitif|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buka iklan yang sedang berjalan dan landing page-nya secara berdampingan. Verifikasi **4 poin alignment**: headline match, visual continuity, offer clarity, dan CTA consistency. Temukan satu mismatch — perbaiki hari ini.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Budget Scaling Strategy',
                'order' => 7, 'xp' => 50, 'min' => 9,
                'content' => <<<MD
**Cara scale iklan yang benar tanpa merusak performa** — ketika sebuah campaign sudah profitable dan kamu ingin scale, godaan terbesar adalah menaikkan budget secara drastis sekaligus. Ini hampir selalu berakhir buruk. Platform (terutama Meta) akan keluar dari fase learning dan masuk ke mode "exploration" yang membuang budget sambil mencari audiens baru.

Aturan emas scaling: **naikkan maksimum 20-30% setiap 2-3 hari**, bukan lompat 200% sekaligus.

[CASE_STUDY]
**Observasi Lapangan: Scaling yang Menghancurkan Campaign Profitable**
Campaign dengan ROAS 4.2x dan budget Rp 200.000/hari. Pemilik bisnis yang antusias menaikkan budget ke Rp 1.000.000/hari sekaligus. Apa yang terjadi: ROAS langsung turun ke 1.3x. Platform terpaksa keluar dari audiens yang sudah dioptimasi dan mulai mengeksplor audiens baru secara agresif. Butuh 2 minggu untuk stabilisasi — 2 minggu berarti Rp 14 juta dengan ROAS yang tidak profitable. Scaling bertahap 20-30% setiap beberapa hari: ROAS rata-rata tetap di 3.8-4.2x selama proses scaling.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Scaling yang Stabil]
Horizontal Scaling | Duplikasi ad set dengan budget sama ke audience berbeda — tidak mengganggu campaign yang berjalan
Vertical Scaling | Naikkan budget campaign aktif 20-30% setiap 2-3 hari jika ROAS masih positif
CBO Scaling | Campaign Budget Optimization — biarkan platform mendistribusikan budget ke ad set terbaik
Duplicate & Scale | Duplikasi campaign winner dengan budget lebih tinggi — preserve learning yang sudah ada
[/INFOGRAPHIC]

[QUIZ:Mengapa menaikkan budget iklan secara drastis sekaligus dapat merusak performa kampanye yang sedang profitable?|Karena platform akan mengenakan biaya penalti untuk kenaikan budget yang terlalu cepat|Karena platform keluar dari fase learning dan mulai mengeksplor audiens baru yang belum tentu relevan|Karena kompetitor akan mendeteksi kenaikan budget dan meningkatkan bid mereka|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Jika kamu punya campaign yang profitable, **rancang rencana scaling 7 hari**: tentukan berapa budget hari ini, dan berapa kenaikan yang akan diterapkan setiap 2 hari (20-30%). Komit pada jadwal ini tanpa tergoda untuk naik lebih cepat — bahkan jika hasilnya sangat bagus.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Retargeting Funnel',
                'order' => 8, 'xp' => 50, 'min' => 9,
                'content' => <<<MD
**Iklan ke audience yang sudah mengenal kamu** — retargeting adalah strategi iklan yang menargetkan orang yang sudah pernah berinteraksi dengan brand-mu — mengunjungi website, menonton video, menambahkan produk ke keranjang, atau mengklik iklan sebelumnya. Ini adalah **audiens paling hangat** yang bisa kamu targetkan, dan biasanya menghasilkan ROI tertinggi dari semua kampanye.

Logikanya sederhana: orang yang sudah tahu kamu jauh lebih mudah dikonversi daripada orang yang melihat kamu pertama kali.

[CASE_STUDY]
**Observasi Lapangan: Add-to-Cart Retargeting dengan ROAS 8x**
Toko online sepatu mengalokasikan 20% budget ke campaign khusus retargeting audience yang menambahkan produk ke keranjang tapi tidak checkout. Mereka menggunakan dynamic ads yang menampilkan persis produk yang ditinggalkan, + pesan urgency "Stok terbatas — produk pilihanmu tersisa 3". Campaign ini secara konsisten menghasilkan ROAS 7-8x — 2x lebih tinggi dari cold audience campaign terbaik mereka. 20% budget menghasilkan 45% total revenue dari ads.
[/CASE_STUDY]

[INFOGRAPHIC:Tingkatan Retargeting Audience]
Add to Cart | Intent tertinggi — sudah hampir membeli, butuh sedikit dorongan (urgency/diskon)
Product View | Tertarik dengan produk spesifik — tampilkan social proof dan benefit lebih detail
Website Visitor | Sudah tahu brand — perkenalkan produk terlaris atau penawaran terkuat
Video Viewer 75%+ | Sudah engage dalam — siap menerima pesan yang lebih direct dan spesifik
[/INFOGRAPHIC]

[QUIZ:Mengapa retargeting ke audience yang sudah mengenal brand biasanya menghasilkan ROAS tertinggi?|Karena biaya iklan ke audiens lama lebih murah dari audiens baru|Karena audiens yang sudah familiar sudah melewati tahap awareness sehingga lebih mudah dikonversi|Karena platform memberikan prioritas iklan ke audiens historis pengguna|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Identifikasi **semua audience retargeting yang bisa kamu buat sekarang**: website visitor 30 hari, video viewer 75%+, add-to-cart non-buyer, dan customer lama (untuk upsell). Pasang pixel jika belum ada. Buat campaign retargeting pertamamu mulai dari audience dengan intent tertinggi — add to cart.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Metrics That Actually Matter',
                'order' => 9, 'xp' => 45, 'min' => 8,
                'content' => <<<MD
**KPI iklan yang benar-benar menentukan profitabilitas** — banyak advertiser terjebak mengoptimasi metrik yang terlihat bagus tapi tidak berhubungan dengan profit. Likes, shares, reach, dan impressions adalah **vanity metrics** — mereka terlihat meyakinkan di laporan tapi tidak membayar operational cost bisnismu.

Tiga metrik yang benar-benar menentukan apakah iklanmu profit atau rugi: **CPA** (Cost Per Acquisition), **ROAS** (Return on Ad Spend), dan **Conversion Rate**.

[CASE_STUDY]
**Observasi Lapangan: Reach Tinggi, Profit Negatif**
Seorang pebisnis bangga dengan campaign-nya yang menghasilkan 500.000 reach dan 50.000 impressions dengan biaya Rp 5 juta. Kelihatannya besar. Tapi ketika dihitung: dari 500.000 reach, hanya 800 yang klik, dan dari 800 yang klik, hanya 12 yang beli dengan rata-rata order Rp 150.000. Total revenue: Rp 1.800.000 dari biaya Rp 5.000.000. ROAS: 0.36x — artinya untuk setiap Rp 1 yang diinvestasikan di iklan, ia mendapat kembali 36 sen. Bisnis rugi Rp 3.2 juta.
[/CASE_STUDY]

[INFOGRAPHIC:Metrik Profit vs Vanity Metric]
ROAS | Revenue dibagi ad spend — ROAS 3x berarti setiap Rp 1 iklan menghasilkan Rp 3 revenue
CPA | Biaya untuk mendapatkan satu pembeli — harus di bawah profit margin per transaksi
Conversion Rate | Persentase visitor yang jadi pembeli — mengukur efektivitas landing page
Vanity Metrics | Reach, impressions, likes, followers — tidak langsung berkorelasi dengan profit
[/INFOGRAPHIC]

[QUIZ:Metrik iklan mana yang paling langsung menentukan apakah sebuah kampanye menguntungkan atau merugikan?|Jumlah likes dan shares yang menunjukkan popularitas konten iklan|ROAS — rasio revenue yang dihasilkan per unit biaya iklan yang diinvestasikan|Total reach yang menunjukkan berapa banyak orang yang melihat iklan|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Hitung **ROAS target bisnismu**: berapa minimum ROAS yang dibutuhkan agar campaign-mu profitable setelah mempertimbangkan COGS, ongkir, dan biaya operasional? Jika profit margin per produkmu 40%, ROAS minimum yang dibutuhkan adalah 2.5x (1/0.4). Gunakan angka ini sebagai benchmark semua keputusan iklan ke depan.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Ads Optimization Loop',
                'order' => 10, 'xp' => 60, 'min' => 10,
                'content' => <<<MD
**Iterasi performa yang mengubah ads menjadi profit engine** — advertiser yang konsisten profitable bukan yang beruntung menemukan winning campaign di percobaan pertama. Mereka adalah yang paling sistematis dalam menjalankan **optimization loop** — siklus yang tidak pernah berhenti selama campaign masih berjalan.

Framework: **Test → Analyze → Kill Loser → Scale Winner → Repeat**.

[CASE_STUDY]
**Observasi Lapangan: Dari ROAS 1.2x ke 5.8x dalam 6 Minggu**
Advertiser memulai dengan 5 ad set berbeda, semua dengan budget Rp 100.000/hari. Minggu 1: analisis data, matikan 3 ad set dengan CPA tertinggi. Budget 3 ad set mati dialihkan ke 2 yang survive. Minggu 2: buat 4 creative baru untuk 2 ad set winner. Ulangi proses. Minggu 3-6: proses yang sama — kill yang lemah, scale yang kuat, test variasi baru. Setelah 6 minggu: 1 campaign dengan 2 ad set dan ROAS stabil 5.8x. Budget yang sama, hasil 4.8x lebih baik. Optimization loop — bukan keberuntungan.
[/CASE_STUDY]

[INFOGRAPHIC:Siklus Optimization Loop]
Test | Launch minimum 3 variasi dengan budget identik — beri platform cukup data
Analyze | Tunggu minimum 3-7 hari — jangan ambil keputusan dari data 1-2 hari
Kill Loser | Matikan semua yang ROAS-nya di bawah target tanpa kompromi
Scale Winner | Naikkan budget 20-30% untuk yang ROAS-nya di atas target
Repeat | Buat variasi baru untuk winner — bahkan yang terbaik akan fatigue
[/INFOGRAPHIC]

[QUIZ:Langkah apa yang harus dilakukan setelah data analisis menunjukkan iklan mana yang underperform?|Berhenti semua kampanye dan mulai dari awal dengan pendekatan yang berbeda|Matikan ad set dengan performa di bawah target dan alihkan budgetnya ke winner|Tambahkan budget ke semua ad set agar platform punya lebih banyak data|1]

Selamat menyelesaikan modul **Meta & Google Ads Mastery**! Kamu kini memiliki framework lengkap dari fundamental hingga scaling sistematis. Satu langkah terakhir:

[CHALLENGE]
**Action Plan Hari Ini:**
Audit campaign yang sedang berjalan menggunakan optimization loop: identifikasi **mana yang harus dimatikan** (ROAS di bawah target), **mana yang harus di-scale** (ROAS di atas target), dan **variasi baru apa** yang akan kamu test minggu ini. Eksekusi audit ini sekarang, bukan besok.
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

        $this->command->info('✅ Meta & Google Ads Mastery — 10 lessons berhasil dimasukkan ke database.');
    }
}
