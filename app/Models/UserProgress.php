<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    protected $table = 'user_progress';

    protected $fillable = [
        'user_id',
        'experience_version',
        'xp_points',
        'level',
        'achievements',
        // Milestone flags (backward compat)
        'is_rgp_completed',
        'is_simulator_used',
        'is_mentor_completed',
        'is_roadmap_generated',
        'rgp_completed_at',
        'simulator_used_at',
        'mentor_completed_at',
        'roadmap_generated_at',
        'roadmap_completion_percent',
    ];

    protected $casts = [
        // Gamification Phase 3B
        'xp_points'                  => 'integer',
        'level'                      => 'integer',
        'achievements'               => 'array',
        // Milestone flags
        'is_rgp_completed'           => 'boolean',
        'is_simulator_used'          => 'boolean',
        'is_mentor_completed'        => 'boolean',
        'is_roadmap_generated'       => 'boolean',
        'rgp_completed_at'           => 'datetime',
        'simulator_used_at'          => 'datetime',
        'mentor_completed_at'        => 'datetime',
        'roadmap_generated_at'       => 'datetime',
        'roadmap_completion_percent' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
