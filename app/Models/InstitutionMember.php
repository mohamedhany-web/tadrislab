<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionMember extends Model
{
    public const ROLE_COORDINATOR = 'coordinator';

    public const ROLE_PARTICIPANT = 'participant';

    protected $fillable = [
        'institution_id',
        'user_id',
        'member_role',
        'name',
        'email',
        'phone',
        'title',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function displayName(): string
    {
        return $this->name ?: ($this->user?->name ?? '—');
    }

    public function roleLabel(): string
    {
        return $this->member_role === self::ROLE_COORDINATOR ? 'منسق / مسؤول' : 'معلم / مشارك';
    }
}
