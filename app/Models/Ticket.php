<?php

/**
 * Ticket model for support conversations.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Represents a support ticket with messages and status transitions.
 */
class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'status',
        'priority',
        'closed_at',
        'last_reply_by',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    /**
     * Get the user who created the ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all messages associated with the ticket.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at');
    }

    /**
     * Get the most recent message for the ticket.
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(TicketMessage::class)->latestOfMany();
    }

    /**
     * Scope query to tickets owned by a specific user.
     */
    public function scopeOwnedBy($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope query to tickets with a given status.
     */
    public function scopeStatus($query, ?string $status)
    {
        if ($status) {
            $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Mark the ticket as closed.
     */
    public function markClosed(): void
    {
        $this->forceFill([
            'status' => 'closed',
            'closed_at' => now(),
        ])->save();
    }

    /**
     * Reopen the ticket.
     */
    public function reopen(): void
    {
        $this->forceFill([
            'status' => 'open',
            'closed_at' => null,
        ])->save();
    }
}
