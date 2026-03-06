<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimulationOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'step_id',
        'label',
        'effect_json',
        'feedback_text',
    ];

    protected $casts = [
        'effect_json' => 'array',
    ];

    public function step()
    {
        return $this->belongsTo(SimulationStep::class, 'step_id');
    }
}
