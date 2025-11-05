<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringContributionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id','user_id','recipient_name','recipient_email','recipient_phone',
        'amount_cents','currency','interval','day_of_month','weekday','start_date','end_date',
        'status','next_run_at','metadata',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_run_at' => 'datetime',
        'metadata' => 'array',
    ];
}


