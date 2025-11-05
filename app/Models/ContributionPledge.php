<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContributionPledge extends Model
{
    use HasFactory;

    protected $fillable = [
        'rule_id','group_id','user_id','campaign_id','due_date','amount_cents','currency',
        'status','paid_at','reminder_count','metadata',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];
}


