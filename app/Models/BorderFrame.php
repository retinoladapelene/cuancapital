<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorderFrame extends Model
{
    protected $fillable = [
        'name',
        'rarity',
        'rarity_weight',
        'css_class',
        'icon',
        'unlock_condition',
    ];

    public function userBorderFrames()
    {
        return $this->hasMany(UserBorderFrame::class);
    }
}
