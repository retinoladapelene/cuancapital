<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class StrategicDecisionExpertSeed extends Seeder
{
    public function run(): void
    {
        Course::where('title', 'Strategic Decision Making & Business Intelligence')->delete();

        $course = Course::create([
            'title'         => 'Strategic Decision Making & Business Intelligence',
            'description'   => 'Level expert — kuasai cara berpikir eksekutif: leverage thinking, data-driven decision, opportunity scoring, risk mapping, second order thinking, hingga forecasting revenue.',
            'level'         => 'expert',
            'category'      => 'strategy',
            'xp_reward'     => 1250,
            'lessons_count' => 10,
            'is_active'     => true,
        ]);

        $lessons = [

            [
                'title' => 'Thinking in Leverage',
                'order' => 1, 'xp' => 85, 'min' => 12,
                'content' => <<<MD
**Keputusan kecil vs keputusan leverage tinggi** — CEO dan founder level elite bukan orang yang paling banyak membuat keputusan. Justru sebaliknya: mereka adalah orang yang **paling sedikit membuat keputusan** — karena mereka sudah membangun sistem yang menangani keputusan rutin secara otomatis. Energi mental mereka dikonservasi untuk satu jenis keputusan yang paling langka dan paling berharga: **keputusan leverage tinggi** — keputusan yang satu tindakannya berdampak pada puluhan atau ratusan tindakan lain di seluruh organisasi.

Leverage thinking bukan tentang kerja keras. Ini tentang **mengalikan dampak setiap unit energi yang kamu keluarkan**.

[CASE_STUDY]
**Observasi Lapangan: Founder yang Berhenti Memutuskan Hal Kecil**
Founder e-commerce menghabiskan waktu rata-rata 3 jam per hari untuk keputusan operasional: approve desain thumbnail, pilih filter foto produk, review setiap konten sebelum posting, dan approve setiap pengeluaran di atas Rp 500.000. Ketika ia mengaudit waktu ini, ia menemukan bahwa semua keputusan ini memiliki total dampak kurang dari 2% terhadap revenue bisnis. Setelah mengimplementasikan framework tiga level — ignore yang low impact, delegasikan yang medium impact, dan putuskan sendiri hanya yang high impact — ia membebaskan 2.5 jam per hari untuk fokus pada dua keputusan leverage tinggi: masuk ke channel distribusi baru dan renegosiasi kontrak supplier. Dua keputusan itu menghasilkan 340% pertumbuhan dalam 6 bulan berikutnya.
[/CASE_STUDY]

[INFOGRAPHIC:Framework 3 Level Keputusan]
Low Impact | Output tidak signifikan terhadap revenue atau growth → Ignore atau buat jadi preset otomatis
Medium Impact | Output dapat diukur tapi tidak kritis → Delegasikan dengan SOP dan batas authority yang jelas
High Impact | Mengubah arah bisnis, partnership kunci, alokasi modal → Decide personally dengan analisis penuh
Leverage Multiplier | Satu high-impact decision dapat menggerakkan puluhan medium dan ratusan low-impact outcomes
[/INFOGRAPHIC]

[QUIZ:Mengapa CEO dan founder level elite membuat lebih sedikit keputusan dibanding rekan-rekan mereka yang kurang sukses?|Karena mereka memiliki lebih sedikit tanggung jawab berkat struktur tim yang sudah mandiri|Karena mereka mengonservasi energi mental untuk keputusan leverage tinggi dan mendelegasikan atau mengotomasi sisanya|Karena pengalaman bertahun-tahun membuat mereka mampu memutuskan hal-hal dengan sangat cepat|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat daftar **5 keputusan yang kamu buat minggu ini** — dari yang paling kecil hingga yang paling besar. Kategorikan setiap keputusan ke dalam 3 level: Low Impact (seharusnya di-ignore atau otomasi), Medium Impact (seharusnya didelegasikan), High Impact (harus kamu putuskan sendiri). Berapa % waktumu dihabiskan di kuadran yang benar?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Data vs Intuisi — Kapan Menggunakan Keduanya',
                'order' => 2, 'xp' => 80, 'min' => 11,
                'content' => <<<MD
**Kapan menggunakan data dan kapan mempercayai intuisi** — ini adalah pertanyaan yang menentukan kualitas pengambilan keputusan di level strategic. Founder pemula cenderung ke salah satu ekstrem: terlalu mengandalkan data sehingga kehilangan momen yang bergerak lebih cepat dari data, atau terlalu mengandalkan intuisi sehingga mengabaikan bukti yang sudah jelas di depan mata.

**Rule expert: Data untuk validasi. Intuisi untuk arah.**

[CASE_STUDY]
**Observasi Lapangan: Brand Skincare yang Terjebak Data Kompetitor**
Brand skincare lokal memiliki data analytics yang sangat canggih — mereka melacak setiap langkah kompetitor: produk apa yang laris, harga berapa, konten seperti apa. Mereka membuat semua keputusan berdasarkan data kompetitor ini. Masalahnya: data yang mereka analisis adalah data masa lalu — apa yang sudah terjadi. Sementara itu, ada founder lain yang memperhatikan sesuatu yang tidak terlihat di spreadsheet: percakapan di komunitas online tentang kecemasan "skin barrier damage" dan pencarian keyword yang melonjak untuk "skincare minimalis". Ia membangun produk di sekitar insight ini — 8 bulan sebelum data penjualan menunjukkan tren tersebut. Ketika kompetitor baru melihat datanya, brand ini sudah punya 200.000 customer loyal. Intuisi membaca arah. Data kemudian memvalidasinya.
[/CASE_STUDY]

[INFOGRAPHIC:Panduan Data vs Intuisi dalam Pengambilan Keputusan]
Gunakan Data | Validasi hipotesis, ukur hasil eksperimen, benchmark performa, justifikasi kepada investor
Gunakan Intuisi | Identifikasi tren awal sebelum data ada, keputusan non-repetitif, pattern dari pengalaman panjang
Bahaya Data-Only | Terlambat mengidentifikasi tren baru, analysis paralysis, mengikuti kompetitor bukan memimpin
Bahaya Intuisi-Only | Bias kognitif, overconfidence, keputusan finansial besar tanpa verifikasi
[/INFOGRAPHIC]

[QUIZ:Mengapa mengandalkan data kompetitor secara eksklusif berisiko untuk pengambilan keputusan strategis jangka panjang?|Karena data kompetitor selalu tidak akurat dan menyesatkan apabila dianalisis secara mendalam|Karena data adalah representasi masa lalu yang tidak mencerminkan arah tren yang sedang bergerak|Karena menganalisis data kompetitor melanggar regulasi persaingan usaha yang berlaku|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Ambil **satu keputusan strategis yang sedang atau akan kamu hadapi**. Identifikasi: apa data yang tersedia dan apa yang dikatakannya? Apa intuisimu berdasarkan pengalaman dan observasi pasar? Di mana keduanya agree dan di mana mereka diverge? Keputusan akhir harus mempertimbangkan kedua aspek ini — bukan mengabaikan salah satunya.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Decision Fatigue Management',
                'order' => 3, 'xp' => 75, 'min' => 10,
                'content' => <<<MD
**Energi keputusan adalah sumber daya terbatas yang harus dikelola** — penelitian Roy Baumeister yang terkenal tentang ego depletion menunjukkan bahwa kemampuan mengambil keputusan berkualitas tinggi menurun secara signifikan seiring dengan jumlah keputusan yang sudah diambil sepanjang hari. Bukan karena kamu kurang tidur atau kurang pintar — tapi karena **decision-making menggunakan kapasitas kognitif yang limited**.

Inilah mengapa founder yang paling productive bekerja lebih sedikit jam dalam sehari tapi menghasilkan output strategis yang jauh lebih besar.

[CASE_STUDY]
**Observasi Lapangan: Seragam, Template, dan Robot Keputusan**
Mark Zuckerberg dan Barack Obama keduanya diketahui meminimalisasi pilihan dalam kehidupan personal mereka — pakaian yang sama setiap hari, menu makan yang sudah ditentukan, rutinitas pagi yang identik — bukan karena mereka tidak peduli dengan detail, tapi karena mereka sangat sadar bahwa setiap "keputusan kecil yang tidak perlu" menggerogoti kapasitas untuk "keputusan besar yang krusial." Founders yang menerapkan prinsip ini — template respons email, preset anggaran untuk kategori tertentu, decision rules untuk skenario yang berulang — secara konsisten melaporkan kualitas keputusan strategis yang lebih baik di akhir hari kerja mereka.
[/CASE_STUDY]

[INFOGRAPHIC:3 Senjata Melawan Decision Fatigue]
Decision Templates | Untuk keputusan berulang, buat template jawaban atau respons — tidak perlu berpikir ulang
Preset Rules | "Jika X terjadi, saya selalu melakukan Y" — eliminasi deliberasi untuk skenario yang sudah diketahui
Time-Boxing | Tempatkan keputusan paling penting di pagi hari — saat kognitif paling tajam
Batch Processing | Kumpulkan semua keputusan sejenis dan buat sekaligus, bukan terserak sepanjang hari
[/INFOGRAPHIC]

[QUIZ:Strategi terbaik untuk menghindari decision fatigue dalam operasional bisnis sehari-hari adalah?|Memaksakan diri untuk bekerja lebih lama agar semua keputusan bisa diselesaikan dalam satu hari|Mengurangi jumlah keputusan kecil dengan template, preset rules, dan delegasi sistematis|Mengonsumsi lebih banyak kafein dan suplemen untuk mempertahankan fokus sepanjang hari|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Identifikasi **5 jenis keputusan yang kamu buat berulang setiap minggu** — respons email tertentu, approval budget kecil, pemilihan jadwal posting, dll. Buat **decision template atau preset rule** untuk masing-masing. Mulai hari ini, ketika skenario itu muncul, ikuti template — jangan deliberasi ulang.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Opportunity Scoring System',
                'order' => 4, 'xp' => 85, 'min' => 12,
                'content' => <<<MD
**Memilih peluang bisnis secara objektif bukan berdasarkan kegembiraan** — salah satu penyebab terbesar kegagalan entrepreneur yang sudah berpengalaman adalah **oportunisme yang tidak terfilter**: mengejar setiap peluang yang terlihat menarik tanpa sistem evaluasi yang konsisten. Akibatnya, resources terfragmentasi, eksekusi menjadi dangkal di banyak front sekaligus, dan tidak ada yang benar-benar berhasil secara maksimal.

Opportunity Scoring System memberikan cara **objektif dan replicable** untuk mengevaluasi setiap peluang dengan standar yang sama.

[CASE_STUDY]
**Observasi Lapangan: Scoring yang Mencegah Kesalahan Rp 400 Juta**
Founder bisnis digital mendapat tawaran untuk masuk ke kategori produk fisik — peluang yang secara emosional sangat menarik karena marginnya tampak besar. Ketika ia menerapkan Opportunity Scoring System: Market size (7/10), Margin potential (8/10), Speed to revenue (2/10 — butuh 18 bulan minimum), Skill fit (3/10 — tidak ada pengalaman di supply chain fisik). Score: 5.0/10. Bandingkan dengan peluang yang kurang "seksi": mengoptimasi funnel digital yang sudah ada. Market (7), Margin (9), Speed (9 — bisa dieksekusi minggu ini), Skill fit (10 — ini adalah keahlian utamanya). Score: 8.75/10. Dana Rp 400 juta yang hampir diinvestasikan ke produk fisik dialihkan ke optimasi funnel — menghasilkan 3x ROI dalam 4 bulan.
[/CASE_STUDY]

[INFOGRAPHIC:Formula Opportunity Scoring]
Market | 1-10: Seberapa besar dan growing pasar ini? Apakah ada demand yang terbukti?
Margin | 1-10: Seberapa besar profit yang bisa dihasilkan? Apakah unit economics masuk akal?
Speed | 1-10: Seberapa cepat bisa menghasilkan revenue pertama? Time to profitability?
Skill Fit | 1-10: Seberapa aligned peluang ini dengan kemampuan unik yang sudah kamu miliki?
Score | (Market + Margin + Speed + SkillFit) ÷ 4 — bandingkan skor antar peluang secara apple-to-apple
[/INFOGRAPHIC]

[QUIZ:Mengapa Opportunity Scoring System menghasilkan keputusan yang lebih baik dibanding mengandalkan excitement terhadap sebuah peluang?|Karena sistem scoring diverifikasi oleh investor dan lembaga keuangan sehingga lebih dipercaya|Karena scoring memberikan perbandingan yang objektif dan konsisten antar peluang berdasarkan kriteria yang sama|Karena sistem ini menghilangkan faktor emosi yang selalu negatif dalam pengambilan keputusan bisnis|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Identifikasi **3 peluang atau ide bisnis** yang sedang kamu pertimbangkan saat ini — tidak harus besar. Evaluasi setiap peluang menggunakan 4 komponen scoring (Market, Margin, Speed, Skill Fit) dengan skala 1-10 yang jujur. Hitung skor rata-rata masing-masing. Apakah hasilnya mengejutkan dibanding ekspektasi intuitifmu?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Risk Mapping Framework',
                'order' => 5, 'xp' => 80, 'min' => 11,
                'content' => <<<MD
**Risiko bukan untuk dihindari — tapi untuk dihitung, diantisipasi, dan dimigrasi** — entrepreneur pemula menghindari risiko karena takut. Entrepreneur menengah mengambil risiko tanpa memikirkan konsekuensinya. Entrepreneur expert **memetakan risiko secara sistematis sebelum eksekusi** — sehingga tidak ada risiko yang mengejutkan di tengah jalan. Mereka tahu persis apa yang bisa salah, seberapa besar kemungkinannya, dan apa respons yang sudah disiapkan.

Mitigation plan bukan tanda kelemahan. Ini tanda kematangan strategis.

[CASE_STUDY]
**Observasi Lapangan: Risk Map yang Menyelamatkan Bisnis dari Krisis**
Brand FMCG lokal sebelum meluncurkan produk baru melakukan Risk Mapping Exercise bersama tim: Financial risk — supplier terlambat (probability 60%, impact tinggi), mitigation: 2 backup supplier identified. Operational risk — kapasitas produksi tidak cukup (probability 30%), mitigation: partnership dengan co-packer. Market risk — kompetitor copy produk (probability 80%), mitigation: filing design patent + komunitas loyal sebagai moat. Reputation risk — review negatif batch pertama (probability 20%), mitigation: produk gratiskan ke 200 beta tester dulu. Ketika supplier utama memang terlambat 3 minggu sebelum launch — backup supplier aktivasi dalam 48 jam. Tidak ada krisis. Hanya eksekusi plan B yang sudah disiapkan.
[/CASE_STUDY]

[INFOGRAPHIC:4 Kategori Risiko yang Harus Dipetakan]
Financial Risk | Apa yang bisa membuat cashflow terganggu? Supplier, collection, FX, unexpected cost
Operational Risk | Apa yang bisa menghentikan operasional? Sistem down, ketergantungan satu orang kunci
Market Risk | Apa yang bisa mengubah kondisi pasar? Kompetitor baru, perubahan regulasi, pergeseran trend
Reputation Risk | Apa yang bisa merusak trust dalam semalam? Produk gagal, CS buruk, konten kontroversial
[/INFOGRAPHIC]

[QUIZ:Apa perbedaan pendekatan entrepreneur expert dengan entrepreneur pemula dalam menghadapi risiko bisnis?|Expert menghindari semua risiko, pemula mengambil risiko tanpa pikir panjang|Expert memetakan risiko secara sistematis sebelum eksekusi dan menyiapkan mitigation plan|Expert memiliki modal lebih besar sehingga tidak peduli terhadap kemungkinan kerugian|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Ambil **satu inisiatif bisnis terbesar yang sedang kamu jalankan atau rencanakan**. Lakukan Risk Mapping: untuk setiap kategori (Financial, Operational, Market, Reputation) — apa risiko spesifik yang bisa terjadi? Berapa probability-nya (1-10)? Berapa dampaknya (1-10)? Apa mitigation plan yang sudah disiapkan? Risiko dengan probability × impact terbesar adalah prioritas mitigasimu.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Second Order Thinking',
                'order' => 6, 'xp' => 85, 'min' => 12,
                'content' => <<<MD
**Berpikir melampaui dampak langsung ke dampak lanjutan** — kebanyakan keputusan bisnis yang terlihat pintar di permukaan ternyata kontraproduktif ketika kamu menelusuri dampak lanjutannya. First order thinking berhenti pada "jika A, maka B." Second order thinking bertanya: "jika A maka B, dan jika B maka apa selanjutnya? Dan setelah itu?"

Kemampuan ini adalah **skill differentiator terbesar** antara tactical thinker dan strategic thinker.

[CASE_STUDY]
**Observasi Lapangan: Diskon yang Menghancurkan Brand Value dalam 18 Bulan**
Marketplace fashion memberikan diskon agresif 50% selama 3 bulan berturut-turut untuk meningkatkan GMV. First order: GMV naik 180%. Tapi second order: customer belajar bahwa harga "normal" tidak perlu dibayar — tunggu periode diskon. Third order: penjualan di bulan non-diskon turun drastis karena customer menunggu. Fourth order: untuk mempertahankan GMV, diskon harus dilanjutkan. Fifth order: brand positioning sebagai "produk mahal yang worth it" hancur — tidak ada yang mau membeli di harga penuh lagi. Dalam 18 bulan, GMV kembali ke level sebelum program diskon dimulai — tapi margin per unit sudah 40% lebih rendah secara permanen. Decision yang terlihat brilliant di first order, merusak bisnis di fifth order.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Second Order Thinking]
Level 1 | "Jika saya lakukan X, maka Y terjadi" — dampak langsung yang paling obvious
Level 2 | "Jika Y terjadi, maka apa selanjutnya?" — reaksi dari reaksi pertama
Level 3 | "Dan dari konsekuensi level 2, apa yang akan terjadi berikutnya?" — dampak sistemik
Expert Rule | Untuk setiap keputusan besar: trace minimal 3 level ke depan sebelum eksekusi
[/INFOGRAPHIC]

[QUIZ:Apa yang membedakan second order thinking dari cara berpikir konvensional dalam pengambilan keputusan bisnis?|Second order thinking selalu menghasilkan keputusan yang lebih lambat karena perlu analisis lebih banyak|Second order thinking menelusuri konsekuensi dari konsekuensi — bukan hanya dampak langsung yang obvious|Second order thinking menggunakan model matematika kompleks yang membutuhkan software khusus|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Ambil **strategi yang sedang berjalan atau yang akan kamu eksekusi** — bisa diskon, rekrutmen, ekspansi produk, atau channel baru. Trace dampaknya **3 level ke depan**: Level 1: apa dampak langsung? Level 2: apa reaksi selanjutnya? Level 3: apa konsekuensi sistemik jangka panjang? Apakah kesimpulanmu berubah setelah analisis ini?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Revenue Forecasting Framework',
                'order' => 7, 'xp' => 80, 'min' => 11,
                'content' => <<<MD
**Memprediksi performa bisnis sebelum bulan berjalan** — forecasting bukan crystal ball. Ini adalah **model matematis** yang memungkinkan kamu menetapkan target yang realistis, mengidentifikasi gap antara kondisi saat ini dan target, dan menginter-vensi **sebelum** bulan berakhir dengan hasil yang buruk — bukan setelah.

Tanpa forecasting, kamu selalu bereaksi terhadap apa yang sudah terjadi. Dengan forecasting, kamu mengantisipasi dan mempersiapkan apa yang akan datang.

[CASE_STUDY]
**Observasi Lapangan: Forecasting yang Menyelamatkan Target Q4**
E-commerce memasuki Oktober dengan target revenue Rp 500 juta untuk Q4. Mereka membangun model forecasting sederhana: Traffic × Conversion Rate × AOV per bulan. Proyeksi Oktober: traffic 80.000 × CR 3.2% × AOV Rp 185.000 = Rp 473.6 juta — di bawah target proportional. Mereka mengidentifikasi dua lever: naikkan CR dengan optimasi checkout (cepat, biaya rendah) atau naikkan AOV dengan bundle promotion. Setelah implementasi, CR naik ke 3.8% dan AOV ke Rp 195.000. Revenue aktual Oktober: Rp 592 juta — 18% di atas target. Tanpa model forecasting, mereka tidak akan tahu ada gap di awal bulan sehingga bisa diintervensi.
[/CASE_STUDY]

[INFOGRAPHIC:Model Revenue Forecasting]
Traffic Forecast | Berapa estimasi pengunjung bulan depan? Berdasarkan trend historis + planned campaigns
Conversion Rate | CR rata-rata 90 hari terakhir, disesuaikan dengan campaign quality yang direncanakan
AOV Estimation | Rata-rata nilai transaksi, dengan pertimbangan promotion atau bundle yang akan dijalankan
Revenue = Traffic × CR × AOV | Simulasikan 3 skenario: pessimistic, realistic, optimistic
[/INFOGRAPHIC]

[QUIZ:Apa keunggulan utama dari menggunakan revenue forecasting model dalam pengelolaan bisnis bulanan?|Forecasting menjamin bahwa target revenue akan selalu tercapai sesuai proyeksi|Forecasting memungkinkan identifikasi gap lebih awal sehingga ada waktu untuk intervensi sebelum bulan berakhir|Forecasting mengurangi kebutuhan untuk team meeting bulanan karena data sudah tersedia otomatis|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **simple revenue forecast bulan depan** menggunakan formula Traffic × Conversion Rate × AOV: estimasikan traffic dari semua channel, gunakan CR rata-rata 90 hari terakhir, hitung AOV rata-rata historical. Bandingkan hasilnya dengan target — berapa gap yang ada? Lever mana yang paling realistis untuk di-improve dalam 30 hari ke depan?
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Pattern Recognition Skill',
                'order' => 8, 'xp' => 80, 'min' => 11,
                'content' => <<<MD
**Kemampuan melihat tren sebelum orang lain menyadarinya** — ini adalah skill yang paling mahal di dunia bisnis dan yang paling sulit diajarkan secara eksplisit. Pattern recognition adalah kemampuan melihat koneksi di antara data dan peristiwa yang secara superfisial terlihat tidak berhubungan — dan dari koneksi itu, mengidentifikasi arah yang akan datang sebelum menjadi obvious.

Setiap monopoli pasar yang ada hari ini dibangun oleh orang yang melihat pattern sebelum orang lain sadar apa yang sedang terjadi.

[CASE_STUDY]
**Observasi Lapangan: Brand yang Masuk TikTok Sebelum Semua Orang**
Pada awal 2020, TikTok sudah tersedia di Indonesia tapi masih dianggap "aplikasi anak sekolah" oleh pelaku bisnis. Seorang founder brand makanan melihat pattern: penetrasi smartphone di tier 2-3 meningkat drastis, konten video pendek dikonsumsi lebih lama dari konten foto, dan engagement rate TikTok creator jauh lebih tinggi dari Instagram creator di titik yang sama dalam career-nya. Ia mulai memposting konten dan berkolaborasi dengan micro-creator TikTok saat harga masih sangat murah. Pada pertengahan 2021, ketika semua brand berlomba-lomba masuk TikTok dan biaya creator sudah 5-10x lebih mahal, brand ini sudah memiliki 800.000 followers organic dan network creator yang solid dengan harga pre-scaling. Pattern recognition = 18 bulan competitive advantage.
[/CASE_STUDY]

[INFOGRAPHIC:Cara Melatih Pattern Recognition]
Cross-Industry Reading | Baca tren dari industri yang berbeda — inovasi sering migrates dari satu sektor ke bisnis kamu
Weak Signal Detection | Perhatikan conversation kecil di forum niche, komunitas, dan media sosial sebelum mainstream
Historical Pattern | Pelajari bagaimana tren teknologi sebelumnya tumbuh — adopsi curve selalu mengikuti pola serupa
First Principles | Ketika hal-hal berubah, tanya "mengapa?" sampai ke root — bukan hanya apa yang berubah
[/INFOGRAPHIC]

[QUIZ:Apa yang membuat pattern recognition menjadi competitive advantage yang sangat powerful dalam bisnis?|Kemampuan ini memungkinkan akses ke informasi rahasia yang tidak tersedia untuk kompetitor|Kemampuan ini memungkinkan identifikasi tren lebih awal sehingga posisi diambil sebelum pasar menjadi penuh|Kemampuan ini menghilangkan risiko dari setiap keputusan bisnis karena tren sudah diprediksi dengan akurat|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Dedikasikan **30 menit untuk membaca** dari sumber yang tidak biasa kamu baca — laporan industri berbeda dari industrimu, forum komunitas niche, atau media internasional tentang tren teknologi. Identifikasi **satu pattern** yang kamu lihat yang mungkin relevan untuk bisnismu dalam 12-24 bulan ke depan. Tulis hipotesismu — meskipun belum ada data untuk memvalidasinya.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Decision Speed vs Accuracy',
                'order' => 9, 'xp' => 80, 'min' => 11,
                'content' => <<<MD
**Keputusan cepat vs keputusan benar — dan mengapa ini adalah dikotomi palsu** — banyak founder mengalami dua masalah berlawanan: ada yang terlalu lambat memutuskan karena menunggu data sempurna, sementara yang lain terlalu cepat memutuskan karena tidak nyaman dengan ambiguitas. Keduanya mahal. **Rule expert yang paling pragmatis untuk keputusan high-pace: putuskan ketika kamu memiliki 70% informasi yang cukup.**

Menunggu 100% data di dunia bisnis hampir selalu berarti terlambat — pasar bergerak, peluang menutup, dan kompetitor yang bersedia memutuskan lebih cepat mengambil posisi yang seharusnya milikmu.

[CASE_STUDY]
**Observasi Lapangan: Analysis Paralysis vs Decisive Action**
Dua competitor dalam industri e-learning lokal melihat peluang yang sama: konten video pendek berdurasi 60-90 detik mulai mendominasi konsumsi edukasi. Founder A: menghabiskan 4 bulan melakukan riset mendalam, focus group, dan analisis competitive sebelum memutuskan format baru. Founder B: dengan 65% keyakinan (bukan 100%), mulai bereksperimen dengan format baru dalam 2 minggu, iterasi berdasarkan data aktual. Ketika Founder A akhirnya launching, Founder B sudah punya 60 konten dengan data engagement nyata, sudah mengetahui format apa yang work, dan sudah membangun audience yang ekuivalen dengan 4 bulan pertumbuhan organik. Kecepatan keputusan dengan data yang cukup — bukan sempurna — adalah keunggulan kompetitif.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Decision Speed]
70% Rule | Ketika kamu memiliki 70% informasi yang relevan, itu cukup untuk memutuskan dan mulai iterasi
Reversible vs Irreversible | Keputusan yang bisa di-undo: decide quickly. Keputusan permanen: investasikan lebih banyak waktu
Cost of Delay | Hitung: apa biayanya jika keputusan ini tertunda 1 minggu, 1 bulan? Bandingkan dengan cost of wrong decision
Commit + Course Correct | Putuskan, eksekusi, monitor, dan adjust berdasarkan data real — lebih baik dari menunda tanpa batas
[/INFOGRAPHIC]

[QUIZ:Mengapa menunggu 100% data sebelum mengambil keputusan bisnis hampir selalu kontraproduktif?|Karena tidak ada keputusan bisnis yang membutuhkan lebih dari 70% data untuk dieksekusi dengan baik|Karena pasar bergerak lebih cepat dari pengumpulan data sempurna, sehingga peluang hilang sebelum keputusan dibuat|Karena pengumpulan data 100% membutuhkan biaya riset yang tidak proporsional dengan nilai keputusan|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Identifikasi **satu keputusan yang sudah terlalu lama kamu tunda** karena merasa belum cukup informasi. Tanya dirimu jujur: berapa % informasi yang sebenarnya sudah kamu miliki? Jika di atas 60% — buat keputusan itu hari ini dengan komitmen untuk monitor dan adjust dalam 2-4 minggu pertama eksekusi.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Strategic Framework Stack',
                'order' => 10, 'xp' => 90, 'min' => 13,
                'content' => <<<MD
**Arsenal mental tools yang membedakan pemikir strategis dari pemikir taktis** — founder level expert tidak membuat keputusan besar berdasarkan opini atau perasaan hari itu. Mereka memiliki **koleksi framework mental** yang dapat diaplikasikan secara cepat untuk menganalisis situasi dari berbagai sudut pandang. Framework bukan pengganti intuisi — ia adalah struktur yang memastikan intuisi yang tepat diaplikasikan pada situasi yang tepat.

Semakin banyak framework yang dikuasai, semakin kompleks masalah yang bisa kamu dekati dengan metodis.

[CASE_STUDY]
**Observasi Lapangan: Satu Framework yang Mengubah Arah Bisnis**
Founder agency digital menggunakan SWOT untuk evaluasi tahunan — tapi hasilnya selalu generik dan tidak actionable. Ketika ia menambahkan Unit Economics Framework ke arsenal-nya — menghitung LTV:CAC ratio untuk setiap segmen klien — ia menemukan bahwa 20% klien-nya menghasilkan 80% profit, sementara 40% klien terendah menghabiskan 60% waktu tim dengan margin negatif. Ini tidak terlihat dari SWOT. Dari insight Unit Economics, ia membuat keputusan untuk off-board klien yang tidak profitable dan fokus pada akuisisi segmen premium. Revenue turun 15% dalam 3 bulan tapi profit naik 180% karena hampir tidak ada margin leak.
[/CASE_STUDY]

[INFOGRAPHIC:5 Framework Wajib dalam Strategic Toolkit]
SWOT Analysis | Strengths / Weaknesses / Opportunities / Threats — untuk strategic review periodik
Unit Economics | LTV, CAC, Payback Period, Gross Margin per unit — untuk memvalidasi model bisnis
Leverage Map | Identifikasi titik di sistem bisnis yang menghasilkan dampak tertinggi per unit usaha
Risk Matrix | Probability × Impact grid untuk semua risiko yang teridentifikasi — prioritas mitigasi
Decision Scorecard | Objective scoring untuk membandingkan alternatif keputusan secara apple-to-apple
[/INFOGRAPHIC]

[QUIZ:Apa fungsi utama dari memiliki strategic framework stack yang beragam dalam pengambilan keputusan expert level?|Mengesankan investor dan stakeholder dengan pendekatan yang terlihat lebih akademis dan terstruktur|Memberikan perspektif yang berbeda pada situasi yang sama sehingga blind spot teridentifikasi sebelum keputusan diambil|Memperlambat proses pengambilan keputusan agar lebih hati-hati dan menghindari keputusan impulsif|1]

Selamat menyelesaikan modul **Strategic Decision Making & Business Intelligence** — level Expert! Kamu telah membangun arsenal framework yang membedakan pemikir taktis dari pemikir strategis. Final challenge:

[CHALLENGE]
**Final Strategic Challenge:**
Kamu memiliki budget Rp 20 juta yang harus dialokasikan ke SATU inisiatif: (A) Ads scaling, (B) Hiring staff baru, atau (C) Product development. Gunakan Opportunity Scoring System dari Lesson 4 untuk menilai ketiga opsi (Market, Margin, Speed, Skill Fit). Kemudian gunakan Revenue Forecasting dari Lesson 7 untuk memodelkan dampak revenue 3 bulan dari setiap opsi. Opsi mana yang menang secara framework? Apakah ini konsisten dengan intuisimu? Jika tidak — mengapa?
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

        $this->command->info('✅ Strategic Decision Making & Business Intelligence (Expert) — 10 lessons berhasil dimasukkan ke database.');
    }
}
