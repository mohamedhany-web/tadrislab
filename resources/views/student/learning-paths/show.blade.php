@extends('layouts.student-timeline')

@section('title', $path->title())

@section('content')
@php
    $locale = app()->getLocale();
    $completedKeys = $completedKeys ?? collect();
    $enrollment = $enrollment ?? null;
    $progress = $enrollment ? min(100, (float) $enrollment->progress) : 0;
    $isCompleted = $enrollment && $enrollment->status === 'completed';

    $flatItems = collect();
    foreach ($path->units as $unitIndex => $unit) {
        foreach ($unit->lessons as $lesson) {
            $flatItems->push((object) [
                'kind' => 'lesson',
                'id' => $lesson->id,
                'title' => $lesson->title(),
                'unit' => $unit,
                'unit_index' => $unitIndex,
                'model' => $lesson,
                'done' => (bool) $completedKeys->get('lesson:'.$lesson->id),
                'url' => route('student.learning-paths.lesson', [$path->slug, $lesson]),
                'type_label' => __('student_timeline.path_show_next_lesson'),
            ]);
        }
        foreach ($unit->practices as $practice) {
            $flatItems->push((object) [
                'kind' => 'practice',
                'id' => $practice->id,
                'title' => $practice->title(),
                'unit' => $unit,
                'unit_index' => $unitIndex,
                'model' => $practice,
                'done' => (bool) $completedKeys->get('practice:'.$practice->id),
                'url' => route('student.learning-paths.practice', [$path->slug, $practice]),
                'type_label' => $practice->typeLabel() ?: __('student_timeline.path_show_next_practice'),
            ]);
        }
    }

    $totalItems = $flatItems->count();
    $doneItems = $flatItems->where('done', true)->count();
    $leftItems = max(0, $totalItems - $doneItems);
    $nextItem = $flatItems->firstWhere('done', false);
    $currentUnitIndex = $nextItem?->unit_index ?? ($path->units->count() > 0 ? $path->units->count() - 1 : 0);

    $loopSteps = [
        ['key' => 'diagnose', 'label' => __('student_timeline.path_show_loop_diagnose'), 'hint' => __('student_timeline.path_show_loop_diagnose_hint')],
        ['key' => 'access', 'label' => __('student_timeline.path_show_loop_access'), 'hint' => __('student_timeline.path_show_loop_access_hint')],
        ['key' => 'develop', 'label' => __('student_timeline.path_show_loop_develop'), 'hint' => __('student_timeline.path_show_loop_develop_hint')],
        ['key' => 'measure', 'label' => __('student_timeline.path_show_loop_measure'), 'hint' => __('student_timeline.path_show_loop_measure_hint')],
    ];
    $unitCount = max(1, $path->units->count());
    $activeLoop = $isCompleted ? 3 : min(3, (int) floor(($currentUnitIndex / $unitCount) * 3));

    $packagesUrl = Route::has('student.packages.index') ? route('student.packages.index') : route('dashboard');
    $toolsUrl = Route::has('student.tools.index') ? route('student.tools.index') : route('dashboard');
    $pathsUrl = route('student.learning-paths.index');
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $path->title(),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.paths_title'), 'url' => $pathsUrl],
        ['label' => $path->title(), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif

<section class="st-join-hero st-lp-hero" aria-label="{{ $path->title() }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">{{ __('student_timeline.path_show_kicker') }}</p>
        <h2 class="st-join-hero__title">{{ $path->title() }}</h2>
        @if($path->skillFocus())
            <p class="st-lp-skill">{{ __('student_timeline.path_show_skill', ['skill' => $path->skillFocus()]) }}</p>
        @endif
        @if($path->summary())
            <p class="st-join-hero__meta">{{ $path->summary() }}</p>
        @endif
        <div class="st-lp-hero__meta-row">
            @if($enrollment?->package)
                <span class="st-lp-chip">{{ __('student_timeline.path_show_via_package', ['name' => $enrollment->package->name]) }}</span>
            @endif
            <span class="st-lp-chip">{{ __('student_timeline.path_show_items_done', ['done' => $doneItems, 'total' => $totalItems]) }}</span>
        </div>
    </div>
    <div class="st-lp-hero__side">
        <div class="st-lp-ring" style="--p: {{ (int) round($progress) }}" aria-label="{{ __('student_timeline.path_show_progress') }}">
            <strong>{{ (int) round($progress) }}%</strong>
            <span>{{ __('student_timeline.path_show_progress') }}</span>
        </div>
        <div class="st-join-hero__actions">
            @if($isCompleted)
                <span class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.path_show_completed') }}</span>
            @elseif($nextItem)
                <a href="{{ $nextItem->url }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.path_show_continue') }}</a>
            @else
                <a href="{{ $pathsUrl }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.path_show_back') }}</a>
            @endif
            <a href="{{ $pathsUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.path_show_back') }}</a>
        </div>
    </div>
</section>

<section class="st-lp-loop" aria-label="{{ __('student_timeline.path_show_loop_title') }}">
    <h3 class="st-lp-section-title">{{ __('student_timeline.path_show_loop_title') }}</h3>
    <ol class="st-lp-loop__track">
        @foreach($loopSteps as $i => $step)
            <li class="st-lp-loop__step {{ $i < $activeLoop ? 'is-done' : '' }} {{ $i === $activeLoop ? 'is-current' : '' }}">
                <span class="st-lp-loop__num">{{ $i + 1 }}</span>
                <span class="st-lp-loop__label">{{ $step['label'] }}</span>
                <span class="st-lp-loop__hint">{{ $step['hint'] }}</span>
            </li>
        @endforeach
    </ol>
</section>

@if($nextItem && ! $isCompleted)
    <section class="st-lp-next" aria-label="{{ __('student_timeline.path_show_next_title') }}">
        <div class="st-lp-next__copy">
            <p class="st-lp-next__kicker">{{ __('student_timeline.path_show_next_title') }}</p>
            <p class="st-lp-next__type">{{ $nextItem->type_label }} · {{ $nextItem->unit->title() }}</p>
            <h3>{{ $nextItem->title }}</h3>
        </div>
        <a href="{{ $nextItem->url }}" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.path_show_open_item') }}</a>
    </section>
@endif

<section class="st-stats st-stats--classes" aria-label="{{ __('student_timeline.path_show_progress') }}">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.path_show_stat_units') }}</p>
        <p class="st-stat-card__value">{{ $path->units->count() }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.path_show_stat_items') }}</p>
        <p class="st-stat-card__value">{{ $totalItems }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.path_show_stat_done') }}</p>
        <p class="st-stat-card__value">{{ $doneItems }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ __('student_timeline.path_show_stat_left') }}</p>
        <p class="st-stat-card__value">{{ $leftItems }}</p>
    </article>
</section>

@if(($path->teacherTools ?? collect())->isNotEmpty())
    <section class="st-lp-tools" aria-label="{{ __('student_timeline.path_show_tools_title') }}">
        <div class="st-msg-intro">
            <div>
                <h2>{{ __('student_timeline.path_show_tools_title') }}</h2>
                <p>{{ __('student_timeline.path_show_tools_hint') }}</p>
            </div>
            <a href="{{ $toolsUrl }}" class="st-see">{{ __('student_timeline.nav_tools') }}</a>
        </div>
        <div class="st-lp-tools__grid">
            @foreach($path->teacherTools as $tool)
                <div class="st-lp-tool">
                    <span class="st-lp-tool__type">{{ $tool->typeLabel() }}</span>
                    <strong>{{ $tool->title() }}</strong>
                </div>
            @endforeach
        </div>
    </section>
@endif

<section class="st-msg-intro">
    <div>
        <h2>{{ __('student_timeline.path_show_roadmap') }}</h2>
        <p>{{ __('student_timeline.path_show_roadmap_hint') }}</p>
    </div>
</section>

<section class="st-lp-roadmap" aria-label="{{ __('student_timeline.path_show_roadmap') }}">
    @forelse($path->units as $unitIndex => $unit)
        @php
            $unitLessons = $unit->lessons;
            $unitPractices = $unit->practices;
            $unitItemKeys = $unitLessons->map(fn ($l) => 'lesson:'.$l->id)
                ->merge($unitPractices->map(fn ($p) => 'practice:'.$p->id));
            $unitDone = $unitItemKeys->filter(fn ($k) => $completedKeys->get($k))->count();
            $unitTotal = $unitItemKeys->count();
            $unitPct = $unitTotal > 0 ? (int) round(($unitDone / $unitTotal) * 100) : 0;
            $isCurrentUnit = $unitIndex === $currentUnitIndex && ! $isCompleted;
            $unitLoopLabel = $loopSteps[min(3, $unitIndex)]['label'] ?? '';
        @endphp
        <article class="st-lp-unit {{ $isCurrentUnit ? 'is-current' : '' }} {{ $unitPct === 100 ? 'is-done' : '' }}">
            <header class="st-lp-unit__head">
                <div>
                    <p class="st-lp-unit__eyebrow">
                        {{ __('student_timeline.path_show_unit', ['n' => $unitIndex + 1]) }}
                        @if($unitLoopLabel)
                            · {{ $unitLoopLabel }}
                        @endif
                    </p>
                    <h3>{{ $unit->title() }}</h3>
                    @if($unit->summary())
                        <p class="st-lp-unit__summary">{{ $unit->summary() }}</p>
                    @endif
                </div>
                <div class="st-lp-unit__progress" aria-label="{{ __('student_timeline.path_show_progress') }}">
                    <strong>{{ $unitPct }}%</strong>
                    <span>{{ __('student_timeline.path_show_items_done', ['done' => $unitDone, 'total' => $unitTotal]) }}</span>
                </div>
            </header>

            <div class="st-lp-unit__cols">
                <div class="st-lp-unit__col">
                    <h4>{{ __('student_timeline.path_show_lessons') }}</h4>
                    @forelse($unitLessons as $lesson)
                        @php $done = (bool) $completedKeys->get('lesson:'.$lesson->id); @endphp
                        <a href="{{ route('student.learning-paths.lesson', [$path->slug, $lesson]) }}" class="st-lp-item {{ $done ? 'is-done' : '' }} {{ $nextItem && $nextItem->kind === 'lesson' && (int) $nextItem->id === (int) $lesson->id ? 'is-next' : '' }}">
                            <span class="st-lp-item__mark" aria-hidden="true">{{ $done ? '✓' : ($loop->iteration) }}</span>
                            <span class="st-lp-item__body">
                                <strong>{{ $lesson->title() }}</strong>
                                <small>{{ $done ? __('student_timeline.path_show_done') : __('student_timeline.path_show_todo') }}</small>
                            </span>
                        </a>
                    @empty
                        <p class="st-lp-empty">{{ __('student_timeline.path_show_locked_hint') }}</p>
                    @endforelse
                </div>
                <div class="st-lp-unit__col">
                    <h4>{{ __('student_timeline.path_show_practices') }}</h4>
                    @forelse($unitPractices as $practice)
                        @php $done = (bool) $completedKeys->get('practice:'.$practice->id); @endphp
                        <a href="{{ route('student.learning-paths.practice', [$path->slug, $practice]) }}" class="st-lp-item st-lp-item--practice {{ $done ? 'is-done' : '' }} {{ $nextItem && $nextItem->kind === 'practice' && (int) $nextItem->id === (int) $practice->id ? 'is-next' : '' }}">
                            <span class="st-lp-item__mark" aria-hidden="true">{{ $done ? '✓' : '◆' }}</span>
                            <span class="st-lp-item__body">
                                <em>{{ $practice->typeLabel() }}</em>
                                <strong>{{ $practice->title() }}</strong>
                                <small>{{ $done ? __('student_timeline.path_show_done') : __('student_timeline.path_show_todo') }}</small>
                            </span>
                        </a>
                    @empty
                        <p class="st-lp-empty">{{ __('student_timeline.path_show_locked_hint') }}</p>
                    @endforelse
                </div>
            </div>
        </article>
    @empty
        <div class="st-empty-panel">
            <h3>{{ __('student_timeline.path_show_empty_units') }}</h3>
            <a href="{{ $pathsUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.path_show_back') }}</a>
        </div>
    @endforelse
</section>
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>

@if($nextItem && ! $isCompleted)
    <a href="{{ $nextItem->url }}" class="st-event-card st-event-card--orange">
        <h3>{{ __('student_timeline.path_show_next_title') }}</h3>
        <p class="st-event-card__sub">{{ $nextItem->title }}</p>
    </a>
@endif

<a href="{{ $toolsUrl }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.nav_tools') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.path_show_tools_hint') }}</p>
</a>

<a href="{{ $packagesUrl }}" class="st-event-card st-event-card--green">
    <h3>{{ __('student_timeline.nav_packages') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.paths_quick_packages_hint') }}</p>
</a>

<a href="{{ $pathsUrl }}" class="st-event-card st-event-card--blue">
    <h3>{{ __('student_timeline.path_show_back') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.paths_list_hint') }}</p>
</a>

<div class="st-events__see">
    <a href="{{ route('dashboard') }}">{{ __('student_timeline.school_gate') }}</a>
</div>
@endsection
