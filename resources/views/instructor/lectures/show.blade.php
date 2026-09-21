@extends('layouts.instructor-timeline')

@section('title', __('instructor.lecture_details') . ' - ' . $lecture->title)
@section('page_title', __('instructor.lecture_details'))

@push('scripts')
<script>
function updateAttendance(studentId, status) {
    fetch('{{ route("instructor.lectures.update-attendance", $lecture) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ student_id: studentId, status: status, _token: '{{ csrf_token() }}' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || @json(__('instructor.attendance_update_error')));
    })
    .catch(() => alert(@json(__('instructor.attendance_update_error'))));
}

function updateStatus(status) {
    fetch('{{ route("instructor.lectures.update-status", $lecture) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || @json(__('instructor.status_update_error')));
    })
    .catch(() => alert(@json(__('instructor.status_update_error'))));
}
</script>
@endpush

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $statusChip = match ($lecture->status) {
        'scheduled' => 'su-soft-1',
        'in_progress' => 'su-chip--warn',
        'completed' => 'su-chip--ok',
        default => 'su-chip--off',
    };
    $statusLabel = match ($lecture->status) {
        'scheduled' => __('instructor.scheduled_lecture'),
        'in_progress' => __('instructor.in_progress_status'),
        'completed' => __('instructor.completed_status'),
        default => __('instructor.cancelled_lecture'),
    };
