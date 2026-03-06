<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadmapLibraryStep extends Model
{
    use HasFactory;

    protected $table = 'roadmap_library_steps';

    protected $fillable = [
        'title',
        'description',
        'category',
        'stage',
        'channel',
        'difficulty',
        'impact',
        'priority_weight',
        'conditions',
        'outcome_type',
        'required_budget',
        'required_time',
        'tags',
    ];

    protected $casts = [
        'conditions' => 'array',
        'tags' => 'array',
    ];
}
