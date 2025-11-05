<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'actor_id', 'action', 'target_type', 'target_id', 'ip', 'user_agent', 'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}


