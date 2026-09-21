@extends('layouts.instructor-timeline')

@section('title', __('instructor.notif_title'))
@section('page_title', __('instructor.notifications'))

@section('content')
@php
    $filter = request('status');
@endphp

<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-bell su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.notifications') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.notif_subtitle') }}</p>
        </div>
        <div class="su-page-head__actions">
            @if(($stats['unread'] ?? 0) > 0)
                <form method="post" action="{{ route('instructor.notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="su-btn su-btn--primary">
                        <i class="fas fa-check-double" aria-hidden="true"></i>
                        {{ __('instructor.notif_mark_all_read') }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    <section class="su-kpi-row su-kpi-row--3" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.notif_stat_total') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-bell" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.notif_stat_unread') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['unread'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-envelope" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.notif_stat_read') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['read'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-envelope-open" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="su-alert su-alert--ok" role="status">
            <i class="fas fa-check-circle" aria-hidden="true"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="su-filters">
        <a href="{{ route('instructor.notifications.index') }}"
           class="su-filter {{ ! $filter ? 'is-on' : '' }}">{{ __('instructor.notif_filter_all') }}</a>
        <a href="{{ route('instructor.notifications.index', ['status' => 'unread']) }}"
           class="su-filter {{ $filter === 'unread' ? 'is-on' : '' }}">{{ __('instructor.notif_filter_unread') }}</a>
        <a href="{{ route('instructor.notifications.index', ['status' => 'read']) }}"
           class="su-filter {{ $filter === 'read' ? 'is-on' : '' }}">{{ __('instructor.notif_filter_read') }}</a>
    </div>

    <section class="su-list su-notif-list">
        @forelse($notifications as $n)
            <article class="su-list-item su-notif-item {{ $n->is_read ? '' : 'su-notif-item--unread' }}">
                <span class="su-list-item__ico {{ $n->is_read ? 'su-soft-3' : 'su-soft-4' }}">
                    <i class="fas {{ $n->is_read ? 'fa-envelope-open' : 'fa-envelope' }}" aria-hidden="true"></i>
                </span>
                <div class="su-list-item__body">
                    <div class="su-chip-row" style="margin:0 0 6px">
                        @unless($n->is_read)
                            <span class="su-chip su-chip--warn">{{ __('instructor.notif_filter_unread') }}</span>
                        @else
                            <span class="su-chip su-soft-3">{{ __('instructor.notif_filter_read') }}</span>
                        @endunless
                        @if($n->sender?->name)
                            <span class="su-chip">{{ $n->sender->name }}</span>
                        @endif
                    </div>
                    <div class="su-list-item__title" style="white-space:normal">{{ $n->title }}</div>
                    @if($n->message)
                        <div class="su-list-item__meta" style="white-space:normal;margin-top:4px;line-height:1.5">{{ $n->message }}</div>
                    @endif
                    <div class="su-notif-item__time">{{ $n->created_at?->diffForHumans() }}</div>
                </div>
                <div class="su-list-item__actions">
                    @if($n->action_url)
                        <a href="{{ route('instructor.notifications.go', $n) }}" class="su-btn su-btn--primary" style="height:32px">
                            <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                            {{ $n->action_text ?: __('instructor.notif_open') }}
                        </a>
                    @endif
                    @unless($n->is_read)
                        <form method="post" action="{{ route('instructor.notifications.mark-read', $n) }}">
                            @csrf
                            <button type="submit" class="su-btn" style="height:32px">
                                <i class="fas fa-check" aria-hidden="true"></i>
                                {{ __('instructor.notif_mark_read') }}
                            </button>
                        </form>
                    @endunless
                </div>
            </article>
        @empty
            <div class="su-card">
                <div class="su-empty" style="padding:48px 16px">
                    <i class="fas fa-bell-slash" aria-hidden="true"></i>
                    <p>{{ __('instructor.notif_empty') }}</p>
                </div>
            </div>
        @endforelse
    </section>

    @if(method_exists($notifications, 'hasPages') && $notifications->hasPages())
        <div class="su-pager">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
