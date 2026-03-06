<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimulationSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'simulation_id',
        'current_step_id',
        'state_json',
        'started_at',
    ];

    protected $casts = [
        'state_json' => 'array',
        'started_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function simulation()
    {
        return $this->belongsTo(Simulation::class);
    }

    public function currentStep()
    {
        return $this->belongsTo(SimulationStep::class, 'current_step_id');
    }
}
