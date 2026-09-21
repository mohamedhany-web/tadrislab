@extends('layouts.student-timeline')

@section('title', __('student_timeline.paths_title'))

@section('content')
@php
    $locale = app()->getLocale();
    $paths = $paths ?? collect();
    $enrollments = $enrollments ?? collect();
    $tones = ['blue', 'orange', 'green', 'purple'];
    $completedCount = $enrollments->where('status', 'completed')->count();
    $inProgressCount = $enrollments->filter(function ($enr) {
        return $enr->status !== 'completed' && (float) $enr->progress > 0;
    })->count();
    $avgProgress = $enrollments->isNotEmpty()
        ? (int) round($enrollments->avg(fn ($e) => (float) $e->progress))
        : 0;
    $packagesUrl = Route::has('student.packages.index') ? route('student.packages.index') : route('dashboard');
    $pricingUrl = Route::has('public.pricing') ? route('public.pricing') : route('dashboard');
    $toolsUrl = Route::has('student.tools.index') ? route('student.tools.index') : route('dashboard');
    $catalogUrl = Route::has('public.learning-paths.index') ? route('public.learning-paths.index') : $pricingUrl;
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => __('student_timeline.paths_title'),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.paths_title'), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="st-flash st-flash--err">{{ session('error') }}</div>
@endif

@if($paths->isNotEmpty())
    <section class="st-join-hero" aria-label="{{ __('student_timeline.paths_title') }}">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">{{ __('student_timeline.paths_kicker_ready') }}</p>
            <h2 class="st-join-hero__title">{{ __('student_timeline.paths_hero_ready', ['count' => $paths->count()]) }}</h2>
            <p class="st-join-hero__meta">{{ __('student_timeline.paths_hint') }}</p>
        </div>
        <div class="st-join-hero__actions">
            <a href="{{ $packagesUrl }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.paths_open_packages') }}</a>
            <a href="{{ $toolsUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.nav_tools') }}</a>
        </div>
    </section>
@else
    <section class="st-join-hero st-join-hero--muted" aria-label="{{ __('student_timeline.paths_title') }}">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">{{ __('student_timeline.paths_kicker_empty') }}</p>
            <h2 class="st-join-hero__title">{{ __('student_timeline.paths_hero_empty') }}</h2>
            <p class="st-join-hero__meta">{{ __('student_timeline.paths_hint_empty') }}</p>
        </div>
        <div class="st-join-hero__actions">
            <a href="{{ $pricingUrl }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.paths_explore_packages') }}</a>
            <a href="{{ route('dashboard') }}" class="st-pill st-pill--outline">{{ __('student_timeline.school_gate') }}</a>
        </div>
    </section>
@endif

<section class="st-stats st-stats--classes" aria-label="{{ __('student_timeline.paths_title') }}">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.paths_stat_total') }}</p>
        <p class="st-stat-card__value">{{ $paths->count() }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.paths_stat_in_progress') }}</p>
        <p class="st-stat-card__value">{{ $inProgressCount }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.paths_stat_completed') }}</p>
        <p class="st-stat-card__value">{{ $completedCount }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.paths_stat_avg') }}</p>
        <p class="st-stat-card__value">{{ $avgProgress }}%</p>
    </article>
</section>

<section class="st-msg-intro">
    <div>
        <h2>{{ __('student_timeline.paths_list_title') }}</h2>
        <p>{{ __('student_timeline.paths_list_hint') }}</p>
    </div>
    @if(Route::has('public.learning-paths.index'))
        <a href="{{ $catalogUrl }}" class="st-see">{{ __('student_timeline.paths_browse_catalog') }}</a>
    @endif
</section>

<section class="st-credit-list" aria-label="{{ __('student_timeline.paths_list_title') }}">
    @forelse($paths as $i => $path)
        @php
            $enr = $enrollments[$path->id] ?? null;
            $tone = $tones[$i % count($tones)];
            $progress = $enr ? min(100, (float) $enr->progress) : 0;
            $isCompleted = $enr && $enr->status === 'completed';
            $skill = $path->skillFocus() ?: __('student_timeline.paths_skill_fallback');
            $showUrl = route('student.learning-paths.show', $path->slug);
        @endphp
        <article class="st-credit-card st-credit-card--{{ $tone }}">
            <div class="st-credit-card__main">
                <div class="st-credit-card__copy">
                    <div class="st-credit-card__badges">
                        <span class="st-credit-card__badge {{ $isCompleted ? 'is-ok' : '' }}">
                            {{ $isCompleted ? __('student_timeline.paths_status_completed') : __('student_timeline.paths_status_active') }}
                        </span>
                        <span class="st-credit-card__mins">{{ $skill }}</span>
                        @if($enr?->package)
                            <span class="st-credit-card__mins">{{ __('student_timeline.paths_via_package', ['name' => $enr->package->name]) }}</span>
                        @endif
                    </div>
                    <h3>{{ $path->title() }}</h3>
                    @if($path->summary())
                        <p class="st-credit-card__meta">{{ \Illuminate\Support\Str::limit($path->summary(), 120) }}</p>
                    @endif
                    <p class="st-credit-card__meta" style="margin-top:6px">
                        {{ __('student_timeline.paths_units_count', ['count' => (int) $path->units_count]) }}
                        @if($enr)
                            · {{ __('student_timeline.paths_progress') }}: {{ number_format($progress, 0) }}%
                        @endif
                    </p>
                    @if($enr)
                        <div class="st-path-progress" style="margin-top:10px;height:6px;border-radius:999px;background:rgba(21,42,74,.08);overflow:hidden" aria-hidden="true">
                            <div style="height:100%;width:{{ $progress }}%;border-radius:999px;background:var(--st-blue,#1E4E8C)"></div>
                        </div>
                    @endif
                </div>
                <div class="st-credit-card__nums" aria-label="{{ __('student_timeline.paths_progress') }}">
                    <strong>{{ number_format($progress, 0) }}</strong>
                    <span>%</span>
                </div>
            </div>
            <div class="st-credit-card__foot">
                <span class="st-credit-card__bookable">{{ $skill }}</span>
                <a href="{{ $showUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.paths_continue') }}</a>
            </div>
        </article>
    @empty
        <div class="st-empty-panel">
            <h3>{{ __('student_timeline.paths_empty_title') }}</h3>
            <p>{{ __('student_timeline.paths_empty_hint') }}</p>
            <div class="st-biz-banner__actions">
                <a href="{{ $pricingUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.paths_explore_packages') }}</a>
                <a href="{{ $packagesUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.paths_open_packages') }}</a>
            </div>
        </div>
    @endforelse
</section>
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>

<a href="{{ $packagesUrl }}" class="st-event-card st-event-card--orange">
    <h3>{{ __('student_timeline.nav_packages') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.paths_quick_packages_hint') }}</p>
</a>

<a href="{{ $toolsUrl }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.nav_tools') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.paths_quick_tools_hint') }}</p>
</a>

@if(Route::has('public.learning-paths.index'))
    <a href="{{ $catalogUrl }}" class="st-event-card st-event-card--green">
        <h3>{{ __('student_timeline.paths_browse_catalog') }}</h3>
        <p class="st-event-card__sub">{{ __('student_timeline.paths_quick_catalog_hint') }}</p>
    </a>
@endif

<a href="{{ $pricingUrl }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.paths_explore_packages') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.packages_quick_explore_hint') }}</p>
</a>

<div class="st-events__see">
    <a href="{{ route('dashboard') }}">{{ __('student_timeline.school_gate') }}</a>
</div>
@endsection
