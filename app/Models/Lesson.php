<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id', 'title', 'content', 'order', 'type',
        'xp_reward', 'estimated_minutes',
    ];

    protected $casts = [
        'xp_reward'          => 'integer',
        'order'              => 'integer',
        'estimated_minutes'  => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
