<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'rarity',
        'rarity_weight',
    ];

    public function achievements()
    {
        return $this->hasMany(Achievement::class);
    }
}
