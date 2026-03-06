<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSimulationResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'simulation_id',
        'attempts',
        'score',
        'result_json',
        'rating',
        'completed_at',
    ];

    protected $casts = [
        'result_json' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function simulation()
    {
        return $this->belongsTo(Simulation::class);
    }
}
