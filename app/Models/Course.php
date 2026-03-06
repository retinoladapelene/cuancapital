<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'title', 'description', 'level', 'category',
        'thumbnail', 'xp_reward', 'lessons_count', 'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'xp_reward'      => 'integer',
        'lessons_count'  => 'integer',
    ];

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function userProgress()
    {
        return $this->hasMany(UserCourseProgress::class);
    }

    public function simulation()
    {
        return $this->hasOne(Simulation::class, 'module_id');
    }

    public function simulations()
    {
        return $this->hasMany(Simulation::class, 'module_id')->orderBy('id');
    }
}
