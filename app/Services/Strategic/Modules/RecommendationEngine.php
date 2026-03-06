<?php

namespace App\Services\Strategic\Modules;

use Illuminate\Support\Arr;

class RecommendationEngine
{
    /**
     * Returns diagnosis-specific recommendations.
     * Priority: per-diagnosis recommendations > strategy label fallback.
     */
    public function getRecommendationsForDiagnoses(array $diagnoses, string $strategyLabel): array
    {
        if (empty($diagnoses)) {
            return $this->getRecommendations($strategyLabel);
        }

        $recs = [];

        foreach ($diagnoses as $d) {
            $diagRecs = $this->diagnosisRecs($d['code']);
            foreach ($diagRecs as $rec) {
                $recs[] = ['label' => $d['label'], 'text' => $rec, 'severity' => $d['severity']];
            }
        }

        // Top 6 by severity (critical first), then trim
        return array_slice($recs, 0, 8);
    }

    private function diagnosisRecs(string $code): array
    {
        return match($code) {
            'cashflow_crisis' => [
                Arr::random([
                    'Hentikan semua pengeluaran non-esensial sekarang juga.',
                    'Bekukan anggaran belanja untuk hal-hal yang tidak berdampak langsung pada penjualan.',
                    'Segera pangkas biaya operasional yang tidak krusial untuk menyelamatkan kas.'
                ]),
                Arr::random([
                    'Prioritas 1: konversi piutang/stok jadi kas dalam 7 hari.',
                    'Fokuskan tim untuk menagih piutang dan mencairkan dead stock menjadi uang tunai.',
                    'Cairkan aset kurang produktif dan inventory berlebih untuk menambah bantalan kas.'
                ]),
                Arr::random([
                    'Hubungi supplier untuk negosiasi tempo bayar 30–45 hari.',
                    'Minta perpanjangan waktu jatuh tempo pembayaran kepada vendor utama Anda.',
                    'Restrukturisasi hutang jangka pendek menjadi jangka menengah dengan supplier.'
                ]),
                Arr::random([
                    'Fokus ke produk margin tertinggi saja sampai kas stabil.',
                    'Hanya promosikan barang dengan keuntungan terbesar untuk mendongkrak likuiditas.',
                    'Pusatkan sumber daya pada lini produk yang paling cepat menghasilkan uang tunai tinggi.'
                ]),
            ],
            'low_cash_buffer' => [
                Arr::random([
                    'Bangun emergency fund minimal 2x biaya bulanan sebelum scale.',
                    'Sisihkan keuntungan hingga mencapai cadangan kas untuk 2 bulan operasional.',
                    'Amankan dana darurat agar bisnis tahan terhadap guncangan di bulan-bulan lambat.'
                ]),
                Arr::random([
                    'Reduce stok berlebih — konversi ke kas dulu.',
                    'Lakukan pembersihan gudang untuk mengubah barang lambat laku menjadi modal segar.',
                    'Jangan menimbun inventaris; cairkan sebagian menjadi uang tunai sebagai pengaman.'
                ]),
                Arr::random([
                    'Cari revenue cepat: flash sale, bundle, atau upsell ke existing customer.',
                    'Eksekusi promo jangka pendek untuk menyegarkan aliran kas.',
                    'Tawarkan paket diskon terbatas ke basis pelanggan setia untuk mendongkrak pemasukan tunai.'
                ]),
            ],
            'operating_at_loss' => [
                Arr::random([
                    'Audit biaya detail — identifikasi biaya terbesar yang bisa dipotong.',
                    'Telusuri laporan keuangan dan eliminasi komponen biaya yang membengkak.',
                    'Lakukan perampingan anggaran agresif pada pos pengeluaran sekunder.'
                ]),
                Arr::random([
                    'Naikkan harga segera, bahkan 10% saja sudah berdampak besar ke margin.',
                    'Sesuaikan harga jual Anda untuk menambal kerugian operasional.',
                    'Lakukan penyesuaian harga ke atas secara perlahan untuk menguji sensitivitas pasar.'
                ]),
                Arr::random([
                    'Hentikan channel/produk yang paling boncos dalam 14 hari.',
                    'Tutup divisi atau hentikan lini layanan yang terbukti menyedot kas terbanyak.',
                    'Karantina atau hentikan eksperimen pemasaran yang tidak memberikan ROI positif.'
                ]),
                Arr::random([
                    'Hitung break-even per produk — jual hanya yang menguntungkan.',
                    'Validasi kembali metrik unit economics Anda; buang produk yang berdarah.',
                    'Konsentrasikan penjualan secara eksklusif pada item yang telah terbukti melampaui BEP.'
                ]),
            ],
            'margin_compression' => [
                Arr::random([
                    'Naikkan harga — reframe value proposition agar tidak terasa mahal.',
                    'Tingkatkan harga jual dengan menambahkan perceived value yang tidak makan biaya.',
                    'Kemas ulang penawaran Anda sebagai produk premium untuk membenarkan harga lebih tinggi.'
                ]),
                Arr::random([
                    'Negosiasi ulang harga supplier atau ganti supplier lebih murah.',
                    'Cari pemasok alternatif atau tekan supplier saat ini untuk diskon volume.',
                    'Audit rantai pasok Anda untuk menemukan celah penghematan biaya produksi.'
                ]),
                Arr::random([
                    'Bundle produk untuk naikkan AOV tanpa naikkan biaya per unit.',
                    'Pasangkan produk lambat laku dengan best-seller untuk melikuidasi stok dan menaikkan omset.',
                    'Ciptakan paket bundling untuk meningkatkan Average Order Value pelanggan Anda.'
                ]),
                Arr::random([
                    'Stop diskon terlalu dalam — diskon membunuh margin lebih cepat dari apapun.',
                    'Kurangi ketergantungan pada promo potongan harga; gunakan bonus fisik atau layanan sebagai gantinya.',
                    'Lindungi integritas harga Anda. Diskon besar hanya merusak persepsi nilai audiens.'
                ]),
            ],
            'thin_margin' => [
                Arr::random([
                    'Target margin minimal 25–30% — audit komponen biaya terbesar.',
                    'Petakan kembali strategi pricing untuk mengamankan margin sehat.',
                    'Sisir setiap elemen rincian biaya pokok penjualan untuk menaikkan net margin.'
                ]),
                Arr::random([
                    'Upsell ke tier premium untuk naikkan AOV dan margin sekaligus.',
                    'Tawarkan versi upgrade dari produk reguler di halaman checkout.',
                    'Implementasikan cross-selling dengan layanan bernilai tambah tinggi dengan biaya marjinal rendah.'
                ]),
                Arr::random([
                    'Kurangi SKU — fokus ke 20% produk yang hasilkan 80% profit.',
                    'Sederhanakan katalog produk Anda; konsentrasi pada jajaran pemenang.',
                    'Hapus varian produk yang lambat bergerak dan merugikan manajemen inventaris.'
                ]),
            ],
            'ads_dependency_critical' => [
                Arr::random([
                    'Mulai build organic channel: konten, SEO, atau komunitas.',
                    'Investasikan waktu di pembuatan aset konten untuk mendatangkan traffic gratis.',
                    'Bangun kolam audiens di platform sosial sebagai bantalan turunnya efisiensi iklan.'
                ]),
                Arr::random([
                    'Program referral — manfaatkan customer existing untuk akuisisi baru.',
                    'Ciptakan sistem insentif agar pembeli Anda membawa pembeli baru.',
                    'Ubah pelanggan setia menjadi tenaga pemasar gratis dengan program afiliasi.'
                ]),
                Arr::random([
                    'Bangun email/WA list — aset traffic yang tidak bisa di-turn-off.',
                    'Tingkatkan upaya penangkapan prospek melalui lead generator sebelum berjualan.',
                    'Prioritaskan akuisisi data kontak pelanggan dibanding penjualan langsung dari iklan.'
                ]),
                Arr::random([
                    'Sementara kurangi ad budget hingga margin kembali sehat.',
                    'Pangkas pengeluaran kampanye berbayar yang marginnya terlalu tipis untuk bertahan.',
                    'Skala turun iklan secara bertahap sambil membangun fondasi akuisisi organik.'
                ]),
            ],
            'ads_dependency_moderate' => [
                Arr::random([
                    'Audit ROAS per campaign — matikan yang ROAS <2x segera.',
                    'Review konversi semua Ad Sets, jeda semua strategi yang tidak segera profitable.',
                    'Hanya jalankan iklan ke kampanye yang memiliki batas keamanan konversi ganda.'
                ]),
                Arr::random([
                    'Fokus ads ke LTV tinggi — retargeting customer lama lebih murah.',
                    'Jalankan iklan remarketing ke orang yang pernah membeli untuk ROAS ekstrem.',
                    'Pusatkan anggaran iklan digital untuk mem-follow up calon pelanggan yang ragu.'
                ]),
                Arr::random([
                    'Diversifikasi: mulai 1 channel organic sebagai backup.',
                    'Aktifkan opsi SEO atau mulai publikasi tulisan organik mendalam di 1 platform utama.',
                    'Eksperimen dengan traffic non-berbayar di kanal seperti Short Video atau LinkedIn.'
                ]),
            ],
            'large_revenue_gap' => [
                Arr::random([
                    'Gap terlalu besar — turunkan target jangka pendek, fokus ke foundation.',
                    'Sesuaikan kembali proyeksi pendapatan Anda dan perkuat unit economics terlebih dulu.',
                    'Karena realitas meleset jauh dari harapan, mundurkan target agresif dan perbaiki kelemahan operasional.'
                ]),
                Arr::random([
                    'Lakukan customer interview — pahami mengapa orang tidak beli.',
                    'Bicaralah dengan prospek yang batal membeli untuk mendapat wawasan berharga.',
                    'Validasi kembali product-market fit Anda dengan mewawancarai leads yang mendingin.'
                ]),
                Arr::random([
                    'Uji coba 3-5 channel akuisisi berbeda, double down yang berhasil.',
                    'Tebar jaring eksperimen pemasaran berskala kecil di beberapa platform, lalu fokus di pemenangnya.',
                    'Cari tahu tempat berkumpul pelanggan yang belum tersentuh melalui micro-testing.'
                ]),
            ],
            'low_retention' => [
                Arr::random([
                    'Buat follow-up otomatis H+3 setelah pembelian pertama.',
                    'Jadwalkan komunikasi sapaan bagi pembeli baru untuk menaikkan kepuasan panca beli.',
                    'Tanyakan umpan balik kepada pelanggan segera setelah mereka menerima produk Anda.'
                ]),
                Arr::random([
                    'Program loyalty sederhana — poin atau diskon khusus repeat buyer.',
                    'Kenalkan program VIP club dengan keuntungan eklusif untuk menstimulasi belanja ulang.',
                    'Sediakan voucher belanja khusus hanya bagi pelanggan yang pernah bertransaksi.'
                ]),
                Arr::random([
                    'Hubungi 10 customer pertama bulan ini — tanyakan kenapa tidak repeat.',
                    'Telepon survei perihal churn rate kepada mantan pembeli aktif Anda.',
                    'Tangkap wawasan kelemahan layanan dari pembeli lama yang sudah tidak aktif.'
                ]),
                Arr::random([
                    'Tawarkan subscription/bundling jangka panjang untuk lock-in.',
                    'Ubah model bisnis menjadi langganan bulanan jika memungkinkan untuk jaminan pendapatan.',
                    'Perkenalkan paket penyediaan komoditas sehingga mereka jarang perlu berpikir untuk reorder.'
                ]),
            ],
            'scaling_without_base' => [
                Arr::random([
                    'Tunda scaling — investasi ke margin dulu sebelum scale.',
                    'Pending rencana ekspansi besar sampai persentase margin kotor stabil di zona ideal.',
                    'Jangan membakar uang untuk memoles hal yang dasarnya retak; perbaiki margin produk terlemah.'
                ]),
                Arr::random([
                    'Buktikan unit economics positif di 1 channel dulu.',
                    'Temukan profitabilitas sejati di satu saluran pemasaran tunggal sebelum cabang ke platform lain.',
                    'Prioritas menemukan satu kanal yang sangat bisa ditebak dan menguntungkan dibanding banyak kanal boncos.'
                ]),
                Arr::random([
                    'Hitung contribution margin per unit — scaling harus profitable dari unit pertama.',
                    'Teliti ulang unit economics pada tingkatan paling atomik sebelum mengeksekusi budget pemasaran jumbo.',
                    'Pastikan setiap tambahan pesanan baru tidak menyebabkan kerugian variabel.'
                ]),
            ],
            'ops_chaos' => [
                Arr::random([
                    'Buat SOP untuk 3 proses terpenting dalam bisnis minggu ini.',
                    'Dokumentasikan dengan ketat cara kerja layanan pelanggan, pemenuhan order, dan pengembalian barang.',
                    'Ciptakan standar operasi prosedur untuk meminimalkan ketergantungan tim pada kehadiran Anda.'
                ]),
                Arr::random([
                    'Otomasi tugas repetitif — gunakan tools gratis dulu (Notion, Google Sheet).',
                    'Biarkan perangkat lunak mengambil alih entri data manual yang menyumbat fokus tim inti.',
                    'Lakukan perampingan sistem operasional dengan pemanfaatan Zapier, Make, atau automasi serupa.'
                ]),
                Arr::random([
                    'Delegasikan — kamu tidak bisa scale kalau semuanya lewat kamu.',
                    'Identifikasi tugas harian Anda yang paling berulang dan segera latih staf untuk mengambil alihnya.',
                    'Hentikan menjadi botol neck prosedur; berikan wewenang kepada manajer lapis kedua Anda.'
                ]),
            ],
            'strategy_unclear' => [
                Arr::random([
                    'Pilih 1 metric utama untuk 90 hari ke depan — jangan semuanya.',
                    'Fokuskan upaya kru pada satu North Star Metric (contoh: pertumbuhan pengguna atau pendapatan baru).',
                    'Hindarkan kejaran ganda yang kontradiktif; pilih apakah Anda sedang fase growth atau profitabilitas.'
                ]),
                Arr::random([
                    'Tulis ICP (Ideal Customer Profile) — siapa yang paling profitable?',
                    'Definisikan ulang secara spesifik, siapa target audiens yang membeli tercepat tanpa mengeluh.',
                    'Petakan kembali siapa sebetulnya Persona Pelanggan Ideal Anda dari data historical penjualan.'
                ]),
                Arr::random([
                    'Buat sprint 2 minggu dengan maksimal 3 prioritas saja.',
                    'Jalankan eksekusi sistem Sprint agile jangka pendek dengan tujuan yang amat konkrit.',
                    'Pecah rencana muluk tahunan Anda menjadi sasaran mikroskopis setiap dua minggu.'
                ]),
            ],
            'stagnant_revenue' => [
                Arr::random([
                    'Lakukan audit produk: identifikasi penawaran mana yang paling menguntungkan dan buang yang lambat laku.',
                    'Segarkan kembali angle marketing Anda; audiens yang sama mungkin kebosanan dengan iklan yang itu-itu saja.',
                    'Gali ulang nilai manfaat unik bisnis Anda yang membedakannya secara mutlak dari kompetitor.'
                ]),
                Arr::random([
                    'Kembangkan dan uji penawaran bundling baru untuk re-aktivasi minat basis pelanggan lama.',
                    'Tes dua penawaran frontend baru (diskon tajam vs bonus) kepada audiens cold dalam satu minggu ini.',
                    'Berikan promo "flash sale" eksklusif ke pelanggan loyal untuk suntikan pendapatan jangka pendek.'
                ]),
                Arr::random([
                    'Petakan market baru yang belum tergarap namun bersinggungan erat dengan pembeli eksisting Anda.',
                    'Hubungi 10 pelanggan terbaik Anda hari ini, tanyakan produk pelengkap apa yang biasa mereka beli di tempat lain.',
                    'Validasi apakah harga jual Anda masih sesuai pasar atau sudah tertinggal standar nilai kompetisi saat ini.'
                ]),
            ],
            default => [Arr::random([
                    'Review model bisnis dan fokus pada satu area perbaikan utama.',
                    'Kaji ulang pilar fundamental bisnis dan tetapkan satu sasaran kunci bulan ini.',
                    'Validasi efisiensi dari penawaran utama Anda pada kelompok audiens saat ini.'
                ])],
        };
    }

