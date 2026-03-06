<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserMetric extends Model
{
    protected $fillable = [
        'user_id',
        'lessons_completed',
        'courses_completed',
        'simulations_passed',
        'perfect_runs',
        'blueprints_saved',
        'roadmap_items_completed',
        'mentor_lab_count',
        'goal_planner_count',
        'minutes_spent',
        'total_xp',
        'level',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
