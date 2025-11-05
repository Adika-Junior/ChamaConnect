<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Contribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'category', 'kind', 'sacco_rule', 'target_amount', 'collected_amount',
        'status', 'deadline', 'organizer_id', 'approver_id', 'approved_at',
        'meeting_id', 'group_id', 'rejection_reason'
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'collected_amount' => 'decimal:2',
        'deadline' => 'datetime',
        'approved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'contribution_participants')
                    ->withPivot('amount_contributed', 'payment_method', 'contributed_at', 'notified', 'notified_at')
                    ->withTimestamps();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(ContributionNotification::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ContributionPayment::class);
    }

    public function pledges(): HasMany
    {
        return $this->hasMany(Pledge::class);
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function progressPercentage(): float
    {
        if (!$this->target_amount) {
            return 0;
        }
        return round(($this->collected_amount / $this->target_amount) * 100, 2);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && now()->lte($this->deadline);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'active' && now()->gt($this->deadline);
    }

    public function needsApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
