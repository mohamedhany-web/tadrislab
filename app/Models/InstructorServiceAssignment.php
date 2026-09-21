<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorServiceAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'service_key',
        'is_active',
        'assigned_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
