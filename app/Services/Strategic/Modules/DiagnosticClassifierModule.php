<?php

namespace App\Services\Strategic\Modules;

use App\DTO\StrategicInput;
use Illuminate\Support\Arr;

/**
 * DiagnosticClassifierModule
 *
 * Identifies specific business problems based on actual data + self-reported
 * problem areas. Returns a list of Diagnosis objects, each with:
 *  - code        (string identifier)
 *  - label       (human-readable name)
 *  - severity    ('critical' | 'warning' | 'info')
 *  - description (1-line explanation)
 */
class DiagnosticClassifierModule
{
    /** Severity weights for sorting (lower = more severe) */
    private const SEVERITY_WEIGHT = ['critical' => 1, 'warning' => 2, 'info' => 3];

    public function diagnose(StrategicInput $input): array
    {
        $diagnoses = [];

        if (! $input->hasDiagnosticData()) {
            // Not enough data — return empty (engine will skip diagnostic section)
            return [];
        }

        $rev     = $input->actualRevenue;
        $exp     = $input->actualExpenses;
        $profit  = $input->actualProfit();
        $cash    = $input->cashBalance;
        $target  = $input->targetRevenue;
        $adSpend = $input->adSpend;
        $repeat  = $input->repeatRate;
        $aov     = $input->avgOrderValue;
        $problems = $input->problemAreas;

        // ── 1. CASHFLOW CRISIS ────────────────────────────────────────────
        if ($exp > 0 && $cash < $exp) {
            $months = $exp > 0 ? round($cash / $exp, 1) : 0;
            $descriptions = [
                "Saldo kas hanya cukup untuk {$months} bulan pengeluaran. Bisnis dalam zona bahaya.",
                "Kas yang tersedia sangat menipis, hanya menutupi {$months} bulan pengeluaran.",
                "Posisi likuiditas kritis: dana tunai tersisa untuk operasional {$months} bulan ke depan."
            ];
            $diagnoses[] = $this->make(
                'cashflow_crisis',
                'Cashflow Crisis',
                'critical',
                Arr::random($descriptions)
            );
        } elseif ($exp > 0 && $cash < ($exp * 2)) {
            $descriptions = [
                'Kas hanya cukup <2 bulan — berisiko jika ada order besar atau biaya tak terduga.',
                'Bantalan dana darurat kurang dari 2 bulan, operasional berjalan di area rentan.',
                'Likuiditas menipis, disarankan memiliki buffer kas 3 bulan untuk keamanan.'
            ];
            $diagnoses[] = $this->make(
                'low_cash_buffer',
                'Buffer Kas Tipis',
                'warning',
                Arr::random($descriptions)
            );
        }

        // ── 2. NEGATIVE PROFIT / BLEEDING ────────────────────────────────
        if ($profit < 0) {
            $descriptions = [
                'Revenue tidak menutupi biaya. Bisnis sedang "berdarah" setiap bulan.',
                'Operasional membakar kas. Pendapatan saat ini gagal menyubsidi pengeluaran rutin.',
                'Tingkat kerugian struktural terdeteksi, memerlukan pemotongan beban operasional segera.'
            ];
            $diagnoses[] = $this->make(
                'operating_at_loss',
                'Operasional Merugi',
                'critical',
                Arr::random($descriptions)
            );
        }

        // ── 3. MARGIN COMPRESSION ─────────────────────────────────────────
        if ($rev > 0) {
            $actualMarginPct = ($profit / $rev) * 100;
            if ($actualMarginPct < 10 && $profit >= 0) {
                $descriptions = [
                    "Margin aktual hanya " . round($actualMarginPct, 1) . "% — sulit scale tanpa memperbaiki struktur biaya.",
                    "Tingkat margin " . round($actualMarginPct, 1) . "% sangat rawan tergerus biaya pemasaran tambahan.",
                    "Keuntungan bersih " . round($actualMarginPct, 1) . "% membatasi ruang gerak untuk melakukan ekspansi agresif."
                ];
                $diagnoses[] = $this->make(
                    'margin_compression',
                    'Margin Sangat Tipis',
                    'critical',
                    Arr::random($descriptions)
                );
            } elseif ($actualMarginPct < 20 && $profit >= 0) {
                $descriptions = [
                    "Margin " . round($actualMarginPct, 1) . "% — aman tapi rentan. Target minimal 25–30% untuk bisa scale.",
                    "Margin " . round($actualMarginPct, 1) . "% masih di bawah benchmark ideal untuk pertumbuhan eksponensial.",
                    "Dengan margin " . round($actualMarginPct, 1) . "%, Anda harus fokus pada efisiensi sebelum menambah akuisisi."
                ];
                $diagnoses[] = $this->make(
                    'thin_margin',
                    'Margin Di Bawah Ideal',
                    'warning',
                    Arr::random($descriptions)
                );
            }
        }

        // ── 4. ADS DEPENDENCY RISK ───────────────────────────────────────
        if ($adSpend > 0 && $rev > 0) {
            $adRatio = ($adSpend / $rev) * 100;
            if ($adRatio > 50) {
                $descriptions = [
                    round($adRatio, 0) . "% revenue habis untuk ads. Satu perubahan algoritma bisa matikan bisnis.",
                    "Ketergantungan akuisisi berbayar ekstrem. " . round($adRatio, 0) . "% omset tergerus iklan.",
                    "Struktur bisnis sangat rentan karena memakan " . round($adRatio, 0) . "% dari total penjualan untuk iklan."
                ];
                $diagnoses[] = $this->make(
                    'ads_dependency_critical',
                    'Ketergantungan Ads Tinggi',
                    'critical',
                    Arr::random($descriptions)
                );
            } elseif ($adRatio > 30) {
                $descriptions = [
                    round($adRatio, 0) . "% revenue untuk ads. Perlu diversifikasi traffic ke organic/referral.",
                    "Suntikan dana iklan memakan " . round($adRatio, 0) . "%. Mulai kurangi porsi dengan pemasaran organik.",
                    "Beban pemasaran digital " . round($adRatio, 0) . "% cukup tinggi, mengancam profitabilitas akhir."
                ];
                $diagnoses[] = $this->make(
                    'ads_dependency_moderate',
                    'Biaya Ads Terlalu Besar',
                    'warning',
                    Arr::random($descriptions)
                );
            }
        }

        // ── 5. REVENUE STAGNATION GAP ─────────────────────────────────────
        if ($target > 0 && $rev > 0) {
            $gapPct = (($target - $rev) / $target) * 100;
            if ($gapPct > 60) {
                $descriptions = [
                    "Revenue aktual hanya " . round(100 - $gapPct, 0) . "% dari target. Growth signifikan diperlukan.",
                    "Deviasi dari angka target masih cukup curam (" . round($gapPct, 0) . "%).",
                    "Momentum penjualan hanya memenuhi " . round(100 - $gapPct, 0) . "% dari ekspektasi awal."
                ];
                $diagnoses[] = $this->make(
                    'large_revenue_gap',
                    'Gap Revenue Besar',
                    'warning',
                    Arr::random($descriptions)
                );
            }
        }

        // ── 6. LOW CUSTOMER RETENTION ─────────────────────────────────────
        if ($repeat > 0 && $repeat < 20) {
            $descriptions = [
                "Hanya {$repeat}% pembeli repeat. Biaya akuisisi terus tinggi tanpa LTV.",
                "Tingkat kembalinya pelanggan hanya {$repeat}%. Fundamental retensi membutuhkan perhatian segera.",
                "Loyalty pelanggan ada di angka {$repeat}%. Bisnis selalu harus mencari darah baru untuk hidup."
            ];
            $diagnoses[] = $this->make(
                'low_retention',
                'Retensi Pelanggan Rendah',
                'warning',
                Arr::random($descriptions)
            );
        } elseif (in_array('low_retention', $problems)) {
            $descriptions = [
                'Pembeli tidak kembali — biaya akuisisi selalu tinggi dan LTV rendah.',
                'Absennya siklus repeat order membunuh profitabilitas dari margin backend.',
                'Kebocoran pelanggan kronis. Fokus akuisisi terasa sia-sia tanpa strategi retensi.'
            ];
            $diagnoses[] = $this->make(
                'low_retention',
                'Retensi Pelanggan Rendah',
                'warning',
                Arr::random($descriptions)
            );
        }

        // ── 7. SCALE WITHOUT FOUNDATION (Intent mismatch) ─────────────────
        if ($input->riskIntent === 'scale_fast') {
            $margin = $rev > 0 ? $profit / $rev : 0;
            if ($margin < 0.15) {
                $descriptions = [
                    'Ingin scale fast tapi margin <15%. Scaling sekarang akan memperbesar kerugian, bukan profit.',
                    'Target agresif namun margin ( ' . round($margin*100) . '% ) belum sehat. Perbaiki efisiensi dulu.',
                    'Premature scaling terdeteksi. Margin tipis akan membuat ekspansi semakin boros.'
                ];
                $diagnoses[] = $this->make(
                    'scaling_without_base',
                    'Scale Tanpa Fondasi',
                    'critical',
                    Arr::random($descriptions)
                );
            }
        }

        // ── 8. SELF-REPORTED PROBLEMS (dari problem selector) ─────────────
        if (in_array('ops_chaos', $problems)) {
            $descriptions = [
                'Tanpa SOP yang solid, growth akan menciptakan chaos bukan scale.',
                'Keluhan operasional berlebih: Sistem dan pembagian tugas memerlukan standardisasi ketat.',
                'Gesekan di lini eksekusi harian akan menghambat mesin pemasaran, bagaimanapun hebatnya.'
            ];
            $diagnoses[] = $this->make(
                'ops_chaos',
                'Operasional Tidak Terstruktur',
                'warning',
                Arr::random($descriptions)
            );
        }

        if (in_array('no_focus', $problems)) {
            $descriptions = [
                'Tidak ada fokus yang jelas — energi tersebar tanpa impact.',
                'Distraksi berlebih memecah porsi modal dan perhatian. Kembali ke satu penawaran inti.',
                'Produk dan target market melebar terlalu luas, mematikan traksi spesialisasi.'
            ];
            $diagnoses[] = $this->make(
                'strategy_unclear',
                'Strategi Tidak Jelas',
                'info',
                Arr::random($descriptions)
            );
        }

        if (in_array('stagnant_revenue', $problems)) {
            $descriptions = [
                'Pertumbuhan revenue stagnan, mengindikasikan perlunya evaluasi ulang pada produk atau penetrasi pasar.',
                'Omzet bulanan cenderung datar, strategi akuisisi pelanggan baru mungkin sudah mencapai titik jenuh.',
                'Tanda-tanda stagnasi penjualan terlihat nyata, membutuhkan penyegaran pada kampanye penawaran Anda.'
            ];
            $diagnoses[] = $this->make(
                'stagnant_revenue',
                'Sales Stagnan',
                'warning',
                Arr::random($descriptions)
            );
        }

        $existingCodes = array_column($diagnoses, 'code');

        if (in_array('cashflow_crisis', $problems) && !in_array('cashflow_crisis', $existingCodes)) {
            $descriptions = [
                'Laporan mungkin menunjukkan profit, namun realitas kas berlawanan. Kemungkinan ada kebocoran atau masalah piutang.',
                'Siklus konversi kas bermasalah. Uang tertahan di inventory atau tersendat di piutang pelanggan.',
                'Meski secara teori untung, uang tunai menipis. Segera audit perputaran uang masuk dan keluar.'
            ];
            $diagnoses[] = $this->make(
                'cashflow_crisis',
                'Krisis Kas Aktual',
                'critical',
                Arr::random($descriptions)
            );
        }

        if (in_array('margin_compression', $problems) && !in_array('margin_compression', $existingCodes) && !in_array('thin_margin', $existingCodes)) {
            $descriptions = [
                'Terdapat tekanan besar pada margin keuntungan akibat biaya yang terus merangkak naik.',
                'Harga jual sulit dinaikkan sementara ongkos operasional dan pemasaran membesar.',
                'Ruang gerak profitabilitas semakin sempit, efisiensi struktur biaya menjadi kewajiban absolut.'
            ];
            $diagnoses[] = $this->make(
                'margin_compression',
                'Tekanan Margin',
                'warning',
                Arr::random($descriptions)
            );
        }

        if (in_array('ads_dependency_moderate', $problems) && !in_array('ads_dependency_moderate', $existingCodes) && !in_array('ads_dependency_critical', $existingCodes)) {
            $descriptions = [
                'Biaya akuisisi berbayar menekan keuntungan. Membutuhkan kanal distribusi yang lebih organik.',
                'Terlalu bergantung pada iklan membakar modal kerja dengan ROI yang stagnan.',
                'Suntikan dana iklan terasa sia-sia tanpa strategi retensi atau up-sell yang solid.'
            ];
            $diagnoses[] = $this->make(
                'ads_dependency_moderate',
                'Ketergantungan Ads',
                'warning',
                Arr::random($descriptions)
            );
        }

        // Sort by severity (critical first)
        usort($diagnoses, fn($a, $b) =>
            self::SEVERITY_WEIGHT[$a['severity']] <=> self::SEVERITY_WEIGHT[$b['severity']]
        );

        return $diagnoses;
    }

