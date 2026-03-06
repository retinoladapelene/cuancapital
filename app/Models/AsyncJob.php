<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AsyncJob extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'input_parameters',
        'reference_type',
        'reference_id',
        'error_message'
    ];

    protected $casts = [
        'input_parameters' => 'array',
    ];
}
