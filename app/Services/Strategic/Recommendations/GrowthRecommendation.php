<?php

namespace App\Services\Strategic\Recommendations;

use App\Services\Strategic\Contracts\RecommendationContract;
use App\Enums\StrategyCategory;

class GrowthRecommendation implements RecommendationContract
{
    public function category(): StrategyCategory
    {
        return StrategyCategory::GROWTH;
    }

    public function actionItems(?string $diagnosisCode = null): array
    {
        return match($diagnosisCode) {
            'scaling_without_base' => [
                'Tunda scaling — investasi ke margin dulu sebelum scale.',
                'Buktikan unit economics positif di 1 channel dulu.',
                'Hitung contribution margin per unit — scaling harus profitable dari unit pertama.',
            ],
            default => [
                'Lakukan split test A/B pada halaman penawaran utama.',
                'Eksplorasi partnership atau kolaborasi dengan brand/influencer.',
                'Scale up budget promo bertahap 20% setiap minggu.',
            ],
        };
    }

    public function tools(?string $diagnosisCode = null): array
    {
        return match($diagnosisCode) {
            'scaling_without_base' => ['CuanCapital Simulator', 'Excel'],
            default => ['Google Analytics', 'Google Optimize', 'Hotjar'],
        };
    }

    public function estimatedWeeks(?string $diagnosisCode = null): int
    {
        return 4;
    }

    public function expectedImpact(?string $diagnosisCode = null): array
    {
        return match($diagnosisCode) {
            'scaling_without_base' => [
                'revenue_increase_percent' => 5,
                'cost_reduction_percent' => 20,
            ],
            default => [
                'revenue_increase_percent' => 30,
                'cost_reduction_percent' => 0,
            ],
        };
    }

    public function numericScore(?string $diagnosisCode = null): int
    {
        return match($diagnosisCode) {
            'scaling_without_base' => 90,
            default => 60,
        };
    }
}