    private function make(string $code, string $label, string $severity, string $description): array
    {
        return compact('code', 'label', 'severity', 'description');
    }

    /**
     * Build a human-readable problem summary from diagnoses.
     */
    public function buildSummary(array $diagnoses): string
    {
        if (empty($diagnoses)) {
            return 'Data aktual tidak tersedia — menampilkan proyeksi berbasis target.';
        }

        $critical = array_filter($diagnoses, fn($d) => $d['severity'] === 'critical');
        $warnings = array_filter($diagnoses, fn($d) => $d['severity'] === 'warning');

        if (count($critical) > 0) {
            $labels = implode(', ', array_column(array_slice(array_values($critical), 0, 2), 'label'));
            $summaries = [
                "Terdeteksi " . count($critical) . " masalah kritis: {$labels}. Butuh tindakan segera.",
                "Perhatian: Terdapat " . count($critical) . " area berisiko tinggi ({$labels}) yang perlu perbaikan instan.",
                "Segera selesaikan " . count($critical) . " kendala krusial berikut: {$labels}."
            ];
            return Arr::random($summaries);
        }

        if (count($warnings) > 0) {
            $labels = implode(', ', array_column(array_slice(array_values($warnings), 0, 2), 'label'));
            $summaries = [
                "Terdeteksi beberapa area yang perlu diperbaiki: {$labels}.",
                "Fokus optimasi di " . count($warnings) . " sektor kunci: {$labels}.",
                "Ada ruang pertumbuhan tersembunyi jika Anda mengatasi perbaikan pada: {$labels}."
            ];
            return Arr::random($summaries);
        }

        $summaries = [
            'Kondisi bisnis relatif baik — fokus pada optimasi dan scaling.',
            'Secara makro indikator stabil. Waktunya menaikkan porsi ekspektasi ke volume.',
            'Fundamental kuat. Sekarang saatnya mendorong limit kapasitas untuk scale.'
        ];
        return Arr::random($summaries);
    }
}
