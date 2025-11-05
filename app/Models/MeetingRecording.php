<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingRecording extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id', 'contribution_id', 'file_path', 'file_name', 'duration_seconds', 
        'created_by', 'processing_status', 'processed_at', 'processing_error'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function contribution(): BelongsTo
    {
        return $this->belongsTo(Contribution::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}


