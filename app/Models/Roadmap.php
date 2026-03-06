<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roadmap extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'strategic_analysis_id',
        'target_goal',
        'timeline_months',
        'is_active',
        'current_phase',
        'completion_percentage',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function simulation()
    {
        return $this->belongsTo(Simulation::class);
    }

    public function steps()
    {
        // Order by 'order' by default
        return $this->hasMany(RoadmapStep::class)->orderBy('order');
    }
}
