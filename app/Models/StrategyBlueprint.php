<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrategyBlueprint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'strategic_analysis_ids',
        'simulations_completed',
        'modules_completed',
        'performance_metrics',
        'current_phase',
        'business_valuation',
        'risk_score',
        'cashflow_health',
        'created_at',
        'updated_at' // added timestamps in case they are manually assigned, otherwise not strictly needed
    ];

    protected $casts = [
        'default_actions' => 'array',
    ];
}
