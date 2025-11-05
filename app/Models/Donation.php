<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'donor_name',
        'donor_email',
        'donor_phone',
        'currency',
        'amount_cents',
        'campaign_id',
        'mpesa_receipt',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function amountFormatted(): string
    {
        return $this->currency.' '.number_format($this->amount_cents / 100, 2);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    protected $fillable = [
        'campaign_id',
        'donor_id',
        'donor_name',
        'amount',
        'is_anonymous',
        'message',
        'payment_id',
        'metadata',
        'payment_status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(ContributionPayment::class);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous Donor';
        }
        return $this->donor_name ?: ($this->donor ? $this->donor->name : 'Anonymous');
    }
}

