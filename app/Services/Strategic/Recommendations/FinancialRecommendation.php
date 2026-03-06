<?php

namespace App\Services\Strategic\Recommendations;

use App\Services\Strategic\Contracts\RecommendationContract;
use App\Enums\StrategyCategory;

class FinancialRecommendation implements RecommendationContract
{
    public function category(): StrategyCategory
    {
        return StrategyCategory::FINANCIAL;
    }

    public function actionItems(?string $diagnosisCode = null): array
    {
        return match($diagnosisCode) {
            'cashflow_crisis' => [
                'Hentikan semua pengeluaran non-esensial sekarang juga.',
                'Prioritas 1: konversi piutang/stok jadi kas dalam 7 hari.',
                'Hubungi supplier untuk negosiasi tempo bayar 30–45 hari.',
                'Fokus ke produk margin tertinggi saja sampai kas stabil.',
            ],
            'low_cash_buffer' => [
                'Bangun emergency fund minimal 2× biaya bulanan sebelum scale.',
                'Reduce stok berlebih — konversi ke kas dulu.',
                'Cari revenue cepat: flash sale, bundle, atau upsell ke existing customer.',
            ],
            'operating_at_loss' => [
                'Audit biaya detail — identifikasi biaya terbesar yang bisa dipotong.',
                'Naikkan harga segera, bahkan 10% saja sudah berdampak besar ke margin.',
                'Hentikan channel/produk yang paling boncos dalam 14 hari.',
                'Hitung break-even per produk — jual hanya yang menguntungkan.',
            ],
            'margin_compression', 'thin_margin' => [
                'Naikkan harga — reframe value proposition agar tidak terasa mahal.',
                'Negosiasi ulang harga supplier atau ganti supplier lebih murah.',
                'Bundle produk untuk naikkan AOV tanpa naikkan biaya per unit.',
                'Stop diskon terlalu dalam — diskon membunuh margin lebih cepat dari apapun.',
            ],
            default => [
                'Hitung ulang Break Even Point (BEP).',
                'Simulasikan ulang proyeksi cashflow 6 bulan.',
                'Revisi target revenue agar lebih realistis.',
            ],
        };
    }

    public function tools(?string $diagnosisCode = null): array
    {
        return ['CuanCapital Simulator', 'Google Sheets', 'QuickBooks / Xero'];
    }

    public function estimatedWeeks(?string $diagnosisCode = null): int
    {
        return match($diagnosisCode) {
            'cashflow_crisis' => 1,
            'operating_at_loss' => 2,
            default => 4,
        };
    }

    public function expectedImpact(?string $diagnosisCode = null): array
    {
        return [
            'revenue_increase_percent' => 5,
            'cost_reduction_percent' => 20,
        ];
    }

    public function numericScore(?string $diagnosisCode = null): int
    {
        return match($diagnosisCode) {
            'cashflow_crisis' => 100,
            'operating_at_loss' => 95,
            'margin_compression' => 85,
            'thin_margin' => 85,
            'low_cash_buffer' => 80,
            default => 70,
        };
    }
}
