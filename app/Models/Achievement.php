<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'code',
        'title',
        'description',
        'icon',
        'category',
        'trigger_event',
        'condition_value',
        'badge_id',
        'border_frame_id',
        'xp_reward',
        'is_hidden',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function borderFrame()
    {
        return $this->belongsTo(BorderFrame::class);
    }
}
