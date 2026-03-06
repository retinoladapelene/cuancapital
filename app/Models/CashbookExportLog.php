<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashbookExportLog extends Model
{
    protected $fillable = [
        'user_id',
        'export_type',
        'file_hash',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
