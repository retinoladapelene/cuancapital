<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class ProductIdeationSeed extends Seeder
{
    public function run(): void
    {
        Course::where('title', 'Product Ideation & Validasi Market')->delete();

        $course = Course::create([
            'title'         => 'Product Ideation & Validasi Market',
            'description'   => 'Pelajari cara meracik ide produk dari masalah nyata, memvalidasi pasar, dan memutuskan kapan harus lanjut atau pivot — sebelum keluar modal besar.',
            'level'         => 'beginner',
            'category'      => 'product',
            'xp_reward'     => 300,
            'lessons_count' => 10,
            'is_active'     => true,
        ]);

        $lessons = [

            [
                'title' => 'Ide Produk Berbasis Masalah',
                'order' => 1,
                'xp'    => 40,
                'min'   => 8,
                'content' => <<<MD
**Problem-first ideation** — produk sukses selalu lahir dari masalah nyata. Jika masalahnya kuat, produk hampir pasti dibutuhkan. Sebaliknya, produk yang dibuat tanpa akar masalah yang jelas akan berjuang keras meyakinkan pasar bahwa mereka butuh sesuatu yang tidak mereka rasakan.

[CASE_STUDY]
**Observasi Lapangan: Booking Dokter Online**
Platform booking dokter online sukses bukan karena teknologinya canggih — tapi karena masalahnya nyata dan menyakitkan: antrean rumah sakit yang bisa memakan 3-5 jam. Siapapun yang pernah mengalami itu langsung mengerti value-nya tanpa perlu penjelasan panjang. Masalah yang besar membuat produk menjual dirinya sendiri.
[/CASE_STUDY]

[INFOGRAPHIC:Rantai Nilai Problem-First]
Problem Kuat | Banyak orang merasakannya, sering terjadi, dan menyakitkan bila tidak diselesaikan
Demand Kuat | Orang aktif mencari solusi dan mau membayar untuk menghilangkan rasa sakit itu
Produk Laku | Solusi yang tepat sasaran tidak perlu dijual keras — ia ditarik oleh pasar
[/INFOGRAPHIC]

[QUIZ:Dari mana ide produk terbaik seharusnya berasal?|Inspirasi random yang tiba-tiba muncul di kepala|Masalah nyata yang dialami orang banyak secara berulang|Meniru langsung apa yang dilakukan kompetitor|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Tulis **10 masalah sehari-hari** yang sering kamu lihat, alami sendiri, atau dengar dari orang sekitarmu. Jangan filter — tulis semuanya dulu. Setelah 10 tertulis, tandai 3 yang paling sering muncul dan paling menyakitkan. Itulah kandidat ide terkuatmu.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Ideation Framework 5M',
                'order' => 2,
                'xp'    => 40,
                'min'   => 9,
                'content' => <<<MD
**Metode brainstorming terstruktur** — ide bagus tanpa framework evaluasi sering berakhir di eksekusi yang kacau. Framework 5M membantu kamu menilai kesiapan sebuah ide secara holistik sebelum memutuskan untuk maju.

**5M: Market, Money, Manpower, Momentum, Moat.**

[CASE_STUDY]
**Observasi Lapangan: Startup Gagal Bukan Karena Idenya**
Sebuah startup teknologi pendidikan memiliki ide yang brilian dan market yang jelas. Namun mereka gagal di bulan ke-8 bukan karena produknya buruk — melainkan karena **manpower tidak mencukupi**. Satu developer harus mengerjakan semuanya sendiri. Bottleneck manusia menggerogoti momentum lebih cepat dari kehabisan dana.
[/CASE_STUDY]

[INFOGRAPHIC:Framework 5M]
Market | Apakah ada cukup orang yang butuh dan mau bayar?
Money | Apakah modal awal cukup untuk mencapai titik validasi?
Manpower | Apakah kamu punya orang yang tepat untuk mengeksekusi?
Momentum | Apakah timing-nya tepat — tidak terlalu awal, tidak terlalu terlambat?
Moat | Apa yang membuat bisnismu sulit ditiru kompetitor dalam jangka panjang?
[/INFOGRAPHIC]

[QUIZ:Di awal bisnis, M mana yang paling kritis untuk divalidasi lebih dulu?|Money — karena tanpa modal tidak bisa mulai|Market — karena tanpa ada yang beli, semua usaha sia-sia|Moat — karena diferensiasi adalah segalanya|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Ambil ide terbaikmu dari lesson sebelumnya. Evaluasi menggunakan 5M — beri skor 1-5 untuk setiap dimensi. Dimensi mana yang skornya paling rendah? Itulah risiko terbesar yang harus kamu mitigasi sebelum lanjut.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Tren vs Opportunity',
                'order' => 3,
                'xp'    => 40,
                'min'   => 8,
                'content' => <<<MD
**Membedakan hype dan peluang nyata** — tidak semua tren layak dijadikan bisnis. Tren yang naik cepat biasanya turun sama cepatnya. Opportunity sejati memiliki **growth curve yang stabil dan berkelanjutan**.

[CASE_STUDY]
**Observasi Lapangan: Fidget Spinner — Pelajaran Mahal**
Fidget spinner viral secara global pada 2017. Ribuan pengusaha berbondong-bondong masuk pasar, memesan stok dalam jumlah besar. Dalam **6 bulan**, demandnya hampir nol. Mereka yang terlambat masuk — atau yang terlalu banyak stok — menanggung kerugian besar. Tren adalah undangan tipu muslihat bagi yang tidak membaca data.
[/CASE_STUDY]

[INFOGRAPHIC:Tren vs Opportunity]
Trend | Kurva spike — naik tajam, turun tajam. Butuh timing sempurna dan exit strategy jelas.
Opportunity | Growth curve — naik perlahan tapi stabil. Bisnis bisa dibangun di atasnya untuk jangka panjang.
[/INFOGRAPHIC]

[QUIZ:Bisnis yang stabil dan sustainable biasanya dibangun di atas?|Viral trend yang sedang naik daun|Opportunity jangka panjang yang addressable marketnya terus tumbuh|Niche yang dipilih secara random|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Cari **3 tren** yang sedang berlangsung saat ini (dari TikTok, Google Trends, atau berita). Analisis satu per satu: apakah ini trend spike yang akan hilang, atau opportunity yang punya fondasi jangka panjang? Tulis argumentasimu untuk masing-masing.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Market Size Validation',
                'order' => 4,
                'xp'    => 45,
                'min'   => 9,
                'content' => <<<MD
**Mengukur besar pasar** — ide produk yang brilliant tetap akan gagal jika pasarnya terlalu kecil. Market size validation memastikan ada cukup potensi revenue yang bisa kamu kejar sebelum menginvestasikan waktu dan uang.

Gunakan konsep **TAM → SAM → SOM** untuk estimasi yang terstruktur:
- **TAM** (Total Addressable Market): total pasar global
- **SAM** (Serviceable Addressable Market): pasar yang bisa kamu jangkau
- **SOM** (Serviceable Obtainable Market): realistis yang bisa kamu raih di tahun pertama

[CASE_STUDY]
**Observasi Lapangan: Produk Bagus, Pasar Terlalu Kecil**
Sebuah startup membuat aplikasi diet khusus untuk penderita penyakit langka tertentu. Produknya sangat bagus dan mendapat ulasan positif. Namun setelah 18 bulan, growth stagnan. Masalahnya bukan produk — tapi **market size yang terlalu kecil** untuk menopang biaya operasional dan pertumbuhan yang diharapkan investor.
[/CASE_STUDY]

[INFOGRAPHIC:TAM SAM SOM]
TAM | Berapa total orang di dunia yang punya masalah ini?
SAM | Berapa yang bisa kamu jangkau dengan channel dan bahasa yang kamu kuasai?
SOM | Berapa realistis yang bisa kamu konversi menjadi pembeli di tahun pertama?
[/INFOGRAPHIC]

[QUIZ:Mengapa market size menjadi pertimbangan krusial sebelum membangun produk?|Agar terlihat keren saat presentasi ke investor|Menentukan batas atas potensi revenue yang bisa kamu raih|Untuk keperluan branding dan positioning saja|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Lakukan estimasi TAM-SAM-SOM untuk ide produkmu. Gunakan angka nyata dari riset singkat (cek populasi target di BPS, jumlah pencarian di Google Keyword Planner, atau jumlah follower akun sejenis). Berapa SOM realistis yang bisa kamu raih dalam 12 bulan pertama?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Customer Persona Mapping',
                'order' => 5,
                'xp'    => 40,
                'min'   => 8,
                'content' => <<<MD
**Profil pelanggan ideal** — persona bukan karakter fiksi. Persona yang baik dibangun dari data nyata: **umur, tujuan hidup, frustrasi terbesar, kebiasaan beli, dan platform yang aktif mereka gunakan**.

Semakin spesifik personamu, semakin tepat sasaran setiap keputusan marketing, pricing, dan produk yang kamu buat.

[CASE_STUDY]
**Observasi Lapangan: Brand Skincare yang Salah Target**
Sebuah brand skincare premium menargetkan remaja usia 16-20 tahun dengan kampanye besar-besaran. Hasilnya buruk. Setelah dievaluasi, ditemukan bahwa produk premium dengan harga Rp 400.000 ke atas tidak cocok dengan daya beli remaja yang masih bergantung pada uang saku. **Persona yang salah = budget marketing yang terbakar sia-sia.**
[/CASE_STUDY]

[INFOGRAPHIC:Komponen Persona yang Lengkap]
Demografis | Umur, jenis kelamin, lokasi, pendapatan, pekerjaan
Psikografis | Nilai hidup, aspirasi, ketakutan, gaya hidup
Frustasi | Masalah apa yang paling sering membuat mereka frustrasi?
Kebiasaan Beli | Di mana mereka belanja? Kapan? Apa yang mempengaruhi keputusan?
[/INFOGRAPHIC]

[QUIZ:Persona pelanggan yang akurat harus dibangun berdasarkan?|Tebakan dan intuisi founder|Data riil dari riset, wawancara, dan observasi perilaku nyata|Perasaan dan preferensi tim internal|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **1 profil persona detail** untuk pelanggan ideal produkmu. Beri nama, umur, pekerjaan, pendapatan bulanan, frustrasi terbesar, dan platform utama yang mereka gunakan. Ceritakan satu hari dalam hidupnya — di mana masalah yang ingin kamu selesaikan muncul.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Competitor Gap Analysis',
                'order' => 6,
                'xp'    => 45,
                'min'   => 9,
                'content' => <<<MD
**Mencari celah pasar** — kompetitor bukan musuh. Mereka adalah guru terbaik yang sudah membayar biaya sekolahnya di depan. Dari mereka, kamu bisa belajar apa yang bekerja, apa yang tidak, dan yang paling berharga: **apa yang belum mereka lakukan**.

[CASE_STUDY]
**Observasi Lapangan: Fitur Offline yang Mengubah Permainan**
Sebuah aplikasi catatan kecil berhasil merebut pangsa pasar dari raksasa yang sudah mapan. Caranya? Simple: **fitur offline yang berfungsi sempurna tanpa koneksi internet**. Kompetitor besar terlalu sibuk menambah fitur kompleks hingga mengabaikan kebutuhan dasar pengguna di daerah dengan koneksi tidak stabil. Satu gap, satu kemenangan besar.
[/CASE_STUDY]

[INFOGRAPHIC:Proses Competitor Gap Analysis]
Identifikasi | Siapa 3-5 kompetitor langsung di space yang sama?
Analisis | Apa yang mereka lakukan dengan baik? Apa yang sering dikeluhkan user mereka?
Gap Finding | Apa yang belum mereka tawarkan? Di mana user mereka merasa kecewa?
Positioning | Bagaimana kamu bisa masuk dan memenangkan pelanggan yang underserved itu?
[/INFOGRAPHIC]

[QUIZ:Tujuan utama melakukan analisis kompetitor adalah?|Meniru semua yang mereka lakukan agar tidak tertinggal|Menjatuhkan reputasi mereka di mata konsumen|Menemukan celah yang belum mereka isi dan kamu bisa dominasi|2]

[CHALLENGE]
**Action Plan Hari Ini:**
Pilih **3 kompetitor** dari ide produkmu. Untuk masing-masing, cari **5 review negatif** dari pengguna mereka (Google Play, App Store, Tokopedia, atau forum). Apa pola keluhan yang berulang? Itulah gap market yang menunggu untuk diisi.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'MVP Concept',
                'order' => 7,
                'xp'    => 45,
                'min'   => 9,
                'content' => <<<MD
**Minimum Viable Product** — MVP bukan produk yang jelek atau setengah jadi. MVP adalah versi **paling sederhana** dari produkmu yang masih bisa menyelesaikan masalah inti pengguna dan cukup untuk diuji di pasar nyata.

Tujuannya satu: mendapatkan **validated learning** dengan investasi sekecil mungkin.

[CASE_STUDY]
**Observasi Lapangan: Marketplace yang Mulai dari Website Sederhana**
Salah satu marketplace terbesar di Asia Tenggara tidak langsung membangun app iOS dan Android yang mewah. Versi pertamanya adalah **website paling sederhana** — tidak ada fitur rekomendasi, tidak ada chat real-time, tidak ada notifikasi push. Hanya listing produk dan tombol kontak. Dari sana, mereka belajar apa yang benar-benar dibutuhkan pengguna sebelum membangun fitur berikutnya.
[/CASE_STUDY]

[INFOGRAPHIC:Siklus MVP]
Build Kecil | Bangun hanya fitur inti yang menyelesaikan masalah utama
Test Cepat | Rilis ke sekelompok kecil pengguna nyata
Learn | Kumpulkan data dan feedback — apa yang berhasil, apa yang tidak?
Improve | Iterasi berdasarkan data, bukan asumsi
[/INFOGRAPHIC]

[QUIZ:Tujuan utama dari membangun MVP adalah?|Meluncurkan produk yang sempurna dan bebas bug|Mendapatkan pembelajaran tervalidasi dari pasar nyata dengan investasi minimal|Membangun brand awareness yang kuat sebelum kompetitor bergerak|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Deskripsikan **MVP produkmu** dalam satu paragraf: fitur apa yang **wajib ada** (tanpa ini produk tidak berfungsi), fitur apa yang **boleh ditunda** (nice to have), dan bagaimana cara kamu mengujinya ke 10-20 orang pertama.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Pre-Selling Strategy',
                'order' => 8,
                'xp'    => 45,
                'min'   => 9,
                'content' => <<<MD
**Validasi dengan penjualan awal** — cara paling kuat untuk membuktikan ide adalah membuat orang mengeluarkan uang, bukan sekadar mengatakan "menarik" atau menekan tombol like. **Payment is proof** — segalanya sebelum itu hanya asumsi.

[CASE_STUDY]
**Observasi Lapangan: Kelas Online yang Terjual Sebelum Direkam**
Seorang creator konten keuangan mengumumkan kelas online dengan deskripsi singkat dan halaman pendaftaran. Sebelum satu video pun direkam, **120 orang membayar** dalam 72 jam. Ia baru mulai merekam setelah itu — dengan kepastian penuh bahwa konten ini dibutuhkan. Tidak ada yang lebih memotivasi daripada uang yang sudah masuk ke rekening.
[/CASE_STUDY]

[INFOGRAPHIC:Interest vs Intent vs Proof]
Interest | "Wah, menarik!" — banyak yang bilang ini, tidak membuktikan apa-apa
Intent | Mengisi survey atau waitlist — sinyal lebih kuat, tapi masih bisa dibatalkan
Payment | Orang mengeluarkan uang nyata — ini validasi yang tidak bisa diperdebatkan
[/INFOGRAPHIC]

[QUIZ:Validasi paling kuat untuk membuktikan ide bisnis siap dieksekusi?|Ratusan likes di postingan media sosial|Survey dengan ratusan responden yang mengatakan tertarik|Orang yang bersedia membayar sebelum produk selesai dibuat|2]

[CHALLENGE]
**Action Plan Hari Ini:**
Rancang **penawaran pre-order** untuk produkmu: tentukan harga early bird (biasanya 30-50% dari harga normal), batas waktu, dan apa yang mereka dapatkan sebagai early supporter. Buat draft satu halaman penawaran ini — bahkan jika kamu belum yakin akan merilisnya, proses menulis akan memaksa kamu memperjelas value proposition-mu.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Feedback Loop System',
                'order' => 9,
                'xp'    => 40,
                'min'   => 8,
                'content' => <<<MD
**Iterasi produk berbasis user** — produk terbaik di dunia tidak dilahirkan sempurna. Mereka dibentuk oleh siklus feedback yang tidak pernah berhenti: **launch, dengarkan, perbaiki, ulangi**.

Pendapat founder adalah yang paling tidak objektif. Pendapat user adalah yang paling bernilai.

[CASE_STUDY]
**Observasi Lapangan: Fitur yang Disuarakan 70% User**
Sebuah aplikasi desain grafis melakukan survei kepuasan setiap 3 bulan. Satu permintaan yang sama muncul dari **70% pengguna aktif**: kemampuan mengekspor dalam format tertentu. Tim sempat menunda karena menganggap ini bukan prioritas. Setelah akhirnya diimplementasikan, rating di app store naik 0.4 bintang dalam sebulan dan churn rate turun signifikan. Pengguna berbicara — dengarkan.
[/CASE_STUDY]

[INFOGRAPHIC:Siklus Feedback yang Sehat]
Launch | Rilis ke pengguna nyata — jangan menunggu sempurna
Feedback | Kumpulkan respons: apa yang berhasil, apa yang membingungkan, apa yang hilang?
Improve | Prioritaskan perbaikan berdasarkan frekuensi keluhan dan dampak
Repeat | Lakukan siklus ini setiap 2-4 minggu tanpa henti
[/INFOGRAPHIC]

[QUIZ:Sumber insight terbaik untuk pengembangan produk yang berkelanjutan adalah?|Mentor atau advisor berpengalaman di industri|Pengguna aktif yang berinteraksi langsung dengan produk setiap hari|Laporan tentang strategi kompetitor|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **5 pertanyaan feedback** yang akan kamu tanyakan kepada 10 calon user pertamamu setelah mereka mencoba MVP-mu. Pertanyaan yang baik bersifat terbuka (bukan ya/tidak), spesifik pada pengalaman mereka, dan mencari tahu "mengapa" di balik perasaan mereka.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Go / No-Go Decision',
                'order' => 10,
                'xp'    => 55,
                'min'   => 10,
                'content' => <<<MD
**Menentukan lanjut atau pivot** — keberanian terbesar seorang founder bukan memulai, melainkan **tahu kapan harus berhenti, mengubah arah, atau mempercepat**. Tidak semua ide harus dilanjutkan, dan tidak ada yang salah dengan pivot jika data berbicara.

**Data menentukan keputusan — bukan ego founder.**

[CASE_STUDY]
**Observasi Lapangan: Pivot yang Menyelamatkan Bisnis**
Sebuah startup food delivery menghadapi stagnansi growth selama 4 bulan berturut-turut. Alih-alih keras kepala, mereka menganalisis data dan menemukan bahwa demand grocery delivery di kota mereka tumbuh 3x lebih cepat dari food delivery. Dalam 6 minggu mereka pivot — dan dalam 3 bulan berikutnya revenue mereka tumbuh 240%. Pivot bukan kalah, pivot adalah membaca peluang.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Go / No-Go]
Green Light (GO) | Validasi berhasil → ada yang mau bayar → market size cukup → tim siap
Yellow Light (CAUTION) | Ada indikasi demand tapi perlu refinement → pivot kecil dulu
Red Light (NO-GO) | Tidak ada yang mau bayar → market terlalu kecil → tim tidak siap → stop dan mulai ulang
[/INFOGRAPHIC]

[QUIZ:Kapan waktu yang tepat untuk melakukan pivot pada sebuah bisnis?|Ketika kamu bosan dan ingin mencoba hal baru|Ketika data secara konsisten menunjukkan arah saat ini tidak bisa menghasilkan|Ketika kompetitor terlalu banyak di pasar yang sama|1]

Selamat menyelesaikan modul **Product Ideation & Validasi Market**! Kamu kini punya framework lengkap dari penemuan ide hingga keputusan go/no-go. Satu langkah terakhir:

[CHALLENGE]
**Action Plan Hari Ini:**
Tentukan **3 indikator sukses yang terukur** untuk ide produkmu sebelum launch — misalnya: "Minimum 50 pre-order dalam 14 hari", "Conversion rate landing page minimal 5%", "NPS dari 20 tester minimal 7/10". Jika indikator ini tercapai: GO. Jika tidak: evaluasi dan pivot. Komit pada angka ini sebelum kamu terlanjur jatuh cinta pada idenya sendiri.
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

        $this->command->info('✅ Product Ideation & Validasi Market — 10 lessons berhasil dimasukkan ke database.');
    }
}
