<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBorderFrame extends Model
{
    protected $fillable = [
        'user_id',
        'border_frame_id',
        'unlocked_at',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function borderFrame()
    {
        return $this->belongsTo(BorderFrame::class);
    }
}
