<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbookDisciplineLog extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'did_input_today',
        'transaction_count',
        'last_input_time',
        'is_skipped',
    ];

    protected $casts = [
        'date' => 'date',
        'did_input_today' => 'boolean',
        'is_skipped' => 'boolean',
        'last_input_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
