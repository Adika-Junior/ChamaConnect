<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaccoRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','registration_number','registered_at','address','county','contact_email','contact_phone',
        'certificate_path','bylaws_path','officials','status','submitted_by','reviewed_by','rejection_reason'
    ];

    protected $casts = [
        'registered_at' => 'date',
        'officials' => 'array',
    ];

    public function submitter(): BelongsTo { return $this->belongsTo(User::class, 'submitted_by'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
}


