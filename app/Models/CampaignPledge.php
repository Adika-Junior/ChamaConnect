<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignPledge extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id','donor_name','donor_email','donor_phone','amount_cents','currency','due_date','status','reminder_count','fulfilled_at','metadata'
    ];

    protected $casts = [
        'due_date' => 'date',
        'fulfilled_at' => 'datetime',
        'metadata' => 'array',
    ];
}


