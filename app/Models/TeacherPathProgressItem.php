<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** تقدّم المعلم على عنصر داخل المسار (درس أو تطبيق/نشاط/تقييم). */
class TeacherPathProgressItem extends Model
{
    protected $fillable = [
        'user_id',
        'learning_path_id',
        'item_type',
        'item_id',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public const TYPE_LESSON = 'lesson';

    public const TYPE_PRACTICE = 'practice';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function path(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class, 'learning_path_id');
    }
}
