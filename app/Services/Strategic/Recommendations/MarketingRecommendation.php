<?php

namespace App\Services\Strategic\Recommendations;

use App\Services\Strategic\Contracts\RecommendationContract;
use App\Enums\StrategyCategory;

class MarketingRecommendation implements RecommendationContract
{
    public function category(): StrategyCategory
    {
        return StrategyCategory::MARKETING;
    }

    public function actionItems(?string $diagnosisCode = null): array
    {
        return match($diagnosisCode) {
            'ads_dependency_critical' => [
                'Mulai build organic channel: konten, SEO, atau komunitas.',
                'Program referral — manfaatkan customer existing untuk akuisisi baru.',
                'Bangun email/WA list — aset traffic yang tidak bisa di-turn-off.',
                'Sementara kurangi ad budget hingga margin kembali sehat.',
            ],
            'ads_dependency_moderate' => [
                'Audit ROAS per campaign — matikan yang ROAS <2× segera.',
                'Fokus ads ke LTV tinggi — retargeting customer lama lebih murah.',
                'Diversifikasi: mulai 1 channel organic sebagai backup.',
            ],
            'low_retention' => [
                'Buat follow-up otomatis H+3 setelah pembelian pertama.',
                'Program loyalty sederhana — poin atau diskon khusus repeat buyer.',
                'Hubungi 10 customer pertama bulan ini — tanyakan kenapa tidak repeat.',
                'Tawarkan subscription/bundling jangka panjang untuk lock-in.',
            ],
            'large_revenue_gap' => [
                'Gap terlalu besar — turunkan target jangka pendek, fokus ke foundation.',
                'Lakukan customer interview — pahami mengapa orang tidak beli.',
                'Uji coba 3–5 channel akuisisi berbeda, double down yang berhasil.',
            ],
            default => [
                'Lakukan riset keyword dan optimasi SEO.',
                'Buat 10 konten edukasi untuk social media.',
                'Optimasi konversi halaman landing page / website.',
            ],
        };
    }

    public function tools(?string $diagnosisCode = null): array
    {
        return match($diagnosisCode) {
            'low_retention' => ['Mailchimp / WA Autoresponder', 'Loyalty App'],
            'ads_dependency_critical', 'ads_dependency_moderate' => ['Meta Ads Manager', 'Google Search Console'],
            default => ['Google Search Console', 'Ahrefs', 'Canva'],
        };
    }

    public function estimatedWeeks(?string $diagnosisCode = null): int
    {
        return match($diagnosisCode) {
            'ads_dependency_critical' => 4,
            'low_retention' => 2,
            default => 3,
        };
    }

    public function expectedImpact(?string $diagnosisCode = null): array
    {
        return match($diagnosisCode) {
            'low_retention' => [
                'revenue_increase_percent' => 20,
                'cost_reduction_percent' => 0,
            ],
            'ads_dependency_critical' => [
                'revenue_increase_percent' => 0,
                'cost_reduction_percent' => 30,
            ],
            default => [
                'revenue_increase_percent' => 10,
                'cost_reduction_percent' => 0,
            ],
        };
    }

    public function numericScore(?string $diagnosisCode = null): int
    {
        return match($diagnosisCode) {
            'ads_dependency_critical' => 90,
            'low_retention' => 85,
            'large_revenue_gap' => 75,
            'ads_dependency_moderate' => 70,
            default => 60,
        };
    }
}
