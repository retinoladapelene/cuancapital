<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class OperationalEfficiencySeed extends Seeder
{
    public function run(): void
    {
        Course::where('title', 'Operational Efficiency & Automation')->delete();

        $course = Course::create([
            'title'         => 'Operational Efficiency & Automation',
            'description'   => 'Level advanced — bangun sistem operasional yang scalable: dari bottleneck detection, SOP architecture, automation layering, hingga self-running business model.',
            'level'         => 'advanced',
            'category'      => 'operations',
            'xp_reward'     => 750,
            'lessons_count' => 10,
            'is_active'     => true,
        ]);

        $lessons = [

            [
                'title' => 'Business as a System',
                'order' => 1, 'xp' => 55, 'min' => 9,
                'content' => <<<MD
**Bisnis bukan kerja manual — tapi sistem** — perbedaan terbesar antara entrepreneur yang kehabisan energi dengan yang berhasil membangun kerajaan bisnis bukan terletak pada kecerdasan atau kerja keras. Perbedaannya ada pada satu pertanyaan: *"Apakah bisnis ini bisa berjalan jika saya tidak ada selama 2 minggu?"*

Bisnis yang scalable dirancang — bukan dijalani. Owner yang baik adalah arsitek sistem, bukan tulang punggung operasional.

[CASE_STUDY]
**Observasi Lapangan: Dari Operator ke Builder**
Seorang pemilik toko online mengerjakan semua sendiri: packing, CS, posting konten, cek keuangan, dan iklan. Ia bekerja 14 jam sehari, revenue stagnan di Rp 25 juta per bulan karena kapasitasnya sudah mencapai batas. Setelah mendokumentasikan semua proses ke dalam SOP, melatih 2 asisten, dan mengotomatisasi 4 alur kerja — ia turun ke 4 jam kerja per hari. Revenue naik ke Rp 78 juta per bulan dalam 5 bulan. Bukan karena kerja lebih keras. Tapi karena berhenti menjadi operator dan mulai menjadi builder sistem.
[/CASE_STUDY]

[INFOGRAPHIC:Evolusi Peran Entrepreneur]
Operator | Mengerjakan semua sendiri — output terbatas oleh waktu dan energi personal
Manager | Mendelegasikan kerja ke orang — output terbatas oleh kemampuan koordinasi
Builder | Membangun sistem yang berjalan — output tidak terbatas oleh keterlibatan langsung
Owner | Memiliki sistem yang menghasilkan — bebas untuk berinovasi ke level berikutnya
[/INFOGRAPHIC]

[QUIZ:Karakteristik utama bisnis yang scalable adalah?|Pemilik yang bekerja sangat keras dan selalu hadir dalam setiap operasional|Sistem yang bisa berjalan secara konsisten tanpa bergantung pada intervensi langsung pemilik|Produk yang viral dan terus mendapatkan perhatian di media sosial secara organik|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Identifikasi **3 proses bisnis yang masih sepenuhnya manual dan bergantung pada kamu**: packing/fulfillment, CS, pembuatan konten, pembukuan, atau monitoring iklan. Untuk setiap proses, nilai: apakah bisa dibuat template? Apakah bisa didokumentasikan? Apakah bisa diotomasi? Ini adalah peta jalan systemisasi bisnismu.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Bottleneck Detection',
                'order' => 2, 'xp' => 55, 'min' => 9,
                'content' => <<<MD
**Menemukan dan mengeliminasi titik hambatan operasional** — bottleneck adalah bagian dari proses bisnis yang paling lambat — dan ia menentukan kecepatan seluruh sistem, bukan bagian yang tercepat. Tidak peduli seberapa efisien tahap lainnya, jika satu tahap lambat, seluruh alur terhenti menunggunya.

Ini adalah **Theory of Constraints** — hukum fundamental dalam manajemen operasional.

[CASE_STUDY]
**Observasi Lapangan: Bottleneck yang Tersembunyi di QC**
Sebuah bisnis hamper Lebaran mampu menerima 200 order per hari. Tim packing bisa memproses 180 unit/hari. Tim pengiriman bisa menangani 250 unit/hari. Tapi ada satu QC person yang hanya bisa memeriksa 80 unit/hari — dan setiap order harus melalui QC. Kapasitas efektif bisnis: 80 unit/hari — bukan 200. Mereka melatih satu orang QC tambahan. Output naik ke 160 unit/hari tanpa mengubah proses lain. Investasi: gaji satu orang part-time. Return: kapasitas revenue doubled.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Bottleneck Detection]
Mapping Flow | Gambar alur proses end-to-end — dari order masuk sampai produk diterima customer
Timing Each Step | Ukur waktu rata-rata setiap tahap — data, bukan asumsi
Identify Slowest | Tahap yang paling lambat = bottleneck utama — fokuskan optimasi di sini
Elevate the Constraint | Tambah kapasitas atau otomasi di tahap bottleneck sebelum mempercepat tahap lain
[/INFOGRAPHIC]

[QUIZ:Mengapa dalam Theory of Constraints, mempercepat proses yang sudah cepat tidak akan meningkatkan output sistem secara keseluruhan?|Karena sistem selalu mencari keseimbangan dan akan memperlambat proses cepat secara otomatis|Karena kecepatan keseluruhan sistem ditentukan oleh bagian yang paling lambat, bukan yang tercepat|Karena mempercepat proses cepat akan meningkatkan biaya operasional tanpa manfaat tambahan|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Audit alur kerja **dari order masuk hingga produk diterima customer** — catat setiap tahap dan waktunya. Temukan tahap yang paling sering menjadi penyebab keterlambatan atau penumpukan. Itu adalah bottleneck prioritas yang harus diselesaikan sebelum mengoptimasi apapun yang lain.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'SOP Architecture',
                'order' => 3, 'xp' => 50, 'min' => 8,
                'content' => <<<MD
**Struktur SOP profesional yang benar-benar digunakan** — kebanyakan SOP gagal bukan karena tidak dibuat, tapi karena dibuat tapi tidak bisa dieksekusi. SOP yang terlalu panjang tidak dibaca. SOP yang terlalu abstrak tidak bisa diikuti. SOP yang baik adalah dokumen yang bisa digunakan oleh orang baru untuk mengerjakan tugas dengan standar yang konsisten — tanpa harus bertanya kepada siapapun.

Empat komponen wajib SOP yang efektif: **langkah terurut yang jelas, standar hasil yang terukur, waktu target per langkah, dan checkpoint quality control**.

[CASE_STUDY]
**Observasi Lapangan: SOP Video 5 Menit yang Menggantikan 2 Jam Training**
Toko online dengan tingkat turnover staff tinggi mengalami masalah konsistensi — setiap karyawan baru membutuhkan 2-3 minggu training satu-satu. Mereka membuat SOP untuk proses packing dalam format: video screen record 5 menit + checklist 12 poin + standar hasil dengan foto referensi + waktu target per item. Karyawan baru bisa memenuhi standar dalam 2 hari kerja pertama. Training cost turun 90%. Error rate di packing turun 74%.
[/CASE_STUDY]

[INFOGRAPHIC:Anatomi SOP yang Dieksekusi, Bukan Diabaikan]
Format | Video pendek + checklist tertulis — tidak ada manusia yang mau membaca dokumen Word 20 halaman
Langkah | Berurutan, spesifik, tidak ambigu — "klik tombol merah di pojok kanan atas" bukan "simpan data"
Standar Hasil | Foto atau contoh output yang benar — bukan deskripsi verbal yang bisa diinterpretasi beda
Waktu Target | Berapa menit setiap langkah harus selesai — benchmark untuk efisiensi dan training
[/INFOGRAPHIC]

[QUIZ:Fungsi utama SOP dalam operasional bisnis yang scalable adalah?|Dokumen formal untuk keperluan audit eksternal dan compliance regulasi|Memastikan setiap proses dikerjakan secara konsisten sesuai standar tanpa bergantung pada pengetahuan satu orang|Melindungi bisnis secara legal jika terjadi sengketa dengan karyawan atau supplier|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Pilih **satu proses yang paling sering dilakukan** di bisnismu — misalnya proses packing, respon CS, atau posting konten. Buat SOP-nya dalam format: rekam screen atau proses dalam video ≤ 5 menit, buat checklist ≤ 15 poin, dan tentukan standar hasil yang bisa diverifikasi. Test: bisakah orang yang belum pernah melakukan ini mengikuti SOP-mu tanpa bertanya?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Automation Layering',
                'order' => 4, 'xp' => 60, 'min' => 10,
                'content' => <<<MD
**Level otomatisasi bisnis yang bisa diterapkan secara bertahap** — otomatisasi bukan binary antara "manual sepenuhnya" atau "AI mengerjakan segalanya". Ia adalah **spektrum** dengan 5 level yang bisa dinaiki secara bertahap sesuai kapasitas dan kebutuhan bisnis. Kesalahan terbesar: mencoba lompat dari level 1 langsung ke level 5 tanpa fondasi yang kuat.

Setiap proses bisa diidentifikasi posisinya dalam spektrum — dan potensi kenaikan ke level berikutnya.

[CASE_STUDY]
**Observasi Lapangan: Automation Journey Toko Dropship**
Dimulai dari semua manual (L1). Bulan 1: buat template respons CS untuk 10 pertanyaan paling sering — waktu per tiket turun 70% (L2). Bulan 2: gunakan auto-reply WhatsApp Business untuk FAQ — 40% pertanyaan terjawab tanpa intervensi manusia (L3). Bulan 3: integrasi toko dengan sistem warehouse third-party — tidak perlu update stok manual (L4). Bulan 6: full automation order → konfirmasi → fulfillment → tracking tanpa sentuhan manual (L5). Total waktu yang dibebaskan: 6 jam per hari — diinvestasikan ke business development.
[/CASE_STUDY]

[INFOGRAPHIC:5 Level Automation Spectrum]
L1 Manual | Semua dikerjakan manusia setiap kali — tidak scalable, error rate tinggi
L2 Template | Pola berulang dipercepat dengan template — friction berkurang, konsistensi meningkat
L3 Tool | Software yang menggantikan tindakan manual spesifik — WhatsApp Business, Canva, spreadsheet otomatis
L4 Integration | Sistem berbicara satu sama lain — data mengalir otomatis antar platform tanpa input manual
L5 Full Automation | Trigger → action tanpa intervensi manusia — Zapier, Make, n8n, atau custom API
[/INFOGRAPHIC]

[QUIZ:Mengapa disarankan untuk menaikkan level otomatisasi secara bertahap daripada langsung ke level tertinggi?|Karena platform automation membutuhkan waktu lama untuk setup dan konfigurasi awal yang kompleks|Karena fondasi proses yang belum terdokumentasi dengan baik akan membuat automation memreplikasi ketidakefisienan yang ada|Karena biaya subscription tool automation sangat mahal dan perlu dipertimbangkan dengan matang|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Pilih **satu proses yang saat ini di level L1 atau L2** dan identifikasi: apa yang dibutuhkan untuk naik ke level berikutnya? Apakah template? Apakah tool gratis yang sudah tersedia? Apakah koneksi antar sistem? Implementasikan kenaikan satu level itu hari ini atau dalam 48 jam.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Tool Stack Design',
                'order' => 5, 'xp' => 50, 'min' => 8,
                'content' => <<<MD
**Memilih dan menyusun tools operasional yang efektif** — lebih banyak tools tidak berarti lebih efisien. Setiap tools tambahan adalah: biaya subscription, kurva belajar baru, potensi overlap fungsi, dan satu lagi titik kegagalan potensial dalam sistem. Tool stack yang ideal adalah **minimal tapi cukup** — covering kebutuhan inti tanpa menciptakan fragmentasi.

Empat kategori tools yang setiap bisnis digital butuhkan: **CRM, project management, finance tracking, dan automation**.

[CASE_STUDY]
**Observasi Lapangan: Dari 14 Tools ke 5, Produktivitas Naik 40%**
Startup e-commerce menggunakan 14 tools berbeda yang sebagian besar fungsinya overlap: 3 tools untuk komunikasi, 2 untuk project management, 2 untuk desain, 3 untuk analytics, dan 4 lainnya yang "dicoba tapi tidak dihapus". Tim menghabiskan rata-rata 90 menit per hari hanya untuk berpindah antar tools dan mencari informasi yang tersebar. Audit dilakukan → konsolidasi ke 5 tools pilihan yang covers semua kebutuhan. Waktu koordinasi turun 62%. Biaya subscription turun 55%. Tidak ada fitur penting yang hilang.
[/CASE_STUDY]

[INFOGRAPHIC:Core Tool Stack untuk Bisnis Digital]
CRM | Kelola hubungan customer — track interaksi, pipeline, dan follow-up (HubSpot Free, Notion CRM)
Project Management | Koordinasi tim dan tracking progress (Notion, Trello, atau Linear)
Finance Tracker | Pembukuan dan cashflow monitoring (Wave, Jurnal.id, atau Google Sheets terstruktur)
Automation Hub | Koneksi antar tools dan trigger workflow (Zapier, Make, atau n8n self-hosted)
Communication | Satu channel utama — jangan sebar tim di 3 platform sekaligus
[/INFOGRAPHIC]

[QUIZ:Apa risiko utama dari menggunakan terlalu banyak tools dalam operasional bisnis?|Biaya yang terlalu tinggi karena setiap tools membutuhkan subscription berbayar|Fragmentasi informasi, overlap fungsi, dan waktu yang terbuang untuk berpindah antar platform|Kesulitan untuk mendapatkan dukungan teknis karena terlalu banyak vendor yang harus dihubungi|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **daftar semua tools** yang kamu gunakan saat ini — termasuk yang berbayar, gratis, dan yang "mungkin masih terpakai". Untuk setiap tools, kategorikan: (1) essential — tidak bisa tanpa ini, (2) nice-to-have — ada tapi tidak urgent, (3) zombie — tidak digunakan tapi belum dihapus. Cancel atau hapus semua zombie. Konsolidasi nice-to-have yang overlap.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Time vs Output Optimization',
                'order' => 6, 'xp' => 55, 'min' => 9,
                'content' => <<<MD
**Memaksimalkan output tanpa menambah jam kerja** — hustle culture mengajarkan bahwa success berbanding lurus dengan jam kerja. Ini adalah mitos yang melegitimasi inefisiensi. Entrepreneur paling produktif bukan yang paling lama bekerja — tapi yang memiliki **output per jam tertinggi**. Fokus bukan pada durasi, tapi pada *leverage*: setiap jam yang diinvestasikan menghasilkan dampak berapa kali lipat?

Formula: **Efisiensi = Output ÷ Waktu**.

[CASE_STUDY]
**Observasi Lapangan: 4-Hour Workday dengan Revenue 3x**
Seorang founder menerapkan audit radikal: tracking semua aktivitas selama 2 minggu dengan timer. Temuan: 34% waktu dihabiskan untuk meeting yang bisa digantikan dengan dokumen async, 21% untuk tasks yang bisa didelegasikan, 18% untuk distraksi digital. Setelah eliminasi, delegasi, dan deep work block 2 jam tanpa interupsi — jam kerja turun dari 10 jam ke 4 jam per hari. Output? Revenue naik 3x dalam 4 bulan berikutnya karena waktu yang tersisa dialokasikan ke high-leverage activities: strategi dan relationship building.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Output Optimization]
Time Audit | Track semua aktivitas 3-5 hari — tanpa asumsi, data nyata tentang ke mana waktu benar-benar pergi
Eliminate | Hapus aktivitas yang tidak berkontribusi langsung pada revenue atau growth — termasuk meeting tidak perlu
Delegate | Pindahkan semua yang bisa dikerjakan orang lain dengan SOP yang ada
Deep Work | Block 2-4 jam tanpa interupsi untuk high-leverage work yang hanya kamu yang bisa lakukan
[/INFOGRAPHIC]

[QUIZ:Apa yang dimaksud dengan meningkatkan efisiensi dalam konteks manajemen waktu entrepreneur?|Bekerja lebih banyak jam setiap hari untuk menyelesaikan lebih banyak pekerjaan|Meningkatkan jumlah output yang dihasilkan per unit waktu yang sama atau lebih sedikit|Mengurangi kualitas output agar proses bisa diselesaikan dalam waktu yang lebih singkat|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Hitung **output per jam nyata-mu**: berapa revenue atau deliverable yang kamu hasilkan hari ini? Bagi dengan total jam kerja aktif (bukan jam di meja). Berapa angkanya? Sekarang identifikasi: aktivitas mana hari ini yang paling berkontribusi pada angka itu? Fokuskan 80% jadwalmu ke sana mulai besok.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Delegation Engineering',
                'order' => 7, 'xp' => 55, 'min' => 9,
                'content' => <<<MD
**Seni mendelegasikan yang menghasilkan, bukan mengecewakan** — kebanyakan entrepreneur yang mencoba mendelegasikan mengalami kekecewaan: hasil tidak sesuai standar, harus dikerjakan ulang, akhirnya dikerjakan sendiri lagi. Ini bukan bukti bahwa delegasi tidak berhasil — ini adalah bukti bahwa **sistem delegasi-nya yang tidak ada**.

Delegasi gagal hampir selalu karena tiga hal: instruksi tidak jelas, standar tidak terdefinisi, dan mekanisme kontrol tidak ada — bukan karena orang yang menerima delegasi tidak kompeten.

[CASE_STUDY]
**Observasi Lapangan: Delegasi yang Memperbesar Tim 5x**
Founder mempekerjakan VA pertamanya tapi terus kecewa dengan hasil kerja — "tidak sesuai yang dimaksud." Masalahnya: briefing dilakukan secara verbal, tidak ada contoh output yang benar, dan tidak ada checkpoint di tengah proses. Setelah mengubah sistem: semua instruksi dalam dokumen tertulis dengan contoh output + standar kualitas + deadline per milestone + QC checkpoint di 50% dan 100% penyelesaian. Success rate delegasi naik dari 30% ke 87%. Tim berkembang dari 1 ke 5 orang dalam setahun tanpa penurunan standar output.
[/CASE_STUDY]

[INFOGRAPHIC:3 Komponen Delegasi yang Berhasil]
Clarity | SOP + contoh output yang benar + definisi "selesai" yang tidak ambigu — bukan "kamu tahu yang saya maksud"
Capability | Train dulu sebelum delegate — jangan delegate tugas yang orang belum punya skill untuk melakukannya
Control | Checkpoint di tengah proses — bukan supervisi konstan, tapi titik review yang sudah dijadwalkan
[/INFOGRAPHIC]

[QUIZ:Mengapa delegasi yang dilakukan tanpa sistem yang jelas hampir selalu menghasilkan output yang tidak memuaskan?|Karena kebanyakan orang tidak memiliki motivasi untuk bekerja sebaik yang dikerjakan pemilik bisnis|Karena tanpa instruksi jelas dan standar terukur, orang harus menebak apa yang diharapkan — dan tebakan sering salah|Karena delegasi hanya efektif untuk tugas-tugas sederhana yang tidak membutuhkan judgment|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Identifikasi **satu tugas operasional** yang masih kamu kerjakan sendiri padahal seharusnya bisa didelegasikan. Buat brief singkat: apa yang dikerjakan, output seperti apa yang diharapkan (dengan contoh), deadline, dan kapan checkpoint review. Delegasikan hari ini dengan brief itu — bukan secara verbal.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Error Reduction Framework',
                'order' => 8, 'xp' => 50, 'min' => 8,
                'content' => <<<MD
**Mengurangi kesalahan operasional secara sistematis** — ketika terjadi kesalahan berulang dalam operasional, reaksi paling umum adalah menyalahkan orang yang melakukannya. Ini adalah diagnosa yang salah — dan ia tidak menyelesaikan masalah, hanya menciptakan ketakutan. Dalam manajemen operasional modern, prinsipnya jelas: **jika error terjadi berulang, masalahnya ada di sistem, bukan orang**.

Orang yang baik di sistem yang buruk akan selalu menghasilkan output yang buruk.

[CASE_STUDY]
**Observasi Lapangan: Checklists yang Menghilangkan 94% Error**
Gudang fulfillment memiliki error rate packing 8.3% — hampir 1 dari 12 paket salah isi, salah ukuran, atau salah alamat. Manajemen awalnya menganggap ini masalah ketelitian staff. Investigasi mengungkap: tidak ada checklist sebelum seal paket, proses QC bergantung pada ingatan, dan area kerja tidak terorganisir dengan jelas. Setelah implementasi checklist 8 poin yang harus dicentang sebelum setiap paket diseal + reorganisasi area kerja + label warna untuk SKU berbeda: error rate turun ke 0.5% dalam 6 minggu. Staff yang sama — sistem yang berbeda.
[/CASE_STUDY]

[INFOGRAPHIC:Hierarki Error Prevention]
Eliminate | Hilangkan langkah yang menyebabkan error kemungkinannya — redesign proses
Automate | Biarkan sistem yang melakukan — tidak ada human touch, tidak ada human error
Checklist | Verifikasi wajib sebelum tahap kritis — pilot dan dokter berbuat ini, kamu juga bisa
Training | Pastikan semua orang memahami SOP — error dari ketidaktahuan bisa dieliminasi
Monitor | Tracking error rate secara berkala — apa yang diukur akan meningkat
[/INFOGRAPHIC]

[QUIZ:Berdasarkan prinsip manajemen operasional modern, apa yang biasanya menjadi penyebab sebenarnya dari error yang terjadi berulang?|Kurangnya motivasi dan dedikasi dari karyawan yang melakukan kesalahan tersebut|Sistem yang dirancang buruk yang membuat kemungkinan terjadinya kesalahan manusia sangat tinggi|Tingkat kompleksitas pekerjaan yang terlalu tinggi untuk dikerjakan oleh manusia secara konsisten|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Identifikasi **satu jenis error yang paling sering terjadi** di bisnismu dalam 30 hari terakhir. Lakukan root cause analysis: apakah ada checklist? Apakah instruksi cukup jelas? Apakah ada distraksi di lingkungan kerja yang memicu error? Perbaiki satu penyebab root-nya hari ini.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Scalability Stress Test',
                'order' => 9, 'xp' => 60, 'min' => 10,
                'content' => <<<MD
**Menguji kesiapan sistem sebelum scale** — scaling bisnis yang belum siap adalah keputusan yang bisa menghancurkan bisnis yang sebenarnya sudah baik. Ketika order tiba-tiba naik 10x — apakah sistem fulfillment-mu bisa menangani? Ketika traffic meledak — apakah server dan customer service kamu siap? **Stress test** adalah cara untuk menemukan kelemahan sebelum pasar yang menemukannya untuk kamu — dengan konsekuensi yang jauh lebih mahal.

[CASE_STUDY]
**Observasi Lapangan: Flash Sale yang Hampir Menghancurkan Brand**
Brand fashion lokal mendapatkan liputan media besar dan dalam 48 jam menerima 1.200 order — 15x kapasitas normal mereka. Tak ada yang siap: website down, CS overwhelmed, gudang kacau, pengiriman tertunda 2-3 minggu, dan ratusan komplain membanjiri media sosial. Revenue Rp 1.8 miliar dalam 2 hari — tapi reputasi hancur selama 8 bulan berikutnya. Tiga bulan sebelum itu, satu stress test sederhana (simulasi order 10x normal) akan mengungkap semua kelemahan ini dalam kondisi terkontrol — dengan waktu untuk memperbaikinya.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Scalability Stress Test]
Fulfillment Test | Simulasikan 3x atau 10x volume order — di mana sistem pertama kali mulai collaps?
Tech Infrastructure | Apakah server, checkout, dan payment gateway sudah di-load test untuk volume tinggi?
CS Capacity | Berapa volume pertanyaan yang bisa ditangani per hari? Apa yang terjadi jika 5x lebih banyak?
Supply Chain | Apakah supplier bisa memenuhi jika order naik drastis? Berapa lead time mereka?
[/INFOGRAPHIC]

[QUIZ:Mengapa penting untuk melakukan scalability stress test sebelum menjalankan kampanye besar atau promosi viral?|Untuk memastikan brand terlihat profesional dan siap di mata investor yang mungkin tertarik|Untuk menemukan kelemahan sistem dalam kondisi terkontrol sebelum pasar menemukannya dalam kondisi nyata yang tidak bisa dikontrol|Untuk memastikan karyawan familiar dengan prosedur darurat jika terjadi gangguan sistem|1]

[CHALLENGE]
**Action Plan Hari Ini:**
**Simulasikan kenaikan order 3x** dari volume rata-rata harianmu: apakah tim bisa menangani? Apakah stok cukup? Apakah CS bisa merespons dalam SLA? Apakah sistem pengiriman tidak collapse? Identifikasi 3 titik lemah yang muncul dalam simulasi ini — itulah yang harus diperkuat sebelum kamu scale.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Self-Running Business Model',
                'order' => 10, 'xp' => 75, 'min' => 11,
                'content' => <<<MD
**Membangun bisnis yang berjalan stabil tanpa intervensi harian pemilik** — ini adalah tujuan akhir dari semua sistem operasional yang sudah kamu bangun. Bukan bisnis yang autopilot sehingga pemilik tidak peduli — tapi bisnis yang **desainnya cukup kuat untuk beroperasi konsisten tanpa kehadiran konstanmu**, sehingga kamu bebas untuk fokus pada pertumbuhan strategis, inovasi, atau bahkan istirahat.

**System > Hustle.** Selalu.

[CASE_STUDY]
**Observasi Lapangan: Liburan 3 Minggu, Revenue Naik**
Founder agency digital mengambil cuti 3 minggu penuh tanpa membuka laptop — pertama kalinya dalam 4 tahun. Prerequisite yang dipastikan sebelum pergi: semua proses memiliki SOP dan backup person, monitoring dashboard sudah otomatis, tim memiliki authority untuk keputusan rutin tanpa persetujuan founder, dan hanya emergency level 3 yang boleh menghubungi ia (didefinisikan secara ketat). Hasil 3 minggu tanpa founder: revenue naik 12% karena tim lebih autonomous dan tidak menunggu approval. Founder kembali dengan energi penuh dan perspektif segar untuk strategi berikutnya.
[/CASE_STUDY]

[INFOGRAPHIC:Komponen Self-Running Business]
Documented Systems | Setiap proses terdokumentasi — pengetahuan organisasional tidak tersimpan di satu kepala
Empowered Team | Tim memiliki authority untuk keputusan rutin dan tahu kapan harus eskalasi
Automated Monitoring | Dashboard otomatis yang tracking KPI — tidak harus ada orang yang menghitung manual
Clear Communication | Async-first — update melalui dokumen, bukan rapat harian yang wajib dihadiri founder
[/INFOGRAPHIC]

[QUIZ:Apa ciri utama dari bisnis yang sudah mencapai tahap "self-running" atau semi-autopilot?|Bisnis yang tidak membutuhkan karyawan karena semua sudah diotomasi oleh teknologi|Bisnis yang bisa beroperasi secara konsisten dan menghasilkan revenue bahkan ketika pemilik tidak hadir setiap hari|Bisnis yang sudah listed di bursa saham sehingga didukung oleh struktur governance yang kuat|1]

Selamat menyelesaikan modul **Operational Efficiency & Automation** — level Advanced! Kamu kini memiliki blueprint lengkap dari bisnis sebagai sistem hingga self-running business model. Satu langkah terakhir:

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **blueprint bisnis autopilot versimu**: gambarkan 5 area operasional utama (fulfillment, CS, marketing, finance, product) dan untuk setiap area — apa sistem yang sudah ada? Apa yang masih bergantung padamu? Apa satu hal yang bisa kamu dokumentasikan, otomasi, atau delegasikan minggu ini untuk bergerak satu langkah lebih dekat ke bisnis yang berjalan sendiri?
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

        $this->command->info('✅ Operational Efficiency & Automation (Advanced) — 10 lessons berhasil dimasukkan ke database.');
    }
}
