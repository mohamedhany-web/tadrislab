<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Delivery ledger for the Platform Notification Layer (Brief V3).
 * Expansion-ready for WhatsApp / Email / SMS / Push without requiring all channels in MVP.
 */
class NotificationDelivery extends Model
{
    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_FAILED = 'failed';

    public const STATUS_SKIPPED = 'skipped';

    protected $fillable = [
        'event_key',
        'channel',
        'status',
        'notifiable_type',
        'notifiable_id',
        'recipient_email',
        'recipient_phone',
        'recipient_name',
        'subject',
        'body',
        'provider_response',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'sent_at' => 'datetime',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
