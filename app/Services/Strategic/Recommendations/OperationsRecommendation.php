<?php

namespace App\Services\Strategic\Recommendations;

use App\Services\Strategic\Contracts\RecommendationContract;
use App\Enums\StrategyCategory;

class OperationsRecommendation implements RecommendationContract
{
    public function category(): StrategyCategory
    {
        return StrategyCategory::OPERATIONS;
    }

    public function actionItems(?string $diagnosisCode = null): array
    {
        return match($diagnosisCode) {
            'ops_chaos' => [
                'Buat SOP untuk 3 proses terpenting dalam bisnis minggu ini.',
                'Otomasi tugas repetitif — gunakan tools gratis dulu (Notion, Google Sheet).',
                'Delegasikan — kamu tidak bisa scale kalau semuanya lewat kamu.',
            ],
            'strategy_unclear' => [
                'Pilih 1 metric utama untuk 90 hari ke depan — jangan semuanya.',
                'Tulis ICP (Ideal Customer Profile) — siapa yang paling profitable?',
                'Buat sprint 2 minggu dengan maksimal 3 prioritas saja.',
            ],
            default => [
                'Dokumentasikan alur kerja harian (Daily Workflow).',
                'Gunakan project management tool untuk track task.',
                'Review KPI mingguan dengan tim.',
            ],
        };
    }

    public function tools(?string $diagnosisCode = null): array
    {
        return ['Notion', 'Trello', 'Google Workspace'];
    }

    public function estimatedWeeks(?string $diagnosisCode = null): int
    {
        return 2;
    }

    public function expectedImpact(?string $diagnosisCode = null): array
    {
        return [
            'revenue_increase_percent' => 0,
            'cost_reduction_percent' => 15,
        ];
    }

    public function numericScore(?string $diagnosisCode = null): int
    {
        return match($diagnosisCode) {
            'ops_chaos' => 80,
            'strategy_unclear' => 75,
            default => 50,
        };
    }
}
