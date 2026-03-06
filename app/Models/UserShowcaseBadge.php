<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserShowcaseBadge extends Model
{
    protected $table = 'user_showcase_badges';

    protected $fillable = [
        'user_id',
        'achievement_id', // Key string e.g. "roadmap_builder"
        'slot_index',     // 1, 2, or 3
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
