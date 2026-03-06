<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadmapAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'step_id',
        'action_text',
        'is_completed',
        'order',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'tool_recommendation' => 'array',
    ];

    public function step()
    {
        return $this->belongsTo(RoadmapStep::class, 'step_id');
    }
}
