<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'status',
        'created_by',
        'approved_at',
        'approved_by',
        'department_id',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_backup_codes',
        'two_factor_verified_at',
        'calendar_token',
        'digest_frequency',
        'quiet_hours_start',
        'quiet_hours_end',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'two_factor_verified_at' => 'datetime',
            'two_factor_backup_codes' => 'array',
        ];
    }

    public function ensureCalendarToken(): string
    {
        if (!$this->calendar_token) {
            $this->calendar_token = \Illuminate\Support\Str::random(64);
            $this->save();
        }
        return $this->calendar_token;
    }

    /**
     * Check if user is admin (simplified - will be enhanced with role system)
     */
    public function isAdmin(): bool
    {
        if ($this->status !== 'active') return false;
        return $this->roles()->where('name', 'admin')->exists();
    }

    /**
     * Check if user is pending approval
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'user_department_role')
                    ->withPivot('role_id')
                    ->withTimestamps();
    }

    public function rolesInDepartment(Department $department): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_department_role')
                    ->wherePivot('department_id', $department->id)
                    ->withTimestamps();
    }

    public function hasRole(string $roleName, ?Department $department = null): bool
    {
        $query = $this->roles()->where('name', $roleName);
        
        if ($department) {
            $query->where('department_id', $department->id);
        }
        
        return $query->exists();
    }

    public function hasAnyRole(array $roleNames, ?Department $department = null): bool
    {
        $query = $this->roles()->whereIn('name', $roleNames);
        
        if ($department) {
            $query->where('department_id', $department->id);
        }
        
        return $query->exists();
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')
                    ->withPivot('role', 'total_contributed', 'joined_at')
                    ->withTimestamps();
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function getNotificationPreference(string $type): ?NotificationPreference
    {
        return $this->notificationPreferences()->where('type', $type)->first();
    }
}
