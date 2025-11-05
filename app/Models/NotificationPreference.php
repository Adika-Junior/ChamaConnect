<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'email',
        'sms',
        'in_app',
        'push',
        'quiet_hours',
    ];

    protected $casts = [
        'email' => 'boolean',
        'sms' => 'boolean',
        'in_app' => 'boolean',
        'push' => 'boolean',
        'quiet_hours' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isQuietHour(): bool
    {
        if (!$this->quiet_hours) {
            return false;
        }

        $now = now();
        $start = $this->quiet_hours['start'] ?? null;
        $end = $this->quiet_hours['end'] ?? null;

        if (!$start || !$end) {
            return false;
        }

        $currentTime = $now->format('H:i');
        // Handle overnight quiet hours (e.g., 22:00 to 08:00)
        if ($start > $end) {
            return $currentTime >= $start || $currentTime <= $end;
        }

        return $currentTime >= $start && $currentTime <= $end;
    }
}

