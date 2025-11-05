<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id', 'user_id', 'status', 'application_data',
        'reason', 'rejection_reason', 'reviewed_by', 'reviewed_at'
    ];

    protected $casts = [
        'application_data' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approve(User $reviewer): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
        
        // Add user to group as member
        $this->group->members()->attach($this->user_id, [
            'role' => 'member',
            'joined_at' => now(),
        ]);
        
        // Update member count
        $this->group->increment('current_members');
    }

    public function reject(User $reviewer, ?string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }
}

