<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CashbookTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'type',
        'amount',
        'note',
        'transaction_date',
        'reference_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(CashbookAccount::class, 'account_id');
    }

    public function category()
    {
        return $this->belongsTo(CashbookCategory::class, 'category_id');
    }

    public function transferPair()
    {
        return $this->belongsTo(self::class, 'reference_id');
    }
}
