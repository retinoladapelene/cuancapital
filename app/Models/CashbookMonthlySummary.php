<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbookMonthlySummary extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'total_income',
        'total_expense',
        'net_cashflow',
        'saving_rate',
        'bocor_ratio',
        'health_score',
    ];

    protected $casts = [
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'net_cashflow' => 'decimal:2',
        'saving_rate' => 'decimal:2',
        'bocor_ratio' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
