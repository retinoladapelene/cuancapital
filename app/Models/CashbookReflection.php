<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbookReflection extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'what_worked',
        'what_failed',
        'improvement_plan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
