<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GlossaryTerm;

class GlossaryTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terms = [
            [
                'key' => 'margin',
                'title' => 'Profit Margin',
                'simple_explanation' => 'Sisa uang dari setiap penjualan setelah dikurangi biaya produk (HPP).',
                'advanced_explanation' => 'Persentase pendapatan tetap setelah mengurangi biaya langsung yang terkait dengan produksi barang atau jasa.',
                'formula' => '(Harga Jual - Harga Modal) / Harga Jual × 100%'
            ],
            [
                'key' => 'gross_profit',
                'title' => 'Gross Profit (Laba Kotor)',
                'simple_explanation' => 'Keuntungan sebelum dikurangi biaya operasional seperti gaji, iklan, atau sewa.',
                'advanced_explanation' => 'Total pendapatan dikurangi Harga Pokok Penjualan (HPP). Metrik kunci efisiensi produksi/sourcing inti.',
                'formula' => 'Total Pendapatan - Harga Pokok Penjualan (HPP)'
            ],
            [
                'key' => 'net_profit',
                'title' => 'Net Profit (Laba Bersih)',
                'simple_explanation' => 'Keuntungan murni perusahaan setelah SEMUA biaya (produksi, operasional, iklan, pajak) dibayarkan. Uang yang benar-benar dibawa pulang.',
                'advanced_explanation' => 'Bottom line profit. Indikator paling akurat atas kesehatan finansial dan profitabilitas bisnis setelah seluruh beban diselesaikan.',
                'formula' => 'Laba Kotor - Total Biaya Operasional (termasuk pajak, bunga, dll)'
            ],
            [
                'key' => 'revenue',
                'title' => 'Revenue (Pendapatan)',
                'simple_explanation' => 'Total uang masuk dari penjualan produk atau jasa sebelum dipotong biaya apapun.',
                'advanced_explanation' => 'Top line income. Nilai total penjualan yang dihasilkan pada periode tertentu tanpa mempertimbangkan expenses apapun.',
                'formula' => 'Jumlah Unit Terjual × Harga per Unit'
            ],
            [
                'key' => 'break_even',
                'title' => 'Break Even Point (BEP)',
                'simple_explanation' => 'Titik impas atau "balik modal", dimana pendapatan yang didapat sama persis dengan biaya yang dikeluarkan (tidak untung, tidak rugi).',
                'advanced_explanation' => 'Skenario di mana Total Revenue = Total Fixed Costs + Total Variable Costs. Berguna untuk memahami volume penjualan minimum yang dibutuhkan untuk menutupi biaya.',
                'formula' => 'Total Fixed Cost / (Harga per Unit - Variable Cost per Unit)'
            ],
            [
                'key' => 'conversion_rate',
                'title' => 'Conversion Rate',
                'simple_explanation' => 'Persentase dari orang yang melihat produk kita dan akhirnya benar-benar membeli.',
                'advanced_explanation' => 'Metrik performa funnel yang mengukur efektivitas mengubah prospects menjadi paying customers.',
                'formula' => '(Jumlah Pembeli / Jumlah Pengunjung) × 100%'
            ],
            [
                'key' => 'ad_spend',
                'title' => 'Ad Spend',
                'simple_explanation' => 'Total biaya atau uang yang dikeluarkan untuk iklan (Facebook Ads, TikTok Ads, dll).',
                'advanced_explanation' => 'Budget total untuk advertising campaigns yang akan menjadi bagian dari Customer Acquisition Cost (CAC).',
                'formula' => null
            ],
            [
                'key' => 'roi',
                'title' => 'ROI (Return on Investment)',
                'simple_explanation' => 'Seberapa besar imbal hasil dari modal atau investasi yang dikeluarkan. Jika ROI 50%, untung 50% dari modal.',
                'advanced_explanation' => 'Metrik profitabilitas dan efisiensi modal yang mengukur return sebuah investasi dibandingkan biaya awal.',
                'formula' => '(Total Keuntungan / Total Modal atau Biaya Iklan) × 100%'
            ],
            [
                'key' => 'roas',
                'title' => 'ROAS (Return on Ad Spend)',
                'simple_explanation' => 'Seberapa besar uang yang dihasilkan kembali per kelipatan dari biaya iklan. ROAS 3x artinya setiap Rp10rb iklan menghasilkan penjualan Rp30rb.',
                'advanced_explanation' => 'Metrik spesifik marketing untuk mengukur gross revenue yang dihasilkan untuk setiap dollar/rupiah yang dihabiskan pada campaign advertising.',
                'formula' => 'Total Pendapatan Iklan / Total Biaya Iklan'
            ],
            [
                'key' => 'runway',
                'title' => 'Runway',
                'simple_explanation' => 'Berapa bulan bisnis bisa bertahan hidup dengan uang tunai (cash) yang ada saat ini sebelum bangkrut.',
                'advanced_explanation' => 'Jumlah waktu (biasanya dalam hitungan bulan) sebuah entitas dapat tetap solvent berdasarkan cash balance dan burn rate kasaran yang ada.',
                'formula' => 'Uang Kas Saat Ini / Burn Rate per Bulan'
            ],
            [
                'key' => 'burn_rate',
                'title' => 'Burn Rate',
                'simple_explanation' => 'Kecepatan bisnis menghabiskan uang cash. Misal pengeluaran 10 juta per bulan tanpa ada pemasukan, maka burn rate adalah 10 juta/bulan.',
                'advanced_explanation' => 'Rate of negative cash flow, umum digunakan oleh startups untuk mengukur runway hingga titik profitabilitas atau next funding round.',
                'formula' => '(Kas Awal - Kas Akhir) / Jumlah Bulan'
            ],
            [
                'key' => 'cac',
                'title' => 'CAC (Customer Acquisition Cost)',
                'simple_explanation' => 'Total biaya marketing atau iklan rata-rata yang dikeluarkan untuk mendapatkan SATU orang pembeli (customer) baru.',
                'advanced_explanation' => 'Biaya komprehensif untuk memperoleh satu pelanggan baru. Metrik unit economics yang krusial yang ketika dinilai bersama LTV (Life Time Value) menentukan keberlanjutan ekspansi.',
                'formula' => 'Total Biaya Sales & Marketing / Jumlah Pelanggan Baru'
            ],
            [
                'key' => 'cpa',
                'title' => 'CPA (Cost per Acquisition/Action)',
                'simple_explanation' => 'Biaya iklan hanya untuk mendatangkan satu hasil/aksi spesifik. Biasanya sama artinya dengan CAC di toko online (biaya per satu pesanan).',
                'advanced_explanation' => 'Biasa digunakan pada kampanye digital untuk merujuk cost konversi dari aksi spesifik.',
                'formula' => 'Total Biaya Iklan / Total Konversi'
            ],
            [
                'key' => 'traffic',
                'title' => 'Traffic',
                'simple_explanation' => 'Banyaknya orang pengunjung (views/clicks) yang masuk ke website atau melihat halaman jualan kita.',
                'advanced_explanation' => 'Volume pengunjung pada website atau aset digital. Traffic quantity tanpa quality akan menyebabkan conversion rate yang buruk.',
                'formula' => null
            ],
            [
                'key' => 'unit_profit',
                'title' => 'Unit Profit',
                'simple_explanation' => 'Keuntungan bersih dari penjualan 1 satuan produk (setelah dipotong harga modal, tanpa iklan).',
                'advanced_explanation' => 'Contribution margin absolut yang diperoleh per product item. Harus cukup besar untuk menutupi biaya akuisisi pelanggan (CAC).',
                'formula' => 'Harga Jual per Unit - Harga Pokok per Unit (HPP)'
            ],
            [
                'key' => 'target_gap',
                'title' => 'Target Gap',
                'simple_explanation' => 'Jarak antara jumlah uang yang kamu dapat sekarang dengan target yang ingin kamu capai. Semakin kecil angkanya, semakin dekat ke target!',
                'advanced_explanation' => 'Kekurangan revenue atau profit yang perlu dipenuhi agar bisnis mencapai target finansial (Business Goal). Berguna untuk mengetahui agresivitas intervensi marketing di masa mendatang.',
                'formula' => 'Target Goal - Current Performance'
            ],
            [
                'key' => 'upsell',
                'title' => 'Upsell (Upselling)',
                'simple_explanation' => 'Praktik menawarkan barang yang lebih mahal atau tambahan ke pelanggan yang sudah mau beli, biar belanjanya makin banyak.',
                'advanced_explanation' => 'Strategi sales untuk meningkatkan Average Order Value (AOV). Pelanggan ditawari versi upgrade atau premium dari apa yang sedang mereka beli.',
                'formula' => null
            ],
            [
                'key' => 'take_rate',
                'title' => 'Take Rate (%)',
                'simple_explanation' => 'Persentase orang yang akhirnya \'mengambil\' tawaran tambahan (upsell) yang diberikan. Kalau dari 10 orang ada 2 yang nambah pesanan, take rate-nya 20%.',
                'advanced_explanation' => 'Attachment rate atau conversion rate dari tawaran tambahan cross-sell / upsell dalam suatu funnel. Mempengaruhi blended margin sebuah campaign.',
                'formula' => '(Jumlah Pembeli Upsell / Jumlah Total Pembeli) × 100%'
            ],
            // --- NEW TERMS FOR SIMULATORS & MENTOR LAB ---
            [
                'key' => 'product_price',
                'title' => 'Product Price (Harga Jual)',
                'simple_explanation' => 'Harga di mana Anda menjual satu unit produk atau jasa kepada pelanggan.',
                'advanced_explanation' => 'Titik harga eceran. Komponen utama pembentuk Gross Revenue sebelum dipotong diskon atau retur.',
                'formula' => null
            ],
            [
                'key' => 'cogs',
                'title' => 'COGS (Harga Pokok Penjualan)',
                'simple_explanation' => 'Modal dasar per produk. Biaya untuk membuat barangnya (bahan baku, ongkos jahit, kemasan).',
                'advanced_explanation' => 'Cost of Goods Sold (HPP). Biaya langsung yang dapat diatribusikan pada produksi barang yang dijual.',
                'formula' => null
            ],
            [
                'key' => 'monthly_ads_budget',
                'title' => 'Monthly Ads Budget',
                'simple_explanation' => 'Batas maksimal uang yang siap Anda bakar (keluarkan) untuk iklan bulan ini.',
                'advanced_explanation' => 'Alokasi plafon pengeluaran (spend cap) bulanan untuk direct-response marketing.',
                'formula' => null
            ],
            [
                'key' => 'target_revenue',
                'title' => 'Target Revenue (Omzet)',
                'simple_explanation' => 'Target total uang masuk kotor yang ingin Anda capai (belum dipotong modal/iklan).',
                'advanced_explanation' => 'Target Top-Line bulanan. Angka acuan untuk dipecah kembali (reverse-engineered) menjadi target penjualan unit harian.',
                'formula' => null
            ],
            [
                'key' => 'daily_sales_target',
                'title' => 'Daily Sales Target',
                'simple_explanation' => 'Berapa banyak barang (atau paket minuman/jasa) yang HARUS terjual SETIAP HARI agar target bulanan tercapai.',
                'advanced_explanation' => 'Pecahan mikro dari Target Volume Bulanan dibagi jumlah hari operasional. KPI utama bagi tim sales.',
                'formula' => 'Target Unit Bulanan / 30 Hari'
            ],
            [
                'key' => 'daily_ad_spend',
                'title' => 'Daily Ad Spend',
                'simple_explanation' => 'Batas maksimal biaya harian untuk bayar iklan Facebook/TikTok ads.',
                'advanced_explanation' => 'Pecahan dari Monthly Budget yang di-set pada level Campaign/AdSet (Daily Budget) untuk smoothing algorithm delivery.',
                'formula' => 'Monthly Ads Budget / 30 Hari'
            ],
            [
                'key' => 'cpa_target',
                'title' => 'CPA Target (Max Cost per Purchase)',
                'simple_explanation' => 'Batas paling mahal biaya iklan yang rela kamu bayar hanya untuk MENDAPATKAN 1 PESANAN.',
                'advanced_explanation' => 'Maximum acceptable Cost Per Acquisition agar unit economics kampanye iklan tetap profit (Breakeven CPA).',
                'formula' => 'Unit Profit - Desired Net Profit per unit'
            ],
            [
                'key' => 'traffic_quality',
                'title' => 'Traffic Quality',
                'simple_explanation' => 'Seberapa \'haus\' atau tepat sasaran pengunjung website kamu. Pengunjung asal-asalan (low quality) tidak akan beli.',
                'advanced_explanation' => 'Relevansi audiens terhadap offer. Traffic quality yang rendah akan menggerus conversion rate dan melonjakkan CPA.',
                'formula' => null
            ],
            [
                'key' => 'offer_strength',
                'title' => 'Offer Strength',
                'simple_explanation' => 'Seberapa menggoda penawaran kamu. Bukan sekadar diskon, tapi benefit yang bikin orang merasa rugi kalau nggak beli sekarang.',
                'advanced_explanation' => 'Daya pikat irresistible offer (Penawaran Tak Terbantahkan), mencakup garansi, bonus, urgensi, dan risk-reversal.',
                'formula' => null
            ],
            [
                'key' => 'funnel_leak',
                'title' => 'Funnel Leak (Kebocoran Funnel)',
                'simple_explanation' => 'Orang sudah klik iklan web, udah masukin ke keranjang tapi kabur (nggak jadi transfer/bayar).',
                'advanced_explanation' => 'Titik friksi (drop-off point) dalam spesifik tahap User Journey (misal: Initiate Checkout tapi Drop di Payment Gateway).',
                'formula' => '100% - Persentase Lanjut ke Step Berikut'
            ],
            [
                'key' => 'ltv',
                'title' => 'LTV (Lifetime Value)',
                'simple_explanation' => 'Total uang yang secara rata-rata dihabiskan satu pelanggan selama dia kenal toko kamu (pembelian pertama + repeat order).',
                'advanced_explanation' => 'Net Present Value dari proyeksi profit yang diatribusikan pada masa hubungan dengan pelanggan. Semakin tinggi LTV, semakin berani kita naikkan anggaran CAC.',
                'formula' => 'Rata-rata Nilai Order × Frekuensi Pembelian per Tahun × Umur Berlangganan (Tahun)'
            ],
            [
                'key' => 'profit_simulator',
                'title' => 'Profit Simulator',
                'simple_explanation' => 'Mesin hitung untuk bermain "Bagaimana Jika...". Anda bisa tahu efeknya ke profit jika harga dinaikkan, konversi naik, dsb.',
                'advanced_explanation' => 'Modul uji sensitivitas untuk mensimulasikan dampak perubahan variabel mikro (seperti CPA/BOM) terhadap hasil makro (Net Profit).',
                'formula' => null
            ],
            [
                'key' => 'mentor_logic',
                'title' => 'Mentor Lab & Roadmap',
                'simple_explanation' => 'Konsultan AI virtual yang membaca hasil angka plan milikmu dan membuatkan tindakan step-by-step.',
                'advanced_explanation' => 'Engine LLM yang men-generate Standard Operating Procedure (SOP) spesifik berdasar celah/gap finansial dari plan pengguna.',
                'formula' => null
            ],
            // --- MENTOR LAB SCORING PILLARS ---
            [
                'key' => 'score_feasibility',
                'title' => 'Feasibility (Kelayakan Bisnis)',
                'simple_explanation' => 'Status seberapa masuk akal rencana omzet/profit ini dikerjakan di dunia nyata.',
                'advanced_explanation' => 'Indikator validasi asumsi matematis bisnis berdasar rasio standar industri (khususnya benchmark ROAS & CPA).',
                'formula' => null
            ],
            [
                'key' => 'score_profitability',
                'title' => 'Profitability (Tingkat Keuntungan)',
                'simple_explanation' => 'Mengukur seberapa \'tebal\' margin keuntungan atau sisa uang bersih yang Anda dapat setelah dipotong semua pengeluaran.',
                'advanced_explanation' => 'Kesehatan margin bersih (Net Margin) dibandingkan dengan gross revenue. Skor rendah berarti bisnis berjalan, tapi laba terlalu tipis.',
                'formula' => null
            ],
            [
                'key' => 'score_risk',
                'title' => 'Risk Safety (Keamanan Risiko)',
                'simple_explanation' => 'Tingkat keamanan bisnis Anda dari kebangkrutan gara-gara modal iklan atau budget bulanan yang jebol.',
                'advanced_explanation' => 'Ketahanan operasional (Runway) dan Margin of Safety terhadap membengkaknya Customer Acquisition Cost (CAC) sebelum break-even.',
                'formula' => null
            ],
            [
                'key' => 'score_efficiency',
                'title' => 'Efficiency (Tingkat Efisiensi)',
                'simple_explanation' => 'Seberapa hebat Anda meminimalisir biaya iklan atau operasional tapi tetap mendapat pengunjung (traffic) yang maksimal.',
                'advanced_explanation' => 'Skor efektivitas budget (Return on Ad Spend) serta minimnya Funnel Drop-off Rate dalam mengeksekusi konversi penjualan.',
                'formula' => null
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
