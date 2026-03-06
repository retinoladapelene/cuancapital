<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'endpoint',
        'method',
        'status_code',
        'latency_ms',
        'user_id',
        'created_at',
        'updated_at'
    ];
}
