<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blueprint extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'reverse_goal_data',
        'simulation_data',
        'session_id',
        'type',
        'title',
        'persona',
        'data',
        'version',
        'status',
        'linked_source_id',
        'linked_source_type'
    ];

    protected $casts = [
        'reverse_goal_data' => 'array',
        'simulation_data' => 'array',
        'data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
