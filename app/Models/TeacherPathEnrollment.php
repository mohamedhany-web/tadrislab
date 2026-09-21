<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** تسجيل معلم (متعلّم مهني) على مسار تطوير — يُفعَّل عادة عبر باقة. */
class TeacherPathEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'learning_path_id',
        'package_id',
        'order_id',
        'status',
        'progress',
        'enrolled_at',
        'completed_at',
        'expires_at',
        'activated_by',
    ];

    protected $casts = [
        'progress' => 'decimal:2',
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function path(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class, 'learning_path_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function activatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function isAccessible(): bool
    {
        if (! in_array($this->status, ['active', 'completed'], true)) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function scopeActiveAccessible($query)
    {
        return $query->whereIn('status', ['active', 'completed'])
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }
}
