<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Course 1: Dasar Bisnis Digital ──────────────────────────────────────
        $course = Course::create([
            'title'          => 'Dasar Bisnis Digital',
            'description'    => 'Pelajari fondasi bisnis digital yang menghasilkan: pricing strategy, profit planning, dan digital funnel. Dirancang untuk pengusaha yang ingin bisnis berkelanjutan.',
            'level'          => 'beginner',
            'category'       => 'business-fundamentals',
            'xp_reward'      => 200,
            'lessons_count'  => 4,
            'is_active'      => true,
        ]);

        $lessons = [
            [
                'title'             => 'Kenapa Bisnis Kamu Jalan di Tempat?',
                'content'           => "Jujur saja, banyak pebisnis pemula merasa mereka sudah berdarah-darah bekerja setiap hari, tapi kok rasanya saldo rekening malah makin menipis? Masalah terbesarnya bukan karena kamu kurang keras berusaha, melainkan karena strategi *pricing* (penetapan harga) kamu salah dari awal.\n\n[CASE_STUDY]\n**Bencana Perang Harga:**\nAda sebuah toko hijab lokal yang laku keras setiap hari mengirimkan ratusan paket. Saking senangnya, sang Owner mengira dia kaya raya. Sayangnya di akhir bulan dia menunggak gaji karyawan. Kenapa? Karena dia menetapkan harga cuma beda Rp 5.000 dari modal, ikut-ikutan harga pabrik dari Tiongkok. Uang itu habis dipotong admin marketplace, biaya stiker bungkus, dan subsidi ongkir. Ramai sih, tapi buntung!\n[/CASE_STUDY]\n\nKamu nggak akan bisa memenangkan perang harga melawan konglomerat raksasa. Jangan jadi yang termurah, jadilah yang *paling bernilai* di mata pembelimu.\n\n[INFOGRAPHIC:3 Dosa Besar Pebisnis Pemula]\nMalas Ngitung | Harga pokok (COGS) cuma dihitung dari bahan baku, lupa ngitung lakban, listrik, dan ongkos lelah.\nSumbangan Startup | Ngebayarin diskon pelanggan dari kantong sendiri tanpa ngitung Return on Investment (ROI).\nButa Angka | Enggak hafal berapa minimal penjualan harian agar usahanya nggak gulung tikar (Break-Even Point).\n[/INFOGRAPHIC]\n\n[QUIZ:Kalau kita mencoba perang harga mati-matian menjadi yang paling murah se-Indonesia, siapa yang sebenarnya diuntungkan?|Kita dong, karena pasti laku keras tiap detik|Platform e-commerce dan pelanggan, sedangkan kita akan mati perlahan karena margin tipis berdarah-darah|Karyawan kita karena rajin nge-packing|1]\n\n[CHALLENGE]\nStop rebahan! Ambil kertas dan hitung satu produk terbaikmu. Apakah profit per barang (Margin) sudah di atas 30% setelah dipotong semua biaya siluman (admin platform, kardus)? Kalau masih di bawah 10%, kamu lagi kerja bakti, bukan berbisnis!\n[/CHALLENGE]",
                'order'             => 1,
                'type'              => 'text',
                'xp_reward'         => 30,
                'estimated_minutes' => 6,
            ],
            [
                'title'             => 'Pricing Strategy yang Bikin Merinding Market',
                'content'           => "Orang rela bayar Rp 60.000 untuk segelas Kopi Siren hijau, padahal di Warkop ujung jalan rasanya mirip dengan harga Rp 5.000. Itulah keajaiban yang namanya *Value-Based Pricing*.\n\n[CASE_STUDY]\n**Jurus Bundling Misterius:**\nBrand Skincare 'Glowing Terus' awal mulanya hampir bangkrut jualan sabun cuci muka Rp 50.000 per botol. Lalu mereka merubah strategi. Sabun itu tidak dijual satuan lagi, melainkan di-bundling dengan Krim Malam eksklusif jadi \"Paket 30 Hari Bebas Jerawat\" seharga Rp 250.000. Customer merasa membeli sebuah SOLUSI lengkap, bukan sekadar membeli sabun. Ajaibnya? Omzet mereka naik 400% di bulan pertama.\n[/CASE_STUDY]\n\nKamu nggak jualan produk, kamu menyembuhkan rasa sakit audiensmu. Kalau solusimu tepat sasaran, harga berapapun akan dibayar.\n\n[INFOGRAPHIC:Formula Harga Anti Melarat]\nHarga Modal (COGS) | Hitung modal jujur! 1 Jahitan baju, 1 meter kain, sampai 1 lembar plastik ziplock.\nHarga Solusi (Value) | Tambahkan Rp 50.000+ untuk Brand, Kemasan, Stiker, dan Rasa Aman si Pembeli.\nTeknik Psikologis | Pasang Harga Rp 199.000, jangan Rp 200.000. Otak manusia memproses angka depan 1 sebagai 'Lebih Murah Jauh'.\n[/INFOGRAPHIC]\n\n[QUIZ:Strategi mana yang terbukti ampuh secara psikologis membuat pelanggan ikhlas merogoh kocek lebih dalam?|Upload katalog produk jelek pakai kamera VGA|Jual eceran terus menaikkan harga tiap minggu|Menawarkan 'Paket Hemat/Bundling' yang membuat nilai gabungannya terasa jauh lebih murah dibanding beli satuan|2]\n\n[CHALLENGE]\Hapus semua harga cantik di tokomu. Ubah menjadi akhiran angka ganjil sakti (Misalnya 97.000, 149.000). Kemudian, gabungkan 2 produk terlarismu menjadi 1 Paket eksklusif bulan ini. Rasakan perbedaannya besok pagi!\n[/CHALLENGE]",
                'order'             => 2,
                'type'              => 'text',
                'xp_reward'         => 35,
                'estimated_minutes' => 7,
            ],
            [
                'title'             => 'Digital Funnel: Menangkap Ikan di Lautan Euforia',
                'content'           => "Pernah lihat iklan berseliweran di Instagram/TikTok tapi kamu langsung ngeskip? Nah, itulah *Traffic* yang gagal masuk ke *Funnel*. Funnel (Corong Penjualan) adalah seni merayu orang asing menjadi pelanggan garis keras.\n\n[CASE_STUDY]\n**Jebakan Batman Ala Digital:**\nKlinik Gigi X pasang iklan 'Perawatan Gigi' ke semua orang. Boncos. Akhirnya mereka bikin Funnel. Di tahap atas (Top Funnel), mereka kasih e-book gratis \"Cara Hilangkan Bau Mulut di Pagi Hari\". Ribuan orang download pakai e-mail. Di tahap tengah (Middle Funnel), mereka broadcast email isinya testimoni pemutihan gigi. Di tahap bawah (Bottom Funnel), mereka kasih diskon 30% cuci karang gigi eksklusif untuk pembaca email. Hasilnya? Klinik full booking sebulan!\n[/CASE_STUDY]\n\nInilah kejamnya Matematika Konversi (Conversion Rate). 1000 orang melihat Iklanmu, 100 orang klik link, 10 orang masuk keranjang, tapi cuma 2 yang transfer. \n\nTugas utamamu BUKAN mencari 1.000.000 orang numpang lewat. Tugasmu adalah merubah angka kecil konversi menjadi 5 kali lipat.\n\n[QUIZ:Apa fungsi dari metrik 'Conversion Rate' dalam mesin penjulanmu?|Buat ngukur seberapa banyak Likes & Komen palsu di postingan kita|Untuk menghitung persentase nyata berapa orang yang AKHIRNYA membeli dari total orang yang berkunjung ke tokomu|Sekadar angka keren buat dipasang di presentasi investor|1]\n\n[CHALLENGE]\nBuka dashboard Instagram / Tiktok mu hari ini. Bandingkan Total Profile Visits dengan link di Bio yang diklik. Kalau ada 1000 Visits tapi cuma 5 yang klik link, artinya Bio kamu kurang menarik. Ganti text Bio-mu dari \"Jual Baju Murah\" menjadi \"Klik Link ini untuk Klaim Diskon Ongkir Pertamamu!\".\n[/CHALLENGE]",
                'order'             => 3,
                'type'              => 'text',
                'xp_reward'         => 40,
                'estimated_minutes' => 9,
            ],
            [
                'title'             => 'Cetak Biru 90 Hari: Jangan Jadi Pemimpi',
                'content'           => "Resolusi tahun baru itu sampah kalau nggak ada *Action Plan* matematis. Bisnis skala dunia beroperasi menggunakan siklus kuartalan (90-Days Cycle). Kenapa? Karena 1 tahun terlalu panjang untuk dievaluasi, dan 1 minggu terlalu pendek untuk melihat pergerakan.\n\n[CASE_STUDY]\n**Keajaiban Rencana Harian:**\nDaripada bermimpi kosong *\"Gue mau omzet 1 Milyar tahun ini\"*, Founder SaaS pintar memecahnya jadi kecil: *\"Gue butuh 1 Milyar setahun. Artinya 83 Juta sebulan. Kalau harga produk gue 1 Juta, gue BUTUH 83 Closing per bulan. Berarti gue HANYA BUTUH 3 orang closing per hari\"*. Tiba-tiba, 1 Milyar yang mustahil itu terdengar sangat gampang, bukan? Cuma cari 3 orang sehari!\n[/CASE_STUDY]\n\nPecah batu besar targetmu menjadi kerikil-kerikil kecil yang gampang dikunyah tiap hari.\n\n[INFOGRAPHIC:Siklus Mesin Uang 90 Hari]\nBulan 1: Validasi Berdarah | Hajar tester market. Jual sana-sini organik. Buktikan ada 10 O-R-A-N-G a-s-i-n-g yang mau transfer uangnya ke rekeningmu.\nBulan 2: Injeksi Skala | Punya winning produk di Bulan 1? Siram bensin pakai Ads (Iklan) secara brutal di bulan ke dua ini.\nBulan 3: Sistemasi Waktu | Penjualan mulai meledak? Rekrut asisten CS/Packing. Keluarlah dari penjara pekerjaan rendahan.\n[/INFOGRAPHIC]\n\n[QUIZ:Mengapa framework target 90 Hari jauh lebih terbukti berhasil dibanding nunggu pergantian Tahun Baru?|Bisa ngadain party tiap akhir bulan bayar pake duit kantor|Kuartal (90 Hari) sangat pas untuk menjaga ritme kerja tetap nge-gas tapi ngasih waktu cukup untuk otak menganalisa data tanpa tergesa-gesa|Cuma mitos bule aja sih, mending jalanin aja kek air mengalir|1]\n\n[CHALLENGE]\nSelamat, kamu telah menyelesaikan Fundamental Bisnis Digital!\n\nSaatnya eksekusi sungguhan. Buka kembali halaman Dashboard utama CualiCapital milikmu. Cari alat \"Simulator Reverse Goal\" atau \"Mentor Lab\". Tulis impian 90 harimu disana, dan biarkan AI kita ngebantu bedah hancur-hancurannya berapa budget modal yang WAJIB kamu siapkan besok pagi!\n[/CHALLENGE]",
                'order'             => 4,
                'type'              => 'text',
                'xp_reward'         => 50,
                'estimated_minutes' => 10,
            ],
        ];

        foreach ($lessons as $lessonData) {
            $course->lessons()->create($lessonData);
        }

        $this->command->info('✅ CourseSeeder: 1 course + 4 lessons seeded.');
    }
}
