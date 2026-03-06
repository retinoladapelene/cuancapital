<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadmapProgress extends Model
{
    use HasFactory;

    protected $table = 'roadmap_progress';

    protected $fillable = [
        'user_id',
        'roadmap_id',
        'phase_number',
        'status',
        'completed_actions',
        'notes',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
