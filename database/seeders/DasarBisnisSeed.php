<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;

class DasarBisnisSeed extends Seeder
{
    public function run(): void
    {
        // ── Hapus kursus lama dengan judul sama ────────────────────────────
        Course::where('title', 'Dasar Bisnis Digital')->delete();

        // ── Buat kursus ────────────────────────────────────────────────────
        $course = Course::create([
            'title'         => 'Dasar Bisnis Digital',
            'description'   => 'Bangun fondasi pola pikir dan strategi bisnis digital yang solid. Dari mindset founder, model monetisasi, hingga sistem bisnis otomatis — semua dikupas tuntas dengan pendekatan eksekusi nyata.',
            'level'         => 'beginner',
            'category'      => 'business',
            'xp_reward'     => 250,
            'lessons_count' => 10,
            'is_active'     => true,
        ]);

        $lessons = [

            // ── LESSON 1 ────────────────────────────────────────────────────
            [
                'title'             => 'Mindset Pebisnis Digital',
                'order'             => 1,
                'xp_reward'         => 40,
                'estimated_minutes' => 8,
                'content'           => <<<MD
**Pola pikir founder vs pekerja** — itulah garis pemisah antara mereka yang membangun kekayaan dan mereka yang menukar waktu dengan uang.

Pebisnis digital fokus pada **asset creation**, bukan sekadar income. Mereka membangun sistem yang tetap menghasilkan walau tidak aktif bekerja.

Mari kita lihat buktinya dari lapangan:

[CASE_STUDY]
**Observasi Lapangan: Freelancer yang Menjadi Founder**
Seorang freelancer desain menghasilkan 5 juta/bulan dari proyek klien. Setiap bulan, ia harus terus mencari klien baru — berhenti kerja berarti berhenti income. Setelah beralih menjual template digital di marketplace, income naik menjadi **20 juta/bulan tanpa tambahan jam kerja**. Satu sistem, ribuan pembeli.
[/CASE_STUDY]

Berikut perbedaan fundamental ketiga posisi ini:

[INFOGRAPHIC:Tiga Posisi dalam Ekonomi Digital]
Worker | Dibayar per waktu — stop kerja = stop income
Freelancer | Dibayar per skill — lebih fleksibel, tapi masih terikat proyek
Founder | Dibayar oleh sistem — aset terus bekerja bahkan saat tidur
[/INFOGRAPHIC]

Sebelum lanjut, uji pemahamanmu:

[QUIZ:Apa perbedaan utama founder dan freelancer?|Founder kerja lebih lama setiap harinya|Founder memiliki sistem income yang bekerja tanpa kehadiran mereka|Freelancer tidak memiliki skill yang berguna|1]

Tantangan terbesar bukan modal atau ide — melainkan keberanian untuk berpindah dari pola pikir pekerja ke pola pikir pembangun sistem. Mulai dari langkah kecil ini:

[CHALLENGE]
**Action Plan Hari Ini:**
Tulis 3 ide aset digital yang bisa kamu buat dan jual berulang kali tanpa perlu hadir secara aktif (contoh: ebook, template, preset, kursus video, tools). Tidak perlu sempurna — cukup 3 ide konkret hari ini.
[/CHALLENGE]
MD,
            ],

            // ── LESSON 2 ────────────────────────────────────────────────────
            [
                'title'             => 'Model Bisnis Digital',
                'order'             => 2,
                'xp_reward'         => 40,
                'estimated_minutes' => 9,
                'content'           => <<<MD
**Jenis model monetisasi online** — memahami ini adalah peta jalan menuju revenue stream yang tepat untukmu.

Model umum yang terbukti menghasilkan: produk digital, affiliate marketing, SaaS, jasa online, membership, dan dropship. Masing-masing punya kelebihan dan konteks ideal yang berbeda.

[CASE_STUDY]
**Observasi Lapangan: Kekuatan Produk Digital**
Seorang creator membuat eBook panduan bisnis seharga Rp 79.000. Dengan 1.000 pembeli, total revenue mencapai **Rp 79 juta** — tanpa stok barang, tanpa ongkir, tanpa gudang. Satu produk, dijual selamanya. Biaya produksi hanya waktu sekali di awal.
[/CASE_STUDY]

[INFOGRAPHIC:Produk Fisik vs Produk Digital]
Produk Fisik | Biaya produksi per unit → margin terbatas → logistik kompleks
Produk Digital | Biaya sekali di awal → dijual tak terbatas → margin mendekati 100%
Jasa Online | Bayar per waktu → income tergantung kapasitas → susah di-scale
[/INFOGRAPHIC]

[QUIZ:Model bisnis mana yang paling scalable?|Jasa manual yang ditagih per jam|Produk digital yang dijual tak terbatas tanpa biaya tambahan|Reseller offline yang butuh stok fisik|1]

Sekarang giliramu untuk mengambil keputusan:

[CHALLENGE]
**Action Plan Hari Ini:**
Pilih **1 model bisnis digital** yang paling ingin kamu jalankan berdasarkan skill dan resourcemu saat ini. Tulis 3 alasan konkret kenapa model itu cocok untukmu — dan satu langkah yang bisa kamu lakukan minggu ini untuk memulainya.
[/CHALLENGE]
MD,
            ],

            // ── LESSON 3 ────────────────────────────────────────────────────
            [
                'title'             => 'Niche Market Strategy',
                'order'             => 3,
                'xp_reward'         => 40,
                'estimated_minutes' => 8,
                'content'           => <<<MD
**Mengapa fokus pada market yang sempit justru lebih kuat** — ini adalah salah satu kontra-intuisi terbesar dalam bisnis digital.

Bisnis niche menang karena pesan marketing lebih spesifik → resonansi lebih tinggi → conversion rate jauh di atas rata-rata. Ketika kamu bicara ke semua orang, kamu tidak berbicara ke siapapun.

[CASE_STUDY]
**Observasi Lapangan: Toko Sepatu Generik vs Niche**
Toko "Sepatu" bersaing dengan ribuan kompetitor di marketplace. Margin tipis, iklan mahal, sulit menonjol. Toko "Sepatu Running untuk Pelari Pemula" sebaliknya: iklan lebih relevan, rating lebih tinggi, dan pelanggan lebih loyal karena merasa "ini memang untuk aku". Spesifisitas adalah diferensiasi.
[/CASE_STUDY]

[INFOGRAPHIC:Luas vs Niche]
Market Luas | Kompetisi sangat tinggi → relevasi pesan rendah → konversi kecil
Market Niche | Kompetisi lebih sedikit → pesan sangat relevan → konversi tinggi
[/INFOGRAPHIC]

[QUIZ:Mana pilihan niche yang paling tajam dan paling berpotensi tinggi konversinya?|Fashion — market yang sangat besar|Fashion wanita — lebih spesifik tapi masih lebar|Hijab olahraga untuk wanita muslimah aktif — sangat spesifik|2]

[CHALLENGE]
**Action Plan Hari Ini:**
Definisikan niche idealmu secara spesifik: **umur**, **minat utama**, **masalah terbesar** yang sering mereka hadapi, dan **platform** di mana mereka aktif. Semakin spesifik, semakin kuat pondasimu.
[/CHALLENGE]
MD,
            ],

            // ── LESSON 4 ────────────────────────────────────────────────────
            [
                'title'             => 'Value Proposition',
                'order'             => 4,
                'xp_reward'         => 45,
                'estimated_minutes' => 9,
                'content'           => <<<MD
**Alasan pelanggan membeli dari kamu** — bukan karena produkmu sempurna, tapi karena kamu menjawab pertanyaan paling penting dalam pikiran mereka: *"Kenapa harus beli dari kamu?"*

Value proposition yang kuat selalu terdiri dari tiga komponen: **hasil yang jelas**, **waktu yang cepat**, dan **risiko yang rendah**.

[CASE_STUDY]
**Observasi Lapangan: Kursus Diet yang Meledak**
Kursus diet biasa: "Pelajari cara hidup sehat." Hasilnya biasa-biasa saja. Kursus diet dengan VP yang kuat: **"Turun 5kg dalam 30 hari tanpa pergi ke gym."** Kalimat kedua menjawab tiga hal sekaligus: *hasil konkret* (5kg), *tenggat waktu* (30 hari), *hambatan dihilangkan* (tanpa gym). Perbedaan konversinya bisa 10 kali lipat.
[/CASE_STUDY]

[INFOGRAPHIC:Formula Value Proposition]
Result | Apa hasil spesifik yang pelanggan dapatkan?
Speed | Dalam berapa lama mereka akan merasakannya?
Trust | Apa yang membuat mereka aman untuk mencoba? (garansi, bukti, dll)
[/INFOGRAPHIC]

[QUIZ:Value proposition yang kuat harus berfokus pada?|Harga yang paling murah di pasaran|Logo dan desain brand yang keren|Manfaat utama yang dirasakan pelanggan secara langsung|2]

[CHALLENGE]
**Action Plan Hari Ini:**
Tulis **1 kalimat value proposition** untuk produk atau jasa utamamu menggunakan formula: *"[Siapa kamu bantu] mendapatkan [hasil spesifik] dalam [berapa lama] tanpa [hambatan terbesar mereka]."*
[/CHALLENGE]
MD,
            ],

            // ── LESSON 5 ────────────────────────────────────────────────────
            [
                'title'             => 'Customer Problem Mapping',
                'order'             => 5,
                'xp_reward'         => 40,
                'estimated_minutes' => 8,
                'content'           => <<<MD
**Menemukan masalah yang layak dijual** — produk sukses selalu memecahkan masalah nyata, bukan masalah asumsi dari balik meja.

Kesalahan fatal yang paling sering dilakukan founder pemula: membangun produk berdasarkan apa yang *mereka* inginkan, bukan apa yang *market* butuhkan.

[CASE_STUDY]
**Observasi Lapangan: Aplikasi Planner yang Gagal**
Sebuah tim membangun aplikasi planner dengan fitur catatan yang sangat lengkap. Setelah diluncurkan, pengguna tidak mau bayar. Setelah wawancara mendalam, ternyata masalah sebenarnya bukan kurangnya tempat mencatat — mereka butuh **reminder otomatis yang agresif**. Tim membangun solusi untuk masalah yang salah. Enam bulan kerja keras sia-sia.
[/CASE_STUDY]

[INFOGRAPHIC:Besar Kecilnya Masalah Menentukan Market]
Masalah Kecil | Sedikit orang peduli → demand rendah → susah monetisasi
Masalah Urgent | Banyak orang sangat butuh solusi → demand tinggi → mudah dijual
[/INFOGRAPHIC]

[QUIZ:Produk yang paling mudah menghasilkan revenue berasal dari?|Ide random yang tiba-tiba muncul|Ikut tren viral yang sedang populer saat ini|Masalah nyata yang dialami banyak orang di target market|2]

[CHALLENGE]
**Action Plan Hari Ini:**
List **5 masalah konkret** yang paling sering dialami oleh target market yang kamu incar. Bukan asumsimu — tapi berdasarkan komentar, review, atau keluhan yang pernah kamu baca atau dengar langsung dari mereka.
[/CHALLENGE]
MD,
            ],

            // ── LESSON 6 ────────────────────────────────────────────────────
            [
                'title'             => 'Validasi Ide Cepat',
                'order'             => 6,
                'xp_reward'         => 45,
                'estimated_minutes' => 10,
                'content'           => <<<MD
**Cara tes ide tanpa modal besar** — validasi bukan berarti membangun produk sempurna terlebih dahulu. Validasi berarti membuktikan ada cukup orang yang *mau bayar* sebelum kamu membuang waktu dan modal produksi.

Teknik validasi cepat yang terbukti: polling audiens, waitlist, landing page sederhana, dan pre-order.

[CASE_STUDY]
**Observasi Lapangan: Startup yang Memvalidasi Sebelum Membangun**
Sebuah startup SaaS lokal tidak langsung coding selama 6 bulan. Mereka membuat halaman pre-order sederhana yang menjelaskan manfaat produk dan tombol "Daftar Antrian". Dalam 2 minggu, **300 orang mendaftar** dan 50 di antaranya membayar deposit. Baru setelah itu tim mulai membangun produk — dengan kepastian ada demand nyata.
[/CASE_STUDY]

[INFOGRAPHIC:Siklus Validasi yang Benar]
Test | Buat hipotesis: siapa yang mau beli dan kenapa?
Data | Jalankan eksperimen kecil: polling, landing page, pre-order
Build | Bangun hanya jika ada bukti cukup orang mau bayar
Launch | Rilis ke audiens yang sudah tervalidasi
[/INFOGRAPHIC]

[QUIZ:Langkah pertama yang harus dilakukan sebelum membuat produk adalah?|Langsung produksi massal agar siap jual|Validasi ide dengan membuktikan ada yang mau membayar|Buat logo dan nama brand yang keren dulu|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **polling di Instagram Story** (atau platform manapun yang kamu aktif) dengan pertanyaan: *"Apakah kamu butuh [solusi dari idemu]?"* dan dua pilihan jawaban. Post hari ini — minimal 10 responden sudah cukup sebagai sinyal awal.
[/CHALLENGE]
MD,
            ],

            // ── LESSON 7 ────────────────────────────────────────────────────
            [
                'title'             => 'Pricing Psychology',
                'order'             => 7,
                'xp_reward'         => 45,
                'estimated_minutes' => 9,
                'content'           => <<<MD
**Strategi harga yang meningkatkan penjualan** — harga bukan sekadar angka. Harga adalah sinyal. Sinyal kualitas, sinyal eksklusivitas, sinyal kepercayaan.

Persepsi value *jauh lebih menentukan* willingness to pay daripada harga aktual itu sendiri.

[CASE_STUDY]
**Observasi Lapangan: Efek Rp 1.000 yang Mengubah Revenue**
Sebuah toko online menjual produknya seharga Rp 200.000. Konversi biasa-biasa saja. Setelah mengubah harga menjadi **Rp 199.000** — selisih hanya Rp 1.000 — penjualan naik 23%. Otak manusia memroses angka dari kiri ke kanan: 199 terbaca sebagai "100-an" bukan "200-an". Satu angka, dampak besar.
[/CASE_STUDY]

[INFOGRAPHIC:Harga dan Persepsi Value]
Harga Murah | Tidak selalu laris — sering justru memunculkan kecurigaan kualitas
Harga Benar | Harga yang mencerminkan value dan membangun kepercayaan
Brand Kuat + Value Tinggi | Harga premium tetap laku — bahkan meningkatkan status sosial pembeli
[/INFOGRAPHIC]

[QUIZ:Harga premium paling cocok diterapkan pada produk dengan kondisi apa?|Produk dengan value rendah yang perlu dijual cepat|Brand yang sudah kuat dan memiliki bukti hasil yang jelas|Produk baru tanpa testimoni satu pun|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Rancang **3 versi harga** untuk produk atau jasamu: **Basic** (entry level), **Pro** (best value — ini yang kamu ingin semua orang pilih), dan **Premium** (harga tinggi untuk segmen eksklusif). Harga Pro biasanya terjual paling banyak karena efek anchor dari harga Premium di sebelahnya.
[/CHALLENGE]
MD,
            ],

            // ── LESSON 8 ────────────────────────────────────────────────────
            [
                'title'             => 'Digital Trust Building',
                'order'             => 8,
                'xp_reward'         => 40,
                'estimated_minutes' => 8,
                'content'           => <<<MD
**Cara membuat orang percaya secara online** — di dunia digital, kepercayaan adalah mata uang utama. Orang tidak bisa memegang produkmu, mencium aromanya, atau berbicara langsung denganmu. Karena itu, trust harus dibangun secara sistematis.

Empat pilar trust digital: **testimoni**, **portfolio**, **social proof**, dan **konsistensi konten**.

[CASE_STUDY]
**Observasi Lapangan: Kekuatan 100 Review**
Sebuah toko online baru meluncur tanpa satu review pun. Conversion rate mereka stagnan di 1% meski produknya berkualitas. Tim memfokuskan 30 hari pertama hanya untuk mengumpulkan review dengan mengirim produk gratis ke 20 tester relevan. Setelah **100 review masuk**, conversion rate naik organik menjadi **6%** — enam kali lipat — tanpa mengubah produk atau harga.
[/CASE_STUDY]

[INFOGRAPHIC:Pilar Trust Digital]
Testimoni | Bukti dari orang nyata yang sudah merasakan — paling kuat
Portfolio | Bukti pekerjaan sebelumnya — membangun kredibilitas
Konsistensi Konten | Posting rutin = terlihat profesional dan serius
Social Proof | Jumlah pengguna, rating bintang, badge media — "kalau banyak yang pakai, pasti bagus"
[/INFOGRAPHIC]

[QUIZ:Elemen trust digital yang paling kuat dalam mendorong konversi adalah?|Logo yang keren dan desain website profesional|Testimoni nyata dari pelanggan yang sudah merasakan manfaatnya|Caption postingan yang panjang dan penuh informasi|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Hubungi **minimal 3 user pertamamu** (bisa teman, keluarga, atau kenalan yang sudah mencoba produkmu). Minta mereka menulis testimoni jujur — 2-3 kalimat sudah cukup. Screenshot dan simpan. Ini adalah fondasi trust pertamamu.
[/CHALLENGE]
MD,
            ],

            // ── LESSON 9 ────────────────────────────────────────────────────
            [
                'title'             => 'Traffic Sources',
                'order'             => 9,
                'xp_reward'         => 40,
                'estimated_minutes' => 8,
                'content'           => <<<MD
**Dari mana pelanggan datang** — tanpa traffic, bisnis terbaikmu tidak akan ditemukan siapapun. Memahami sumber traffic adalah kunci untuk mengalokasikan waktu dan budget dengan benar.

Traffic dibagi menjadi 3 kategori besar: **Owned**, **Paid**, dan **Organic**.

[CASE_STUDY]
**Observasi Lapangan: UMKM Viral Tanpa Ads**
Sebuah brand camilan rumahan dari Solo memutuskan tidak punya budget untuk iklan. Mereka konsisten membuat konten behind-the-scenes produksi di TikTok setiap hari selama 45 hari. Di hari ke-46, satu video mendapat 2,3 juta views organik. Pesanan membanjir hingga closed order dalam 6 jam. Traffic organik bisa meledak kapan saja — tapi konsistensi adalah harga yang harus dibayar.
[/CASE_STUDY]

[INFOGRAPHIC:Tiga Sumber Traffic]
Paid Traffic | Cepat dan terukur → tapi berhenti saat budget habis
Organic Traffic | Murah (waktu adalah biayanya) → membangun jangka panjang
Owned Audience | Paling stabil → email list/WA blast tidak tergantung algoritma manapun
[/INFOGRAPHIC]

[QUIZ:Traffic jenis mana yang paling stabil dan tidak bisa diambil platform dalam jangka panjang?|Paid ads yang bisa dihentikan kapan saja oleh platform|Owned audience seperti email list dan database WA yang kamu kontrol|Viral organic yang tidak bisa diprediksi kapan datangnya|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Pilih **1 channel traffic utama** yang akan kamu fokuskan selama **30 hari ke depan**. Tulis rencana konkretnya: konten apa yang akan kamu buat, seberapa sering, dan metrik apa yang akan kamu ukur sebagai indikator keberhasilan.
[/CHALLENGE]
MD,
            ],

            // ── LESSON 10 ───────────────────────────────────────────────────
            [
                'title'             => 'Business System Thinking',
                'order'             => 10,
                'xp_reward'         => 55,
                'estimated_minutes' => 10,
                'content'           => <<<MD
**Mengubah bisnis jadi mesin otomatis** — ini bukan tentang bekerja lebih keras. Ini tentang membangun sistem yang bekerja bahkan saat kamu tidak ada.

Bisnis digital ideal punya empat tahap sistem yang terhubung: **Traffic → Leads → Sales → Retention**.

[CASE_STUDY]
**Observasi Lapangan: Email Automation yang Mengubah Segalanya**
Sebuah toko online fashion lokal mengalami masalah klasik: 95% penjualan dari pelanggan baru, repeat order hampir nol. Setelah mengimplementasikan sistem email automation — selamat datang, follow-up 3 hari, reminder abandoned cart, dan penawaran repeat buyer — **35% dari total revenue** mulai datang dari pelanggan lama dalam 60 hari. Sistem bekerja 24 jam tanpa tambahan tenaga.
[/CASE_STUDY]

[INFOGRAPHIC:Empat Tahap Sistem Bisnis Digital]
Traffic | Bagaimana orang menemukan kamu? (konten, ads, SEO, kolaborasi)
Leads | Bagaimana kamu menangkap data mereka? (email, WA, follow IG)
Sales | Bagaimana kamu mengkonversi mereka menjadi pembeli?
Retention | Bagaimana kamu membuat mereka kembali lagi dan lagi?
[/INFOGRAPHIC]

[QUIZ:Dalam alur bisnis digital Traffic → Leads → Sales → Retention, tahap setelah leads adalah?|Kembali ke traffic untuk mencari lebih banyak orang baru|Sales — mengkonversi leads menjadi transaksi nyata|Membuat produk baru untuk dijual kepada mereka|1]

Selamat — kamu telah menyelesaikan seluruh modul **Dasar Bisnis Digital**. Ini bukan akhir, ini adalah fondasi. Sekarang buktikan dengan action terakhir:

[CHALLENGE]
**Action Plan Hari Ini:**
Gambar (bisa di kertas, bisa di Figma/Canva) **full flow bisnis digitalmu** dari titik pertama orang menemukan kamu hingga mereka menjadi pelanggan repeat yang loyal. Identifikasi: di mana titik paling lemahnya? Itulah yang harus kamu perbaiki paling pertama.
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
                'xp_reward'         => $data['xp_reward'],
                'estimated_minutes' => $data['estimated_minutes'],
            ]);
        }

        $this->command->info('✅ Dasar Bisnis Digital — 10 lessons berhasil dimasukkan ke database.');
    }
}
