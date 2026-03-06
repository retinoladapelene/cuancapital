<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CopywritingStorytellingSeed extends Seeder
{
    public function run(): void
    {
        Course::where('title', 'Copywriting & Storytelling')->delete();

        $course = Course::create([
            'title'         => 'Copywriting & Storytelling',
            'description'   => 'Kuasai seni merangkai kata yang menggerakkan emosi dan mendorong konversi. Dari psychology of persuasion, hook writing, PAS framework, hingga high-converting offer structure.',
            'level'         => 'intermediate',
            'category'      => 'marketing',
            'xp_reward'     => 400,
            'lessons_count' => 10,
            'is_active'     => true,
        ]);

        $lessons = [

            [
                'title' => 'Psychology of Persuasion',
                'order' => 1,
                'xp'    => 45,
                'min'   => 9,
                'content' => <<<MD
**Mengapa orang membeli** — jawabannya tidak akan pernah kamu temukan di spesifikasi produk atau perbandingan harga. Orang membeli karena **emosi** — dan emosi itu kemudian dibenarkan oleh logika.

Tugas copywriting bukan menjelaskan produk. Tugas copywriting adalah **menyentuh emosi yang tepat pada waktu yang tepat**, lalu menyediakan logika sebagai justifikasi agar keputusan terasa rasional.

[CASE_STUDY]
**Observasi Lapangan: Janji Kepercayaan Diri yang Laku Keras**
Dua produk skincare dengan kandungan bahan yang hampir identik. Satu menjual "formula hyaluronic acid 2% dengan niacinamide" — terjual biasa-biasa saja. Yang lainnya menjual **"Kulit glowing yang membuat kamu berani tampil tanpa filter"** — sold out dalam 3 hari. Perbedaannya bukan di bahan — tapi di emosi yang dijanjikan. Kepercayaan diri adalah komoditas yang tidak ada habisnya.
[/CASE_STUDY]

[INFOGRAPHIC:Siklus Keputusan Pembelian]
Emotion | Pemicu awal — rasa ingin, rasa takut ketinggalan, rasa ingin diakui, harapan akan transformasi
Justification | Otak mencari alasan logis: harga, spesifikasi, review, perbandingan
Purchase | Transaksi terjadi — tapi keputusan sudah dibuat jauh sebelum checkout
[/INFOGRAPHIC]

[QUIZ:Apa trigger utama yang mendorong sebagian besar keputusan pembelian?|Analisis logis terhadap spesifikasi dan perbandingan harga|Emosi — perasaan yang kemudian dibenarkan dengan logika|Penawaran diskon yang membuat harga terasa lebih rasional|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Tulis **3 alasan emosional** kenapa orang harus membeli produkmu — bukan fiturnya, bukan harganya. Fokus pada **perasaan** yang mereka dapatkan setelah membelinya: apa yang berubah dalam hidup mereka? Apa rasa takut yang hilang? Apa rasa bangga yang muncul?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Target Audience Voice',
                'order' => 2,
                'xp'    => 45,
                'min'   => 8,
                'content' => <<<MD
**Bahasa customer** — copy terbaik bukan yang paling puitis atau paling akademis. Copy terbaik adalah yang terdengar **persis seperti suara di kepala target marketmu** saat mereka sedang memikirkan masalah yang ingin kamu selesaikan.

Ketika orang membaca copymu dan berpikir *"Hei, ini persis yang aku rasakan"* — kepercayaan terbentuk secara instan. Itu yang dimaksud dengan audience voice.

[CASE_STUDY]
**Observasi Lapangan: Slang yang Mengangkat Engagement**
Sebuah brand fashion remaja mengubah semua caption-nya dari bahasa formal ke bahasa yang benar-benar dipakai anak muda: singkatan, slang, emoji, dan referensi budaya pop yang relevan. Dalam 30 hari, engagement rate naik dari 1,2% ke 6,8%. Bukan karena produknya berubah — tapi karena brand-nya akhirnya **berbicara seperti teman**, bukan seperti perusahaan.
[/CASE_STUDY]

[INFOGRAPHIC:Prinsip Audience Voice]
Dengarkan | Kumpulkan kata-kata nyata dari review, komentar, DM, dan forum target marketmu
Adopsi | Gunakan kata-kata *mereka* — bukan kata-katamu — dalam copywriting
Trust | Audiens merasa dipahami → kepercayaan terbentuk sebelum produk disentuh
[/INFOGRAPHIC]

[QUIZ:Copy yang paling efektif menggunakan bahasa seperti apa?|Bahasa formal agar terkesan profesional dan kredibel|Bahasa yang biasa dipakai oleh target audience dalam keseharian mereka|Bahasa akademik agar terkesan berbobot dan terpercaya|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buka 20 komentar atau review dari target marketmu (di postingan kompetitor, marketplace, atau forum). Catat **5 kata atau frasa khas** yang berulang — itulah kosakata asli mereka. Masukkan kata-kata ini ke dalam copy atau caption berikutmu.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Hook Writing Formula',
                'order' => 3,
                'xp'    => 50,
                'min'   => 9,
                'content' => <<<MD
**Kalimat pembuka yang menentukan segalanya** — di era scrolling tanpa henti, kamu punya kurang dari 3 detik untuk membuat seseorang berhenti dan membaca. Hook adalah satu-satunya senjata yang kamu miliki di 3 detik itu.

Tiga formula hook yang terbukti bekerja: **Question Hook** (pancing rasa penasaran), **Shock Hook** (fakta mengejutkan), **Curiosity Hook** (janjikan sesuatu tapi jangan ungkap semuanya).

[CASE_STUDY]
**Observasi Lapangan: Kekuatan Angka dalam Headline**
Penelitian dari BuzzSumo terhadap 100 juta artikel menunjukkan: **headline yang mengandung angka spesifik mendapatkan engagement 2× lebih tinggi** dari headline tanpa angka. "Cara Meningkatkan Penjualan" vs "7 Cara yang Meningkatkan Penjualan 47% dalam 30 Hari" — keduanya topik sama, dampaknya sangat berbeda. Angka spesifik = kredibilitas instan.
[/CASE_STUDY]

[INFOGRAPHIC:3 Formula Hook Terbukti]
Question Hook | Tanyakan sesuatu yang tidak bisa mereka jawab — memaksa otak untuk lanjut membaca
Shock Hook | Fakta mengejutkan yang menghancurkan asumsi — "90% bisnis gagal bukan karena modal"
Curiosity Hook | Janjikan sesuatu berharga tapi tahan informasinya — "Satu kesalahan ini yang membuat iklanmu membuang uang"
[/INFOGRAPHIC]

[QUIZ:Apa fungsi utama hook dalam sebuah copy atau konten?|Menutup argumen dengan kesimpulan yang kuat|Menarik perhatian di detik pertama agar audiens mau melanjutkan membaca|Menjadi call to action yang mendorong pembelian|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **3 hook berbeda** untuk produk atau konten terbarumu — satu menggunakan Question Hook, satu Shock Hook, satu Curiosity Hook. Post ketiganya di hari berbeda dan ukur mana yang mendapat engagement tertinggi.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Problem–Agitate–Solution',
                'order' => 4,
                'xp'    => 50,
                'min'   => 10,
                'content' => <<<MD
**Framework copywriting paling klasik dan paling mematikan** — PAS (Problem-Agitate-Solution) adalah struktur yang bekerja di semua platform, semua industri, dan semua ukuran bisnis. Dari email marketing, landing page, caption Instagram, hingga skrip iklan video.

Strukturnya sederhana: **identifikasi masalah → perbesar rasa sakitnya → tawarkan solusi sebagai pelepasan**.

[CASE_STUDY]
**Observasi Lapangan: Landing Page Diet yang Mengkonversi**
Landing page kursus diet yang biasa-biasa saja bertransformasi setelah menerapkan PAS. Problem: *"Sudah coba diet ber kali-kali tapi berat badan selalu kembali naik?"* Agitate: *"Frustrasi melihat foto lama, merasa tidak percaya diri di acara keluarga, pakaian favorit tidak muat lagi — dan tidak tahu harus mulai dari mana."* Solution: *"Program 30 hari kami dirancang khusus untuk mengubah kebiasaan dari akar, bukan sekadar memotong kalori."* Conversion rate naik 3x setelah perubahan ini.
[/CASE_STUDY]

[INFOGRAPHIC:Anatomi PAS]
Problem | Sebutkan masalah yang sangat spesifik — bukan masalah umum, tapi masalah yang terasa personal
Agitate | Perbesar konsekuensinya — gambarkan kehidupan jika masalah ini tidak diselesaikan
Solution | Hadirkan produkmu sebagai jalan keluar — relief setelah tension yang dibangun
[/INFOGRAPHIC]

[QUIZ:Dalam framework PAS, apa yang dimaksud dengan tahap Agitate?|Memberikan solusi yang menjadi jawaban dari masalah|Memperbesar dan mempertajam rasa sakit dari masalah agar urgensi terasa nyata|Membuat penawaran dengan harga spesial dan tenggat waktu|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Tulis **copy PAS lengkap** untuk produk atau jasamu — minimal 3 paragraf. Problem harus sangat spesifik (bukan "masalah bisnis" tapi "kehilangan 2 jam sehari hanya untuk reply chat pelanggan"). Agitate harus membuat pembaca mengangguk. Solution harus terasa seperti napas lega.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Benefit vs Feature',
                'order' => 5,
                'xp'    => 45,
                'min'   => 8,
                'content' => <<<MD
**Menjual hasil, bukan spesifikasi** — ini adalah kesalahan paling universal yang dilakukan hampir semua penjual pemula. Mereka jatuh cinta pada produk mereka dan ingin menceritakan setiap detail teknisnya. Tapi customer tidak peduli dengan spesifikasi — mereka peduli pada **apa yang berubah dalam hidup mereka** setelah membeli.

Feature menjelaskan *apa* produknya. Benefit menjelaskan *mengapa* itu penting bagi customer.

[CASE_STUDY]
**Observasi Lapangan: RAM vs Produktivitas**
Iklan laptop dengan copy "RAM 16GB, processor Intel Core i7 generasi ke-12" vs "Edit video 4K tanpa lag, render film pendekmu dalam 8 menit, dan multitask tanpa pernah menunggu." Spesifikasi teknis yang sama persis — tapi copy kedua berbicara langsung ke mimpi dan frustrasi editor video. Dalam A/B test, versi kedua menghasilkan CTR 4x lebih tinggi.
[/CASE_STUDY]

[INFOGRAPHIC:Cara Menerjemahkan Feature ke Benefit]
Feature | Apa yang dimiliki produk — spesifikasi, bahan, teknologi, proses
Tanyakan "So What?" | Kenapa fitur ini penting? Apa dampaknya bagi customer?
Benefit | Hasil yang dirasakan customer — waktu yang dihemat, rasa sakit yang hilang, status yang naik
[/INFOGRAPHIC]

[QUIZ:Apa yang sebenarnya mendorong keputusan pembelian customer?|Spesifikasi teknis yang lengkap dan rinci|Benefit — dampak nyata yang dirasakan customer dalam kehidupannya|Nama merek yang sudah terkenal di pasaran|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Ambil **5 fitur utama** produkmu. Untuk setiap fitur, terapkan pertanyaan "So What?" sebanyak dua kali hingga menemukan benefit yang benar-benar relevan secara emosional. Ganti semua deskripsi produkmu dengan bahasa benefit mulai hari ini.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Story Selling',
                'order' => 6,
                'xp'    => 50,
                'min'   => 10,
                'content' => <<<MD
**Jualan lewat cerita** — otak manusia tidak dirancang untuk memproses fakta dan data. Otak manusia dirancang untuk memproses **narasi**. Ketika kamu bercerita, otak pendengar secara harfiah mensimulasikan pengalaman yang kamu ceritakan — itulah yang membuat cerita begitu bertenaga dalam copywriting.

Story selling bukan membohongi customer. Ini tentang memperlihatkan transformasi nyata yang bisa mereka visualisasikan untuk diri mereka sendiri.

[CASE_STUDY]
**Observasi Lapangan: Iklan Narasi vs Iklan Produk**
Sebuah brand supplement lokal membandingkan dua jenis iklan. Versi A: tampilkan produk, list manfaat, harga, tombol beli. Versi B: cerita seorang pria 34 tahun yang selalu kelelahan, tidak bisa produktif hingga sore, meragukan dirinya di depan keluarga — hingga menemukan routinenya berubah setelah 3 minggu. Versi B menghasilkan **conversion rate 5x lebih tinggi** dan cost per acquisition 60% lebih rendah.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Story Selling]
Karakter | Pilih protagonis yang persis seperti target audiensmu — mereka harus bisa bilang "itu aku"
Konflik | Gambarkan masalah dan perjuangan yang dirasakan sebelum menemukan solusi
Transformasi | Tunjukkan kehidupan setelah solusi — spesifik dan bisa diukur
CTA | Ajak mereka memulai perjalanan yang sama
[/INFOGRAPHIC]

[QUIZ:Mengapa storytelling terbukti meningkatkan konversi penjualan?|Karena cerita membuat produk terlihat lebih mahal dan eksklusif|Karena cerita membangun trust dan membantu audiens memvisualisasikan transformasi yang mereka inginkan|Karena cerita membuat iklan lebih panjang sehingga terlihat lebih informatif|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Tulis **cerita singkat 100-150 kata** tentang customer yang terbantu produkmu — bisa nyata atau representatif. Gunakan struktur: situasi sebelum → momen menemukan produkmu → perubahan spesifik yang terjadi. Gunakan cerita ini di bio, halaman produk, atau postingan berikutnya.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'CTA Optimization',
                'order' => 7,
                'xp'    => 45,
                'min'   => 8,
                'content' => <<<MD
**Call to action yang benar-benar diklik** — CTA adalah titik konversi. Semua copy yang kamu tulis, semua emosi yang kamu bangun, semua kepercayaan yang kamu tanam — semuanya berakhir di satu tombol atau satu kalimat ini. Dan kebanyakan CTA dibuat dengan cara yang paling malas.

CTA yang kuat harus memenuhi tiga syarat: **jelas** (orang tahu persis apa yang terjadi setelah klik), **urgent** (ada alasan untuk bertindak sekarang), **spesifik** (tentang mereka, bukan tentang kamu).

[CASE_STUDY]
**Observasi Lapangan: Satu Kata yang Mengubah Revenue**
Sebuah SaaS company mengubah CTA dari "Submit" menjadi "Get my free trial." CTR naik 90%. Lalu diubah lagi menjadi "Start my free 14-day trial — no credit card needed." CTR naik 128% dibanding original. Semakin spesifik benefit yang dijanjikan CTA, semakin tinggi probabilitas klik. Karena specificity = credibility.
[/CASE_STUDY]

[INFOGRAPHIC:Anatomi CTA yang Mengkonversi]
Jelas | Orang tahu persis apa yang terjadi setelah mereka klik — tidak ada ambiguitas
Urgent | Ada alasan untuk bertindak sekarang, bukan nanti — tenggat, stok terbatas, harga naik
Spesifik | Bicara tentang hasil yang MEREKA dapatkan, bukan aksi yang KAMU inginkan
[/INFOGRAPHIC]

[QUIZ:CTA yang paling efektif dalam mendorong konversi harus bersifat?|Umum agar bisa digunakan di semua situasi|Spesifik — menyebutkan benefit yang didapat dan menciptakan urgensi|Panjang agar semua informasi penting bisa disertakan|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **3 versi CTA berbeda** untuk produk atau kontenmu — satu fokus pada benefit, satu fokus pada urgensi, satu fokus pada penghilangan risiko (contoh: "Coba gratis 7 hari"). Test ketiganya dan ukur mana yang paling banyak diklik.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Social Proof Copy',
                'order' => 8,
                'xp'    => 45,
                'min'   => 9,
                'content' => <<<MD
**Bukti sosial sebagai mesin kepercayaan** — manusia adalah makhluk sosial yang secara naluriah mengikuti apa yang dilakukan orang lain, terutama dalam situasi yang tidak pasti. Ketika seseorang ragu membeli produkmu, brain mereka secara otomatis bertanya: *"Apakah orang lain sudah mencoba ini? Apa hasilnya?"*

Social proof menjawab pertanyaan itu sebelum ditanyakan.

[CASE_STUDY]
**Observasi Lapangan: Angka yang Membangun Kepercayaan Instan**
Marketplace riset membuktikan: produk dengan label **"10.000+ pembeli"** mendapat kepercayaan 6x lebih cepat dari produk tanpa angka tersebut — walau kualitas produknya identik. Di level copy, frasa "Sudah dipercaya oleh 2.847 founder di Indonesia" lebih kuat dari "Produk terpercaya" — karena spesifisitas angka menciptakan verifikasi mental yang instan.
[/CASE_STUDY]

[INFOGRAPHIC:Hierarki Social Proof]
Testimoni Spesifik | Nama nyata + hasil terukur + foto = paling kuat
Angka Pengguna | "Dipercaya 5.000+ customer" — volume membangun persepsi aman
Rating & Review | Bintang + komentar — mudah dicerna, universal dipercaya
Media Mention | "Seperti yang diliput oleh..." — otoritas pihak ketiga
[/INFOGRAPHIC]

[QUIZ:Social proof dalam copywriting berfungsi terutama untuk?|Mempercantik tampilan halaman produk|Membangun kepercayaan instan dengan bukti bahwa orang lain sudah berhasil|Meningkatkan peringkat SEO halaman produk di mesin pencari|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Kumpulkan **3 bukti sosial** untuk produkmu dalam format yang paling kuat: nama + masalah sebelumnya + hasil spesifik yang dicapai. Jika belum ada, hubungi 3 customer lama hari ini dan minta mereka menjawab 3 pertanyaan singkat yang kamu siapkan.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Objection Handling Copy',
                'order' => 9,
                'xp'    => 50,
                'min'   => 9,
                'content' => <<<MD
**Menjawab keraguan sebelum ditanyakan** — setiap calon pembeli memiliki pertanyaan dan kekhawatiran di kepala mereka. Jika copy-mu tidak menjawab keraguan itu, mereka pergi dalam diam — kamu tidak pernah tahu kenapa mereka tidak jadi beli.

Copy yang kuat mengantisipasi dan menjawab objection utama **sebelum calon pembeli sempat memikirkannya sepenuhnya**.

[CASE_STUDY]
**Observasi Lapangan: Garansi yang Melipatgandakan Penjualan**
Sebuah brand kursus online menambahkan satu kalimat ke landing page-nya: *"Jika dalam 30 hari kamu tidak merasakan perbedaan, kami kembalikan uangmu 100% tanpa pertanyaan."* Tidak ada perubahan lain — harga sama, produk sama. Konversi naik **64%** dalam satu bulan. Satu kalimat itu menjawab satu objection terbesar: **takut rugi dan kecewa**.
[/CASE_STUDY]

[INFOGRAPHIC:Objection Terbesar dan Cara Menjawabnya]
Terlalu mahal | Tunjukkan ROI — "Harga 1 bulan kopi vs hasil yang bertahan seumur hidup"
Takut tidak berhasil | Berikan garansi + tampilkan bukti orang yang situasinya lebih sulit berhasil
Tidak yakin kualitas | Testimoni spesifik + behind-the-scenes proses + sertifikasi jika ada
Tidak sekarang | Tunjukkan konsekuensi menunda — apa yang hilang setiap hari tanpa solusi ini
[/INFOGRAPHIC]

[QUIZ:Mengatasi objection dalam copy secara proaktif berdampak pada?|Meningkatnya bounce rate karena copy terlalu panjang|Meningkatnya conversion karena keraguan yang menghambat pembelian berhasil dieliminasi|Meningkatnya biaya produksi konten marketing|1]

[CHALLENGE]
**Action Plan Hari Ini:**
List **5 keraguan terbesar** yang paling sering muncul dari calon pembeli produkmu. Untuk setiap keraguan, tulis satu kalimat jawaban yang jujur dan meyakinkan. Masukkan kelimanya ke dalam FAQ di halaman produk atau landing page-mu.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'High Converting Offer Structure',
                'order' => 10,
                'xp'    => 60,
                'min'   => 10,
                'content' => <<<MD
**Struktur penawaran yang tidak bisa ditolak** — offer yang lemah adalah alasan terbesar mengapa produk bagus tidak terjual. Bukan karena produknya buruk, bukan karena iklannya salah — tapi karena penawarannya tidak cukup kuat untuk mendorong tindakan.

Offer kuat terdiri dari empat lapisan yang bekerja bersama: **Value + Urgency + Bonus + Guarantee**.

[CASE_STUDY]
**Observasi Lapangan: Bundling yang Mendongkrak AOV**
Sebuah toko online produk rumah tangga menambahkan penawaran bundling: beli produk utama + produk komplementer + bonus ebook panduan perawatan + garansi tukar 30 hari. Dibanding menjual produk satuan, bundling ini meningkatkan **Average Order Value (AOV) sebesar 40%** — dan yang lebih penting, meningkatkan kepuasan customer karena mereka merasa mendapat jauh lebih banyak dari uang yang dikeluarkan.
[/CASE_STUDY]

[INFOGRAPHIC:4 Lapisan Offer yang Tidak Bisa Ditolak]
Value Stack | Produk utama + bonus tambahan — pastikan total value jauh melebihi harga yang diminta
Urgency | Tenggat waktu nyata / stok terbatas — beri alasan konkret untuk bertindak sekarang
Bonus | Tambahan yang relevan dan bernilai — meningkatkan perceived value tanpa biaya besar
Guarantee | Hilangkan risiko — garansi uang kembali atau garansi hasil yang spesifik
[/INFOGRAPHIC]

[QUIZ:Komponen apa yang membuat sebuah offer menjadi sangat sulit untuk ditolak?|Diskon sebesar-besarnya hingga di bawah harga pasar|Value stack yang lengkap — gabungan value, urgency, bonus, dan guarantee|Logo premium dan desain halaman yang sangat profesional|1]

Selamat menyelesaikan modul **Copywriting & Storytelling**! Kamu kini memiliki arsenal lengkap dari psychology of persuasion hingga offer structure. Saatnya uji semua yang sudah kamu pelajari:

[CHALLENGE]
**Action Plan Hari Ini:**
Rancang **satu offer lengkap** untuk produk atau jasamu menggunakan semua 4 lapisan: (1) Value Stack — apa saja yang didapat customer, (2) Urgency — tenggat atau stok terbatas yang nyata, (3) Bonus — apa tambahan yang relevan bisa kamu berikan, (4) Guarantee — risiko apa yang bisa kamu hilangkan. Tulis offer ini dalam format yang siap dipublish hari ini.
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

        $this->command->info('✅ Copywriting & Storytelling — 10 lessons berhasil dimasukkan ke database.');
    }
}
