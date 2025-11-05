<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pledge extends Model
{
    use HasFactory;

    protected $fillable = [
        'contribution_id', 'user_id', 'amount', 'pledged_at',
        'due_date', 'status', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'pledged_at' => 'date',
        'due_date' => 'date',
    ];

    public function contribution(): BelongsTo
    {
        return $this->belongsTo(Contribution::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && now()->gt($this->due_date);
    }

    public function markOverdue(): void
    {
        if ($this->isOverdue()) {
            $this->update(['status' => 'overdue']);
        }
    }
}

