<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name', 'display_name', 'department_id', 'level'];

    protected $casts = [
        'level' => 'integer',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function usersInDepartment(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_department_role')
                    ->wherePivot('department_id', $this->department_id)
                    ->withTimestamps();
    }

    public function getDisplayNameAttribute($value): string
    {
        return $value ?: ucfirst(str_replace('_', ' ', $this->name));
    }
}
