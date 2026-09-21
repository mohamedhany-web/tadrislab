@extends('layouts.student-timeline')

@section('title', __('student_timeline.packages_title'))

@section('content')
@php
    $locale = app()->getLocale();
    $active = $active ?? collect();
    $entitlements = $entitlements ?? collect();
    $history = $entitlements->reject(fn ($e) => $e->isActive())->values();
    $tones = ['blue', 'orange', 'green', 'purple'];
    $sessionsLeft = (int) $active->sum(fn ($e) => (int) $e->consultation_sessions_remaining);
    $toolsCount = (int) $active->filter(fn ($e) => (bool) $e->includes_tools)->count();
    $pricingUrl = Route::has('public.pricing') ? route('public.pricing') : route('dashboard');
    $pathsUrl = Route::has('student.learning-paths.index') ? route('student.learning-paths.index') : route('dashboard');
    $toolsUrl = Route::has('student.tools.index') ? route('student.tools.index') : route('dashboard');
    $consultUrl = Route::has('public.consultations.book')
        ? route('public.consultations.book')
        : (Route::has('consultations.index') ? route('consultations.index') : route('dashboard'));
    $statusLabels = [
        'active' => __('student_timeline.ent_status_active'),
        'expired' => __('student_timeline.ent_status_expired'),
        'cancelled' => __('student_timeline.ent_status_cancelled'),
    ];
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.packages_title'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.packages_title'), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

@if($active->isNotEmpty())
    <section class="st-join-hero" aria-label="{{ __('student_timeline.packages_title') }}">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">{{ __('student_timeline.packages_kicker_ready') }}</p>
            <h2 class="st-join-hero__title">{{ __('student_timeline.packages_hero_ready', ['count' => $active->count()]) }}</h2>
            <p class="st-join-hero__meta">{{ __('student_timeline.packages_hint') }}</p>
        </div>
        <div class="st-join-hero__actions">
            <a href="{{ $consultUrl }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.packages_book_consult') }}</a>
            <a href="{{ $pathsUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.packages_open_paths') }}</a>
        </div>
    </section>
@else
    <section class="st-join-hero st-join-hero--muted" aria-label="{{ __('student_timeline.packages_title') }}">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">{{ __('student_timeline.packages_kicker_empty') }}</p>
            <h2 class="st-join-hero__title">{{ __('student_timeline.packages_hero_empty') }}</h2>
            <p class="st-join-hero__meta">{{ __('student_timeline.packages_hint_empty') }}</p>
        </div>
        <div class="st-join-hero__actions">
            <a href="{{ $pricingUrl }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.packages_explore') }}</a>
            <a href="{{ route('dashboard') }}" class="st-pill st-pill--outline">{{ __('student_timeline.school_gate') }}</a>
        </div>
    </section>
@endif

