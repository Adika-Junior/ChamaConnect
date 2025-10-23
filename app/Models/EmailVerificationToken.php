<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailVerificationToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'email', 'token', 'created_by', 'expires_at', 'verified_at'
    ];

    protected $dates = ['expires_at', 'verified_at'];
}
