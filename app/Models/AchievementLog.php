<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AchievementLog extends Model
{
    public $timestamps = false; // Custom 'granted_at' handles this

    protected $fillable = [
        'user_id',
        'achievement_id',
        'trigger_event',
        'context_json',
        'granted_at',
    ];

    protected $casts = [
        'context_json' => 'array',
        'granted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function achievement()
    {
        return $this->belongsTo(Achievement::class);
    }
}
