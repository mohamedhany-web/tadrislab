@extends('layouts.instructor-timeline')

@section('title', __('instructor.attendance_absence'))
@section('page_title', __('instructor.attendance_absence'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-clipboard-list su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.attendance_absence') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.attendance_manage_desc') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.courses.index') }}" class="su-btn">
                <i class="fas fa-book" aria-hidden="true"></i>
                {{ __('instructor.courses') }}
            </a>
            <a href="{{ route('instructor.lectures.index') }}" class="su-btn su-btn--primary">
                <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
                {{ __('instructor.lectures') }}
            </a>
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.lectures') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total_lectures'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-chalkboard-teacher" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.attendance_records') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total_attendance_records'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-clipboard-list" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.present') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['present_count'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.absent') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['absent_count'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-times-circle" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <section class="su-card" style="margin-bottom:20px">
        <form method="GET" class="su-form-grid" style="grid-template-columns:repeat(auto-fit,minmax(140px,1fr))">
            <div class="su-field">
                <label for="course_id">{{ __('instructor.course') }}</label>
                <select name="course_id" id="course_id" class="su-select">
                    <option value="">{{ __('instructor.all_courses') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="su-field">
                <label for="status">{{ __('instructor.lecture_status') }}</label>
                <select name="status" id="status" class="su-select">
                    <option value="">{{ __('instructor.all') }}</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>{{ __('instructor.scheduled_lecture') }}</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{ __('instructor.in_progress') }}</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('instructor.completed') }}</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('instructor.cancelled_lecture') }}</option>
                </select>
            </div>
            <div class="su-field">
                <label for="date_from">{{ __('instructor.date_from') }}</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="su-input">
            </div>
            <div class="su-field">
                <label for="date_to">{{ __('instructor.date_to') }}</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="su-input">
            </div>
            <div class="su-form-actions" style="align-items:flex-end">
                <button type="submit" class="su-btn su-btn--primary" style="height:40px">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    {{ __('common.search') }}
                </button>
                @if(request()->anyFilled(['course_id', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('instructor.attendance.index') }}" class="su-btn" style="height:40px;width:40px;padding:0;justify-content:center" title="{{ __('common.reset') ?? 'Reset' }}">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </form>
    </section>

    @if($lectures->count() > 0)
        <div class="su-list">
            @foreach($lectures as $lecture)
                @php
                    $chip = match ($lecture->status) {
                        'scheduled' => 'su-soft-1',
                        'in_progress' => 'su-chip--warn',
                        'completed' => 'su-chip--ok',
                        default => 'su-chip--off',
                    };
                    $statusLabel = match ($lecture->status) {
                        'scheduled' => __('instructor.scheduled_lecture'),
                        'in_progress' => __('instructor.in_progress'),
                        'completed' => __('instructor.completed'),
                        default => __('instructor.cancelled_lecture'),
                    };
                @endphp
                <div class="su-list-item su-card" style="margin-bottom:12px">
                    <div class="su-list-item__body">
                        <div class="su-list-item__title" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center">
                            {{ $lecture->title }}
                            <span class="su-chip {{ $chip }}">{{ $statusLabel }}</span>
                        </div>
                        <div class="su-list-item__meta">
                            <span><i class="fas fa-book" aria-hidden="true"></i> {{ $lecture->course->title ?? '—' }}</span>
                            <span><i class="fas fa-calendar" aria-hidden="true"></i>
                                {{ $lecture->scheduled_at ? $lecture->scheduled_at->format('Y/m/d H:i') : '—' }}
                            </span>
                            @if($lecture->attendance_records_count > 0)
                                <span>{{ $lecture->attendance_records_count }} {{ __('instructor.attendance_record_single') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="su-list-item__actions">
                        <a href="{{ route('instructor.attendance.lecture', $lecture) }}" class="su-btn su-btn--primary" style="height:32px">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                            {{ __('instructor.view_attendance') }}
                        </a>
                        <a href="{{ route('instructor.lectures.show', $lecture) }}" class="su-btn" style="height:32px;width:32px;padding:0;justify-content:center" title="{{ __('common.view') }}">
                            <i class="fas fa-info-circle" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        @if(method_exists($lectures, 'links') && $lectures->hasPages())
            <div class="su-pager" style="margin-top:16px">{{ $lectures->links() }}</div>
        @endif
    @else
        <section class="su-card">
            <div class="su-empty">
                <i class="fas fa-clipboard-check" aria-hidden="true"></i>
                <p>{{ __('instructor.no_lectures') }}</p>
                <p style="color:var(--su-ink-40);font-size:13px;margin:0">{{ __('instructor.no_lectures_yet') }}</p>
            </div>
        </section>
    @endif
</div>
@endsection