    /**
     * Fallback: strategy-label-based recommendations (original behavior)
     */
    public function getRecommendations(string $strategyLabel): array
    {
        $recs = match($strategyLabel) {
            StrategyClassifierModule::STRATEGY_UNREALISTIC => [
                Arr::random([
                    "Target revenue terlalu tinggi untuk modal saat ini.",
                    "Ekspektasi tidak proporsional dengan sumber daya finansial yang ada.",
                    "Perencanaan awal terlalu muluk jika disandingkan dengan amunisi yang tersedia."
                ]),
                Arr::random([
                    "Kurangi target menjadi 30% dari angka sekarang atau cari investor.",
                    "Turunkan target jangka pendek atau segera kumpulkan modal eksternal.",
                    "Revisi ekspektasi pendapatan Anda agar lebih sejalan dengan kemampuan operasional saat ini."
                ]),
                Arr::random([
                    "Fokus pada organic traffic dahulu untuk menekan biaya marketing.",
                    "Manfaatkan saluran pemasaran nirbayar guna mengamankan arus kas.",
                    "Gunakan pendekatan duplikasi konten organik secara masif sebagai pengganti ads berbayar."
                ]),
            ],
            StrategyClassifierModule::STRATEGY_HIGH_EXPOSURE => [
                Arr::random([
                    "Risiko cashflow sangat tinggi — persiapkan contingency plan.",
                    "Tingkat keterpaparan tinggi; cadangkan pelampung dana taktis sesegera mungkin.",
                    "Operasional berjalan di area rentan gagal bayar, siapkan skenario Plan B."
                ]),
                Arr::random([
                    "Siapkan dana darurat minimal 3x biaya operasional sebelum mulai.",
                    "Amankan minimal 3 bulan dana pembakaran kas (burn rate) di luar sirkulasi bisnis aktif.",
                    "Buffer dana cadangan mendesak dibutuhkan untuk menghindari malapetaka arus kas di bulan suram."
                ]),
                Arr::random([
                    "Jangan hire staff tetap di 6 bulan pertama, gunakan freelancer.",
                    "Konversi biaya tetap staf menjadi biaya variabel lewat pekerja lepas untuk memitigasi risiko.",
                    "Pertahankan kelincahan finansial dengan menunda perekrutan penuh waktu."
                ]),
            ],
            StrategyClassifierModule::STRATEGY_CAPITAL_STRAINED => [
                Arr::random([
                    "Margin terlalu tipis untuk scale up.",
                    "Ketebalan margin tidak memadai untuk mendanai upaya ekspansi.",
                    "Profit per satuan produk tidak mencukupi untuk mensubsidi biaya pertumbuhan yang signifikan."
                ]),
                Arr::random([
                    "Naikkan harga jual atau negosiasi ulang dengan supplier.",
                    "Lakukan intervensi pricing atau pangkas komponen biaya modal dasar melalui negosiasi ulang.",
                    "Eksplorasi kenaikan angka penjualan dan penekanan Cost of Goods Sold ke batas rasional terendah."
                ]),
                Arr::random([
                    "Hindari paid ads sampai conversion rate >2%.",
                    "Stop saluran akuisisi berbayar hingga tawaran Anda divalidasi dengan konversi optimal.",
                    "Penundaan investasi di ruang media digital adalah bijak saat metrik persentase pembeli masih suram."
                ]),
            ],
            StrategyClassifierModule::STRATEGY_AGGRESSIVE => [
                Arr::random([
                    "Mode Growth: investasikan 60% profit kembali ke marketing.",
                    "Alirkan mayoritas margin keuntungan kembali untuk pendanaan saluran akuisisi pemasaran.",
                    "Jalankan injeksi modal internal masif untuk memicu traksi pendapatan seketika."
                ]),
                Arr::random([
                    "Fokus utama metric: CAC (Cost Per Acquisition).",
                    "Jadikan biaya akuisisi pengguna sebagai matrik inti satu-satunya harian yang dipantau.",
                    "Penurunan agresif CAC adalah nyawa untuk model pertumbuhan agresif ini."
                ]),
                Arr::random([
                    "Scale winning campaign segera, matikan yang rugi dalam 24 jam.",
                    "Lipatgandakan dana untuk metode yang dikonfirmasi berhasil dan hapus seketika uji peruntungan yang gagal.",
                    "Rutinitas harian utama adalah mematikan kampanye macet dan memperdalam di kanal yang efisien."
                ]),
            ],
            StrategyClassifierModule::STRATEGY_DOMINATION => [
                Arr::random([
                    "Mode Dominasi: bakar uang di depan untuk market share.",
                    "Fase kapitalisasi audiens: korbankan keuntungan finansial sementara untuk dominasi perhatian penuh.",
                    "Gunakan kekuatan injeksi awal besar-besaran guna meruntuhkan posisi pesaing dari puncak."
                ]),
                Arr::random([
                    "Pastikan supply chain siap untuk lonjakan order 3x lipat.",
                    "Siapkan redudansi vendor karena gelombang permintaan bisa menyebabkan efek kemacetan berantai.",
                    "Kesiapan lini pemenuhan sangat krusial agar janji pertumbuhan yang ambisius tidak menghancurkan reputasi merek."
                ]),
                Arr::random([
                    "Lakukan pre-launch campaign untuk mengunci minat pasar.",
                    "Tebar kampanye awal penangkal perhatian sebelum membanjiri target periklanan primer.",
                    "Eksekusi strategi retargeting awal sejak jauh-jauh hari menyiapkan antrean tunggu membludak."
                ]),
            ],
            StrategyClassifierModule::STRATEGY_STABLE => [
                Arr::random([
                    "Mode Aman: jaga retensi pelanggan lama.",
                    "Pusat perhatian ada pada proteksi dan perawat pangkalan pengguna yang sudah kokoh berbayar.",
                    "Prioritaskan kenyamanan layanan purna-jual untuk menjaga kestabilan aliran pundi bulanan."
                ]),
                Arr::random([
                    "Optimalkan LTV dengan upsell, bukan cari traffic baru.",
                    "Ciptakan penawaran suplemen dan pelengkap untuk basis audiens lama dibanding uang ads melelahkan.",
                    "Nilai Transaksi Rata-Rata harus naik dari kolam konsumen sama tanpa memperlebar pangsa jangkauan digital."
                ]),
                Arr::random([
                    "Bangun SOP operasional agar bisnis bisa jalan autopilot.",
                    "Sistematisasi rutinitas pengerjaan proyek sehingga sang pemilik bebas dari beban aktivitas hari lepas hari.",
                    "Kukuhkan landasan kerja agar kualitas pengantaran tidak lagi perlu campur tangan pendiri langsung."
                ]),
            ],
            default => [
                Arr::random([
                    "Lakukan tes pasar kecil-kecilan (split test).",
                    "Eksekusi pengetesan konsep pemasaran dalam volume lalu lintas sempit dulu.",
                    "Luncurkan dua varian pesan sederhana ke sekelompok kecil pengguna ideal."
                ]),
                Arr::random([
                    "Validasi produk ke 50 pembeli pertama sebelum stock banyak.",
                    "Pastikan tingkat permintaan produk memadai secara empiris melalui uji penjualan terbatas.",
                    "Jangan komit pada kapasitas persediaan jumlah besar tanpa sinyal ketertarikan solid dari pembeli."
                ]),
                Arr::random([
                    "Fokus pada kepuasan pelanggan pertama untuk word-of-mouth.",
                    "Raih advokasi penggemar antusias murni melalui produk luar biasa di angkatan rilis mula.",
                    "Suntik modal pada pengalaman pasca-transaksi hebat ketimbang pencarian pengguna tambahan buru-buru."
                ]),
            ],
        };

        // Wrap in same format as diagnosis recs
        return array_map(fn($r) => ['label' => 'Strategy', 'text' => $r, 'severity' => 'info'], $recs);
    }

