<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'goal_amount',
        'current_amount',
        'status',
        'is_public',
        'allow_anonymous',
        'organizer_id',
        'approved_by',
        'approved_at',
        'started_at',
        'ended_at',
        'rejection_reason',
    ];

    protected $casts = [
        'goal_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'is_public' => 'boolean',
        'allow_anonymous' => 'boolean',
        'approved_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function updates(): HasMany
    {
        return $this->hasMany(CampaignUpdate::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(CampaignExpense::class);
    }

    public function getProgressAttribute(): float
    {
        if ($this->goal_amount <= 0) return 0;
        return min(100, ($this->current_amount / $this->goal_amount) * 100);
    }

    public function getRemainingAttribute(): float
    {
        return max(0, $this->goal_amount - $this->current_amount);
    }

    public function approve(User $approver): void
    {
        $this->update([
            'status' => 'active',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'started_at' => now(),
        ]);
    }

    public function reject(User $approver, string $reason): void
    {
        $this->update([
            'status' => 'cancelled',
            'approved_by' => $approver->id,
            'rejection_reason' => $reason,
        ]);
    }
}

