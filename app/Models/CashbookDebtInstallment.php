<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbookDebtInstallment extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'debt_id',
        'amount',
        'payment_date',
        'notes',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'float',
        'payment_date' => 'date',
    ];

    public function debt()
    {
        return $this->belongsTo(CashbookDebt::class, 'debt_id');
    }

    public function transaction()
    {
        return $this->belongsTo(CashbookTransaction::class, 'transaction_id');
    }
}
