<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionProgramParticipant extends Model
{
    protected $fillable = [
        'institution_program_id',
        'institution_member_id',
        'user_id',
        'name',
        'email',
        'phone',
        'status',
        'progress_percent',
        'notes',
    ];

    protected $casts = [
        'progress_percent' => 'integer',
    ];

    public const STATUSES = [
        'enrolled' => 'مسجّل',
        'attended' => 'حضر',
        'completed' => 'أكمل',
        'withdrawn' => 'انسحب',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(InstitutionProgram::class, 'institution_program_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(InstitutionMember::class, 'institution_member_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
