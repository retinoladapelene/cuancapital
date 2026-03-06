<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadmapStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'roadmap_id',
        'phase_number',
        'title',
        'description',
        'status',
        'order',
        'timeframe',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'recommended_tools' => 'array',
    ];

    public function roadmap()
    {
        return $this->belongsTo(Roadmap::class);
    }

    public function actions()
    {
        return $this->hasMany(RoadmapAction::class, 'step_id');
    }
}