<section class="st-stats st-stats--classes" aria-label="{{ __('student_timeline.packages_title') }}">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.packages_stat_active') }}</p>
        <p class="st-stat-card__value">{{ $active->count() }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.packages_stat_sessions') }}</p>
        <p class="st-stat-card__value">{{ $sessionsLeft }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.packages_stat_tools') }}</p>
        <p class="st-stat-card__value">{{ $toolsCount }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.packages_stat_history') }}</p>
        <p class="st-stat-card__value">{{ $entitlements->count() }}</p>
    </article>
</section>

<section class="st-msg-intro">
    <div>
        <h2>{{ __('student_timeline.packages_list_title') }}</h2>
        <p>{{ __('student_timeline.packages_list_hint') }}</p>
    </div>
    @if($active->isNotEmpty())
        <a href="{{ $toolsUrl }}" class="st-see">{{ __('student_timeline.packages_open_tools') }}</a>
    @endif
</section>

<section class="st-credit-list" aria-label="{{ __('student_timeline.packages_list_title') }}">
    @forelse($active as $i => $ent)
        @php
            $pkg = $ent->package;
            $tone = $tones[$i % count($tones)];
            $name = $pkg?->name ?? (__('student_timeline.packages_type_fallback').' #'.$ent->package_id);
            $type = method_exists($pkg, 'typeLabel') ? ($pkg->typeLabel() ?: __('student_timeline.packages_type_fallback')) : __('student_timeline.packages_type_fallback');
            $remaining = (int) $ent->consultation_sessions_remaining;
            $total = (int) $ent->consultation_sessions_total;
        @endphp
        <article class="st-credit-card st-credit-card--{{ $tone }}">
            <div class="st-credit-card__main">
                <div class="st-credit-card__copy">
                    <div class="st-credit-card__badges">
                        <span class="st-credit-card__badge is-ok">{{ $statusLabels['active'] }}</span>
                        <span class="st-credit-card__mins">{{ $type }}</span>
                        @if($ent->expires_at)
                            <span class="st-credit-card__mins">
                                {{ __('student_timeline.expires_on', ['date' => $ent->expires_at->timezone(config('app.timezone'))->format('Y-m-d')]) }}
                            </span>
                        @else
                            <span class="st-credit-card__mins">{{ __('student_timeline.packages_flexible') }}</span>
                        @endif
                    </div>
                    <h3>{{ $name }}</h3>
                    <p class="st-credit-card__meta">
                        {{ $ent->includes_tools ? __('student_timeline.packages_tools_yes') : __('student_timeline.packages_tools_no') }}
                    </p>
                </div>
                <div class="st-credit-card__nums" aria-label="{{ __('student_timeline.packages_sessions_left') }}">
                    <strong>{{ $remaining }}</strong>
                    <span>/ {{ $total }}</span>
                </div>
            </div>
            <div class="st-credit-card__foot">
                <span class="st-credit-card__bookable">{{ __('student_timeline.packages_sessions_left') }}</span>
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    <a href="{{ $pathsUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.nav_paths') }}</a>
                    <a href="{{ $toolsUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_tools') }}</a>
                    <a href="{{ $consultUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.packages_book_consult') }}</a>
                </div>
            </div>
        </article>
    @empty
        <div class="st-empty-panel">
            <h3>{{ __('student_timeline.packages_empty_title') }}</h3>
            <p>{{ __('student_timeline.packages_empty_hint') }}</p>
            <div class="st-biz-banner__actions">
                <a href="{{ $pricingUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.packages_explore') }}</a>
                <a href="{{ route('dashboard') }}" class="st-pill st-pill--outline">{{ __('student_timeline.school_gate') }}</a>
            </div>
        </div>
    @endforelse
</section>

@if($history->isNotEmpty())
    <section class="st-msg-intro" style="margin-top:1.5rem">
        <div>
            <h2>{{ __('student_timeline.packages_history_title') }}</h2>
            <p>{{ __('student_timeline.packages_history_hint') }}</p>
        </div>
    </section>

    <section class="st-credit-list" aria-label="{{ __('student_timeline.packages_history_title') }}">
        @foreach($history as $i => $ent)
            @php
                $pkg = $ent->package;
                $tone = $tones[$i % count($tones)];
                $name = $pkg?->name ?? (__('student_timeline.packages_type_fallback').' #'.$ent->package_id);
                $status = $statusLabels[$ent->status] ?? $ent->status;
            @endphp
            <article class="st-credit-card st-credit-card--{{ $tone }} is-dim">
                <div class="st-credit-card__main">
                    <div class="st-credit-card__copy">
                        <div class="st-credit-card__badges">
                            <span class="st-credit-card__badge">{{ $status }}</span>
                            @if($ent->expires_at)
                                <span class="st-credit-card__mins">
                                    {{ __('student_timeline.expires_on', ['date' => $ent->expires_at->format('Y-m-d')]) }}
                                </span>
                            @endif
                        </div>
                        <h3>{{ $name }}</h3>
                    </div>
                    <div class="st-credit-card__nums">
                        <strong>{{ (int) $ent->consultation_sessions_remaining }}</strong>
                        <span>/ {{ (int) $ent->consultation_sessions_total }}</span>
                    </div>
                </div>
            </article>
        @endforeach
    </section>
@endif
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>

<a href="{{ $pricingUrl }}" class="st-event-card st-event-card--orange">
    <h3>{{ __('student_timeline.packages_quick_explore') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.packages_quick_explore_hint') }}</p>
</a>

<a href="{{ $pathsUrl }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.nav_paths') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.packages_quick_paths_hint') }}</p>
</a>

<a href="{{ $toolsUrl }}" class="st-event-card st-event-card--green">
    <h3>{{ __('student_timeline.nav_tools') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.packages_quick_tools_hint') }}</p>
</a>

<a href="{{ $consultUrl }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.packages_book_consult') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.packages_quick_consult_hint') }}</p>
</a>

<div class="st-events__see">
    <a href="{{ route('dashboard') }}">{{ __('student_timeline.school_gate') }}</a>
</div>
@endsection
