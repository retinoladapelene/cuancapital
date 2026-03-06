<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserAchievement extends Model
{
    protected $fillable = [
        'user_id',
        'achievement_id',
        'progress_cached',
        'unlocked_at',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
        'progress_cached' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
