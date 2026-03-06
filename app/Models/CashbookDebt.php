<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbookDebt extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'user_id',
        'debt_name',
        'debt_type',
        'total_amount',
        'remaining_amount',
        'interest_rate',
        'due_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'remaining_amount' => 'float',
        'interest_rate' => 'float',
        'due_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function installments()
    {
        return $this->hasMany(CashbookDebtInstallment::class, 'debt_id');
    }
}
