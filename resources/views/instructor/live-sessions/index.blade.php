@extends('layouts.instructor-timeline')

@section('title', __('instructor.ls_title'))
@section('page_title', __('instructor.ls_title'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-broadcast-tower su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.ls_title') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.ls_subtitle') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.live-sessions.create') }}" class="su-btn su-btn--primary">
                <i class="fas fa-plus" aria-hidden="true"></i>
                {{ __('instructor.ls_new') }}
            </a>
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.ls_total') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-layer-group" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.ls_live_now') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['live'] ?? 0) }}</div>
                <div class="su-kpi__d"><span class="su-pulse" aria-hidden="true"></span></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.ls_scheduled') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['scheduled'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-calendar-alt" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.ls_ended') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['ended'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-check" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <div class="su-filters">
        <a href="{{ route('instructor.live-sessions.index') }}"
           class="su-filter {{ !request('status') ? 'is-on' : '' }}">{{ __('instructor.ls_all') }}</a>
        <a href="{{ route('instructor.live-sessions.index', ['status' => 'live']) }}"
           class="su-filter su-filter--live {{ request('status') === 'live' ? 'is-on' : '' }}">
            <span class="su-pulse" aria-hidden="true"></span> {{ __('instructor.ls_live') }}
        </a>
        <a href="{{ route('instructor.live-sessions.index', ['status' => 'scheduled']) }}"
           class="su-filter {{ request('status') === 'scheduled' ? 'is-on' : '' }}">{{ __('instructor.ls_scheduled') }}</a>
        <a href="{{ route('instructor.live-sessions.index', ['status' => 'ended']) }}"
           class="su-filter {{ request('status') === 'ended' ? 'is-on' : '' }}">{{ __('instructor.ls_ended') }}</a>
    </div>

    <div class="su-list">
        @forelse($sessions as $session)
            <article class="su-list-item {{ $session->isLive() ? 'su-list-item--live' : '' }}">
                <span class="su-list-item__ico {{ $session->isLive() ? 'su-soft-4' : ($session->isScheduled() ? 'su-soft-1' : 'su-soft-3') }}">
                    <i class="fas {{ $session->isLive() ? 'fa-broadcast-tower' : ($session->isScheduled() ? 'fa-calendar' : 'fa-flag-checkered') }}" aria-hidden="true"></i>
                </span>
                <div class="su-list-item__body">
                    <div class="su-chip-row" style="margin:0 0 6px">
                        @if($session->isLive())
                            <span class="su-chip su-chip--off" style="background:rgba(239,68,68,.14);color:#b91c1c">
                                <span class="su-pulse" aria-hidden="true"></span> {{ __('instructor.ls_live') }}
                            </span>
                        @elseif($session->isScheduled())
                            <span class="su-chip su-soft-1">{{ __('instructor.ls_scheduled') }}</span>
                        @elseif($session->isEnded())
                            <span class="su-chip su-soft-3">{{ __('instructor.ls_ended') }}</span>
                        @else
                            <span class="su-chip su-chip--warn">{{ __('instructor.ls_cancelled') }}</span>
                        @endif
                        @if($session->course)
                            <span class="su-chip">{{ Str::limit($session->course->title, 30) }}</span>
                        @endif
                    </div>
                    <div class="su-list-item__title">{{ $session->title }}</div>
                    <div class="su-list-item__meta">
                        @if($session->scheduled_at)
                            <x-app-datetime :at="$session->scheduled_at" pattern="Y/m/d H:i" />
                        @else
                            —
                        @endif
                        · {{ $session->attendance_count }} {{ __('instructor.ls_present') }}
                        @if($session->duration_minutes)
                            · {{ $session->duration_for_humans }}
                        @endif
                    </div>
                </div>
                <div class="su-list-item__actions">
                    @if($session->isLive())
                        <a href="{{ route('instructor.live-sessions.room', $session) }}" class="su-btn su-btn--danger">
                            <i class="fas fa-video" aria-hidden="true"></i> {{ __('instructor.ls_enter') }}
                        </a>
                        <form method="POST" action="{{ route('instructor.live-sessions.end', $session) }}" onsubmit="return confirm(@json(__('instructor.ls_end_confirm')))">
                            @csrf
                            <button type="submit" class="su-btn" title="{{ __('instructor.ls_ended') }}">
                                <i class="fas fa-stop" aria-hidden="true"></i>
                            </button>
                        </form>
                    @elseif($session->isScheduled())
                        <form method="POST" action="{{ route('instructor.live-sessions.start', $session) }}">
                            @csrf
                            <button type="submit" class="su-btn su-btn--ok">
                                <i class="fas fa-play" aria-hidden="true"></i> {{ __('instructor.ls_start') }}
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('instructor.live-sessions.show', $session) }}" class="su-icon-link su-icon-link--ghost" title="{{ __('common.view') }}">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                    </a>
                </div>
            </article>
        @empty
            <div class="su-card">
                <div class="su-empty" style="padding:48px 16px">
                    <i class="fas fa-broadcast-tower" aria-hidden="true"></i>
                    <h3 style="margin:0;font-size:16px;font-weight:600;color:var(--su-ink)">{{ __('instructor.ls_empty') }}</h3>
                    <p>{{ __('instructor.ls_empty_hint') }}</p>
                    <a href="{{ route('instructor.live-sessions.create') }}" class="su-btn su-btn--primary">
                        <i class="fas fa-plus" aria-hidden="true"></i> {{ __('instructor.ls_create_first') }}
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if(method_exists($sessions, 'hasPages') && $sessions->hasPages())
        <div class="su-pager">{{ $sessions->links() }}</div>
    @endif
</div>
@endsection
