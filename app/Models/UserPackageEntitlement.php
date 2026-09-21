<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** صلاحيات مستخدم مفعّلة عبر باقة تدريس لاب. */
class UserPackageEntitlement extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'order_id',
        'activated_by',
        'consultation_sessions_total',
        'consultation_sessions_remaining',
        'participant_seats',
        'includes_tools',
        'tools_resources',
        'status',
        'activated_at',
        'expires_at',
    ];

    protected $casts = [
        'consultation_sessions_total' => 'integer',
        'consultation_sessions_remaining' => 'integer',
        'participant_seats' => 'integer',
        'includes_tools' => 'boolean',
        'tools_resources' => 'array',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        return ! $this->expires_at || $this->expires_at->isFuture();
    }

    public function consumeConsultationSession(): bool
    {
        if (! $this->isActive() || $this->consultation_sessions_remaining < 1) {
            return false;
        }

        $this->decrement('consultation_sessions_remaining');

        return true;
    }

    public function restoreConsultationSession(): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        $total = (int) $this->consultation_sessions_total;
        $remaining = (int) $this->consultation_sessions_remaining;
        if ($total > 0 && $remaining >= $total) {
            return false;
        }

        $this->increment('consultation_sessions_remaining');

        return true;
    }
}
