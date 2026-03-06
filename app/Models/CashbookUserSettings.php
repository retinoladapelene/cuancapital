<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbookUserSettings extends Model
{
    protected $fillable = [
        'user_id',
        'saving_rate_target',
        'timezone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
