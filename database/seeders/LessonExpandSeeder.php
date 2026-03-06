<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Lesson;

/**
 * Adds 10 additional lessons (order 5–14) to the existing "Dasar Bisnis Digital" course.
 * Safe to run even if CourseSeeder has already been run — checks by course title.
 */
class LessonExpandSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('title', 'Dasar Bisnis Digital')->first();

        if (!$course) {
            $this->command->error('Course "Dasar Bisnis Digital" tidak ditemukan. Jalankan CourseSeeder terlebih dahulu.');
            return;
        }

        $newLessons = [
            [
                'title'             => 'Customer Acquisition Cost (CAC) & LTV',
                'content'           => "Sering ngerasa udah bakar duit banyak buat iklan tapi kok ujungnya margin tipis banget? Masalahnya biasanya ada di dua angka metrik ini: CAC dan LTV.\n\n[CASE_STUDY]\n**Kisah Brand Sepatu Lokal:**\nDulu mereka ngabisin Rp 150.000 buat dapet 1 pembeli (CAC = 150k). Padahal profit per sepatu cuma Rp 100.000. Jelas tekor tiap hari! Tapi pas mereka bikin program member (loyalty), pembeli yang tadinya beli 1x setahun jadi beli 3x setahun. LTV mereka naik jadi Rp 300.000. Tekor di awal, tapi untung berlipat di akhir.\n[/CASE_STUDY]\n\nKamu harus hitung total biaya marketing dibagi total pelanggan baru buat dapet CAC asli. Ingat, *LTV harus 3x lebih besar dari CAC* kalau mau bisnis kamu sehat dan scalable.\n\n[INFOGRAPHIC:Rules of Thumb: LTV & CAC]\nRasio Maut (1:1) | Berada di ujung tanduk kebangkrutan, hentikan iklan segera.\nRasio Standar (3:1) | Bisnis sehat, setiap Rp 10.000 yang keluar kembali jadi Rp 30.000 profit.\nRasio Emas (5:1) | Terlalu under-spending. Waktunya nge-gas modal iklan biar makin dominan.\n[/INFOGRAPHIC]\n\n[QUIZ:Kalau LTV pelangganmu adalah Rp 500 Ribu, berapa maksimal biaya CAC yang aman agar bisnismu masih sangat sehat (Rasio 3:1)?|Rp 50 Ribu|Rp 160 Ribu|Rp 400 Ribu|1]\n\n[CHALLENGE]\nBuka akun Meta/TikTok Ads-mu! Coba cek fitur 'Cost per Purchase' bulan ini. Kalau angkanya lebih besar dari Net Profit produkmu, langsung pause iklannya hari ini juga dan perbaiki funnel atau harganya!\n[/CHALLENGE]",
                'order'             => 5,
                'type'              => 'text',
                'xp_reward'         => 30,
                'estimated_minutes' => 8,
            ],
            [
                'title'             => 'Break-Even Point: Kapan Bisnis Mulai Untung?',
                'content'           => "Nanya santai: Kamu tahu persis gak di angka penjualan ke-berapa bisnismu bulan ini nutup kerugian (bayar sewa, gaji, dkk)? Kalo nggak tau, bahaya bro.\n\nItu namanya Break-Even Point (BEP). Angka sakti dimana kamu nggak rugi, tapi juga belom untung.\n\n[CASE_STUDY]\n**Jebakan Warung Kopi Estetik:**\nSi Budi buka coffe shop sewa ruko 10Jt/bln, gaji barista 5Jt/bln. Fixed costnya 15Jt. Harga kopi 25rb, modalnya 10rb (Untung 15rb/gelas). \nBudi harus kejual 1.000 gelas sebulan (33 gelas/hari) *hanya untuk nutup modal*. Lucunya Budi baru tau perhitungan ini pas usahanya udah jalan 3 bulan dan duitnya kedodoran.\n[/CASE_STUDY]\n\n[INFOGRAPHIC:Anatomi Breakdown BEP]\nBiaya Tetap (Fixed Cost) | Biaya yang ADA walau toko tutup (Gaji Karyawan Tetap, Sewa Ruko, Tagihan Server).\nBiaya Variabel (Variable Cost) | Biaya HPP yang ikut naik kalo jualan naik (Cup Kopi, Sedotan, Label).\nMargin Kontribusi | Harga Jual dikurangi Biaya Variabel. Inilah untung bersih yang dipakai buat nutupin Biaya Tetap.\n[/INFOGRAPHIC]\n\n[QUIZ:Kamu cuma butuh ngecek 2 jenis biaya ini buat ngitung BEP. Biaya apa aja tuh?|Biaya Iklan & Biaya Ongkir|Fixed Cost & Variable Cost|Biaya Sewa Ruko & Biaya Beli Kopi|1]\n\n[CHALLENGE]\nTulis di kertas: 1. Berapa total biaya wajib bulananmu walau nggak ada penjualan (Sewa Pintu, Gaji, Langganan Software). 2. Berapa net profit dari 1 produkmu. Bagi angka poin 1 dengan poin 2. Dapet tuh target jualan minimunmu bulan ini!\n[/CHALLENGE]",
                'order'             => 6,
                'type'              => 'text',
                'xp_reward'         => 30,
                'estimated_minutes' => 7,
            ],
            [
                'title'             => 'Memahami Cash Flow vs Profit',
                'content'           => "Seorang mentor pernah bilang: *\"Profit itu opini akuntan, Cashflow itu pakta perbankan.\"* Banyak CEO muda bangga perusahaannya profit ratusan juta di kertas, tapi buat bayar gaji karyawan bulan depan kelimpungan nyari utangan.\n\n[CASE_STUDY]\n**Bencana Tempo Pembayaran:**\nVendor B2B A bikin proyek senilai 100 Juta, modalnya cuma 40 Juta. Profit 60 Juta dong? Keren! Eh, masalahnya klien minta *Term of Payment* 3 Bulan mundur. Selama 3 bulan itu vendor A nggak punya uang cash buat muterin modal proyek lain. Bisnisnya *stuck* karena cash flow mandek.\n[/CASE_STUDY]\n\nJangan cuma ngeliat margin, tapi perhatikan jeda hari uang muter.\n\n[INFOGRAPHIC:3 Pilar Pertahanan Uang Tunai]\nKas Masuk Lebih Cepat (Quick In) | Paksa Term Payment lebih cepat, kasih diskon jika bayar lunas dimuka.\nKas Keluar Lebih Lambat (Delay Out) | Nego tenor bahan baku ke vendor menjadi mundur 30-45 hari kedepan.\nCek Saldo Harta Liquid | Pastikan selalu sedia Minimal 3x Burn Rate bulanan di Rekening Utama.\n[/INFOGRAPHIC]\n\n[QUIZ:Cara termudah buat bikin Cash Flow bisnis tetap positif di awal bulan adalah...|Minta DP 50% di awal ke klien / Nego cicilan PO ke Pabrik|Naikin harga jual gila-gilaan|Bakar uang VC (Investor)|0]\n\n[CHALLENGE]\nBuka laporan Bank-mu sekarang. Pastikan total uang CASH yang kamu pegang (Cash on Hand) sanggup menghidupi operasional bisnismu (Runway) setidaknya selama 3 hingga 6 bulan ke depan walau revenue nol. Kalo kurang, stop ngambil proyek B2B jangka panjang dlu.\n[/CHALLENGE]",
                'order'             => 7,
                'type'              => 'text',
                'xp_reward'         => 30,
                'estimated_minutes' => 9,
            ],
            [
                'title'             => 'Unit Economics: Profitabilitas Per Transaksi',
                'content'           => "Nembak iklan jutaan rupiah sebelum hitung *Unit Economics* itu sama kayak buang garam ke laut. Pastiin porsinya dapet.\n\n[CASE_STUDY]\n**Jualan Rame Tapi Rugi:**\nSebuah brand ngejual baju harga 100k. Mereka pikir biaya produksinya cuma 35k (Margin 65k). Nyatanya, belum ngitung admin fee Tokped (5k), packaging + bubble wrap (3k), dan biaya ngiklan per 1 checkout (30k). Sisa bersihnya cuma 27k. Pantas usahanya nggak gede-gede.\n[/CASE_STUDY]\n\n[INFOGRAPHIC:Bocor Halus di Level Satuan]\nCOGS (HPP Asli) | Dihitung dari seluruh bahan baku riil tanpa ampun (contoh: Biaya Bubblewrap + Isolasi masuk ke Cogs).\nPlatform & Gateway Fee | Pajak e-commerce & bank admin per satu kali transaksi transfer sukses (Mis: 4.5% + 5000).\nAkuisisi Iklan Maksimal | Jangan memotong margin lebih dari separuhnya hanya demi akuisisi front-end.\n[/INFOGRAPHIC]\n\n[QUIZ:Kenapa kita perlu ngitung Unit Economics per produk, bukannya langsung di laporan bulanan total?|Biar kelihatan rumit aja laporannya|Supaya bisa nemu produk mana yang boncos saat dijual (Leakage)|Buat di-post di linkedin|1]\n\n[CHALLENGE]\nAmbil produk best seller kamu, bedah struktur biayanya PAAALLLING detail sampai sedotan plastiknya kalau perlu. Kalau *Net Margin* produk tunggal itu dibawah 20%, siap-siap naikin harga atau ganti supplier besok pagi.\n[/CHALLENGE]",
                'order'             => 8,
                'type'              => 'text',
                'xp_reward'         => 35,
                'estimated_minutes' => 10,
            ],
            [
                'title'             => 'Membangun Sistem Penjualan Berulang',
                'content'           => "Bisnis yang ngandalin *\"Yaudah posting aja, moga-moga FYP terus rame beli\"* itu bukan bisnis, itu main judi. Kamu butuh sistem penjualan yang angkanya bisa di-prediksi (Predictable Sales System).\n\n[CASE_STUDY]\n**Formula Sales Funnel Ekstrim:**\nKelas online A dulu ngandalin postingan organin Instagram aja. Ada saatnya rame, ada saatnya sepi banget sampe ga bisa bayar server. \nAkhirnya mereka bikin Sistem: Bikin E-Book Gratis (Lead Magnet) > diiklanin (Trafik) > dapet 100 Email Per Hari > di-follow up otomatis selama 3 Hari via Email > 10% orang yang baca akhirnya closing Beli Kelas Utama. Boom! Mereka sekarang tahu persis berapa yang bakal checkout besok.\n[/CASE_STUDY]\n\n[INFOGRAPHIC:Anatomi Mesin Closing]\nKolam Atas (Traffic) | Membakar uang iklan (Ads) atau bikin mass-content biar jutaan mata nengok brandmu.\nKolam Tengah (Lead Magnet) | Merubah Penonton gratisan menjadi Data base nomer Whatsapp (Prospek) bermodalkan Free Trial/Bonus.\nKolam Bawah (Conversion) | Flow Edukasi CS dan Skrip Follow Up Otomatis yang ramah mencetak Transaksi Deal.\n[/INFOGRAPHIC]\n\n[QUIZ:Komponen mana yang BERFUNGSI sebagai 'perangkap manis' (jebakan) agar pengunjung mau menyerahkan data WhatsApp/Email-nya secara sukarela?|Sales Page|Closing Team|Lead Magnet (Kupon diskon / E-book Gratis)|2]\n\n[CHALLENGE]\nCoba pikirkan 1 materi edukasi PDF 1-2 Halaman (SOP, Checksheet, Katalog Harga Rahasia) yang paling bikin target audiencemu 'ngiler'. Bikin PDF-nya dalam waktu 2 jam, dan pakai hal itu sebagai penukar Whatsapp mereka di Bio Instagrammu.\n[/CHALLENGE]",
                'order'             => 9,
                'type'              => 'text',
                'xp_reward'         => 35,
                'estimated_minutes' => 10,
            ],
            [
                'title'             => 'Content Marketing Terstruktur',
                'content'           => "Iklan itu ibarat nyewa rumah, begitu kamu stop bayar bulanan, kamu diusir (trafik stop). Konten organik itu ngebangun rumah (Aset jangka panjang).\n\n[CASE_STUDY]\n**Skema Repurposing Ninja:**\nAgensi kreatif B nggak punya banyak resource bikin konten harian. Jadi tiap Jumat Bossnya cuma rekaman ngoceh depan kamera selama 1 jam. Tim videonya motong 1 jam itu jadi 15 video pendek Reels/TikTok. Terus tim copywriternya nyalin intisari omongan jadi sebuah artikel Blog (SEO) & 5 utas Twitter (X). Hasilnya: 1x Kerja -> Tersebar ke 4 Platform Penuh Seminggu!\n[/CASE_STUDY]\n\nProporsi yang pas buat konten jualan (biar audience nggak ngerasa muak):\n\n[INFOGRAPHIC:Piramida Konten Seimbang]\nEdukasi (60%) | Ajari audiens menyelesaikan 1 masalah kecil gratis. Bangun Authority!\nHiburan/Relatable (30%) | Tunjukkan meme, drama kantor, atau curhatan yang bikin penonton merasa 'Relate Banget'.\nJualan Keras (10%) | Diskon kilat, rilis produk baru, dan Promo Hard Selling (FOMO).\n[/INFOGRAPHIC]\n\n[QUIZ:Apa jebakan utama (Vanity Metric) yang sering menipu para pebisnis online pemula saat buat konten?|Interaksi yang rame doang (Likes jutaan) tapi Konversi Closing Link-nya nol besar|Males bikin Script|Kualitas videonya kayak resolusi 144p|0]\n\n[CHALLENGE]\nSetop dulu buka TikTok buat scrolling! Ambil kertas kosong. Rancang konten pilar minggu depan: 3 Tema Edukasi, 1 Tema Cerita Bisnismu (Behind the Scene). Jangan rekam dulu sebelum framework 4 video ini beres ditulis draftnya.\n[/CHALLENGE]",
                'order'             => 10,
                'type'              => 'text',
                'xp_reward'         => 30,
                'estimated_minutes' => 8,
            ],
            [
                'title'             => 'Email & WhatsApp Retention Marketing',
                'content'           => "Kamu nggak akan pernah memiliki Followers Tiktok atau Instagrammu. Besok akunmu kena Report Kompetitor dan masuk pusara Shadowbanned, lenyap semua bisnis triliunanmu. Oleh karenanya, boyong mereka ke \"Rumahmu\" sendiri (Email / WhatsApp list).\n\n[CASE_STUDY]\n**Keajaiban Open Rate Retensi:**\nKetika Instagram Reels cuma nyebarin kontenmu ke 3% followers aslimu, Broadcast WhatsApp dan Email List mu akan dibaca oleh 90% (WA) dan 20% (Email) database mu pada hitungan detik pertama rilis. Merek fashion C yang berhasil memanen 10.000 kontak Whatsapp dari event Harbolnas lalu, hanya butuh modal Rp 0 biaya Ads untuk ngehasilin Rp 50Jt via blast promosi End-Year Sale.\n[/CASE_STUDY]\n\n[INFOGRAPHIC:Membangun Kolam Milik Sendiri]\nPancingan Gizi Tinggi | Kasih Ebook Gratis / Kupon 50% di Link Bio untuk ditukar dengan Nomor WA real audiens.\nBelaian Hangat | Jangan jualan di hari pertama, kirim tips & trik bermanfaat 2 Hari berturut-turut.\nPanen Promo | Hari ke-3 sampai 5, sodorkan Flash Sale batas waktu. Boom! Mesin ATM pribadimu bekerja.\n[/INFOGRAPHIC]\n\n[QUIZ:Apa alasan terkuat memindahkan audiens sosmed ke Database pribadi?|Biar bisa nge-SPAM mereka tiap hari jam 2 pagi secara barbar|Supaya aman dari risiko dibanned sepihak oleh Platform Sosial Media & Zero Cost Reklame|Ga ada alasan, cuma iseng biar kelihatan profesional|1]\n\n[CHALLENGE]\nJangan numpuk chat leads kamu secara pasif. Lakukan ekspor massal pembeli 3 Bulan lalu dari Dashboard marketplace (Bila ada data no WA), save dalam format Excell/GSheets dan buat folder \"Kolam Air Hangat\". Mereka adalah sasaran tembak empuk saat perilisan produk barumu nanti.\n[/CHALLENGE]",
                'order'             => 11,
                'type'              => 'text',
                'xp_reward'         => 30,
                'estimated_minutes' => 9,
            ],
            [
                'title'             => 'Paid Advertising: Kalkulasi Skalabilitas',
                'content'           => "Iklan berbayar (Paid Ads) itu ibarat Bensin Premium tingkat Oktan Tinggi. Tolong jangan siram bensin ke kayu yang masih basah (Offer/Produkmu yang jelek/belum tervalidasi). Keringin dulu kayunya pakai organik flow, baru digebyur api Ads biar meledak.\n\n[CASE_STUDY]\n**Fase Testing Kematian:**\nAgan D ngiklan nembak pasar produk skin-care sebesar 5 Juta Ripiah sekaligus di hari pertama. ROAS hancur di 0.5 (Ngiklan 5 Jt closing cuma 2.5 Jt). Mengapa? karena Agan D gatau mana Poster/Video kreatif yang disukai market, yang nentuin \"Tuh kan videonya bagus\" ya CUMA DIRINYA SENDIRI.\nSeharusnya, testing budget 50ribuan x 5 Video Beda di hari pertama. Liat aja CTR mana yg paling gedhe dan CPA termurah, matikan sisanya, dan siram sisa budget ke kampanye video pemenang (Winning Campaign) tersebut.\n[/CASE_STUDY]\n\n[INFOGRAPHIC:Tahapan Skala Iklan Anti Boncos]\nTesting A/B | Coba 5 Video + 5 Copywriting berbeda dengan modal Rp 50.000 per hari.\nMatikan Pecundang (Kill Loser) | Hari ke-3, matikan kampanye yang cost per click (CPC) nya terlalu mahal.\nSiram Bensin (Scaling) | Gandakan budget HARIAN secara eksponensial HANYA di 1 video terbaik (Winning Campaign).\n[/INFOGRAPHIC]\n\n[QUIZ:Metric / Angka yang HARUS dipelototin pertama di Dashboard Ads untuk menilai 'Apakah Gambar Iklan Saya Menarik Dibaca atau Enggak?' adalah....|CPA (Cost per Action)|CPM (Cost per Mille)|CTR (Click-Through Rate)|2]\n\n[CHALLENGE]\nCoba install 'Facebook Pixel Helper' di ekstension browser kamu besok pagi, lalu masuk ke Landing Page atau web kompetitor industrimu. Pelajari event rekaman konversi apa aja yang lagi mereka sadap secara ghaib dari para lead-lead nya.\n[/CHALLENGE]",
                'order'             => 12,
                'type'              => 'text',
                'xp_reward'         => 35,
                'estimated_minutes' => 10,
            ],
            [
                'title'             => 'Delegasi Fundamental & Operasional Skala',
                'content'           => "Kalau kamu kerja 16 jam perhari ngurus balasan chat, packing kardus, bikin video, itu kamu bukan Businessman, tapi 'Pegawai Bergaji Rendah' untuk Dirimu Sendiri. Kamu harus mulai membeli waktu (Buy Back Your Time).\n\n[CASE_STUDY]\n**Ngos-ngosan Mikromanajemen:**\nPak Eko merasa dia super genius, gak ada satupun Customer Service yang balas chat se-ramah dirinya. Akhirnya dia balasin ribuan chat tiap hari. Imbasnya? Dia ga punya otak lagi buat mikirin riset ekspansi pembukaan pabrik cabang kedua yang valuasi untungnya milyaran, gara2 sibuk ngurusin chat bawel komplain resi 30ribu. Pak Eko ini bodoh atau bijaksana?\n[/CASE_STUDY]\n\nKalkulasi Biaya Cerdas (Hitung Valuasi per Jam Kamu):\n```\n(Target Net Profit Bulanan Rp 50.000.000) ÷ (Asumsi Kerja 160 Jam)\n= Harga per Jam Waktumu adalah Rp 312.500 / Jam\n```\n\n[INFOGRAPHIC:Beli Kembali Waktumu]\nDokumentasi Total. | Rekam Layar komputermu saat ngerjain sesuatu berulang (CS, Posting). Jadikan Video SOP.\nDelegasi Level 1. | Pekerjakan part-timer murah Rp 50rb/Hari HANYA untuk ngerjain Task dari Video SOP mu tadi.\nBebas Mikir Besar. | Sisa waktu 6 Jam kamu hari ini PAKAILAH untuk meeting B2B atau nge-desain produk triliunan.\n[/INFOGRAPHIC]\n\n[QUIZ:Apa syarat mutlak nomor SATU sebelum kamu me-rekrut tim atau orang untuk mengerjakan tugas operasional harian?|Latihan teknik Marah/Ngamuk|Buatin dan siapin Dokumen Instruksi Mutlak (SOP) cara ngejalaninnya dengan format Video Layar / Teks Jelas biar ga rancu|Kasih Gaji Gede Tanpa Check & Balance|1]\n\n[CHALLENGE]\nAmbil kertas (Lagi!), Tuliskan minimal 3 Pekerjaan administratif harian (Nulis resi, jawab DM nanya harga bertuturut-turut, desain template dasar) yang valuasinya DI BAWAH 'Harga Per Jam'-mu dan habiskan waktu paling lama. Minggu depan, cari Intern/Freelancer part-time buat eksekusi hal itu.\n[/CHALLENGE]",
                'order'             => 13,
                'type'              => 'text',
                'xp_reward'         => 35,
                'estimated_minutes' => 10,
            ],
            [
                'title'             => 'Mindset Pengusaha: Transisi Operator ke Arsitek',
                'content'           => "Selamat menuntaskan modul krusial! Ini pesan pemungkas yang paling mahal harganya, karena gak ada satupun software CuanCapital/Simulator yang bisa merubah pikiran (Mindset) dari diri kamu.\n\nKita bagi tipe pebisnis jadi 4 tingkatan evolusi:\n1. **Operator Teknis:** Jago eksekusi, jago desain sendiri, tapi kalo dia sakit tepar, omzet = Rp 0.\n2. **Manajer Menengah:** Mulai menyuruh orang lain, tapi harus nunggu komando dan approval tiap jam darinya.\n3. **Arsitek Sistem:** Fokus membenahi dan mendesain 'Mesin Flow' (Standardisasi), gak butuh login tiap hari ke dashboard.\n4. **Investor Capital:** Udah santai, cuma melempar uang sisa omzet ke instrumen bisnis/startup pemula lain untuk muter profit.\n\n[CASE_STUDY]\n**Pemeriksa Akhir KPI:**\nIbu F, Owner Skincare, sekarang hanya membuka laptop hari Senin pagi selama 2 Jam seminggu. Yang Ia buka hanya 1 Excel Sheet CuanCapital. Selama angka CAC, BEP, dan Margin Profit berada pada *Range Toleransi* hijau (Batas Positif Target Laporan Para Manajernya), maka ia akan main Golf di sisa hari kerja. Bila ada deviasi merah ke area Kritis, barulah dia turun gunung meeting marah-marah perbaiki SOP di meja bawahnya.\n[/CASE_STUDY]\n\n[INFOGRAPHIC:Target 12 Bulan Kedepan]\nFase Survival | Fokus validasi bahwa ada SETIDAKNYA SATU orang Asing mau menebus mahal produkmu.\nFase Stabilizer | Mulai menggaji bawahan agar Omset stabil, dan si Arsitek bisa bernapas Lega.\nFase Capitalizer | Meledakkan omset hingga ke level korporasi dengan dana Investor dan IPO saham.\n[/INFOGRAPHIC]\n\n[QUIZ:Kamu dikatakan SUKSES melewati fase Operator Teknis dan menjadi Pebisnis Sejati (Arsitek Sistem) ketika...|Mampu membeli mobil Lambo buat gengsi doang|Bisnismu bisa menghasilkan Laba Bulanan Konstan tanpa memerlukan kehadiran Fisik & Panggilan Telepon kamu sama sekali berturut-turut selama 30 Hari|Udah mempekerjakan ribuan karyawan buat pencitraan pabrik gede|1]\n\n[CHALLENGE]\nUcapkan Bismillah / Berdoa sesuai kepercayaanmu dan Bulatkan Tekad Eksekusi mu mulai detik ini. Buka kembali Modul Reverse Planner & Goal Simulator di web ini, dan jadikan ilmu 11 Course Fundamental yang kita bahas tadi untuk jadi Arsitektur Penjualan Miliaran Rupiahmu! Sampai Jumpa di Puncak Kesuksesan! 🚀🔥\n[/CHALLENGE]",
                'order'             => 14,
                'type'              => 'text',
                'xp_reward'         => 50,
                'estimated_minutes' => 12,
            ],
        ];

        $created = 0;
        foreach ($newLessons as $lessonData) {
            // Skip if lesson with this order already exists
            $exists = $course->lessons()->where('order', $lessonData['order'])->exists();
            if (!$exists) {
                $course->lessons()->create($lessonData);
                $created++;
            }
        }

        // Update the cached lessons_count on the course
        $course->update(['lessons_count' => $course->lessons()->count()]);

        $this->command->info("✅ LessonExpandSeeder: {$created} lessons baru ditambahkan. Total: {$course->lessons_count} lessons.");
    }
}
