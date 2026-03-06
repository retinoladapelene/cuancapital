<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CustomerRetentionAdvancedSeed extends Seeder
{
    public function run(): void
    {
        Course::where('title', 'Customer Service & Retention Strategy')->delete();

        $course = Course::create([
            'title'         => 'Customer Service & Retention Strategy',
            'description'   => 'Level advanced — kuasai retention psychology, loyalty systems, dan lifetime value optimization. Dari economics of retention, CLV, service recovery, hingga retention flywheel system.',
            'level'         => 'advanced',
            'category'      => 'sales',
            'xp_reward'     => 700,
            'lessons_count' => 10,
            'is_active'     => true,
        ]);

        $lessons = [

            [
                'title' => 'Economics of Retention',
                'order' => 1, 'xp' => 55, 'min' => 9,
                'content' => <<<MD
**Kenapa retention lebih penting dari acquisition** — hampir semua bisnis mengalokasikan mayoritas budget marketing untuk mendapatkan customer baru. Ini adalah bias yang sangat mahal. Data dari Harvard Business Review menunjukkan: mendapatkan customer baru bisa **5 hingga 25 kali lebih mahal** dibanding mempertahankan yang sudah ada. Dan kenaikan retention rate sebesar 5% saja dapat meningkatkan profit antara 25% hingga 95%.

Acquisition adalah cost center. Retention adalah profit multiplier.

[CASE_STUDY]
**Observasi Lapangan: Bisnis yang Berhenti Iklan tapi Revenue Naik**
Sebuah brand skincare lokal melakukan eksperimen selama 3 bulan: menghentikan semua iklan akuisisi dan mengalihkan seluruh budget ke program retention — email nurturing, loyalty points, dan exclusive member content. Bulan 1: revenue turun 15% karena tidak ada pelanggan baru. Bulan 2: stabil. Bulan 3: revenue naik 38% di atas baseline. Mengapa? Repeat order rate naik dari 22% ke 61%. Customer yang sudah percaya membeli lebih sering, nilai transaksi lebih tinggi, dan mereferensikan ke orang lain tanpa biaya.
[/CASE_STUDY]

[INFOGRAPHIC:Mengapa Retention Adalah Keunggulan Kompetitif]
Biaya Akuisisi | Rata-rata 5-25x lebih mahal dari biaya mempertahankan customer yang sudah ada
Probability of Sale | Selling ke existing customer: 60-70%. Selling ke new prospect: 5-20%
Revenue per Customer | Repeat customer menghabiskan rata-rata 67% lebih banyak dari first-time buyer
Referral Value | Loyal customer yang puas mereferensikan 3-5 orang baru secara organik
[/INFOGRAPHIC]

[QUIZ:Dari perspektif ekonomi bisnis, mana yang lebih hemat dari sisi biaya per revenue yang dihasilkan?|Acquisition — karena mendatangkan revenue dari pelanggan baru yang belum pernah membeli|Retention — karena biaya mempertahankan customer lama jauh lebih rendah dari akuisisi baru|Keduanya sama — tergantung pada industri dan skala bisnis yang dijalankan|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Hitung **rasio customer baru vs repeat buyer** dari data 3 bulan terakhir. Berapa % dari revenue-mu datang dari repeat order? Jika di bawah 30%, retention adalah area tertinggi prioritas yang perlu diperbaiki sebelum menambah budget acquisition.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Customer Lifetime Value (CLV)',
                'order' => 2, 'xp' => 55, 'min' => 9,
                'content' => <<<MD
**Nilai jangka panjang dari satu customer** — CLV adalah salah satu metrik paling penting dalam bisnis, namun paling jarang dihitung oleh UMKM dan startup early stage. CLV memberitahumu **berapa maksimum yang boleh kamu keluarkan untuk mendapatkan satu customer baru** sambil tetap profitable — dan seberapa agresif kamu bisa bermain di acquisition.

Formula dasar: **CLV = AOV × Purchase Frequency × Retention Time**.

[CASE_STUDY]
**Observasi Lapangan: CLV yang Mengubah Strategi Bisnis Sepenuhnya**
Toko kopi online menghitung CLV pertama kali dan menemukan: AOV Rp 180.000, rata-rata pembelian 8x per tahun, retention rata-rata 2.3 tahun. CLV = Rp 180.000 × 8 × 2.3 = Rp 3.312.000. Sebelumnya mereka batas CPA di Rp 150.000 karena mengira "margin per transaksi sekitar Rp 80.000, jadi CPA max Rp 50.000." Setelah memahami CLV, mereka menaikkan CPA max ke Rp 600.000 dan mulai menjalankan kampanye yang sebelumnya "terlalu mahal" — tapi sebenarnya sangat profitable secara jangka panjang.
[/CASE_STUDY]

[INFOGRAPHIC:Formula dan Lever CLV]
AOV | Average Order Value — tingkatkan dengan upsell, bundle, dan offer stack
Purchase Frequency | Seberapa sering customer kembali — tingkatkan dengan loyalty program dan nurturing
Retention Time | Berapa lama customer tetap aktif — tingkatkan dengan experience dan community
CLV Impact | Tingkatkan salah satu dari tiga lever → CLV naik secara eksponensial
[/INFOGRAPHIC]

[QUIZ:CLV yang tinggi mengindikasikan hal apa dalam sebuah bisnis?|Traffic tinggi dari berbagai channel marketing yang dijalankan secara bersamaan|Adanya customer yang loyal yang melakukan pembelian berulang dalam jangka waktu yang panjang|Produk yang viral dan trending sehingga banyak orang datang untuk membeli|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Hitung **estimasi CLV** untuk segmen customer terbaikmu: berapa AOV rata-rata mereka? Berapa kali mereka membeli dalam setahun? Berapa tahun rata-rata mereka aktif? Kalikan ketiganya. Bandingkan hasilnya dengan CPA saat ini — apakah kamu sudah mengoptimasi budget acquisition berdasarkan CLV, atau masih berdasarkan margin per transaksi saja?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'First Experience Impact',
                'order' => 3, 'xp' => 50, 'min' => 8,
                'content' => <<<MD
**Pengalaman pertama menentukan loyalitas jangka panjang** — customer paling rentan terhadap churn adalah customer yang baru saja melakukan pembelian pertama. Mereka datang dengan ekspektasi yang dibangun oleh iklan dan promise yang kamu buat — dan dalam 72 jam pertama setelah pembelian, mereka memutuskan: *"Apakah keputusan ini benar?"*

Jika onboarding pertama mengecewakan, tidak ada loyalty program atau diskon yang bisa memulihkan kepercayaan itu secara penuh.

[CASE_STUDY]
**Observasi Lapangan: Email Welcome yang Mengubah 30-Day Retention**
Platform SaaS dengan 30-day retention rate 34% — artinya 66% user yang signup tidak kembali setelah bulan pertama. Tim retention melakukan satu perubahan: menambahkan email welcome sequence 5 hari yang berisi: (1) video welcome personal dari founder, (2) quick win tutorial menyelesaikan satu task dalam 10 menit, (3) success metric: "Ini hasil yang bisa kamu capai dalam 2 minggu", (4) community invite, (5) milestone celebration setelah task pertama selesai. 30-day retention naik dari 34% ke 67% dalam 2 bulan. Satu sequence email — hampir double retention.
[/CASE_STUDY]

[INFOGRAPHIC:Anatomi First Experience yang Membangun Loyalitas]
Konfirmasi Segera | Email/pesan otomatis yang mengkonfirmasi pembelian dan menetapkan ekspektasi
Quick Win | Bantu customer mencapai satu hasil kecil secepat mungkin — validasi keputusan beli mereka
Onboarding Content | Panduan step-by-step yang mengurangi friction dalam penggunaan pertama
Personal Touch | Sentuhan personal — walau otomatis — yang membuat mereka merasa diperhatikan
[/INFOGRAPHIC]

[QUIZ:Mengapa fase pembelian pertama adalah yang paling kritis dalam menentukan loyalitas customer jangka panjang?|Karena first-time buyer selalu mendapatkan harga dan penawaran terbaik dari bisnis|Karena customer sedang mengevaluasi apakah ekspektasi yang dibangun oleh iklan terpenuhi|Karena platform marketplace memberikan peringkat lebih tinggi ke toko dengan rating first-time buyer baik|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Audit **onboarding customer baru** kamu dari perspektif pembeli pertama: apa yang terjadi dalam 24 jam pertama setelah mereka membeli? Apakah ada komunikasi yang proaktif? Apakah ada quick win yang bisa mereka capai? Identifikasi satu gap dan perbaiki hari ini.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Service Recovery Strategy',
                'order' => 4, 'xp' => 50, 'min' => 8,
                'content' => <<<MD
**Mengubah komplain menjadi peluang loyalitas** — ini terdengar kontra-intuitif, tapi ada fenomena yang sudah terdokumentasi dalam customer service research: **Service Recovery Paradox**. Customer yang mengalami masalah dan kemudian mendapatkan penanganan yang luar biasa sering menjadi **lebih loyal** dibanding customer yang tidak pernah mengalami masalah sama sekali.

Mengapa? Karena komplain yang ditangani dengan baik membuktikan bahwa kamu peduli, kamu responsif, dan kamu bisa dipercaya — bahkan ketika sesuatu berjalan tidak sempurna.

[CASE_STUDY]
**Observasi Lapangan: Masalah Pengiriman yang Menciptakan Brand Ambassador**
Customer menerima produk yang rusak. Tim CS merespons dalam 30 menit, meminta maaf secara personal (bukan template), mengirimkan pengganti hari itu juga dengan upgrade gratis, dan menambahkan voucher senilai 50% untuk pembelian berikutnya. Customer yang awalnya frustrasi memposting cerita ini di Instagram dengan caption "Ini adalah bisnis yang benar-benar peduli." Post mendapat 2.300 likes dan 180 komentar. Brand awareness dari satu penanganan komplain yang benar mengalahkan puluhan iklan berbayar.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Service Recovery yang Menciptakan Loyalitas]
Acknowledge | Akui masalahnya segera — jangan defensif, jangan lempar tanggung jawab
Apologize | Permintaan maaf yang tulus dan personal — bukan template generik
Act | Tawarkan solusi konkret yang melebihi ekspektasi customer
Appreciate | Ucapkan terima kasih karena mereka memberitahumu — tanpa feedback, kamu tidak bisa memperbaiki
[/INFOGRAPHIC]

[QUIZ:Mengapa customer yang komplain dan mendapat penanganan luar biasa sering menjadi lebih loyal dari yang tidak pernah komplain?|Karena mereka mendapat kompensasi yang membuat mereka merasa beruntung|Karena penanganan yang baik membuktikan bahwa bisnis benar-benar peduli dan dapat dipercaya dalam situasi sulit|Karena customer yang komplain biasanya lebih aktif di media sosial dan suka berbagi pengalaman|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **template respon komplain profesional** untuk 3 skenario paling umum di bisnismu: (1) produk rusak/tidak sesuai, (2) pengiriman terlambat, (3) hasil tidak sesuai ekspektasi. Setiap template harus mencakup: acknowledge, apologize, solusi konkret, dan appreciation. Simpan sebagai SOP tim CS-mu.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Response Time Psychology',
                'order' => 5, 'xp' => 45, 'min' => 8,
                'content' => <<<MD
**Kecepatan respons sebagai leverage kepercayaan** — dalam era digital, ekspektasi kecepatan respons telah bergeser secara dramatis. 82% customer mengharapkan respons dalam kurang dari 10 menit untuk pertanyaan penting — dan setiap menit keterlambatan menurunkan kemungkinan mereka menyelesaikan pembelian atau kembali membeli.

Kecepatan respons bukan sekadar efisiensi operasional — ia adalah **sinyal kuat tentang seberapa besar kamu menghargai customer-mu**.

[CASE_STUDY]
**Observasi Lapangan: Response Time dan Churn Rate**
Sebuah platform membership melakukan analisis korelasi antara response time CS dan 90-day churn rate. Temuan: member yang pertanyaannya direspons dalam < 1 jam memiliki churn rate 8%. Yang direspons dalam 1-4 jam: churn 19%. Yang direspons lebih dari 4 jam: churn 41%. Yang tidak mendapat respons sama sekali: churn 78%. Setiap jam keterlambatan meningkatkan probabilitas churn secara signifikan. Response time adalah metrik retention yang sering diabaikan tapi dampaknya langsung ke bottom line.
[/CASE_STUDY]

[INFOGRAPHIC:SLA Response Time yang Direkomendasikan]
Live Chat / WhatsApp | Target < 5 menit — ini adalah channel dengan ekspektasi respons tertinggi
Email | Target < 4 jam di jam kerja — maksimum 1 hari untuk pertanyaan non-urgent
Media Sosial (comment) | Target < 2 jam — ini adalah respons yang terlihat publik dan mempengaruhi persepsi brand
After-hours | Set auto-reply yang informatif + waktu kapan akan direspons — jangan biarkan kosong
[/INFOGRAPHIC]

[QUIZ:Berdasarkan data customer psychology, apa dampak utama dari respons yang lambat terhadap pertanyaan customer?|Meningkatkan kemungkinan customer memberikan review yang lebih detail dan konstruktif|Meningkatkan frustrasi yang berkorelasi langsung dengan churn rate yang lebih tinggi|Memberi kesan bahwa bisnis sangat sibuk dan populer sehingga meningkatkan perceived value|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Tetapkan **SLA (Service Level Agreement) respons** untuk setiap channel komunikasi bisnismu — WhatsApp, email, dan komentar media sosial. Dokumenkan di SOP internal. Ukur response time rata-rata saat ini vs target — berapa gap yang perlu ditutup?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Loyalty Program Design',
                'order' => 6, 'xp' => 55, 'min' => 9,
                'content' => <<<MD
**Merancang sistem loyalitas yang mendorong repeat purchase** — loyalty program yang efektif bukan sekadar kartu stempel atau poin yang bisa ditukar diskon. Program loyalitas yang benar-benar mengubah perilaku adalah yang **membuat customer merasa memiliki status, progres, dan belonging** — tiga kebutuhan psikologis yang jauh lebih kuat dari sekadar insentif finansial.

[CASE_STUDY]
**Observasi Lapangan: Tier System yang Naikan Purchase Frequency 3x**
Brand kopi premium memperkenalkan 3 tier membership: Bronze (0-10 cups), Silver (11-30 cups), Gold (31+ cups). Setiap tier memberikan benefit yang berbeda — bukan hanya diskon, tapi akses ke menu eksklusif, early access produk baru, dan nama customer tercetak di menu. Dalam 6 bulan: 34% member Bronze naik ke Silver, 18% Silver naik ke Gold. Purchase frequency rata-rata customer yang terlibat program naik 3.2x. Mayoritas kenaikan ini datang dari **desire to move up the tier** — bukan karena diskonnya.
[/CASE_STUDY]

[INFOGRAPHIC:Komponen Loyalty Program yang Efektif]
Points System | Reward setiap transaksi — memberikan rasa progres yang berkelanjutan
Tier / Status | Level yang bisa dinaiki — trigger desire untuk level up dan maintain status
Exclusive Benefit | Akses ke sesuatu yang tidak bisa dibeli — membuat member merasa spesial
Community | Rasa belonging dengan sesama member — loyalty yang emosional, bukan transaksional
[/INFOGRAPHIC]

[QUIZ:Tujuan utama dari loyalty program dalam konteks strategy bisnis jangka panjang adalah?|Meningkatkan brand awareness melalui word-of-mouth dari program yang viral|Mendorong repeat purchase dengan memberikan insentif dan rasa progres kepada customer aktif|Meningkatkan traffic baru ke toko atau website melalui referral dari member aktif|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Desain **sistem reward sederhana** untuk bisnismu: tentukan currency reward (poin, stamp, credit), trigger untuk mendapatkan reward (per transaksi, per referral, per review), nilai tukarnya, dan benefit apa yang tidak bisa dibeli selain melalui loyalty — minimal satu "exclusive benefit" yang membuat member merasa benar-benar spesial.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Emotional Retention Trigger',
                'order' => 7, 'xp' => 50, 'min' => 8,
                'content' => <<<MD
**Faktor emosional yang mengunci loyalitas jangka panjang** — data dari penelitian customer loyalty konsisten menunjukkan satu temuan yang mengejutkan: **customer yang memiliki koneksi emosional dengan brand menghabiskan rata-rata 52% lebih banyak** dibanding customer yang "puas" tapi tidak terhubung secara emosional. Kepuasan adalah baseline — emosi adalah differentiator.

Customer loyal bukan karena hargamu paling murah. Mereka loyal karena mereka **merasa dihargai, dimengerti, dan menjadi bagian dari sesuatu yang lebih besar**.

[CASE_STUDY]
**Observasi Lapangan: Ulang Tahun yang Menciptakan Viral Moment**
Brand teh lokal mengirimkan paket kecil berisi sample produk terbaru + kartu tulisan tangan kepada customer setia mereka di hari ulang tahun. Biaya per paket: Rp 35.000 termasuk ongkir. Dari 200 customer yang menerima: 67 memposting di media sosial (33.5% organic posting rate), menghasilkan rata-rata 450 reach organik per post. Total reach: 90.000 orang dari investasi Rp 7 juta. Dan yang lebih penting: repeat purchase rate dari customer yang menerima paket naik 78% di bulan berikutnya.
[/CASE_STUDY]

[INFOGRAPHIC:Emotional Trigger yang Menciptakan Loyalitas Kuat]
Recognition | Sebut nama — sadari milestone mereka — ingat preferensi mereka
Appreciation | Ucapkan terima kasih yang tulus dan personal, bukan otomatis
Personalization | Komunikasi yang terasa dibuat khusus untuk mereka, bukan blast email generik
Community Belonging | Buat mereka merasa menjadi bagian dari kelompok yang memiliki nilai sama
[/INFOGRAPHIC]

[QUIZ:Mengapa koneksi emosional dengan brand menghasilkan loyalitas yang lebih kuat dibandingkan kepuasan rasional semata?|Karena emosi membuat customer tidak bisa berpikir jernih sehingga sulit beralih ke kompetitor|Karena customer yang terhubung secara emosional memiliki motivasi internal untuk setia yang jauh lebih kuat dari insentif finansial|Karena customer emosional cenderung lebih vocal di media sosial dan membantu promosi brand secara gratis|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Identifikasi **satu sentuhan personal** yang bisa kamu tambahkan ke komunikasi brand-mu mulai hari ini — bisa sesederhana menyebut nama customer dalam setiap pesan, merayakan milestone pembelian ke-5 mereka, atau mengirimkan konten yang relevan dengan preferensi spesifik mereka. Mulai kecil, tapi mulai sekarang.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Churn Detection System',
                'order' => 8, 'xp' => 55, 'min' => 9,
                'content' => <<<MD
**Mendeteksi tanda-tanda customer akan pergi sebelum terlambat** — churn tidak terjadi secara tiba-tiba. Ada sinyal yang muncul jauh sebelum customer benar-benar berhenti membeli — jika kamu tahu cara membacanya. Sayangnya, kebanyakan bisnis baru menyadari customer sudah churn setelah 3-6 bulan mereka tidak aktif — terlalu terlambat untuk intervensi yang efektif.

**Early signal detection adalah investasi terbaik dalam retention.**

[CASE_STUDY]
**Observasi Lapangan: Machine Learning yang Menyelamatkan 23% Revenue**
Platform subscription mendeteksi pola: customer yang tidak login selama 14 hari + tidak membuka 3 email terakhir + tidak menggunakan fitur core dalam 21 hari memiliki 78% probability churn dalam 30 hari ke depan. Mereka mengimplementasikan "win-back flow" otomatis yang dipicu oleh kombinasi sinyal ini: email personal dari founder + penawaran pause subscription (bukan cancel) + survey singkat tentang hambatan penggunaan. Hasil: 23% dari customer yang masuk win-back flow tetap aktif yang sebelumnya hampir pasti churn.
[/CASE_STUDY]

[INFOGRAPHIC:Early Warning Signals Churn]
Engagement Drop | Tidak membuka email, tidak login, tidak melihat konten — pertanda awal yang paling terlihat
Purchase Gap | Jeda antara transaksi terakhir dengan normal purchase interval yang signifikan melebar
Support Spike | Tiba-tiba banyak komplain atau pertanyaan — frustasi yang belum terselesaikan
Inactivity Pattern | Tidak menggunakan produk yang biasanya dipakai rutin — behavioral churn sudah terjadi
[/INFOGRAPHIC]

[QUIZ:Apa yang dimaksud dengan "churn" dalam konteks bisnis berbasis langganan atau repeat customer?|Pertumbuhan jumlah customer baru yang masuk ke dalam funnel akuisisi
|Hilangnya customer aktif yang berhenti membeli atau membatalkan langganan mereka|Penurunan rata-rata order value dari customer existing yang masih aktif bertransaksi|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Identifikasi **5 customer** dari database-mu yang menunjukkan tanda-tanda early churn: siapa yang terakhir membeli lebih dari 60 hari lalu? Siapa yang tidak membuka email 3x berturut-turut? Hubungi mereka secara personal hari ini dengan pertanyaan sederhana: "Apa yang bisa kami lakukan untuk membuat pengalaman belanjamu lebih baik?"
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Upsell & Cross-Sell Science',
                'order' => 9, 'xp' => 55, 'min' => 9,
                'content' => <<<MD
**Memaksimalkan nilai dari customer yang sudah ada** — upsell dan cross-sell bukan teknik manipulasi — mereka adalah bentuk customer service yang baik ketika dilakukan dengan relevan dan tepat waktu. Menawarkan produk yang lebih baik (upsell) atau produk komplementer (cross-sell) kepada customer yang sudah percaya adalah cara memaksimalkan revenue **tanpa biaya akuisisi tambahan**.

Probability of selling to existing customer: 60-70%. Probability of selling to new prospect: 5-20%.

[CASE_STUDY]
**Observasi Lapangan: One-Click Upsell yang Naikan Revenue 67%**
Platform digital course mengimplementasikan upsell di halaman konfirmasi pembelian: setelah customer membeli kursus Rp 500.000, mereka langsung ditawari "upgrade ke paket mentorship 3 bulan — normalnya Rp 2.500.000, tapi karena kamu baru saja bergabung, Rp 1.500.000 hanya hari ini." Conversion rate upsell: 28%. Dari 100 pembeli kursus, 28 upgrade ke mentorship. Bagi revenue: kursus saja = Rp 50 juta. Dengan upsell = Rp 92 juta. Revenue naik 84% dari satu penawaran yang ditiming dengan sempurna.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Upsell & Cross-Sell yang Efektif]
Timing | Upsell paling efektif pada momen post-purchase — ketika customer paling positif terhadap brand
Relevance | Offer harus langsung relevan dengan apa yang baru mereka beli — bukan produk random
Value Framing | Tunjukkan saving atau nilai tambah, bukan sekadar "ada produk lain yang bisa kamu beli"
Friction-Free | One-click tambahan idealnya — semakin sedikit langkah tambahan, semakin tinggi conversion
[/INFOGRAPHIC]

[QUIZ:Mengapa upsell ke existing customer menghasilkan ROI yang lebih tinggi dibandingkan akuisisi customer baru?|Karena harga produk upsell biasanya lebih tinggi sehingga margin per transaksi lebih besar|Karena tidak ada biaya akuisisi dan probability konversi ke existing customer 3-5x lebih tinggi|Karena existing customer tidak perlu diyakinkan dengan iklan dan promosi yang mahal|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Rancang **satu skenario upsell** untuk produk utamamu: apa yang ditawarkan, kapan ditawarkan (pre-purchase, checkout, atau post-purchase), berapa harganya relatif terhadap pembelian utama, dan bagaimana framing value yang membuatnya terasa seperti kesempatan, bukan tekanan.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Retention Flywheel System',
                'order' => 10, 'xp' => 70, 'min' => 10,
                'content' => <<<MD
**Membangun sistem loyalitas yang berjalan otomatis dan self-reinforcing** — retention terbaik bukan kampanye sesekali atau promosi musiman. Retention terbaik adalah **sistem yang menghasilkan loyalitas, referral, dan revenue berulang secara otomatis** — sebuah flywheel yang sekali berputar, semakin mudah dan semakin cepat dengan sendirinya.

Flywheel retention: **Value → Trust → Repeat → Referral → Growth → lebih banyak Value.**

[CASE_STUDY]
**Observasi Lapangan: Flywheel yang Menumbuhkan Bisnis Tanpa Iklan**
Bisnis meal prep sehat membangun flywheel: (1) Setiap order disertai kartu resep eksklusif (Value), (2) CS responsif dan konsisten melebihi ekspektasi (Trust), (3) Loyalty point yang membuat repeat order menguntungkan customer (Repeat), (4) Referral program yang memberikan komisi menarik untuk setiap teman yang join (Referral). Dalam 18 bulan tanpa satu pun iklan berbayar: dari 50 customer awal berkembang ke 1.847 active customer. 73% dari pertumbuhan datang dari referral — bukan iklan. Flywheel bekerja sendiri.
[/CASE_STUDY]

[INFOGRAPHIC:Komponen Retention Flywheel]
Value Delivery | Konsisten memberikan lebih dari yang dijanjikan — ini adalah bahan bakar flywheel
Trust Building | Setiap interaksi membangun atau menggerus trust — pastikan semuanya membangun
Repeat Mechanism | Sistem yang membuat repeat purchase mudah dan menguntungkan customer
Referral Engine | Buat customer ingin dan mudah merekomendasikan ke orang lain
Feedback Loop | Data dari setiap putaran digunakan untuk meningkatkan putaran berikutnya
[/INFOGRAPHIC]

[QUIZ:Mengapa retention flywheel disebut "sustainable business model" dibandingkan model yang bergantung pada acquisition iklan?|Karena flywheel tidak membutuhkan investasi apapun setelah sistem pertama kali dibangun|Karena flywheel menciptakan siklus self-reinforcing di mana value, trust, dan referral saling memperkuat tanpa bergantung pada biaya iklan berulang|Karena sistem flywheel dijamin berhasil di semua jenis bisnis tanpa perlu adjustment|1]

Selamat menyelesaikan modul **Customer Service & Retention Strategy** — level Advanced! Kamu kini memiliki framework lengkap dari economics of retention hingga retention flywheel yang bisa diimplementasikan hari ini. Satu langkah terakhir:

[CHALLENGE]
**Action Plan Hari Ini:**
Rancang **alur retention flywheel bisnismu** secara visual: gambarkan 5 tahap (Value → Trust → Repeat → Referral → Growth) dan untuk setiap tahap, tentukan satu sistem atau mekanisme konkret yang akan kamu implementasikan. Mana satu tahap yang paling lemah saat ini? Itu adalah prioritas pertama minggu ini.
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

        $this->command->info('✅ Customer Service & Retention Strategy (Advanced) — 10 lessons berhasil dimasukkan ke database.');
    }
}
