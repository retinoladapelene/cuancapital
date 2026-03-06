<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class SocialMediaOrganicSeed extends Seeder
{
    public function run(): void
    {
        Course::where('title', 'Digital Marketing — Social Media Organic')->delete();

        $course = Course::create([
            'title'         => 'Digital Marketing — Social Media Organic',
            'description'   => 'Kuasai strategi traffic organik: dari cara kerja algoritma, content pillar, hook visual, hingga growth strategy tanpa ads. Bukan teori — semua berbasis data dan eksekusi nyata.',
            'level'         => 'intermediate',
            'category'      => 'marketing',
            'xp_reward'     => 450,
            'lessons_count' => 10,
            'is_active'     => true,
        ]);

        $lessons = [

            [
                'title' => 'Cara Kerja Algoritma Sosial Media',
                'order' => 1, 'xp' => 45, 'min' => 9,
                'content' => <<<MD
**Mekanisme distribusi konten** — algoritma sosial media bukan kotak hitam misterius. Ia bekerja berdasarkan logika yang bisa dipelajari dan dieksploitasi secara sistematis. Setiap platform memberi konten kesempatan awal kepada sebagian kecil audiens, lalu mengukur respons mereka sebelum memutuskan apakah konten layak disebarkan lebih luas.

Tiga sinyal utama yang dinilai algoritma: **engagement rate**, **watch time**, dan **interaction speed**.

[CASE_STUDY]
**Observasi Lapangan: 10 Menit Pertama yang Menentukan Segalanya**
Sebuah akun edukasi bisnis melakukan eksperimen: mereka posting konten identik di dua akun berbeda, tapi di satu akun mereka meminta komunitasnya untuk berkomentar dalam 10 menit pertama. Hasilnya: akun dengan early engagement cepat mendapat reach **7x lebih besar** dalam 24 jam. Algoritma membaca interaction speed sebagai sinyal kualitas — konten yang langsung diperbincangkan dianggap relevan dan layak disebarkan.
[/CASE_STUDY]

[INFOGRAPHIC:3 Sinyal Utama Algoritma]
Engagement Rate | Rasio interaksi terhadap reach — like, komentar, save, share
Watch Time | Berapa lama orang menonton? Video yang ditonton 80%+ mendapat boost distribusi
Interaction Speed | Seberapa cepat orang berinteraksi setelah konten dipublish? Makin cepat, makin kuat sinyal
[/INFOGRAPHIC]

[QUIZ:Sinyal apakah yang paling kuat memberikan boost distribusi dalam algoritma sosial media modern?|Jumlah followers akun yang memposting konten|Jumlah engagement dan kecepatan interaksi di awal posting|Desain logo dan estetika feed profil|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Posting satu konten sekarang. Set timer 30 menit. Catat: berapa orang yang melihat, berapa yang like, berapa yang komentar, dan berapa yang share dalam 30 menit pertama. Ini adalah baseline engagement rate-mu — semua optimasi ke depan diukur dari sini.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Content Pillar Strategy',
                'order' => 2, 'xp' => 45, 'min' => 8,
                'content' => <<<MD
**Struktur konten yang konsisten** — akun yang tumbuh cepat tidak posting secara acak. Mereka memiliki **3–5 kategori konten tetap** (content pillar) yang membentuk identitas akun dan membangun ekspektasi audiens.

Konsistensi kategori membuat audiens tahu apa yang akan mereka dapatkan — dan algoritma tahu konten kamu relevan untuk siapa.

[CASE_STUDY]
**Observasi Lapangan: Akun 0 ke 50K dengan 4 Pilar**
Seorang kreator konten keuangan membangun akun dari nol dengan 4 pilar tetap: (1) Tips praktis keuangan, (2) Debunking mitos investasi, (3) Review produk keuangan, (4) Behind-the-scenes perjalanan finansialnya. Dalam 8 bulan, akun mencapai 50K followers. Audiens yang datang sudah tahu apa yang mereka subscribe — zero confusion, high retention.
[/CASE_STUDY]

[INFOGRAPHIC:Content Pillar Framework]
Edukasi | Konten yang mengajarkan sesuatu — membangun otoritas dan kepercayaan
Hiburan | Konten yang menghibur — meningkatkan watch time dan share rate
Promosi | Konten yang menjual — maksimal 20% dari total posting agar tidak dianggap spam
Inspirasi | Konten yang memotivasi — meningkatkan save rate dan emotional connection
[/INFOGRAPHIC]

[QUIZ:Berapa jumlah content pillar yang ideal untuk akun yang ingin tumbuh konsisten?|Cukup 1 pillar agar fokus dan tidak membingungkan|3 sampai 5 pillar untuk variasi yang cukup tanpa kehilangan identitas|Sebanyak mungkin agar konten selalu bervariasi|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Tentukan **4 pilar konten** untuk brand atau akun kamu. Untuk setiap pilar, tuliskan: nama kategori, tujuannya, dan 3 contoh ide konten yang bisa kamu buat dalam minggu ini. Ini adalah editorial calendar foundation-mu.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Hook Visual & First 3 Seconds',
                'order' => 3, 'xp' => 50, 'min' => 9,
                'content' => <<<MD
**Perhatian awal yang menentukan nasib konten** — di feed yang penuh dengan konten dari ratusan akun, user memutuskan untuk lanjut menonton atau scroll dalam **kurang dari 3 detik**. Jika 3 detik pertama gagal menarik perhatian, sisa kontenmu tidak akan pernah dilihat — sebagus apapun isinya.

Hook visual bisa berupa: teks besar yang provokatif, ekspresi wajah yang intens, gerakan cepat yang mengejutkan, atau suara yang tidak biasa.

[CASE_STUDY]
**Observasi Lapangan: 3 Detik yang Melipatgandakan Views**
Kreator konten kuliner membandingkan dua video dengan isi identik: cara membuat pasta. Video A dimulai dengan narasi slow: "Halo teman-teman, hari ini kita akan belajar..." Watch time average: 12%. Video B dimulai dengan close-up mozzarella meleleh dan teks besar "PASTA TERENAK 10 MENIT" langsung di frame pertama. Watch time average: 67%. Konten yang sama — hook yang berbeda — hasil yang 5x lebih baik.
[/CASE_STUDY]

[INFOGRAPHIC:Tipe Hook Visual yang Terbukti Bekerja]
Teks Provokatif | Kata-kata besar yang memancing rasa penasaran atau keterkejutan di detik pertama
Ekspresi Intens | Reaksi wajah yang kuat — tawa, syok, atau frustrasi — memicu mirroring emosi
Gerakan Cepat | Jump cut atau transisi cepat yang memberi sinyal "ada yang menarik di sini"
Pattern Interrupt | Sesuatu yang tidak biasa atau tidak terduga yang memaksa otak berhenti sejenak
[/INFOGRAPHIC]

[QUIZ:Apa yang paling kritis ditentukan oleh 3 detik pertama sebuah video konten?|Estetika keseluruhan feed dan konsistensi warna akun|Retention rate — apakah orang akan melanjutkan menonton atau scroll|Panjang caption yang akan ditulis di bawah video|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **3 konsep hook video** yang berbeda untuk konten berikutmu — satu menggunakan teks provokatif, satu menggunakan ekspresi atau reaksi, satu menggunakan pattern interrupt. Pilih yang paling kuat untuk dieksekusi besok.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Format Konten Viral',
                'order' => 4, 'xp' => 45, 'min' => 8,
                'content' => <<<MD
**Struktur konten yang terbukti perform** — ide yang bagus di format yang salah tidak akan viral. Sebaliknya, ide biasa-biasa saja yang dikemas dalam format yang terbukti bisa mendapat jutaan views. Format adalah infrastruktur yang menentukan bagaimana informasi diterima otak.

Format yang terbukti secara data: **list/listicle**, **tutorial singkat step-by-step**, **before/after transformation**, dan **myth vs fact**.

[CASE_STUDY]
**Observasi Lapangan: Format > Ide**
Seorang kreator konten finansial memposting analisis mendalam 800 kata tentang investasi reksa dana — engagement rendah. Video yang sama, dikemas ulang sebagai "5 Mitos Investasi yang Masih Dipercaya" — views 12x lebih tinggi, save rate 8x lebih tinggi. Informasi yang sama, format yang berbeda. Manusia secara kognitif lebih mudah memproses konten yang terstruktur dalam pola yang familiar.
[/CASE_STUDY]

[INFOGRAPHIC:4 Format Konten Tertinggi Engagement]
List | "7 cara...", "5 kesalahan..." — otak suka angka dan urutan yang jelas
Tutorial Singkat | Step 1 → Step 2 → Hasil — memenuhi kebutuhan pembelajaran dan memberikan nilai instan
Before/After | Transformasi visual — membuktikan hasil lebih kuat dari kata-kata manapun
Myth vs Fact | Debunking kepercayaan umum — memancing komentar dan share dari yang tidak setuju
[/INFOGRAPHIC]

[QUIZ:Faktor apa yang paling mempengaruhi performa sebuah konten di sosial media?|Penggunaan hashtag yang tepat dan relevan dengan topik|Format penyampaian yang memudahkan audiens untuk menyerap informasi|Panjang bio dan kelengkapan profil akun|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Ambil satu ide konten yang sudah lama kamu tunda. Ubah menjadi **format list** — minimal 5 poin, maksimal 10. Tulis draft-nya sekarang. Besok jadikan konten postingan atau video carousel.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Caption Copywriting',
                'order' => 5, 'xp' => 45, 'min' => 8,
                'content' => <<<MD
**Caption yang membuat orang berinteraksi** — caption bukan tempat untuk mendeskripsikan konten visual. Caption adalah **conversation starter** — ia harus membuat orang yang sudah berhenti scroll merasa ingin berkomentar, berdebat, atau berbagi pengalaman mereka.

Caption yang bagus selalu mengandung tiga elemen: **hook kalimat pertama** (yang muncul sebelum "more"), **micro-storytelling** (konteks yang relevan), dan **CTA komentar** yang spesifik.

[CASE_STUDY]
**Observasi Lapangan: CTA yang Menggandakan Komentar**
Sebuah akun motivasi bisnis mengubah semua caption-nya dari deskriptif menjadi conversational. Sebelum: "Tips sukses menjadi entrepreneur." Sesudah: "Saya hampir menyerah di bulan ke-6 bisnis pertama. Yang menyelamatkan saya hanya satu kebiasaan. Kamu pernah di titik yang sama? Cerita di komentar — saya baca semua." Komentar meningkat 9x dalam sebulan. Algoritma langsung memberikan boost distribusi karena comment velocity tinggi.
[/CASE_STUDY]

[INFOGRAPHIC:Struktur Caption yang Mengkonversi]
Hook Pertama | Kalimat pembuka yang muncul sebelum "more" — harus memaksa orang klik untuk baca lanjut
Micro-Story | 2-4 kalimat konteks yang relevan dan personal — bangun koneksi sebelum CTA
CTA Komentar | Pertanyaan spesifik yang mudah dijawab — "Mana yang paling relate?" lebih baik dari "Apa pendapatmu?"
[/INFOGRAPHIC]

[QUIZ:Tujuan utama caption dalam konten sosial media adalah?|Memberikan deskripsi formal tentang isi konten visual|Memancing interaksi dan percakapan yang meningkatkan engagement signal|Mengisi ruang kosong di bawah foto agar terlihat lengkap|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Tulis **satu caption** untuk konten berikutmu menggunakan 3 struktur di atas. Pastikan CTA-nya berupa pertanyaan yang spesifik dan mudah dijawab dalam 1-2 kalimat. Post dan ukur berapa komentar yang masuk dalam 2 jam pertama.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Konsistensi Posting vs Kualitas',
                'order' => 6, 'xp' => 45, 'min' => 8,
                'content' => <<<MD
**Mana yang lebih penting untuk growth?** — ini adalah perdebatan abadi di komunitas kreator. Jawabannya tidak romantis tapi jelas: **konsistensi mengalahkan kesempurnaan** dalam jangka panjang, terutama ketika akun masih dalam tahap pertumbuhan awal.

Algoritma menghargai akun yang dapat diprediksi dan konsisten memberikan sinyal engagement — bukan akun yang sesekali viral lalu menghilang selama dua minggu.

[CASE_STUDY]
**Observasi Lapangan: 5x Seminggu vs 1x Seminggu**
Dua akun dengan niche dan kualitas konten yang hampir identik memulai bersamaan. Akun A: posting 5 konten per minggu dengan kualitas "cukup baik". Akun B: posting 1 konten per minggu dengan kualitas sangat tinggi yang dikerjakan berhari-hari. Setelah 3 bulan: Akun A tumbuh ke 12K followers dengan engagement stabil. Akun B: 2.800 followers dengan engagement yang fluktuatif. Konsistensi membangun momentum yang tidak bisa digantikan oleh satu konten sempurna.
[/CASE_STUDY]

[INFOGRAPHIC:Framework Konsistensi yang Realistis]
Volume vs Quality | Di fase awal: volume + cukup baik mengalahkan jarang + sempurna
Batch Creation | Buat 5-7 konten dalam satu sesi lalu schedule — hindari kehabisan ide mendadak
Minimum Viable Content | Tetapkan standar minimum yang bisa kamu capai konsisten setiap minggu
[/INFOGRAPHIC]

[QUIZ:Faktor apa yang paling menentukan pertumbuhan akun dalam jangka panjang menurut data algoritma?|Kualitas konten yang sangat tinggi walau diposting jarang|Konsistensi frekuensi posting yang memberikan sinyal stabil ke algoritma|Jumlah hashtag yang digunakan per posting|0]

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **jadwal posting 7 hari ke depan** — tentukan hari dan jam posting, topik setiap konten, dan format yang akan digunakan. Komit pada jadwal ini sepenuhnya. Kalau tidak sempat membuat konten sempurna, buat yang "cukup baik" — tapi tetap posting di hari yang dijadwalkan.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Engagement Loop Strategy',
                'order' => 7, 'xp' => 50, 'min' => 9,
                'content' => <<<MD
**Cara memancing interaksi yang menguntungkan algoritma** — engagement loop adalah siklus di mana setiap interaksi yang kamu ciptakan menghasilkan lebih banyak distribusi, yang menghasilkan lebih banyak interaksi lagi. Tujuannya: membuat audiens merasa terlibat, bukan hanya menonton.

Strategi yang terbukti: pertanyaan di caption, polling interaktif, CTA komentar yang spesifik, dan — yang paling sering diabaikan — **reply komentar dengan cepat**.

[CASE_STUDY]
**Observasi Lapangan: Reply dalam 15 Menit Pertama**
Kreator konten menemukan pattern menarik: setiap kali ia membalas komentar dalam 15 menit pertama setelah posting, distribusi konten itu konsisten 3-4x lebih luas dibanding konten yang tidak di-reply segera. Mengapa? Karena setiap reply adalah notifikasi baru untuk commenter — dan notifikasi mendorong mereka kembali ke post, menciptakan interaction chain yang dibaca algoritma sebagai "konten ini aktif dan relevan."
[/CASE_STUDY]

[INFOGRAPHIC:4 Teknik Engagement Loop]
Tanya Pendapat | "Setuju atau tidak? Kenapa?" — paksa audiens untuk punya posisi
Polling Story | Binary choice cepat — friction rendah, partisipasi tinggi
CTA Spesifik | "Tag teman yang butuh dengar ini" — extend reach organik
Reply Cepat | Balas semua komentar dalam 15 menit pertama — feed the algorithm
[/INFOGRAPHIC]

[QUIZ:Mengapa membalas komentar dengan cepat setelah posting dapat meningkatkan distribusi konten?|Karena membuat akun terlihat lebih aktif di mata followers|Karena setiap reply menciptakan notifikasi yang membawa audiens kembali, meningkatkan interaction signal|Karena algoritma menghargai akun yang sopan dan responsif|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Posting konten sekarang atau ambil konten terakhirmu. Set alarm 15 menit. Ketika alarm berbunyi, **balas semua komentar yang masuk** — bahkan jika hanya emoji. Lakukan ini untuk 7 posting berturut-turut dan perhatikan perbedaan reach-nya.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Hashtag Strategy Modern',
                'order' => 8, 'xp' => 40, 'min' => 8,
                'content' => <<<MD
**Cara pakai hashtag yang benar di era modern** — hashtag bukan penentu viral, tapi ia adalah alat **discovery** yang membantu orang menemukan kontenmu di luar lingkaran followers yang sudah ada. Kesalahan terbesar: menggunakan semua hashtag yang paling populer dan berharap reach meledak.

Strategi yang benar: kombinasi **niche hashtag** (spesifik), **medium hashtag** (kategori), dan **broad hashtag** (topik umum).

[CASE_STUDY]
**Observasi Lapangan: Niche Tag yang Mengalahkan Viral Tag**
Akun fashion lokal menggunakan hashtag #fashion (900 juta posting) sebagai satu-satunya strategi. Reach dari hashtag: nyaris nol — konten tenggelam dalam detik. Setelah beralih ke kombinasi #fashionlokalindonesia (niche, 2 juta posting) + #fashionwanita (medium, 15 juta posting) + #fashion (broad), reach dari hashtag naik 12x. Konten menonjol di hashtag yang lebih kecil dan tepat sasaran.
[/CASE_STUDY]

[INFOGRAPHIC:Mix Hashtag yang Optimal]
Niche Tag (5-7) | Sangat spesifik ke topik dan target market — kompetisi rendah, relevansi tinggi
Medium Tag (5-7) | Kategori yang lebih luas — jangkauan lebih besar tapi masih targeted
Broad Tag (2-3) | Topik umum — exposure besar tapi kompetisi sangat tinggi, gunakan sparingly
[/INFOGRAPHIC]

[QUIZ:Strategi hashtag yang paling efektif untuk discovery di sosial media adalah?|Menggunakan semua hashtag viral dengan volume tertinggi|Kombinasi niche, medium, dan broad tag yang seimbang dan relevan|Tidak menggunakan hashtag sama sekali dan mengandalkan konten saja|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Riset dan susun **15 hashtag** untuk niche-mu: 6 niche tag (spesifik), 6 medium tag (kategori), dan 3 broad tag (umum). Simpan di notes sebagai "hashtag kit" yang siap dipakai, bukan dikopi-paste buta setiap hari — rotasi antar posting.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Analisis Insight Konten',
                'order' => 9, 'xp' => 45, 'min' => 9,
                'content' => <<<MD
**Membaca data performa konten** — kreator yang tumbuh cepat bukan yang paling kreatif, melainkan yang paling rajin membaca data dan menggunakannya untuk mengoptimasi keputusan berikutnya. Intuisi tanpa data adalah tebakan — data tanpa interpretasi adalah angka.

Empat metrik yang paling penting untuk dipahami: **reach**, **save**, **share**, dan **watch time**.

[CASE_STUDY]
**Observasi Lapangan: Save Rate Sebagai Prediktor Viral**
Seorang kreator edukasi menemukan bahwa konten dengan **save rate di atas 8%** hampir selalu mendapat distribusi organik yang signifikan dalam 48 jam berikutnya. Save adalah sinyal paling kuat ke algoritma: orang menyimpan konten ini karena mereka ingin kembali lagi — artinya konten ini memberikan value nyata. Ia mulai mengoptimasi setiap konten untuk save, bukan likes, dan follower growth-nya naik 3x.
[/CASE_STUDY]

[INFOGRAPHIC:Hierarki Metrik Konten]
Save | Sinyal value terkuat — audiens menganggap konten ini cukup berharga untuk disimpan
Share | Sinyal endorsement — audiens merekomendasikan ke jaringan mereka
Watch Time | Sinyal relevansi — audiens tidak meninggalkan konten di tengah jalan
Like & Comment | Sinyal populer tapi lebih mudah dipalsukan — jangan jadikan metrik utama
[/INFOGRAPHIC]

[QUIZ:Metrik mana yang paling akurat mengukur seberapa berharga sebuah konten bagi audiensnya?|Jumlah likes — karena mudah dilihat dan dimengerti semua orang|Save rate — karena orang menyimpan konten yang ingin mereka gunakan atau baca lagi|Total views — karena semakin banyak yang melihat semakin baik|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Buka insight akun sosial mediamu. Analisa **3 posting terakhirmu** — catat reach, save rate, share, dan watch time untuk masing-masing. Konten mana yang save rate-nya tertinggi? Apa polanya? Buat 1 konten baru minggu ini yang dirancang khusus untuk memaksimalkan save rate.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Growth Strategy Tanpa Ads',
                'order' => 10, 'xp' => 55, 'min' => 10,
                'content' => <<<MD
**Scale organik tanpa satu rupiah pun untuk iklan** — organic growth bukan berarti lambat. Dengan strategi yang tepat, akun baru bisa mencapai ribuan atau bahkan puluhan ribu followers dalam hitungan minggu — tanpa ads, tanpa beli followers, tanpa trik kotor yang berisiko di-shadowban.

Empat strategi yang terbukti paling efektif: **creator collaboration**, **UGC repost**, **trend hijacking**, dan **series content**.

[CASE_STUDY]
**Observasi Lapangan: 10K Followers dalam 30 Hari via Collab**
Akun edukasi bisnis baru berdiri dengan 200 followers. Bulan pertama, founder aktif berkolaborasi dengan 8 kreator di niche yang berdekatan — setiap collab dalam bentuk video duet, joint live, atau mention di caption. Setiap kreator memperkenalkan akun ini ke audiensnya. Dalam 30 hari: 10.200 followers dengan engagement rate 9% — jauh di atas rata-rata akun seumur. Kolaborasi adalah cara tercepat meminjam audiens orang lain secara sah.
[/CASE_STUDY]

[INFOGRAPHIC:4 Strategi Growth Organik Tercepat]
Creator Collab | Kolaborasi dengan akun niche serupa — akses audiens yang sudah tertarget
UGC Repost | Repost konten user yang mention kamu — social proof gratis dan membangun komunitas
Trend Hijacking | Gunakan format atau audio trending dengan sudut pandang niche-mu — riding existing traffic
Series Content | Konten berseri yang membuat audiens kembali — "Part 2" mendapat engagement lebih tinggi
[/INFOGRAPHIC]

[QUIZ:Strategi organic growth mana yang paling cepat memberikan exposure kepada audiens baru yang relevan?|Spam posting setiap jam untuk memaksimalkan visibility di feed|Kolaborasi dengan kreator lain yang memiliki audiens serupa|Mengganti logo dan nama akun agar tampak lebih fresh dan menarik|1]

Selamat menyelesaikan modul **Digital Marketing — Social Media Organic**! Setiap strategi di modul ini bisa dieksekusi mulai hari ini tanpa modal. Satu langkah terakhir:

[CHALLENGE]
**Action Plan Hari Ini:**
Identifikasi **5 akun** di niche-mu yang memiliki audiens serupa tapi tidak langsung bersaing denganmu. Analisa konten mereka, cari peluang kolaborasi yang saling menguntungkan, dan kirimkan DM yang personal dan spesifik — bukan template — ke setidaknya 2 di antaranya hari ini.
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

        $this->command->info('✅ Digital Marketing — Social Media Organic — 10 lessons berhasil dimasukkan ke database.');
    }
}
