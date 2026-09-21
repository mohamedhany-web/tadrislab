<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorLearningPathAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'learning_path_id',
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

    public function path(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class, 'learning_path_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
