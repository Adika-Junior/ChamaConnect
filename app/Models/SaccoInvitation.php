<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaccoInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id','email','phone','token','invited_by','accepted_at','expires_at'
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expires_at' => 'datetime',
    ];
}


