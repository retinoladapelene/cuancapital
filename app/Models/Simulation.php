<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'intro_text',
        'difficulty_level',
        'xp_reward',
        'scoring_formula',
        'initial_state_json',
    ];

    protected $casts = [
        'initial_state_json' => 'array',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'module_id');
    }

    public function steps()
    {
        return $this->hasMany(SimulationStep::class)->orderBy('order');
    }

    public function results()
    {
        return $this->hasMany(UserSimulationResult::class);
    }

    public function sessions()
    {
        return $this->hasMany(SimulationSession::class);
    }
}
