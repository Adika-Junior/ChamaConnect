<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'type', 'scheduled_at', 'duration',
        'meeting_link', 'status', 'organizer_id', 'contribution_id',
        'has_waiting_room', 'password', 'is_locked'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration' => 'integer',
        'has_waiting_room' => 'boolean',
        'is_locked' => 'boolean',
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_participants')
                    ->withPivot('status', 'joined_at', 'left_at')
                    ->withTimestamps();
    }

    public function contribution(): BelongsTo
    {
        return $this->belongsTo(Contribution::class);
    }

    public function chat(): HasOne
    {
        return $this->hasOne(Chat::class);
    }

    public function recordings(): HasMany
    {
        return $this->hasMany(MeetingRecording::class);
    }

    public function transcripts(): HasMany
    {
        return $this->hasMany(MeetingTranscript::class);
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'scheduled' && now()->lt($this->scheduled_at);
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function canStart(): bool
    {
        return now()->gte($this->scheduled_at->subMinutes(15)); // Allow 15 mins early
    }

    public function syncParticipantsToChat(): void
    {
        if (!$this->chat) {
            return;
        }
        $this->load('participants');
        $userIds = $this->participants->pluck('id')->push($this->organizer_id)->unique()->values();
        $this->chat->participants()->sync($userIds);
    }
}
