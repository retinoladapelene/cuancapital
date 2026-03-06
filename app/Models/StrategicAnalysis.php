<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StrategicAnalysis extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'session_id',
        'business_type',
        'current_revenue',
        'target_revenue',
        'profit_margin',
        'available_capital',
        'team_size',
        'market_position',
        'primary_bottleneck',
        'risk_tolerance',
        'timeline_months',
        'ai_analysis',
        'feasibility_score',
        'key_recommendations',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'input_snapshot' => 'array',
        'metrics_snapshot' => 'array',
        'created_at' => 'datetime',
    ];
}
