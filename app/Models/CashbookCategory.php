<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbookCategory extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'pillar',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(CashbookTransaction::class, 'category_id');
    }
}
