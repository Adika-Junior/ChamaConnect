<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'contribution_id', 'user_id', 'amount', 'frequency', 'next_run_date', 'last_run_date', 'status', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'next_run_date' => 'date',
        'last_run_date' => 'date',
    ];

    public function contribution(): BelongsTo
    {
        return $this->belongsTo(Contribution::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advanceNextRunDate(): void
    {
        $current = $this->next_run_date?->copy();
        if (!$current) return;
        switch ($this->frequency) {
            case 'daily':
                $this->next_run_date = $current->addDay();
                break;
            case 'weekly':
                $this->next_run_date = $current->addWeek();
                break;
            default:
                $this->next_run_date = $current->addMonth();
        }
        $this->last_run_date = $current;
    }
}


