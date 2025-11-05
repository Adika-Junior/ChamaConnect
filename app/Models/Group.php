<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'type', 'description', 'treasurer_id', 'secretary_id',
        'created_by', 'total_contributions', 'total_expenses', 'balance',
        'is_public', 'accepting_applications', 'application_requirements',
        'registration_number', 'registered_at', 'by_laws', 'location',
        'contact_email', 'contact_phone', 'min_members', 'member_quota', 'current_members'
    ];

    protected $casts = [
        'total_contributions' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'balance' => 'decimal:2',
        'is_public' => 'boolean',
        'accepting_applications' => 'boolean',
        'registered_at' => 'date',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(GroupApplication::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->withPivot('role', 'total_contributed', 'joined_at')
                    ->withTimestamps();
    }

    public function treasurer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'treasurer_id');
    }

    public function secretary(): BelongsTo
    {
        return $this->belongsTo(User::class, 'secretary_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(GroupExpense::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function updateBalance(): void
    {
        $this->balance = $this->total_contributions - $this->total_expenses;
        $this->save();
    }

    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function hasRole(User $user, string $role): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->wherePivot('role', $role)
            ->exists();
    }

    public function getWasRecentlyApprovedAttribute(): bool
    {
        if (!$this->created_at) return false;
        return $this->created_at->gt(now()->subDays(14));
    }
}

