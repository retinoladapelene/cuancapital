<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class HiringLeadershipExpertSeed extends Seeder
{
    public function run(): void
    {
        Course::where('title', 'Membangun Tim & Sistem')->delete();

        $course = Course::create([
            'title'         => 'Membangun Tim & Sistem',
            'description'   => 'Level expert — bangun struktur tim yang scalable: dari founder trap hingga exit readiness. Hiring framework, delegation architecture, KPI system, culture engineering, dan leadership decision matrix.',
            'level'         => 'expert',
            'category'      => 'leadership',
            'xp_reward'     => 1200,
            'lessons_count' => 10,
            'is_active'     => true,
        ]);

        $lessons = [

            [
                'title' => 'Founder Trap vs CEO Mindset',
                'order' => 1, 'xp' => 80, 'min' => 12,
                'content' => <<<MD
**Transisi dari Operator ke Leader** — kebanyakan founder yang stuck bukan karena strategi yang salah atau market yang buruk. Mereka stuck karena **masih berpikir dan bekerja seperti staff sendiri**. Mereka adalah orang paling sibuk di perusahaan — mengerjakan desain, membalas CS, memantau setiap transaksi, dan mengambil semua keputusan kecil. Ini bukan kehebatan. Ini adalah **Founder Trap** — perangkap yang memastikan bisnis tidak pernah bisa tumbuh melampaui kapasitas personal pemiliknya.

CEO bukan orang yang paling kerja keras. CEO adalah orang yang mengambil keputusan dengan **leverage tertinggi** — keputusan yang multiplies output seluruh tim, bukan hanya menyelesaikan satu task lagi.

[CASE_STUDY]
**Observasi Lapangan: 3 Tahun Stagnan, 1 Keputusan yang Mengubah Segalanya**
Brand fashion lokal stagnan di revenue Rp 80 juta per bulan selama hampir 3 tahun meskipun sudah bekerja 14 jam per hari. Founder melakukan audit digital dengan coach business: 73% waktunya dihabiskan untuk tugas yang seharusnya bisa dikerjakan admin atau asisten. Ia tidak pernah punya waktu untuk hal-hal yang hanya bisa dilakukan founder: relationship dengan supplier kunci, negosiasi channel distribusi baru, dan product innovation. Setelah ia menyerahkan operasional harian kepada tim yang sudah dibangun dengan SOP yang jelas — dalam 8 bulan, revenue mencapai Rp 310 juta per bulan. Bukan karena ia bekerja lebih keras. Tapi karena ia akhirnya bekerja **sebagai CEO, bukan sebagai staff pertama perusahaannya sendiri**.
[/CASE_STUDY]

[INFOGRAPHIC:Evolusi Leadership yang Harus Ditempuh]
Operator | "Saya harus kerjakan ini sendiri agar benar" — bottleneck personal, output terbatas waktu
Manager | "Saya koordinasikan orang lain untuk kerjakan ini" — leverage manusia, tapi masih reaktif
Leader | "Saya bangun sistem agar ini terjadi secara konsisten" — leverage sistem, scalable
Visionary | "Saya tentukan ke mana dan mengapa — tim tahu bagaimana caranya" — leverage culture dan direction
[/INFOGRAPHIC]

[QUIZ:Apa indikator paling jelas bahwa seorang founder masih dalam mode Operator, bukan CEO?|Memiliki visi jangka panjang yang jelas dan terdokumentasi untuk bisnis|Hampir semua task dan keputusan — bahkan yang kecil — harus melalui atau dicek oleh founder|Secara aktif mendelegasikan tugas kepada tim dan mempercayai hasilnya|Fokus pada strategi pertumbuhan jangka panjang, bukan detail operasional harian|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Tulis **10 task terakhir yang kamu kerjakan** dalam 2 hari ini. Untuk setiap task, jujur jawab: apakah ini sesuatu yang **hanya kamu yang bisa lakukan sebagai founder?** Tandai semua yang bisa dikerjakan orang lain dengan SOP yang tepat. Persentase yang ditandai = persentase waktu yang sedang dicuri Founder Trap darimu setiap hari.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Hiring Timing Framework',
                'order' => 2, 'xp' => 75, 'min' => 11,
                'content' => <<<MD
**Kapan waktu yang benar untuk merekrut?** — hiring yang terlalu awal menguras cashflow untuk output yang belum dibutuhkan. Hiring yang terlalu terlambat membakar founder dengan tugas operasional yang seharusnya sudah terdelegasi. Keduanya mahal. Timing hiring yang benar bukan intuisi — ia mengikuti **tiga kriteria yang bisa dianalisis secara rasional**.

**Jangan hire karena capek. Hire karena bottleneck sistem.**

[CASE_STUDY]
**Observasi Lapangan: Admin yang Menggandakan Omzet**
Seller Shopee dengan produk hijab mengerjakan semua sendiri: packing, upload foto, buat konten, balas chat, dan kelola iklan. Sudah bisa menangani 30-40 order per hari tapi merasa selalu overwhelmed. Saat order mulai konsisten di atas 50 per hari, ia merekrut admin part-time untuk handle packing dan chat. Temuannya mengejutkan: setelah 2 minggu dengan admin, waktu bebasnya meningkat 4 jam per hari — dan seluruh 4 jam itu ia investasikan ke konten dan strategi iklan. Omzet naik dari Rp 45 juta ke Rp 97 juta dalam 2 bulan berikutnya. Bukan karena admin yang ajaib, tapi karena founder akhirnya punya waktu untuk melakukan hal-hal high-leverage yang selama ini tertunda.
[/CASE_STUDY]

[INFOGRAPHIC:3 Kriteria Hiring yang Benar]
Repeatable | Task ini terjadi berulang dengan pola yang konsisten — bisa didokumentasikan sebagai SOP
Revenue Impact | Memindahkan task ini ke orang lain akan langsung atau tidak langsung meningkatkan revenue
Documentable | Ada standar jelas tentang apa artinya "selesai dengan benar" untuk task ini
Semua 3 | Jika task memenuhi ketiga kriteria — itu adalah kandidat delegasi atau hire pertama
[/INFOGRAPHIC]

[QUIZ:Berdasarkan Hiring Timing Framework, kapan waktu yang tepat untuk merekrut karyawan pertama?|Ketika founder merasa lelah dan tidak bisa bekerja dengan efektif|Ketika ada bottleneck tugas yang repeatable, memiliki revenue impact, dan bisa didokumentasikan|Ketika revenue sudah cukup besar dan ada dana berlebih untuk gaji tambahan|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Identifikasi **bottleneck terbesar** di bisnismu saat ini menggunakan 3 kriteria di atas: apakah ada task yang repeatable, memiliki revenue impact, dan bisa didokumentasikan? Jika ada — itulah role pertama yang harus kamu hire atau delegasikan, bukan role yang kamu inginkan atau yang terlihat paling keren.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'A Player vs Warm Body Hiring',
                'order' => 3, 'xp' => 75, 'min' => 10,
                'content' => <<<MD
**Mengapa kualitas talent menentukan segalanya** — ada mitos yang berbahaya di kalangan pengusaha UMKM: "hire yang murah dulu, nanti kalau bisnis sudah besar baru upgrade." Ini adalah logika yang terdengar hemat tapi secara matematis sangat mahal. Perbedaan output antara **A Player** (talent terbaik) dengan **B Player** (talent rata-rata) bukan 20% atau 50% — dalam banyak penelitian, nilainya **2 hingga 5 kali lebih tinggi**.

Dan A Player tidak selalu 5x lebih mahal dari B Player.

[CASE_STUDY]
**Observasi Lapangan: Biaya Tersembunyi dari Hiring Murah**
Toko online merekrut admin dengan gaji Rp 1.5 juta per bulan (di bawah UMR) karena ingin hemat. Dalam 6 bulan: 34 kesalahan packing yang menghasilkan komplain dan refund (estimasi kerugian Rp 8.7 juta), turnover 3x sehingga training cost berulang (waktu founder terbuang 60+ jam), dan customer rating turun karena respons CS yang lambat dan tidak profesional (estimasi revenue loss dari rating drop: Rp 15 juta). Total "penghematan" gaji 6 bulan: Rp 9 juta. Total biaya tersembunyi: Rp 23.7 juta. Di bulan ke-7, ia merekrut ulang dengan standar lebih tinggi dan gaji Rp 3 juta — dan tidak mengalami satu masalah pun dari kategori di atas selama 12 bulan berikutnya.
[/CASE_STUDY]

[INFOGRAPHIC:True Cost of Hiring Spectrum]
Warm Body Hire | Gaji rendah, standar rendah → error tinggi, training berulang, turnover tinggi, hidden loss besar
Average Hire | Gaji standar, skill cukup → output predictable, mistake occasional, retention moderate
A Player Hire | Gaji kompetitif, standar tinggi → output 2-5x, error minimal, improvement proaktif, retention tinggi
ROI Reality | Total cost of A Player hire hampir selalu lebih rendah dari total cost of multiple bad hires
[/INFOGRAPHIC]

[QUIZ:Mengapa A Player hire hampir selalu lebih cost-effective dibanding multiple B Player hire?|Karena A Player bersedia bekerja dengan gaji yang lebih rendah karena bangga dengan skillnya|Karena perbedaan output, error rate, turnover, dan hidden cost menjadikan total investment lebih rendah|Karena platform rekrutmen memberikan subsidi untuk bisnis yang memprioritaskan kualitas kandidat|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **standar minimal skill** untuk role berikutnya yang akan kamu hire. Definisikan secara konkret: apa saja skill non-negotiable yang harus dimiliki? Apa satu tugas yang bisa kamu berikan di interview untuk membuktikan skill itu? Standar ini akan melindungimu dari Warm Body Hire — bahkan saat tekanan untuk hire cepat sangat besar.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Role Clarity Architecture',
                'order' => 4, 'xp' => 70, 'min' => 10,
                'content' => <<<MD
**Merancang Job Description yang benar-benar berfungsi** — sebagian besar masalah performa karyawan bukan karena orang yang salah — tapi karena **role yang tidak pernah didefinisikan dengan jelas**. JD yang sekadar berisi daftar tugas menciptakan ambiguitas: apa artinya berhasil dalam role ini? Apa yang boleh diputuskan sendiri dan apa yang harus eskalasi? Bagaimana kinerja diukur?

Ketidakjelasan adalah sumber konflik terbesar dalam tim kecil yang sedang tumbuh.

[CASE_STUDY]
**Observasi Lapangan: JD yang Menghilangkan Konflik Tim**
Startup dengan 8 orang mengalami gesekan internal terus-menerus: siapa yang responsible untuk apa tidak jelas, keputusan kecil sering macet karena semua menunggu founder, dan evaluasi kinerja selalu terasa subjektif dan menimbulkan rasa tidak adil. Setelah melakukan Role Clarity Workshop: setiap role didefinisikan ulang dengan 4 komponen (purpose, KPI, tanggung jawab, authority limit). Dalam 6 minggu: keputusan day-to-day tidak lagi menunggu founder, gesekan internal berkurang drastis, dan evaluasi bulanan berjalan dengan data — bukan opini. Sama persis orangnya — role yang berbeda.
[/CASE_STUDY]

[INFOGRAPHIC:4 Komponen Role Clarity Architecture]
Role Purpose | Dalam satu kalimat: mengapa role ini ada dan apa kontribusi utamanya ke bisnis?
KPIs | 3-5 metrik terukur yang mendefinisikan sukses — bukan aktivitas, tapi output
Responsibilities | Apa yang dikerjakan — spesifik dan dapat diverifikasi, bukan deskripsi umum
Authority Limit | Apa yang bisa diputuskan sendiri tanpa persetujuan atasan — clarity here = speed
[/INFOGRAPHIC]

[QUIZ:Apa konsekuensi utama dari Job Description yang tidak mencantumkan KPI yang terukur?|Kandidat yang melamar akan memiliki ekspektasi gaji yang tidak realistis|Ambiguitas performa — karyawan tidak tahu apa artinya berhasil, dan evaluasi menjadi subjektif|Role akan diisi oleh kandidat yang tidak memenuhi kualifikasi teknis yang dibutuhkan|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Pilih satu role yang sudah ada di timmu atau yang akan kamu hire. Tulis ulang deskripsinya menggunakan **4 komponen Role Clarity Architecture**: purpose satu kalimat, 3-5 KPI terukur, responsibilities yang spesifik, dan authority limit yang jelas. Bandingkan dengan JD lama jika ada — berapa jauh perbedaannya?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Delegation System Design',
                'order' => 5, 'xp' => 75, 'min' => 11,
                'content' => <<<MD
**Delegasi bukan melempar pekerjaan — ini adalah sistem kompetisi** — founder yang sudah pernah mencoba mendelegasikan dan hasilnya mengecewakan biasanya menarik kesimpulan yang salah: "orang-orang saya tidak bisa dipercaya." Kesimpulan yang lebih akurat hampir selalu: **sistem delegasinya yang tidak ada**. Mereka skip dari "ini tugasnya" langsung ke "sekarang kerjakan" — tanpa jembatan pemahaman di tengahnya.

Framework yang benar: **Explain → Demonstrate → Observe → Release**.

[CASE_STUDY]
**Observasi Lapangan: Tahap Observe yang Sering Dilewati**
Founder agency digital mendelegasikan pembuatan laporan klien kepada anggota tim baru. Ia menjelaskan (Explain) apa yang dibutuhkan dalam 5 menit, mencontohkan (Demonstrate) satu laporan lama. Kemudian langsung Release tanpa pernah Observe. Laporan pertama yang dikerjakan sendiri oleh tim: formatnya berbeda, beberapa metrik hilang, dan interpretasi data tidak akurat. Founder frustrasi dan mendelegasikan balik ke dirinya sendiri. Root cause: ia melewati tahap Observe dimana ia seharusnya mengawasi proses pembuatan pertama dan memberikan feedback real-time. Setelah memahami framework, ia mengulang dengan benar — dan dalam 3 delegasi berikutnya, standar terpenuhi.
[/CASE_STUDY]

[INFOGRAPHIC:Delegation Framework — 4 Tahap]
Explain | Jelaskan konteks, tujuan, standar hasil, dan kenapa task ini penting — bukan hanya instruksi teknis
Demonstrate | Tunjukkan bagaimana dikerjakan dengan benar — satu kali, secara langsung atau via rekaman
Observe | Awasi pengerjaan pertama secara aktif — intervene hanya ketika ada bahaya, bukan setiap langkah
Release | Serahkan kepemilikan penuh dengan checkpoint yang sudah disepakati — bukan kontrol melekat
[/INFOGRAPHIC]

[QUIZ:Mengapa tahap Observe dalam delegation framework sering dianggap sebagai tahap yang paling kritis?|Karena pada tahap ini founder masih mengerjakan tugas bersama sehingga kualitas terjaga|Karena ini adalah satu-satunya kesempatan untuk mengidentifikasi gap pemahaman sebelum release penuh|Karena platform manajemen tugas digital hanya bisa tracking tahap ini dengan akurat|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Pilih **satu task operasional** yang akan kamu delegasikan minggu ini. Eksekusi menggunakan 4 tahap framework: jadwalkan waktu untuk Explain (15 menit), sediakan rekaman atau contoh untuk Demonstrate, blokir waktu untuk Observe di hari pertama pengerjaan, dan tentukan kapan checkpoint untuk Release penuh.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'KPI & Performance Tracking System',
                'order' => 6, 'xp' => 70, 'min' => 10,
                'content' => <<<MD
**Mengukur kinerja tim dengan data, bukan opini** — evaluasi kinerja yang didasarkan pada perasaan atau kesan adalah sumber konflik paling produktif dalam tim kecil. "Karyawan ini sepertinya kurang usaha" vs "Karyawan ini menyelesaikan rata-rata 47 tiket per hari dibandingkan standar 55" — mana yang lebih adil, lebih akurat, dan lebih bisa ditindaklanjuti?

**Tanpa KPI = opini. Dengan KPI = data.** Dan data memungkinkan percakapan yang produktif, bukan defensif.

[CASE_STUDY]
**Observasi Lapangan: KPI yang Mengubah Evaluasi Bulanan**
Tim CS 4 orang untuk marketplace fashion. Evaluasi bulanan sebelumnya: "siapa yang paling rajin" berdasarkan kehadiran dan kesan founder. Setelah implementasi KPI: response_time (target < 2 jam), resolution_rate (target > 85%), customer_rating (target > 4.5), dan volume_handled per hari. Temuan mengejutkan: karyawan yang selalu terlihat "paling sibuk" ternyata memiliki resolution_rate terendah (61%) karena menangani tiket berulang yang sama tanpa menyelesaikannya. Karyawan yang terlihat "santai" memiliki resolution_rate 94% karena efisien. KPI mengungkap realitas yang tidak terlihat oleh observasi subjektif.
[/CASE_STUDY]

[INFOGRAPHIC:Kriteria KPI yang Benar-Benar Berguna]
Measurable | Bisa dihitung dengan angka — bukan "memberikan pelayanan yang baik"
Relevant | Terhubung langsung dengan output bisnis yang sesungguhnya — bukan aktivitas palsu
Time-bound | Ada periode pengukuran yang jelas — per hari, per minggu, atau per bulan
Actionable | Jika KPI tidak tercapai, orang tahu apa yang perlu diperbaiki
[/INFOGRAPHIC]

[QUIZ:KPI yang tidak dapat diukur secara kuantitatif memiliki kelemahan utama berupa?|Biaya implementasi sistem tracking yang terlalu tinggi untuk bisnis skala kecil|Tidak bisa dijadikan dasar evaluasi yang objektif — interpretasinya bergantung pada opini subjektif|Sulitnya mengkomunikasikan target kepada karyawan yang tidak terbiasa dengan data|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Pilih satu role di timmu. Tentukan **3-5 KPI** menggunakan 4 kriteria di atas — Measurable, Relevant, Time-bound, Actionable. Presentasikan KPI ini kepada orang yang memegang role itu dan tanyakan: apakah kamu setuju ini adalah ukuran yang adil untuk mengevaluasi kerja kamu? Feedback mereka akan menyempurnakan sistem-mu.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Culture Engineering',
                'order' => 7, 'xp' => 70, 'min' => 10,
                'content' => <<<MD
**Budaya kerja bukan slogan di dinding kantor — budaya adalah perilaku yang kamu toleransi dan yang kamu reward** — ini adalah definisi operasional budaya yang paling penting untuk dipahami oleh setiap founder. Tidak peduli berapa bagus values statement yang kamu tulis, tidak peduli seberapa inspiratif all-hands meeting-mu — **budaya nyata sebuah organisasi adalah sekumpulan perilaku yang secara konsisten di-reward atau tidak dihukum**.

Jika kamu toleransi keterlambatan → budaya keterlambatan terbentuk, apa pun yang tertulis di wall of values-mu.

[CASE_STUDY]
**Observasi Lapangan: Satu Tindakan yang Mengubah Culture**
Founder agency digital berulang kali menekankan nilai "proaktif dan berani berinisiatif" dalam meeting. Tapi setiap kali anggota tim mengambil inisiatif di luar yang diminta dan hasilnya tidak sempurna, founder memberikan feedback yang kritis dan kadang dalam tone yang membuat orang merasa "harusnya tidak berinisiatif." Dalam 4 bulan, tim menjadi sangat pasif — hanya mengerjakan yang diminta, tidak lebih. Nilai "proaktif" masih ada di dinding, tapi budaya nyatanya adalah "tunggu instruksi." Ketika founder menyadari ini dan secara eksplisit mulai merayakan inisiatif — bahkan yang gagal — budaya mulai berubah dalam 6 minggu.
[/CASE_STUDY]

[INFOGRAPHIC:Formula Culture Engineering]
Identify | Tentukan 5 perilaku konkret yang ingin menjadi norma tim — bukan nilai abstrak, tapi perilaku spesifik
Model | Founder harus melakukan perilaku itu duluan — tim mengikuti apa yang dilakukan, bukan dikatakan
Reward | Secara eksplisit dan publik acknowledge ketika perilaku itu muncul — apa yang di-reward akan terulang
Remove | Tindak cepat ketika perilaku berlawanan muncul — diam adalah persetujuan
[/INFOGRAPHIC]

[QUIZ:Bagaimana budaya kerja yang sesungguhnya terbentuk dalam sebuah organisasi menurut prinsip Culture Engineering?|Melalui dokumen values dan misi yang dikomunikasikan secara reguler dalam rapat seluruh tim|Melalui perilaku yang secara konsisten di-reward dan yang secara konsisten tidak mendapat konsekuensi|Melalui rekrutmen kandidat yang sudah memiliki values yang sesuai dengan visi perusahaan|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Tuliskan **5 perilaku konkret** yang ingin kamu jadikan norma di timmu — bukan nilai abstrak ("integritas"), tapi tindakan spesifik ("memberitahu atasan jika deadline tidak bisa dipenuhi minimal 24 jam sebelumnya"). Kemudian tanya dirimu: apakah aku sendiri selalu melakukan kelima perilaku ini? Culture dimulai dari founder.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Communication System Design',
                'order' => 8, 'xp' => 65, 'min' => 9,
                'content' => <<<MD
**Struktur komunikasi yang mencegah miskomunikasi sebelum terjadi** — riset konsisten menunjukkan bahwa **80% masalah dalam tim kecil berakar dari miskomunikasi, bukan skill gap**. Bukan orang-orangnya yang tidak capable — tapi channel, frekuensi, dan format komunikasi yang tidak terstruktur sehingga informasi penting hilang, disalahartikan, atau tidak sampai ke orang yang tepat.

Solusinya bukan "lebih banyak meeting" — solusinya adalah **struktur komunikasi yang purposeful dan efisien**.

[CASE_STUDY]
**Observasi Lapangan: Dari Meeting Chaos ke Async-First**
Tim 12 orang menghabiskan rata-rata 4 jam per hari untuk meeting — sebagian besar dihadiri oleh orang yang tidak perlu ada di sana, dan sebagian besar isinya bisa ditulis dalam dokumen 5 menit. Setelah implementasi struktur komunikasi: Daily update melalui dokumen async (5 menit tulis, baca kapanpun); Weekly 45-menit untuk alignment strategis; Monthly 2 jam review KPI; Quarterly half-day planning. Meeting hanya untuk keputusan yang benar-benar perlu diskusi real-time. Hasil: meeting time turun dari 4 jam ke 1.2 jam per hari. Kepuasan tim naik. Kecepatan eksekusi meningkat karena lebih sedikit waktu yang dihabiskan untuk koordinasi.
[/CASE_STUDY]

[INFOGRAPHIC:Communication Cadence Framework]
Daily | Tactical update — apa yang dikerjakan, apa yang blocked? Async document, bukan meeting
Weekly | Strategy alignment — progress vs target, issue yang perlu keputusan, prioritas minggu depan
Monthly | Performance review — KPI actual vs target, learning, dan adjustment
Quarterly | Vision refresh — arah bisnis, OKR berikutnya, dan celebration wins
[/INFOGRAPHIC]

[QUIZ:Apa penyebab utama dari sebagian besar masalah yang terjadi dalam tim yang sedang berkembang?|Kurangnya skill teknis dan kompetensi dari anggota tim yang direkrut|Miskomunikasi yang berasal dari channel informasi yang tidak terstruktur|Perbedaan motivasi dan ambisi antara founder dan anggota tim|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Desain **Communication Cadence** untuk timmu: tentukan format daily update (async doc? standup 15 menit?), jadwal weekly alignment, struktur monthly review, dan kapan quarterly planning. Implementasikan mulai minggu depan dan komit untuk 60 hari — di mana setelah itu kamu evaluasi apakah efektif.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Leadership Decision Matrix',
                'order' => 9, 'xp' => 75, 'min' => 11,
                'content' => <<<MD
**Cara pengambilan keputusan cepat yang tidak mengorbankan kualitas** — di level expert, jumlah keputusan yang harus diambil setiap hari meningkat secara eksponensial seiring pertumbuhan tim dan bisnis. Tanpa framework yang jelas, setiap keputusan menghabiskan energi yang sama — padahal dampaknya sangat berbeda. Keputusan yang harusnya diselesaikan dalam 30 detik bisa menghabiskan 2 jam rapat. Keputusan yang seharusnya dianalisis dalam 2 minggu malah diputuskan dalam 5 menit karena tekanan waktu.

**Decision Matrix** adalah alat untuk mengalokasikan energi pengambilan keputusan secara proporsional dengan dampaknya.

[CASE_STUDY]
**Observasi Lapangan: Keputusan yang Menguras Energi Salah**
Founder menghabiskan 2 jam mempertimbangkan desain thumbnail konten media sosial (impact rendah, bisa diubah kapan saja) tapi hanya 20 menit mempertimbangkan kontrak lease kantor 2 tahun (impact sangat tinggi, tidak bisa diubah). Ketidakseimbangan energi pengambilan keputusan ini sangat umum terjadi karena keputusan kecil terasa lebih familiar dan nyaman dibanding keputusan besar yang penuh ketidakpastian. Decision Matrix memaksa kamu mengakui perbedaan impact dan urgency secara eksplisit — sehingga energi yang tepat diarahkan ke keputusan yang tepat.
[/CASE_STUDY]

[INFOGRAPHIC:Leadership Decision Matrix]
Impact Tinggi + Urgent | Putuskan langsung dengan informasi yang tersedia — jangan tunda demi kesempurnaan
Impact Tinggi + Tidak Urgent | Analisis mendalam — kumpulkan data, konsultasi, proteksi dari keputusan terburu
Impact Rendah + Urgent | Delegasikan segera — ini bukan penggunaan waktu terbaik seorang leader
Impact Rendah + Tidak Urgent | Delegasikan atau hapus dari daftar — tidak perlu energimu sama sekali
[/INFOGRAPHIC]

[QUIZ:Berdasarkan Leadership Decision Matrix, keputusan dengan dampak tinggi tapi tidak mendesak seharusnya ditangani dengan cara apa?|Diputuskan sesegera mungkin agar tidak mengganggu fokus pada prioritas yang lebih urgent|Dianalisis secara mendalam dengan cukup waktu dan data sebelum keputusan akhir diambil|Didelegasikan kepada anggota senior tim yang memiliki kapasitas untuk menanganinya|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Ambil **5 keputusan terbesar yang kamu buat dalam 30 hari terakhir** — apakah finansial, operasional, tim, atau strategis. Kategorikan masing-masing ke dalam 4 kuadran Decision Matrix. Berapa keputusan impact tinggi yang kamu putuskan terlalu cepat? Berapa keputusan impact rendah yang menghabiskan terlalu banyak energimu?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Founder Exit Readiness',
                'order' => 10, 'xp' => 90, 'min' => 12,
                'content' => <<<MD
**Bisnis yang benar-benar sehat adalah bisnis yang bisa berjalan tanpa kehadiranmu setiap hari** — ini bukan tentang meninggalkan bisnis. Ini tentang **membangun bisnis yang nilainya tidak bergantung pada satu orang**. Bisnis yang bergantung penuh pada pemiliknya untuk beroperasi bukan aset — ia adalah pekerjaan dengan biaya overhead yang sangat tinggi. Investor tidak mau membelinya. Acquirer tidak mau mengakuisisinya. Dan kamu tidak pernah bisa benar-benar istirahat.

Exit Readiness Checklist adalah ukuran kematangan operasional bisnis — bukan hanya persiapan untuk menjual bisnis.

[CASE_STUDY]
**Observasi Lapangan: Uji 3 Minggu Tanpa Handphone**
Founder bisnis produk digital memutuskan untuk melakukan "stress test" paling ekstrem: ia pergi liburan 3 minggu ke pulau tanpa internet dan hanya bisa dihubungi untuk darurat level kritis. Sebelum pergi, ia memverifikasi 4 checklist: semua proses terdokumentasi dalam SOP yang bisa diakses tim, ada manager layer yang memiliki authority untuk keputusan rutin, dashboard KPI diupdate otomatis dan bisa dipantau tim, cashflow stabil dengan reserve 3 bulan. Hasil setelah 3 minggu: tidak ada krisis. Revenue bulan itu naik 8% karena tim yang lebih autonomous mengambil keputusan lebih cepat tanpa menunggu approval. Founder kembali dengan energi yang dipulihkan penuh dan perspektif strategis yang jauh lebih jernih.
[/CASE_STUDY]

[INFOGRAPHIC:Exit Readiness Checklist — 4 Pilar]
SOP Terdokumentasi | Setiap proses kritis terdokumentasi dan bisa dieksekusi oleh orang lain tanpa bertanya
Manager Layer | Ada minimal satu orang yang bisa membuat keputusan operasional rutin tanpa founder
KPI Dashboard Live | Performa bisnis bisa dipantau real-time oleh siapapun — bukan hanya dari kepala founder
Cashflow Stabil | Minimal 3-6 bulan runway tanpa revenue baru — bisnis tidak kolaps jika ada gangguan
[/INFOGRAPHIC]

[QUIZ:Apa tanda utama bahwa sebuah bisnis sudah mencapai tahap Exit Readiness atau founder-independent?|Bisnis sudah memiliki revenue yang sangat besar dan tidak membutuhkan investasi lebih|Bisnis dapat beroperasi secara konsisten dan menghasilkan revenue bahkan ketika founder tidak hadir|Bisnis sudah memiliki rencana untuk dijual kepada investor atau acquirer dalam waktu dekat|1]

Selamat menyelesaikan modul **Membangun Tim & Sistem** — level Expert! Kamu telah menyelesaikan perjalanan dari Operator ke Visionary. Satu langkah terakhir:

[CHALLENGE]
**Action Plan Hari Ini — Final Expert Challenge:**
Evaluasi keempat pilar Exit Readiness bisnismu sekarang. Untuk setiap pilar, beri skor 0-10 jujur: (1) SOP terdokumentasi, (2) Manager layer ada, (3) KPI dashboard live, (4) Cashflow stabil. Total skor-mu menunjukkan seberapa siap bisnismu untuk scale tanpa bergantung pada kehadiranmu setiap hari. Pilar dengan skor terendah adalah prioritas 90 hari ke depan.
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

        $this->command->info('✅ Membangun Tim & Sistem — Hiring & Leadership Architecture (Expert) — 10 lessons berhasil dimasukkan ke database.');
    }
}
