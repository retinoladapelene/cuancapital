<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class BrandingIdentitasSeed extends Seeder
{
    public function run(): void
    {
        Course::where('title', 'Branding & Identitas Visual')->delete();

        $course = Course::create([
            'title'         => 'Branding & Identitas Visual',
            'description'   => 'Bangun merek yang kuat, mudah diingat, dan konsisten. Dari definisi branding modern, psikologi warna, tipografi, hingga brand guideline lengkap.',
            'level'         => 'beginner',
            'category'      => 'marketing',
            'xp_reward'     => 350,
            'lessons_count' => 10,
            'is_active'     => true,
        ]);

        $lessons = [

            [
                'title' => 'Apa Itu Branding Sebenarnya',
                'order' => 1,
                'xp'    => 40,
                'min'   => 8,
                'content' => <<<MD
**Definisi branding modern** — banyak orang mengira brand adalah logo, warna, atau nama. Mereka salah. Brand adalah **persepsi yang terbentuk di pikiran customer** saat mendengar namamu — sebelum mereka melihat produkmu, sebelum mereka membaca deskripsinya.

Kamu tidak sepenuhnya mengontrol brand-mu. Customer yang mengontrolnya. Tugasmu adalah memberikan input yang cukup konsisten agar persepsi itu terbentuk sesuai kehendakmu.

[CASE_STUDY]
**Observasi Lapangan: Kopi Biasa, Harga Luar Biasa**
Biji kopi Arabika dari ladang yang sama bisa dijual seharga Rp 30.000 di warung pinggir jalan, dan Rp 150.000 di kafe dengan brand premium. Biji kopi yang persis sama. Bedanya bukan rasa — bedanya adalah **persepsi yang dibangun oleh brand**. Ruangan yang nyaman, musik yang dipilih dengan cermat, gelas yang elegan, dan narasi "single origin" mengubah kopi biasa menjadi pengalaman yang berharga.
[/CASE_STUDY]

[INFOGRAPHIC:Formula Branding Modern]
Perception | Apa yang langsung terlintas di pikiran orang saat mendengar nama bisnismu?
Experience | Bagaimana rasanya berinteraksi dengan bisnismu — dari DM pertama hingga unboxing?
Brand | Perception + Experience yang konsisten dari waktu ke waktu = Brand yang kuat
[/INFOGRAPHIC]

[QUIZ:Brand sebenarnya terbentuk dari apa?|Desain logo yang dibuat oleh desainer profesional|Persepsi dan pengalaman yang terbentuk di pikiran customer|Pilihan warna dan nama yang tepat untuk bisnis|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Tuliskan **3 kata** yang kamu *inginkan* orang pikirkan saat mendengar nama brand kamu. Lalu tanyakan kepada 5 orang yang sudah tahu bisnismu — apa 3 kata yang *mereka* pikirkan? Gap antara dua daftar itu adalah pekerjaan rumah branding-mu.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Brand Positioning Strategy',
                'order' => 2,
                'xp'    => 40,
                'min'   => 9,
                'content' => <<<MD
**Menentukan posisi brand** — positioning adalah keputusan strategis tentang kamu ingin dikenal sebagai *apa* di benak market. Tanpa positioning yang jelas, brand-mu akan bergeser ke posisi default yang paling tidak menguntungkan: **tidak dikenal sama sekali**.

Positioning bukan tentang menjadi yang terbaik di segalanya — positioning adalah tentang memenangkan satu pertarungan persepsi yang sangat spesifik.

[CASE_STUDY]
**Observasi Lapangan: Sepatu yang Tidak Bersaing di Harga**
Sebuah brand sepatu lokal tidak bersaing dengan "paling murah" atau "paling mahal". Mereka memilih satu positioning yang sangat spesifik: **"Sepatu paling nyaman untuk dipakai seharian penuh."** Semua komunikasi, semua material, semua iklan berputar di sekitar satu klaim ini. Hasilnya: mereka tidak bersaing langsung dengan siapapun — mereka memiliki kategorinya sendiri.
[/CASE_STUDY]

[INFOGRAPHIC:Konsekuensi Positioning]
No Position | Tidak jelas → market tidak tahu kamu untuk apa → kalah sebelum bertanding
Wrong Position | Salah sasaran → pesan tidak relevan → buang budget marketing
Right Position | Tepat sasaran → uang iklan efisien → brand diingat tanpa perlu iklan besar
[/INFOGRAPHIC]

[QUIZ:Apa yang terjadi pada brand yang tidak memiliki positioning yang jelas?|Brand menjadi lebih fleksibel dan bisa menjangkau semua orang|Brand menjadi tidak jelas dan mudah dilupakan oleh target market|Brand akan viral secara organik karena bisa masuk ke semua segmen|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Tulis **1 kalimat positioning brand-mu** menggunakan formula: *"Brand [nama] adalah pilihan utama untuk [siapa] yang ingin [apa] tanpa [hambatan]."* Tempel kalimat ini di tempat yang kamu lihat setiap hari saat bekerja.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Brand Personality',
                'order' => 3,
                'xp'    => 40,
                'min'   => 8,
                'content' => <<<MD
**Karakter brand** — jika brand-mu adalah manusia, siapakah dia? Apakah dia seorang sahabat yang hangat dan humoris? Atau seorang konsultan profesional yang serius dan terpercaya? Atau seorang petualang yang berani dan energik?

Brand personality menentukan **bagaimana brand-mu berbicara, bereaksi, dan berinteraksi** dengan audiensnya di setiap titik kontak.

[CASE_STUDY]
**Observasi Lapangan: Brand Snack yang Playful**
Sebuah brand snack anak-anak memilih personality yang **playful, energik, dan penuh kejutan**. Tone copywriting-nya penuh eksklamasi dan kata-kata yang membuat anak-anak tertawa. Maskot mereka berbicara langsung kepada anak-anak, bukan kepada orang tua. Hasilnya: engagement media sosial 4x lebih tinggi dari kompetitor yang menggunakan tone formal dan "aman". Personality yang tepat menciptakan koneksi emosional yang tidak bisa dibeli dengan iklan biasa.
[/CASE_STUDY]

[INFOGRAPHIC:Rantai Nilai Brand Personality]
Personality | Karakter yang kamu pilih → menentukan cara kamu berkomunikasi
Emotion | Cara berkomunikasi yang konsisten → membangkitkan emosi spesifik pada audiens
Loyalty | Emosi positif yang berulang → pelanggan yang tidak mau pindah ke kompetitor
[/INFOGRAPHIC]

[QUIZ:Brand personality utamanya mempengaruhi hal apa dalam bisnis?|Pemilihan warna dan desain logo semata|Emosi yang dirasakan customer saat berinteraksi dengan brand|Penetapan harga dan struktur produk|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Pilih **3 sifat karakter** yang akan mendefinisikan brand-mu (contoh: berani, hangat, dan jujur). Untuk setiap sifat, tuliskan satu contoh konkret bagaimana sifat itu akan muncul dalam caption media sosial, cara membalas komentar, atau desain kontenmu.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Psikologi Warna Brand',
                'order' => 4,
                'xp'    => 45,
                'min'   => 9,
                'content' => <<<MD
**Warna mempengaruhi persepsi dan keputusan** — ini bukan takhayul desain. Ini neuroscience. Warna memicu respons emosional secara instan, jauh sebelum otak sadar memproses kata-kata atau pesan.

Biru membangun kepercayaan. Merah menciptakan urgensi. Hijau mengasosiasikan kesehatan dan alam. Hitam memancarkan kemewahan. Kuning membangkitkan optimisme. Pilih dengan sadar — bukan secara acak.

[CASE_STUDY]
**Observasi Lapangan: Biru dan Kepercayaan Finansial**
Perhatikan warna brand semua institusi keuangan besar dunia: bank, fintech, asuransi. Hampir semuanya menggunakan biru sebagai warna dominan — BCA, Mandiri, BNI, PayPal, Visa, Mastercard, American Express. Ini bukan kebetulan. Warna biru terbukti secara psikologis memberikan **rasa aman, stabil, dan terpercaya** — tiga hal yang paling dibutuhkan audiens saat mempercayakan uang mereka.
[/CASE_STUDY]

[INFOGRAPHIC:Asosiasi Warna dalam Bisnis]
Biru | Kepercayaan, keamanan, profesionalisme → cocok untuk finance, tech, kesehatan
Merah | Energi, urgensi, gairah → cocok untuk food, sale, olahraga
Hijau | Alam, kesehatan, pertumbuhan → cocok untuk wellness, organik, lingkungan
Hitam | Premium, elegan, eksklusif → cocok untuk luxury, fashion, teknologi
Kuning | Optimisme, hangat, perhatian → cocok untuk anak-anak, makanan, retail
[/INFOGRAPHIC]

[QUIZ:Warna hitam pada brand biasanya diasosiasikan dengan apa?|Produk yang harganya murah dan terjangkau|Kesan premium, elegan, dan eksklusif|Kesan yang lucu dan menyenangkan|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Pilih **palet warna utama brand-mu** — satu warna primer dan dua warna pendukung. Untuk setiap warna, tuliskan: *mengapa* warna ini dipilih dan *emosi apa* yang ingin ditimbulkan. Cek apakah palet ini konsisten dengan personality brand yang kamu tentukan di lesson sebelumnya.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Logo Design Principle',
                'order' => 5,
                'xp'    => 40,
                'min'   => 8,
                'content' => <<<MD
**Prinsip logo efektif** — logo adalah wajah pertama brand-mu. Tapi logo yang bagus bukan logo yang paling rumit atau paling banyak detail. Logo yang bagus adalah logo yang **bekerja** — di kartu nama ukuran 5cm, di billboard 5 meter, di favicon 16x16 pixel.

Empat kriteria logo efektif: **sederhana, scalable, memorable, relevan**.

[CASE_STUDY]
**Observasi Lapangan: Kesederhanaan yang Menang**
Nike Swoosh dibuat dalam beberapa jam oleh mahasiswi desain dan dibeli seharga 35 dolar. Apple adalah sebuah apel dengan gigitan. Google hanya teks berwarna. Ketiga logo paling dikenal di dunia memiliki satu kesamaan: **sangat sederhana**. Otak manusia mengingat bentuk sederhana lebih mudah dan lebih lama. Kompleksitas adalah musuh memorability.
[/CASE_STUDY]

[INFOGRAPHIC:4 Kriteria Logo yang Bekerja]
Sederhana | Dikenali sekilas — tidak butuh waktu untuk dipahami
Scalable | Terlihat bagus di ukuran apapun — dari favicon hingga baliho
Memorable | Mudah diingat setelah melihatnya sekali
Relevan | Merepresentasikan personality dan kategori bisnis dengan tepat
[/INFOGRAPHIC]

[QUIZ:Logo yang paling efektif untuk sebuah brand modern adalah logo yang?|Rumit dengan banyak detail untuk terlihat profesional|Menggunakan banyak warna agar terlihat colorful|Sederhana sehingga mudah dikenali dan diingat|2]

[CHALLENGE]
**Action Plan Hari Ini:**
Evaluasi logo brand-mu (atau draft logo yang ingin kamu buat) menggunakan **4 kriteria**: apakah sederhana, scalable, memorable, dan relevan? Beri skor 1-5 untuk masing-masing. Total di bawah 16? Saatnya revisit desainnya.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Typography Branding',
                'order' => 6,
                'xp'    => 40,
                'min'   => 8,
                'content' => <<<MD
**Font sebagai identitas visual** — tipografi bukan sekadar cara menampilkan teks. Font adalah **suara visual** brand-mu. Font yang dipilih secara sadar berbicara tentang kepribadian brand bahkan sebelum satu kata pun dibaca.

Serif (ada kait di ujung huruf) = elegan, klasik, berwibawa. Sans-serif (tidak ada kait) = modern, bersih, minimalis. Script = personal, kreatif, hangat.

[CASE_STUDY]
**Observasi Lapangan: Luxury dan Serif**
Perhatikan brand luxury fashion dunia: Chanel, Dior, Gucci, Tiffany & Co., Hermès. Hampir semua menggunakan **serif font** pada logo dan materi pemasaran mereka. Bukan kebetulan — serif memiliki sejarah panjang dalam tipografi formal dan publikasi prestisius, menciptakan asosiasi bawah sadar dengan keanggunan dan keahlian yang terbukti oleh waktu.
[/CASE_STUDY]

[INFOGRAPHIC:Karakter Tipografi]
Serif | Elegan, klasik, terpercaya, berwibawa → cocok untuk luxury, legal, media tradisional
Sans-Serif | Modern, bersih, minimalis, approachable → cocok untuk tech, startup, brand muda
Script | Personal, kreatif, hangat, artisanal → cocok untuk beauty, food, wedding, lifestyle
Display | Bold, ekspresif, berani → cocok untuk fashion, entertainment, brand yang ingin menonjol
[/INFOGRAPHIC]

[QUIZ:Font sans-serif memberikan kesan apa pada sebuah brand?|Klasik dan berwibawa seperti institusi lama|Modern, bersih, dan mudah didekati|Vintage dan penuh nostalgia|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Pilih **satu font utama** untuk brand-mu dari Google Fonts (gratis). Pastikan font ini: mencerminkan personality brand, mudah dibaca di ukuran kecil, dan tersedia dalam minimal 3 weight (regular, bold, light). Terapkan font ini di semua konten yang kamu buat minggu ini.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Konsistensi Visual',
                'order' => 7,
                'xp'    => 45,
                'min'   => 9,
                'content' => <<<MD
**Consistency rule** — konsistensi adalah senjata paling underrated dalam branding. Brand yang terlihat berbeda-beda di setiap platform menciptakan kebingungan. Brand yang konsisten menciptakan **familiarity** — dan familiarity adalah pendahulu dari kepercayaan.

Setiap kali seseorang melihat visual yang konsisten dari brand-mu, otak mereka satu langkah lebih dekat untuk mempercayai dan memilihmu.

[CASE_STUDY]
**Observasi Lapangan: Kekuatan Konsistensi Jangka Panjang**
McDonald's menggunakan kombinasi warna merah dan kuning, font yang sama, dan gaya visual yang hampir tidak berubah selama puluhan tahun di 100+ negara. Hasilnya: golden arches mereka **dikenali 88% dari seluruh populasi dunia** — lebih dari salib Kristen menurut beberapa survei. Konsistensi selama puluhan tahun mengalahkan kampanye kreatif yang paling brilian sekalipun.
[/CASE_STUDY]

[INFOGRAPHIC:Dampak Konsistensi vs Inkonsistensi]
Konsisten | Setiap platform terlihat unified → brand dikenali dengan cepat → kepercayaan tumbuh
Inkonsisten | Tampilan berbeda di setiap platform → audiens bingung → brand terasa amatir
Recognition Gap | Rata-rata butuh 7 kali paparan sebelum orang mengingat brand — inkonsistensi membuat hitungan ini mulai ulang
[/INFOGRAPHIC]

[QUIZ:Inkonsistensi visual pada brand dapat menyebabkan apa?|Brand terlihat lebih unik dan kreatif|Audiens menjadi bingung dan sulit mengenali brand|Brand menjadi viral karena selalu terlihat segar|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Lakukan **audit visual** semua platform brand-mu hari ini: Instagram, TikTok, WhatsApp Business, website, packaging, kartu nama. Apakah warna, font, dan tone-nya konsisten? List 3 ketidakkonsistenan yang paling mencolok dan rencana perbaikannya.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Brand Storytelling',
                'order' => 8,
                'xp'    => 45,
                'min'   => 9,
                'content' => <<<MD
**Cerita di balik brand** — manusia tidak mengingat fakta. Manusia mengingat cerita. Brand yang punya narasi yang kuat dan autentik memiliki keunggulan kompetitif yang hampir tidak bisa ditiru: **koneksi emosional dengan audiensnya**.

Storytelling tidak harus dramatis. Ia hanya perlu jujur, relevan, dan membuat audiens merasa terhubung.

[CASE_STUDY]
**Observasi Lapangan: Founder Story yang Membuat Brand Viral**
Sebuah brand skincare lokal yang baru 8 bulan berjalan mendadak viral — bukan karena produknya yang luar biasa, tapi karena **cerita founder-nya**. Seorang ibu rumah tangga yang berjuang dengan masalah kulit sensitif selama bertahun-tahun, gagal berbisnis dua kali, dan akhirnya berhasil setelah formulasi ke-47. Cerita itu dishare di TikTok, direspons jutaan orang yang merasa terhubung, dan menghasilkan antrian pre-order tanpa satu rupiah pun untuk iklan.
[/CASE_STUDY]

[INFOGRAPHIC:Anatomi Brand Story yang Kuat]
Origin | Mengapa brand ini lahir? Masalah apa yang ingin diselesaikan?
Struggle | Apa tantangan terbesar yang dihadapi? Kejujuran membangun kepercayaan.
Transformation | Bagaimana situasinya berubah? Apa yang dipelajari?
Mission | Mengapa brand ini terus ada hari ini? Apa tujuan yang lebih besar?
[/INFOGRAPHIC]

[QUIZ:Fungsi utama brand storytelling dalam bisnis adalah?|Memenuhi persyaratan formal presentasi bisnis|Membangun koneksi emosional antara brand dan audiensnya|Meningkatkan peringkat SEO website brand|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Tulis **cerita singkat brand-mu** maksimal 150 kata menggunakan struktur: *mengapa kamu mulai → tantangan terbesar yang kamu hadapi → apa yang berubah → misi brand-mu hari ini*. Ini akan menjadi pondasi bio, about page, dan pitch deck-mu.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Visual Hierarchy',
                'order' => 9,
                'xp'    => 40,
                'min'   => 8,
                'content' => <<<MD
**Urutan perhatian visual** — desain yang bagus bukan hanya yang cantik, tapi yang **mengarahkan mata audiens** secara natural dari elemen satu ke elemen berikutnya dalam urutan yang kamu inginkan.

Hierarki visual yang baik mengikuti pola: **Headline → Visual Utama → Subkopi → CTA**. Tanpa hierarki, semua elemen bersaing perhatian — dan tidak ada yang menang.

[CASE_STUDY]
**Observasi Lapangan: Landing Page dengan Hierarki yang Tepat**
Sebuah startup SaaS lokal membandingkan dua versi landing page dalam A/B test. Versi A: semua elemen berukuran hampir sama, semua informasi ditampilkan sekaligus. Versi B: headline besar dan berani, satu visual dominan, subkopi singkat, satu tombol CTA yang mencolok. Hasilnya: Versi B menghasilkan **conversion rate 3,4x lebih tinggi** dari Versi A. Hierarki yang jelas = uang yang nyata.
[/CASE_STUDY]

[INFOGRAPHIC:Alur Mata dalam Visual Marketing]
Headline | Elemen terbesar — tangkap perhatian pertama dalam 0.3 detik
Visual Utama | Foto/ilustrasi yang mendukung pesan — perkuat secara emosional
Subkopi | Penjelasan singkat — berikan konteks tanpa membebani
CTA | Tombol atau ajakan yang jelas — satu saja, tidak boleh lebih dari satu
[/INFOGRAPHIC]

[QUIZ:Visual hierarchy dalam desain marketing berfungsi terutama untuk?|Membuat desain terlihat lebih estetis dan artistik|Mengarahkan perhatian audiens secara berurutan menuju CTA|Mendekorasi konten agar terlihat lebih menarik|1]

[CHALLENGE]
**Action Plan Hari Ini:**
Ambil satu konten promosi atau postingan media sosial yang akan kamu buat selanjutnya. Sebelum mendesain, **sketsa wireframe-nya** (bisa di kertas): tentukan elemen mana yang paling besar, mana yang kedua, dan di mana CTA ditempatkan. Desain baru setelah hierarki jelas.
[/CHALLENGE]
MD,
            ],

            [
                'title' => 'Brand Guidelines',
                'order' => 10,
                'xp'    => 55,
                'min'   => 10,
                'content' => <<<MD
**Manual identitas brand** — brand guideline adalah dokumen yang memastikan siapapun yang menyentuh brand-mu — desainer, copywriter, mitra, karyawan baru — menggunakan identitas yang sama persis. Ia adalah penjaga konsistensi brand saat kamu tidak bisa hadir di setiap keputusan.

Brand besar seperti Nike, Apple, dan Google memiliki dokumen guideline setebal 20-100 halaman. Tapi kamu bisa mulai dari satu halaman yang kuat.

[CASE_STUDY]
**Observasi Lapangan: Ketika Tidak Ada Guideline**
Sebuah brand F&B lokal yang berkembang pesat mulai membuka cabang dan merekrut tim baru. Tanpa brand guideline, setiap cabang mulai menginterpretasi brand secara berbeda: warna yang sedikit berbeda, font yang diganti, tone komunikasi yang tidak konsisten. Dalam 6 bulan, brand yang tadinya terlihat cohesive mulai terasa seperti beberapa bisnis berbeda yang hanya berbagi nama yang sama. Kepercayaan pelanggan mulai goyah.
[/CASE_STUDY]

[INFOGRAPHIC:Komponen Brand Guideline Minimal]
Logo | Variasi logo, ukuran minimum, zona eksklusi, penggunaan yang dilarang
Warna | Kode HEX, RGB, CMYK untuk setiap warna dalam palet
Tipografi | Font primer dan sekunder, hierarki ukuran, penggunaan weight
Tone of Voice | Bagaimana brand berbicara — formal/informal, kata yang digunakan/dihindari
Visual Style | Gaya foto, ilustrasi, atau template konten yang mempertahankan konsistensi
[/INFOGRAPHIC]

[QUIZ:Brand guideline digunakan terutama untuk tujuan apa?|Disimpan sebagai arsip sejarah perusahaan|Memastikan konsistensi identitas brand di semua platform dan semua orang yang menyentuh brand|Dipresentasikan kepada investor sebagai bukti profesionalisme|1]

Selamat menyelesaikan modul **Branding & Identitas Visual**! Kamu kini memiliki semua komponen untuk membangun brand yang kohesif dan kuat. Langkah terakhir:

[CHALLENGE]
**Action Plan Hari Ini:**
Buat **draft brand guideline 1 halaman** menggunakan template sederhana (bisa di Canva atau Google Docs). Masukkan: logo (atau deskripsi logo), palet warna (kode HEX), font utama, 3 kata brand personality, dan 3 contoh tone of voice (satu kalimat untuk situasi yang berbeda). Satu halaman ini sudah cukup mengubah brand-mu dari amatir menjadi profesional.
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

        $this->command->info('✅ Branding & Identitas Visual — 10 lessons berhasil dimasukkan ke database.');
    }
}
