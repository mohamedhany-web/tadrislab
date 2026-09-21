@extends('layouts.instructor-timeline')

@section('title', $liveSession->title)
@section('page_title', $liveSession->title)

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp

<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.live-sessions.index') }}">{{ __('instructor.ls_title') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ $liveSession->title }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-broadcast-tower su-page-head__ico" aria-hidden="true"></i>
                {{ $liveSession->title }}
            </h1>
            <p class="su-page-head__sub tabular-nums" style="font-family:ui-monospace,monospace">{{ $liveSession->room_name }}</p>
            <div class="su-chip-row" style="margin-top:8px">
                @if($liveSession->isLive())
                    <span class="su-chip su-chip--off" style="background:rgba(239,68,68,.14);color:#b91c1c">
                        <span class="su-pulse" aria-hidden="true"></span> {{ __('instructor.ls_live') }}
                    </span>
                @elseif($liveSession->isScheduled())
                    <span class="su-chip su-soft-1">{{ __('instructor.ls_scheduled') }}</span>
                @elseif($liveSession->isEnded())
                    <span class="su-chip su-soft-3">{{ __('instructor.ls_ended') }}</span>
                @else
                    <span class="su-chip su-chip--warn">{{ __('instructor.ls_cancelled') }}</span>
                @endif
            </div>
        </div>
        <div class="su-page-head__actions">
            @if($liveSession->isLive())
                <a href="{{ route('instructor.live-sessions.room', $liveSession) }}" class="su-btn su-btn--primary" style="background:#dc2626;border-color:#dc2626">
                    <i class="fas fa-video" aria-hidden="true"></i>
                    {{ __('instructor.ls_enter_broadcast') }}
                </a>
            @elseif($liveSession->isScheduled())
                <form method="POST" action="{{ route('instructor.live-sessions.start', $liveSession) }}">
                    @csrf
                    <button type="submit" class="su-btn su-btn--primary" style="background:#16a34a;border-color:#16a34a">
                        <i class="fas fa-play" aria-hidden="true"></i>
                        {{ __('instructor.ls_start_now') }}
                    </button>
                </form>
            @endif
            <a href="{{ route('instructor.live-sessions.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.ls_back_list') }}
            </a>
        </div>
    </div>

    @if($liveSession->isLive())
        <div class="su-card" style="margin-bottom:16px;padding:14px 18px;display:flex;align-items:center;gap:10px;background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.25);color:#b91c1c;font-size:13px;font-weight:600">
            <span class="su-pulse" aria-hidden="true"></span>
            {{ __('instructor.ls_live_banner', ['ago' => $liveSession->started_at?->diffForHumans()]) }}
        </div>
    @endif

    <div class="su-page-grid">
        <div style="display:flex;flex-direction:column;gap:16px;min-width:0">
            <section class="su-card" style="margin:0">
                <h2 class="su-card__title">
                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                    {{ __('instructor.ls_details') }}
                </h2>
                <div class="su-dl">
                    <div class="su-dl__item">
                        <label>{{ __('instructor.ls_course') }}</label>
                        <div>{{ $liveSession->course?->title ?? __('instructor.ls_general') }}</div>
                    </div>
                    <div class="su-dl__item">
                        <label>{{ __('instructor.ls_when') }}</label>
                        <div><x-app-datetime :at="$liveSession->scheduled_at" pattern="Y/m/d H:i" /></div>
                    </div>
                    <div class="su-dl__item">
                        <label>{{ __('instructor.ls_duration') }}</label>
                        <div>{{ $liveSession->duration_for_humans }}</div>
                    </div>
                    <div class="su-dl__item">
                        <label>{{ __('instructor.ls_max') }}</label>
                        <div>{{ $liveSession->max_participants }}</div>
                    </div>
                </div>
                @if($liveSession->description)
                    <p style="margin:16px 0 0;padding-top:16px;border-top:0.5px solid var(--su-line);font-size:13px;line-height:1.55;color:var(--su-ink-40)">
                        {{ $liveSession->description }}
                    </p>
                @endif
            </section>

            <section class="su-card su-card--flush" style="margin:0">
                <h2 class="su-card__title" style="padding:8px 8px 0">
                    <i class="fas fa-users" aria-hidden="true"></i>
                    {{ __('instructor.ls_attendance') }}
                    <span class="su-chip">{{ $attendees->count() }}</span>
                </h2>
                <div class="su-list">
                    @forelse($attendees as $att)
                        <div class="su-list-item">
                            <span class="su-list-item__ico {{ $att->role_in_session === 'instructor' ? 'su-soft-1' : 'su-soft-3' }}">
                                <i class="fas {{ $att->role_in_session === 'instructor' ? 'fa-chalkboard-teacher' : 'fa-user-graduate' }}" aria-hidden="true"></i>
                            </span>
                            <div class="su-list-item__body">
                                <div class="su-list-item__title">{{ $att->user?->name }}</div>
                                <div class="su-list-item__meta">
                                    {{ __('instructor.ls_joined') }} {{ $att->joined_at?->format('H:i') }}
                                    @if($att->left_at)
                                        — {{ __('instructor.ls_left') }} {{ $att->left_at->format('H:i') }}
                                    @endif
                                </div>
                            </div>
                            <span class="su-chip" style="flex-shrink:0">{{ $att->duration_for_humans }}</span>
                        </div>
                    @empty
                        <div class="su-empty">
                            <i class="fas fa-users" aria-hidden="true"></i>
                            <p>{{ __('instructor.ls_no_attendance') }}</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px">
            <section class="su-card" style="margin:0;text-align:center">
                @if($liveSession->isScheduled())
                    <div class="su-list-item__ico su-soft-1" style="width:56px;height:56px;margin:0 auto 12px;font-size:20px">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                    </div>
                    <p style="margin:0;font-weight:600;color:var(--su-ink)">{{ __('instructor.ls_status_scheduled') }}</p>
                    <p style="margin:6px 0 0;font-size:13px;color:var(--su-ink-40)">{{ $liveSession->scheduled_at?->diffForHumans() }}</p>
                @elseif($liveSession->isLive())
                    <div class="su-list-item__ico" style="width:56px;height:56px;margin:0 auto 12px;background:rgba(239,68,68,.12)">
                        <span class="su-pulse" aria-hidden="true"></span>
                    </div>
                    <p style="margin:0;font-weight:700;color:#dc2626">{{ __('instructor.ls_status_live_now') }}</p>
                @elseif($liveSession->isEnded())
                    <div class="su-list-item__ico su-soft-3" style="width:56px;height:56px;margin:0 auto 12px;font-size:20px">
                        <i class="fas fa-check" aria-hidden="true"></i>
                    </div>
                    <p style="margin:0;font-weight:600;color:var(--su-ink)">{{ __('instructor.ls_status_ended') }}</p>
                    <p style="margin:6px 0 0;font-size:13px;color:var(--su-ink-40)">{{ __('instructor.ls_duration') }}: {{ $liveSession->duration_for_humans }}</p>
                @endif
            </section>

        </div>
    </div>
</div>
@endsection
