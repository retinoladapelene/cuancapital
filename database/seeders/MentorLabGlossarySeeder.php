<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GlossaryTerm;

class MentorLabGlossarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terms = [
            // --- Inputs ---
            [
                'key' => 'mentor_business_type',
                'title' => 'Model Bisnis / Sektor',
                'simple_explanation' => 'Sektor industri dari bisnis kamu (misal: Fisik/Retail vs Digital/Jasa).',
                'advanced_explanation' => 'Model bisnis menentukan struktur biaya standar, siklus kas (cash conversion cycle), dan profil risiko yang digunakan oleh strategic engine untuk menimbang profitabilitas relatif.',
                'formula' => null,
            ],
            [
                'key' => 'mentor_initial_capital',
                'title' => 'Modal Awal',
                'simple_explanation' => 'Perkiraan jumlah uang yang disiapkan khusus untuk memulai atau mengeksekusi rencana bisnis ini.',
                'advanced_explanation' => 'Digunakan sebagai denominator dalam perhitungan Capital Efficiency dan Burn Rate Tolerance. Makin kecil modal dengan target besar = makin ekstrem profil risikonya.',
                'formula' => null,
            ],
            [
                'key' => 'mentor_experience',
                'title' => 'Tingkat Pengalaman Bisnis',
                'simple_explanation' => 'Seberapa jauh kamu sudah pernah mengeksekusi bisnis sebelumnya.',
                'advanced_explanation' => 'Digunakan sebagai multiplier efisiensi dalam algoritma simulasi. Beginner memiliki efisiensi eksekusi margin yang lebih rendah dan risiko blunder lebih tinggi.',
                'formula' => null,
            ],
            [
                'key' => 'mentor_timeframe',
                'title' => 'Timeframe Eksekusi',
                'simple_explanation' => 'Berapa lama (dalam bulan) waktu yang kamu tetapkan untuk mencapai target.',
                'advanced_explanation' => 'Menentukan kecepatan pertumbuhan bulanan (CMGR) yang dibutuhkan. Timeframe pendek dengan target tinggi akan merusak skor Feasibility (realistis).',
                'formula' => null,
            ],
            [
                'key' => 'mentor_actual_revenue',
                'title' => 'Revenue Aktual / Bulan',
                'simple_explanation' => 'Total uang kotor (omzet) yang masuk ke bisnis dalam satu bulan terakhir.',
                'advanced_explanation' => 'Angka baseline aktual yang digunakan untuk engine proyeksi. Berbeda dengan \'target\', ini mencerminkan product-market fit saat ini.',
                'formula' => null,
            ],
            [
                'key' => 'mentor_actual_expenses',
                'title' => 'Total Biaya / Bulan',
                'simple_explanation' => 'Keseluruhan biaya operasional (OpEx) dan modal kerja yang dibakar dalam satu bulan terakhir.',
                'advanced_explanation' => 'Menentukan burn rate dan real net margin aktual. Termasuk COGS, payroll, marketing, dan overhead bulanan.',
                'formula' => null,
            ],
            [
                'key' => 'mentor_cash_balance',
                'title' => 'Saldo Kas Sekarang',
                'simple_explanation' => 'Uang cash/tunai yang riil ada di rekening bisnis hari ini dan bebas digunakan.',
                'advanced_explanation' => 'Komponen utama komponen untuk kalkulasi Runway. Mengukur seberapa dalam \'nafas\' perusahaan untuk menopang defisit (negative cashflow).',
                'formula' => null,
            ],
            [
                'key' => 'mentor_ad_spend',
                'title' => 'Biaya Iklan / Bulan',
                'simple_explanation' => 'Total budget yang dibakar khusus untuk ads/marketing dalam sebulan.',
                'advanced_explanation' => 'Untuk mengukur rasio ROAS (Return on Ad Spend) aktual dan ketergantungan metrik akuisisi berbayar.',
                'formula' => null,
            ],
            [
                'key' => 'mentor_aov',
                'title' => 'AOV (Average Order Value)',
                'simple_explanation' => 'Rata-rata uang yang dibelanjakan customer dalam satu kali transaksi.',
                'advanced_explanation' => 'Metrik unit ekonomi utama. AOV yang terlalu dekat dengan CAC (Customer Acquisition Cost) menunjukkan model bisnis tidak viable tanpa repeat order memadai.',
                'formula' => 'Total Revenue / Total Transaksi',
            ],

            // --- Outputs ---
            [
                'key' => 'mentor_gap_analysis',
                'title' => 'Gap Analysis',
                'simple_explanation' => 'Jarak antara kondisi aktual dan target kamu.',
                'advanced_explanation' => 'Mengidentifikasi berapa persen defisit operasional yang harus ditutup untuk mencapai trajectory objektif simulasi.',
                'formula' => '((Target - Actual) / Target) x 100%',
            ],
            [
                'key' => 'mentor_cash_health',
                'title' => 'Cash Health (Kesehatan Kas)',
                'simple_explanation' => 'Kategori keamanan keuangan bisnis kamu. Hijau = Aman, Merah = Bahaya.',
                'advanced_explanation' => 'Berdasarkan threshold bulan runway. < 3 bulan = Critical Alert (ancaman kebangkrutan likuiditas).',
                'formula' => null,
            ],
            [
                'key' => 'mentor_est_profit',
                'title' => 'Estimated Profit',
                'simple_explanation' => 'Keuntungan bersih bulanan hasil kalkulasi (setelah potong semua biaya).',
                'advanced_explanation' => 'Bottom line margin. Dipengaruhi oleh asumsi gross margin, struktur biaya klasifikasi sektor, dan tingkat efisiensi operasional.',
                'formula' => 'Revenue - (COGS + OpEx)',
            ],
            [
                'key' => 'mentor_break_even',
                'title' => 'Break-Even Point (Bulan)',
                'simple_explanation' => 'Titik atau bulan ke-berapa bisnismu mulai balik modal penuh dan untung murni.',
                'advanced_explanation' => 'Titik di mana kumulatif Net Profit sama dengan Initial Capital outlay. Makin cepat makin aman (mengurangi paparan risiko market).',
                'formula' => 'Kumulatif Profit Bulanan ≥ Initial Capital',
            ],
            [
                'key' => 'mentor_net_margin',
                'title' => 'Net Margin Percentage',
                'simple_explanation' => 'Berapa persen laba bersih yang tersisa dari total penjualan.',
                'advanced_explanation' => 'Indikator bantalan risiko (buffer). Margin terlalu tipis (< 10%) bisa tersapu habis oleh faktor eksternal tak terukur.',
                'formula' => '(Net Profit / Revenue) x 100%',
            ],
            [
                'key' => 'mentor_roi_multiplier',
                'title' => 'ROI Multiplier',
                'simple_explanation' => 'Seberapa kali lipat modal awalmu akan berkembang bila target akhir tercapai.',
                'advanced_explanation' => 'Return on Investment kumulatif di akhir timeframe. Ini menentukan apakah "imbal hasil" sepadan dengan risiko yang diambil VS dimasukkan instrumen investasi pasif.',
                'formula' => 'Terminal Cumulative Profit / Initial Capital',
            ],
            [
                'key' => 'mentor_runway',
                'title' => 'Runway Cash',
                'simple_explanation' => 'Berapa bulan lagi bisnismu bisa bertahan dengan sisa uang kas saat ini kalau rugi terus.',
                'advanced_explanation' => 'Waktu survival likuiditas pada burn rate konstan saat ini. Titik ini nol = bisnis insolvent.',
                'formula' => 'Saldo Kas Aktif / Monthly Net Burn Rate',
            ],
            [
                'key' => 'mentor_burn_rate',
                'title' => 'Monthly Burn Rate',
                'simple_explanation' => 'Jumlah uang / defisit yang terbakar tiap bulan (Bila biaya > pendapatan).',
                'advanced_explanation' => 'Besaran net negative cash flow bulanan yang menggerogoti balance sheet kapital berjalan.',
                'formula' => 'Total Expenses - Actual Revenue',
            ],
            [
                'key' => 'mentor_capital_efficiency',
                'title' => 'Capital Efficiency Index',
                'simple_explanation' => 'Seberapa jago kamu menghasilkan profit tanpa butuh modal terlalu besar.',
                'advanced_explanation' => 'Rasio profit margin tahunan potensial relatif terhadap suntikan modal ekuitas luar. Di atas 100% berarti startup sangat capital-efficient.',
                'formula' => null,
            ],
            
            // --- Scores ---
            [
                'key' => 'score_feasibility',
                'title' => 'Feasibility Score',
                'simple_explanation' => 'Apakah target omzet dan waktunya masuk akal? (Cek Realistis)',
                'advanced_explanation' => 'Mengukur Compounded Monthly Growth Rate yang dibutuhkan untuk sampai ke target. CMGR > 30% divaluasi sangat berisiko dan tidak realistis probabilitasnya.',
                'formula' => null,
            ],
            [
                'key' => 'score_profitability',
                'title' => 'Profitability Score',
                'simple_explanation' => 'Apakah bisnis ini ujungnya ngasih untung yang sehat dan layak?',
                'advanced_explanation' => 'Indeks gabungan dari estimasi Net Margin % dan Break-Even velocity. Memastikan model bukan hanya "Revenue vanity" tanpa profitabilitas dasar.',
                'formula' => null,
            ],
            [
                'key' => 'score_risk',
                'title' => 'Risk Safety Score',
                'simple_explanation' => 'Tingkat keamanan bisnismu dari risiko gagal total (kebangkrutan).',
                'advanced_explanation' => 'Inversi dari akumulasi penalti volatilitas (Cash Crunch Risk, Ad Dependency, Margin Compression). Skor 100 = Aman 100%.',
                'formula' => null,
            ],
            [
                'key' => 'score_efficiency',
                'title' => 'Efficiency Score',
                'simple_explanation' => 'Seberapa pintar operasional dalam mencetak cuan efisien.',
                'advanced_explanation' => 'Agregat metrik unit ekonomi seperti ROAS, Customer LTV vs CAC proxy, dan rasio modal/profit.',
                'formula' => null,
            ],
        ];

        foreach ($terms as $term) {
            GlossaryTerm::updateOrCreate(
                ['key' => $term['key']],
                $term
            );
        }
    }
}