    public function getDescription(string $strategyLabel): string
    {
        $descriptions = match($strategyLabel) {
            StrategyClassifierModule::STRATEGY_UNREALISTIC     => [
                "Strategi ini tidak layak dijalankan dengan sumber daya saat ini.",
                "Ekspektasi terlampau tinggi mengingat kondisi struktur operasional aktual.",
                "Pendekatan saat ini memiliki jurang lebar antara target dan validitas modal."
            ],
            StrategyClassifierModule::STRATEGY_HIGH_EXPOSURE   => [
                "Potensi profit besar, tapi satu kesalahan bisa fatal.",
                "Skenario pengembalian tinggi diiringi oleh risiko kerusakan keuangan yang mematikan.",
                "Permainan agresif dengan ruang keamanan pergerakan (margin-of-error) amat kecil."
            ],
            StrategyClassifierModule::STRATEGY_CAPITAL_STRAINED=> [
                "Bisnis berjalan tapi sulit berkembang karena efisiensi rendah.",
                "Modal perputaran macet membelenggu laju ekspansi ke tahap semestinya.",
                "Bisnis bertahan hidup namun kesulitan mencetak rekam jejak akselerasi yang tajam."
            ],
            StrategyClassifierModule::STRATEGY_AGGRESSIVE      => [
                "Fokus pada pertumbuhan cepat dengan risiko terukur.",
                "Tatanan rancangan dirakit untuk mengejar omset kilat di atas persentase untung bersih.",
                "Kalkulasi orientasi hasil kecepatan tinggi, didesain mendobrak batasan pasar."
            ],
            StrategyClassifierModule::STRATEGY_DOMINATION      => [
                "Strategi agresif untuk menguasai ceruk pasar secepat mungkin.",
                "Skema dirajut untuk membakar landasan menuju dominasi total pangsa segmen.",
                "Pembesaran traksi dengan kekuatan masif melampaui toleransi organik kompetitif biasa."
            ],
            StrategyClassifierModule::STRATEGY_STABLE          => [
                "Fokus pada keamanan cashflow dan pertumbuhan jangka panjang.",
                "Jaga ketebalan pundi tunai secara rasional; percepatan dikesampingkan untuk garansi umur.",
                "Posisi mapan, mementingkan pembetonan dasar kokoh serta laba berkelanjutan."
            ],
            default                                            => [
                "Pendekatan moderat, menyeimbangkan risiko dan potensi pertumbuhan.",
                "Rute pertengahan jalan, meredam bahaya kolaps demi langkah progres konsisten.",
                "Sinergi kompromi cermat antara retensi keamanan saldo bernapas terhadap pencapaian target."
            ],
        };
        return Arr::random($descriptions);
    }
}
