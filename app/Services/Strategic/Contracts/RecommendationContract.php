<?php

namespace App\Services\Strategic\Contracts;

use App\Enums\StrategyCategory;

interface RecommendationContract
{
    /**
     * Get the category this recommendation belongs to.
     */
    public function category(): StrategyCategory;

    /**
     * Get the list of actionable items.
     * Optionally takes the specific diagnosis code if further specialization is needed.
     */
    public function actionItems(?string $diagnosisCode = null): array;

    /**
     * Get recommended tools for this category/diagnosis.
     */
    public function tools(?string $diagnosisCode = null): array;

    /**
     * Get estimated timeline in weeks.
     */
    public function estimatedWeeks(?string $diagnosisCode = null): int;

    /**
     * Get expected impact estimation (e.g. revenue increase %, cost reduction %).
     */
    public function expectedImpact(?string $diagnosisCode = null): array;

    /**
     * Numeric priority score (0-100) for sorting.
     */
    public function numericScore(?string $diagnosisCode = null): int;
}
