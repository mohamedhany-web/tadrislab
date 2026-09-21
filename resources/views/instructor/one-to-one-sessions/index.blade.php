@extends('layouts.instructor-timeline')

@section('title', __('instructor.o1o_title'))
@section('page_title', __('instructor.o1o_title'))

@section('content')
@php
    $lessonDuration = (int) ($lessonDuration ?? 50);
@endphp

<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-user-graduate su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.o1o_title') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.o1o_subtitle') }}</p>
        </div>
        <div class="su-page-head__actions">
            @if(Route::has('instructor.one-to-one-availability.index'))
                <a href="{{ route('instructor.one-to-one-availability.index') }}" class="su-btn su-btn--primary">
                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                    {{ __('instructor.o1a_title') }}
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            {{ session('success') }}
        </div>
    @endif

    @if(($newAssignments ?? collect())->isNotEmpty())
        <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:20px">
            @foreach($newAssignments as $assignment)
                @php
                    $student = $assignment->student;
                    $age = null;
                    if ($student?->birth_date) {
                        $age = $student->birth_date->age;
                    }
                    $related = ($students ?? collect())->first(fn ($row) => (int) ($row['student']->id ?? 0) === (int) ($student->id ?? 0));
                @endphp
                <section class="su-card su-soft-4" style="padding:16px">
                    <div class="su-chip-row" style="margin-bottom:8px">
                        <span class="su-chip su-chip--warn">{{ __('instructor.o1o_new_student') }}</span>
                    </div>
                    <p style="margin:0 0 12px;font-size:13px;color:var(--su-ink)">
                        {{ __('instructor.o1o_new_student_body', ['name' => $student->name ?? __('instructor.pm_student_fallback')]) }}
                    </p>
                    <div class="su-meta-list">
                        <div class="su-meta-row">
                            <span class="su-meta-ico su-soft-1"><i class="fas fa-user" aria-hidden="true"></i></span>
                            <span>{{ __('instructor.o1o_student') }}:</span>
                            <strong>{{ $student->name ?? '—' }}</strong>
                        </div>
                        <div class="su-meta-row">
                            <span class="su-meta-ico su-soft-2"><i class="fas fa-birthday-cake" aria-hidden="true"></i></span>
                            <span>{{ __('instructor.o1o_age') }}:</span>
                            <strong>{{ $age !== null ? $age : '—' }}</strong>
                        </div>
                        <div class="su-meta-row">
                            <span class="su-meta-ico su-soft-3"><i class="fas fa-book" aria-hidden="true"></i></span>
                            <span>{{ __('instructor.o1o_subject_scope') }}:</span>
                            <strong>{{ $related['course']->title ?? $assignment->scopeLabel() }}</strong>
                        </div>
                        <div class="su-meta-row">
                            <span class="su-meta-ico su-soft-4"><i class="fas fa-list-ol" aria-hidden="true"></i></span>
                            <span>{{ __('instructor.o1o_lessons') }}:</span>
                            <strong>{{ $related['total'] ?? '—' }}</strong>
                        </div>
                        <div class="su-meta-row">
                            <span class="su-meta-ico su-soft-1"><i class="fas fa-play" aria-hidden="true"></i></span>
                            <span>{{ __('instructor.o1o_plan_starts') }}:</span>
                            <strong>{{ optional($related['starts_at'] ?? $assignment->starts_at)->format('Y-m-d') ?? '—' }}</strong>
                        </div>
                        <div class="su-meta-row">
                            <span class="su-meta-ico su-soft-2"><i class="fas fa-flag-checkered" aria-hidden="true"></i></span>
                            <span>{{ __('instructor.o1o_plan_ends') }}:</span>
                            <strong>{{ optional($related['ends_at'] ?? $assignment->ends_at)->format('Y-m-d') ?? '—' }}</strong>
                        </div>
                        <div class="su-meta-row">
                            <span class="su-meta-ico su-soft-3"><i class="fas fa-sticky-note" aria-hidden="true"></i></span>
                            <span>{{ __('instructor.o1o_notes') }}:</span>
                            <strong>{{ $related['notes'] ?? $assignment->notes ?? '—' }}</strong>
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
    @endif

    <section class="su-card" style="margin-bottom:20px">
        <h2 class="su-card__title">
            <i class="fas fa-calendar-day" aria-hidden="true"></i>
            {{ __('instructor.o1o_today_schedule') }}
        </h2>
        <div class="su-list">
            @forelse($todaysSchedule ?? [] as $slot)
                @php
                    $dur = (int) ($slot->duration_minutes ?: $lessonDuration);
                    $end = $slot->scheduled_at?->copy()->addMinutes($dur);
                @endphp
                <article class="su-list-item">
                    <span class="su-list-item__ico su-soft-1">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                    </span>
                    <div class="su-list-item__body">
                        <div class="su-list-item__title">
                            {{ $slot->course->title ?? __('instructor.cal_private') }}
                            — {{ $slot->student->name ?? '—' }}
                        </div>
                        <div class="su-list-item__meta">
                            <span class="tabular-nums"><x-app-datetime :at="$slot->scheduled_at" pattern="g:i A" /></span>
                            · {{ $dur }} {{ __('instructor.o1o_minutes') }}
                            @if($end)
                                · <x-app-datetime :at="$slot->scheduled_at" pattern="g:i A" />–<x-app-datetime :at="$end" pattern="g:i A" />
                            @endif
                        </div>
                    </div>
                    <div class="su-list-item__actions">
                        <span class="su-chip su-chip--ok">{{ __('instructor.o1o_upcoming') }}</span>
                        <a href="{{ route('instructor.one-to-one-sessions.show', $slot) }}" class="su-btn" style="height:32px">
                            {{ __('instructor.o1o_manage') }}
                        </a>
                    </div>
                </article>
            @empty
                <div class="su-empty" style="padding:24px 8px">
                    <i class="fas fa-calendar" aria-hidden="true"></i>
                    <p>{{ __('instructor.o1o_no_today') }}</p>
                </div>
            @endforelse
        </div>
    </section>

    @if(($students ?? collect())->isNotEmpty())
        <section class="su-card" style="margin-bottom:20px">
            <h2 class="su-card__title">
                <i class="fas fa-users" aria-hidden="true"></i>
                {{ __('instructor.o1o_students') }}
            </h2>
            <div class="su-list">
                @foreach($students as $row)
                    <div class="su-list-item">
                        <span class="su-list-item__ico su-soft-2">
                            <i class="fas fa-user" aria-hidden="true"></i>
                        </span>
                        <div class="su-list-item__body">
                            <div class="su-list-item__title">{{ $row['student']->name ?? '—' }}</div>
                            <div class="su-list-item__meta">
                                {{ $row['course']->title ?? '' }}
                                · {{ $row['pending'] }} {{ __('instructor.o1o_pending') }}
                                · {{ $row['scheduled'] }} {{ __('instructor.o1o_scheduled') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="su-card su-card--flush">
        <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
            <table class="su-table">
                <thead>
                    <tr>
                        <th>{{ __('instructor.o1o_student') }}</th>
                        <th>{{ __('instructor.o1o_subject_scope') }}</th>
                        <th>#</th>
                        <th>{{ __('instructor.o1o_status') }}</th>
                        <th>{{ __('instructor.o1o_time') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        <tr>
                            <td><strong style="font-weight:600">{{ $session->student->name ?? '—' }}</strong></td>
                            <td style="color:var(--su-ink-40)">{{ $session->course->title ?? '—' }}</td>
                            <td class="tabular-nums">{{ $session->session_number }}</td>
                            <td><span class="su-chip">{{ $session->statusLabel() }}</span></td>
                            <td class="tabular-nums" style="color:var(--su-ink-40)">
                                @if($session->scheduled_at)
                                    <x-app-datetime :at="$session->scheduled_at" pattern="Y-m-d H:i" />
                                @else
                                    —
                                @endif
                            </td>
                            <td style="text-align:end">
                                <a href="{{ route('instructor.one-to-one-sessions.show', $session) }}" class="su-btn" style="height:32px">
                                    {{ __('instructor.o1o_manage') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="su-empty">
                                    <i class="fas fa-user-graduate" aria-hidden="true"></i>
                                    <p>{{ __('instructor.o1o_empty') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($sessions, 'links') && $sessions->hasPages())
            <div class="su-pager" style="padding:12px">{{ $sessions->links() }}</div>
        @endif
    </section>
</div>
@endsection
