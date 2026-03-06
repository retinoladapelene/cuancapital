<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserBadge extends Model
{
    protected $fillable = [
        'user_id',
        'badge_id',
        'is_equipped',
        'unlocked_at',
    ];

    protected $casts = [
        'is_equipped' => 'boolean',
        'unlocked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}
