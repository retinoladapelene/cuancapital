<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCourseProgress extends Model
{
    protected $table = 'user_course_progress';

    protected $fillable = [
        'user_id', 'course_id', 'completed_lessons',
        'completed', 'completed_at',
    ];

    protected $casts = [
        'completed'         => 'boolean',
        'completed_at'      => 'datetime',
        'completed_lessons' => 'integer',
    ];
}
