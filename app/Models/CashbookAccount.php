<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbookAccount extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'balance_cached',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'balance_cached' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(CashbookTransaction::class, 'account_id');
    }
}
