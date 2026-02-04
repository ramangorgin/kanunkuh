<?php

/**
 * Notification template model for event-driven messaging.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Defines message templates and activation state for notifications.
 */
class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_key',
        'channel',
        'title_template',
        'body_template',
        'sms_template_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope query to active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
