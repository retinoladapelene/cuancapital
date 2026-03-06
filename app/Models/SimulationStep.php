<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimulationStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'simulation_id',
        'question',
        'order',
        'step_type',
        'is_irreversible',
    ];

    public function simulation()
    {
        return $this->belongsTo(Simulation::class);
    }

    public function options()
    {
        return $this->hasMany(SimulationOption::class, 'step_id');
    }
}