@endphp
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.lectures.index') }}">{{ __('instructor.lectures') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ Str::limit($lecture->title, 60) }}</strong>
            </nav>
            <h1 class="su-page-head__title">{{ $lecture->title }}</h1>
            <div class="su-chip-row">
                <span class="su-chip {{ $statusChip }}">{{ $statusLabel }}</span>
                @if($lecture->course)
                    <span class="su-chip">{{ Str::limit($lecture->course->title, 40) }}</span>
                @endif
                @if($lecture->scheduled_at)
                    <span class="su-chip su-soft-2">
                        <i class="fas fa-calendar" aria-hidden="true"></i>
                        <x-app-datetime :at="$lecture->scheduled_at" pattern="Y/m/d H:i" />
                    </span>
                @endif
            </div>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.lectures.edit', $lecture) }}" class="su-btn su-btn--primary">
                <i class="fas fa-edit" aria-hidden="true"></i>
                {{ __('common.edit') }}
            </a>
            <a href="{{ route('instructor.lectures.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.present') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($attendanceStats['present'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.late') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($attendanceStats['late'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-clock" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.partial') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($attendanceStats['partial'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-user-clock" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.absent') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($attendanceStats['absent'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-times-circle" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.total') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($attendanceStats['total_students'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-users" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <div class="su-detail-grid">
        <div style="display:flex;flex-direction:column;gap:16px;min-width:0">
            <section class="su-card">
                <h2 class="su-card__title"><i class="fas fa-info-circle" aria-hidden="true"></i> {{ __('instructor.lecture_details') }}</h2>
                <div class="su-dl" style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                    <div class="su-dl__item">
                        <label>{{ __('common.status') }}</label>
                        <div class="su-chip-row" style="margin:0">
                            <span class="su-chip {{ $statusChip }}">{{ $statusLabel }}</span>
                            @if($lecture->status != 'completed' && $lecture->status != 'cancelled')
                                <select onchange="updateStatus(this.value)" class="su-select" style="width:auto;min-width:9rem;height:32px">
                                    <option value="scheduled" {{ $lecture->status == 'scheduled' ? 'selected' : '' }}>{{ __('instructor.scheduled_lecture') }}</option>
                                    <option value="in_progress" {{ $lecture->status == 'in_progress' ? 'selected' : '' }}>{{ __('instructor.in_progress_status') }}</option>
                                    <option value="completed" {{ $lecture->status == 'completed' ? 'selected' : '' }}>{{ __('instructor.completed_status') }}</option>
                                    <option value="cancelled" {{ $lecture->status == 'cancelled' ? 'selected' : '' }}>{{ __('instructor.cancelled_lecture') }}</option>
                                </select>
                            @endif
                        </div>
                    </div>
                    <div class="su-dl__item">
                        <label>{{ __('instructor.date_time') }}</label>
                        <div class="tabular-nums"><x-app-datetime :at="$lecture->scheduled_at" pattern="Y/m/d H:i" /></div>
                    </div>
                    <div class="su-dl__item">
                        <label>{{ __('instructor.duration_minutes_label') }}</label>
                        <div class="tabular-nums">{{ $lecture->duration_minutes }} {{ __('instructor.minute_unit') }}</div>
                    </div>
                    <div class="su-dl__item">
                        <label>{{ __('instructor.course_label') }}</label>
                        <div>{{ $lecture->course->title ?? '—' }}</div>
                    </div>
                    @if($lecture->lesson)
                        <div class="su-dl__item">
                            <label>{{ __('instructor.lesson_label') }}</label>
                            <div>{{ $lecture->lesson->title }}</div>
                        </div>
                    @endif
                </div>
                @if($lecture->description)
                    <div class="su-prose-box" style="margin-top:16px">
                        <label>{{ __('instructor.description') }}</label>
                        <div class="su-prose-body">{{ $lecture->description }}</div>
                    </div>
                @endif
                @if($lecture->notes)
                    <div class="su-prose-box" style="margin-top:16px">
                        <label>{{ __('instructor.notes_section') }}</label>
                        <div class="su-prose-body">{{ $lecture->notes }}</div>
                    </div>
                @endif
                @if($lecture->recording_url)
                    <div style="margin-top:16px">
                        <a href="{{ $lecture->recording_url }}" target="_blank" rel="noopener" class="su-btn">
                            <i class="fas fa-play-circle" aria-hidden="true"></i>
                            {{ __('instructor.lecture_recordings') }}
                            <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                        </a>
                    </div>
                @endif
            </section>

            @if($lecture->has_attendance_tracking)
                <section class="su-card su-card--flush">
                    <div class="su-section-head" style="padding:12px 16px;margin:0;border-bottom:1px solid var(--su-line)">
                        <h3><i class="fas fa-clipboard-list" aria-hidden="true"></i> {{ __('instructor.attendance') }}</h3>
                        <span class="su-chip">{{ $attendanceStats['total_students'] ?? 0 }}</span>
                    </div>
                    <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
                        @if($enrollments->count() > 0)
                            <table class="su-table">
                                <thead>
                                    <tr>
                                        <th>{{ __('instructor.student') }}</th>
                                        <th>{{ __('common.status') }}</th>
                                        <th>{{ __('instructor.attendance_minutes') }}</th>
                                        <th>{{ __('instructor.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                        @php $record = $attendanceRecords->get($enrollment->user_id); @endphp
                                        <tr>
                                            <td>
                                                <strong style="font-weight:600">{{ $enrollment->user->name ?? '—' }}</strong>
                                                <div style="font-size:12px;color:var(--su-ink-40)">{{ $enrollment->user->email ?? '' }}</div>
                                            </td>
                                            <td>
                                                @if($record)
                                                    @php
                                                        $achip = match ($record->status) {
                                                            'present' => 'su-chip--ok',
                                                            'late' => 'su-chip--warn',
                                                            'partial' => 'su-soft-1',
                                                            default => 'su-chip--off',
                                                        };
                                                        $alabel = match ($record->status) {
                                                            'present' => __('instructor.present'),
                                                            'late' => __('instructor.late'),
                                                            'partial' => __('instructor.partial'),
                                                            default => __('instructor.absent'),
                                                        };
                                                    @endphp
                                                    <span class="su-chip {{ $achip }}">{{ $alabel }}</span>
                                                @else
                                                    <span class="su-chip su-chip--off">{{ __('instructor.unspecified') }}</span>
                                                @endif
                                            </td>
                                            <td class="tabular-nums">
                                                {{ $record && isset($record->attendance_minutes) ? $record->attendance_minutes : 0 }} / {{ $lecture->duration_minutes }}
                                                @if($record && isset($record->attendance_percentage) && $record->attendance_percentage)
                                                    <div style="font-size:12px;color:var(--su-ink-40)">{{ number_format($record->attendance_percentage, 1) }}%</div>
                                                @endif
                                            </td>
                                            <td>
                                                <select onchange="updateAttendance({{ $enrollment->user_id }}, this.value)" class="su-select" style="min-width:8rem;height:32px">
                                                    <option value="present" {{ $record && $record->status == 'present' ? 'selected' : '' }}>{{ __('instructor.present') }}</option>
                                                    <option value="late" {{ $record && $record->status == 'late' ? 'selected' : '' }}>{{ __('instructor.late') }}</option>
                                                    <option value="partial" {{ $record && $record->status == 'partial' ? 'selected' : '' }}>{{ __('instructor.partial') }}</option>
                                                    <option value="absent" {{ !$record || $record->status == 'absent' ? 'selected' : '' }}>{{ __('instructor.absent') }}</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="su-empty">
                                <i class="fas fa-users" aria-hidden="true"></i>
                                <p>{{ __('instructor.no_enrolled_students') }}</p>
                            </div>
                        @endif
                    </div>
                </section>
            @endif
        </div>

        <aside style="display:flex;flex-direction:column;gap:16px;min-width:0">
            <section class="su-card">
                <h2 class="su-card__title"><i class="fas fa-cog" aria-hidden="true"></i> {{ __('instructor.options_section') }}</h2>
                <div class="su-meta-list">
                    <div class="su-meta-row" style="justify-content:space-between">
                        <span>{{ __('instructor.attendance_tracking') }}</span>
                        <span class="su-chip {{ $lecture->has_attendance_tracking ? 'su-chip--ok' : 'su-chip--off' }}">
                            {{ $lecture->has_attendance_tracking ? __('instructor.enabled') : __('instructor.inactive') }}
                        </span>
                    </div>
                    <div class="su-meta-row" style="justify-content:space-between">
                        <span>{{ __('instructor.has_assignment') }}</span>
                        <span class="su-chip {{ $lecture->has_assignment ? 'su-chip--ok' : 'su-chip--off' }}">
                            {{ $lecture->has_assignment ? __('common.yes') : __('common.no') }}
                        </span>
                    </div>
                    <div class="su-meta-row" style="justify-content:space-between">
                        <span>{{ __('instructor.has_evaluation') }}</span>
                        <span class="su-chip {{ $lecture->has_evaluation ? 'su-chip--ok' : 'su-chip--off' }}">
                            {{ $lecture->has_evaluation ? __('common.yes') : __('common.no') }}
                        </span>
                    </div>
                </div>
            </section>
            <section class="su-card">
                <h2 class="su-card__title"><i class="fas fa-bolt" aria-hidden="true"></i> {{ __('instructor.quick_actions') }}</h2>
                <div style="display:flex;flex-direction:column;gap:8px">
                    <a href="{{ route('instructor.lectures.edit', $lecture) }}" class="su-btn" style="justify-content:flex-start">
                        <i class="fas fa-edit" aria-hidden="true"></i> {{ __('instructor.edit_lecture') }}
                    </a>
                    @if($lecture->course)
                        <a href="{{ route('instructor.courses.show', $lecture->course) }}" class="su-btn" style="justify-content:flex-start">
                            <i class="fas fa-book" aria-hidden="true"></i> {{ __('instructor.view_course') }}
                        </a>
                    @endif
                    @if(Route::has('instructor.attendance.lecture'))
                        <a href="{{ route('instructor.attendance.lecture', $lecture) }}" class="su-btn" style="justify-content:flex-start">
                            <i class="fas fa-clipboard-list" aria-hidden="true"></i> {{ __('instructor.attendance_details') }}
                        </a>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
