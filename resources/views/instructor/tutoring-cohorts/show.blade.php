@extends('layouts.instructor-timeline')

@section('title', __('instructor.tc_show_title') . ' · ' . $cohort->title)
@section('page_title', __('instructor.tc_show_title'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $group = $cohort->tutoringGroup;
@endphp

<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.tutoring-cohorts.index') }}">{{ __('instructor.tc_all_cohorts') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ $cohort->title }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-chalkboard-teacher su-page-head__ico" aria-hidden="true"></i>
                {{ $cohort->title }}
            </h1>
            <p class="su-page-head__sub">
                {{ $group?->title }}
                @if($group?->schoolYear) · {{ $group->schoolYear->name }} @endif
                @if($group?->schoolSubject) · {{ $group->schoolSubject->name }} @endif
                · {{ $cohort->statusLabel() }}
            </p>
            <div class="su-chip-row" style="margin-top:8px">
                <span class="su-chip su-soft-1">{{ $cohort->scheduleSummary() }}</span>
                <span class="su-chip">{{ __('instructor.tc_command_eyebrow') }}</span>
            </div>
        </div>
        <div class="su-page-head__actions">
            @if($cohort->whatsapp_group_url)
                <a href="{{ $cohort->whatsapp_group_url }}" target="_blank" rel="noopener" class="su-btn">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp
                </a>
            @endif
            <a href="{{ route('instructor.tutoring-cohorts.community', $cohort) }}" class="su-btn su-btn--primary">
                <i class="fas fa-comments" aria-hidden="true"></i>
                {{ __('instructor.tc_community') }}
            </a>
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.tc_students_kpi') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($center['students_count']) }}</div>
                <div class="su-kpi__d"><i class="fas fa-user-graduate" aria-hidden="true"></i></div>
            </div>
            <p style="margin:6px 0 0;font-size:11px;color:var(--su-ink-40);font-weight:600">
                {{ $cohort->enrolled_count }} / {{ $cohort->capacity }} {{ __('instructor.tc_cap') }}
            </p>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.tc_active_today') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($center['active_today']) }}</div>
                <div class="su-kpi__d"><i class="fas fa-bolt" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.tc_avg_progress') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ $center['average_progress'] }}%</div>
                <div class="su-kpi__d"><i class="fas fa-chart-line" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.tc_sessions_kpi') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ $center['sessions_completed'] }}/{{ $center['sessions_total'] }}</div>
                <div class="su-kpi__d"><i class="fas fa-calendar-check" aria-hidden="true"></i></div>
            </div>
            <p style="margin:6px 0 0;font-size:11px;color:var(--su-ink-40);font-weight:600">
                {{ $center['sessions_upcoming'] }} {{ __('instructor.tc_upcoming_count') }}
            </p>
        </div>
    </section>

    @if($center['at_risk']->isNotEmpty())
        <section class="su-card" style="margin-bottom:20px;border-color:rgba(239,68,68,.25);background:rgba(239,68,68,.06)">
            <h2 class="su-card__title" style="color:#b91c1c">
                <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
                {{ __('instructor.tc_at_risk') }}
                <span class="su-chip su-chip--off">{{ $center['at_risk']->count() }}</span>
            </h2>
            <div class="su-list">
                @foreach($center['at_risk']->take(8) as $risk)
                    <div class="su-list-item" style="background:var(--su-bg-2)">
                        <span class="su-list-item__ico su-soft-4">
                            <i class="fas fa-user" aria-hidden="true"></i>
                        </span>
                        <div class="su-list-item__body">
                            <div class="su-list-item__title">{{ $risk->name }}</div>
                            <div class="su-list-item__meta">
                                @if($risk->present_count === 0 && $risk->completed_sessions >= 2)
                                    {{ __('instructor.tc_no_attendance_risk') }}
                                @else
                                    {{ __('instructor.tc_days_silent', ['days' => $risk->days_silent]) }}
                                @endif
                            </div>
                        </div>
                        @if($risk->email)
                            <div class="su-list-item__actions">
                                <a href="mailto:{{ $risk->email }}" class="su-btn" style="height:32px">{{ __('instructor.tc_remind') }}</a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div style="display:grid;gap:16px;margin-bottom:20px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr))">
        <section class="su-card su-card--flush" style="margin:0">
            <h2 class="su-card__title" style="padding:8px 8px 0">
                <i class="fas fa-video" aria-hidden="true"></i>
                {{ __('instructor.tc_upcoming_sessions') }}
            </h2>
            <div class="su-list">
                @forelse($center['upcoming_sessions'] as $session)
                    <div class="su-list-item">
                        <span class="su-list-item__ico su-soft-1">
                            <i class="fas fa-calendar" aria-hidden="true"></i>
                        </span>
                        <div class="su-list-item__body">
                            <div class="su-list-item__title">{{ $session->displayTitle() }}</div>
                            <div class="su-list-item__meta">
                                {{ $session->starts_at?->format('Y-m-d g:i A') }} · {{ $session->statusLabel() }}
                            </div>
                        </div>
                        @if($session->classroomMeeting?->code)
                            <div class="su-list-item__actions">
                                <a href="{{ url('classroom/join/'.$session->classroomMeeting->code) }}" class="su-btn su-btn--primary" style="height:32px">
                                    {{ __('instructor.tc_enter_room') }}
                                </a>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="su-empty">
                        <i class="fas fa-calendar" aria-hidden="true"></i>
                        <p>{{ __('instructor.tc_no_upcoming') }}</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="su-card su-card--flush" style="margin:0">
            <h2 class="su-card__title" style="padding:8px 8px 0">
                <i class="fas fa-users" aria-hidden="true"></i>
                {{ __('instructor.tc_roster') }}
                <span class="su-chip">{{ $center['roster']->count() }}</span>
            </h2>
            <div class="su-list" style="max-height:28rem;overflow-y:auto">
                @forelse($center['roster'] as $student)
                    <div class="su-list-item">
                        <span class="su-list-item__ico {{ $student->is_at_risk ? 'su-soft-4' : 'su-soft-2' }}">
                            <i class="fas fa-user-graduate" aria-hidden="true"></i>
                        </span>
                        <div class="su-list-item__body">
                            <div class="su-list-item__title" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                                {{ $student->name }}
                                @if($student->is_at_risk)
                                    <span class="su-chip su-chip--off">{{ __('instructor.tc_risk') }}</span>
                                @endif
                            </div>
                            <div class="su-list-item__meta">
                                {{ __('instructor.tc_att') }} {{ $student->present_count }}
                                · {{ $student->progress_percent }}%
                                @if($student->last_activity_at)
                                    · {{ __('instructor.tc_last') }} {{ $student->last_activity_at->diffForHumans() }}
                                @endif
                            </div>
                            <div style="margin-top:8px;height:6px;border-radius:999px;background:var(--su-ink-4);overflow:hidden">
                                <span style="display:block;height:100%;border-radius:999px;background:var(--su-ink-40);width:{{ $student->progress_percent }}%"></span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="su-empty">
                        <i class="fas fa-user-graduate" aria-hidden="true"></i>
                        <p>{{ __('instructor.tc_no_students') }}</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <section class="su-card su-card--flush" style="margin-bottom:20px">
        <h2 class="su-card__title" style="padding:8px 8px 0">
            <i class="fas fa-list" aria-hidden="true"></i>
            {{ __('instructor.tc_full_schedule') }}
        </h2>
        <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
            <table class="su-table">
                <thead>
                    <tr>
                        <th>{{ __('instructor.tc_col_num') }}</th>
                        <th>{{ __('instructor.tc_col_title_label') }}</th>
                        <th>{{ __('instructor.tc_col_when') }}</th>
                        <th>{{ __('instructor.tc_col_status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($center['sessions'] as $session)
                        <tr>
                            <td class="tabular-nums">{{ $session->session_number }}</td>
                            <td><strong style="font-weight:600">{{ $session->displayTitle() }}</strong></td>
                            <td style="color:var(--su-ink-40)">{{ $session->starts_at?->format('Y-m-d H:i') }}</td>
                            <td><span class="su-chip">{{ $session->statusLabel() }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="su-empty">
                                    <i class="fas fa-calendar-times" aria-hidden="true"></i>
                                    <p>{{ __('instructor.tc_no_schedule') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="su-card">
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;margin-bottom:16px">
            <h2 class="su-card__title" style="margin:0">
                <i class="fas fa-comments" aria-hidden="true"></i>
                {{ __('instructor.tc_community') }}
            </h2>
            <a href="{{ route('instructor.tutoring-cohorts.community', $cohort) }}" class="su-btn" style="height:32px">
                {{ __('instructor.tc_open_community') }}
            </a>
        </div>

        <form method="POST" action="{{ route('instructor.tutoring-cohorts.feed.store', $cohort) }}" style="margin-bottom:16px">
            @csrf
            <input type="hidden" name="post_type" value="announcement">
            <div class="su-field" style="margin-bottom:10px">
                <label for="cohort_feed_body">{{ __('instructor.tc_type_announcement') }}</label>
                <textarea name="body" id="cohort_feed_body" rows="2" maxlength="1000" required
                          class="su-input" style="height:auto;min-height:72px;padding:10px 12px;resize:vertical"
                          placeholder="{{ __('instructor.tc_announcement_ph') }}"></textarea>
            </div>
            <div class="su-form-actions">
                <label style="display:inline-flex;align-items:center;gap:8px;font-size:12px;font-weight:600;color:var(--su-ink-40)">
                    <input type="checkbox" name="is_pinned" value="1"> {{ __('instructor.tc_pin') }}
                </label>
                <button type="submit" class="su-btn su-btn--primary" style="height:36px;margin-inline-start:auto">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i>
                    {{ __('instructor.tc_publish') }}
                </button>
            </div>
        </form>

        <div class="su-list">
            @forelse(($feedPosts ?? collect()) as $post)
                <div class="su-list-item" style="{{ $post->is_hidden ? 'background:rgba(239,68,68,.06);border-color:rgba(239,68,68,.2)' : '' }}">
                    <span class="su-list-item__ico {{ $post->is_pinned ? 'su-soft-4' : 'su-soft-1' }}">
                        <i class="fas {{ $post->is_pinned ? 'fa-thumbtack' : 'fa-comment' }}" aria-hidden="true"></i>
                    </span>
                    <div class="su-list-item__body">
                        <div class="su-list-item__title">{{ $post->author?->name }} · {{ $post->typeLabel() }}</div>
                        <p style="margin:8px 0 0;font-size:13px;line-height:1.5;color:var(--su-ink);white-space:pre-wrap">{{ $post->body }}</p>
                    </div>
                    <div class="su-list-item__actions" style="display:flex;gap:6px;flex-wrap:wrap">
                        <form method="POST" action="{{ route('instructor.class-feed.pin', $post) }}">
                            @csrf
                            <button type="submit" class="su-btn" style="height:32px">
                                {{ $post->is_pinned ? __('instructor.tc_unpin') : __('instructor.tc_pin') }}
                            </button>
                        </form>
                        @if($post->is_hidden)
                            <form method="POST" action="{{ route('instructor.class-feed.unhide', $post) }}">
                                @csrf
                                <button type="submit" class="su-btn" style="height:32px">{{ __('instructor.tc_unhide') }}</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('instructor.class-feed.hide', $post) }}">
                                @csrf
                                <button type="submit" class="su-btn" style="height:32px">{{ __('instructor.tc_hide') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="su-empty">
                    <i class="fas fa-comments" aria-hidden="true"></i>
                    <p>{{ __('instructor.tc_no_posts') }}</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
