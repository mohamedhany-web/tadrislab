@extends('layouts.student-timeline')

@section('title', $practice->title())

@section('content')
@php
    $locale = app()->getLocale();
    $pathUrl = route('student.learning-paths.show', $path->slug);
    $done = (bool) ($done ?? false);
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $practice->title(),
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => __('student_timeline.paths_title'), 'url' => route('student.learning-paths.index')],
        ['label' => $path->title(), 'url' => $pathUrl],
        ['label' => $practice->title(), 'url' => null],
    ],
])

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif

<section class="st-join-hero st-lp-hero st-lp-hero--item st-lp-hero--practice" aria-label="{{ $practice->title() }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">{{ __('student_timeline.path_practice_kicker') }}</p>
        <p class="st-lp-skill">{{ $practice->typeLabel() }}</p>
        <h2 class="st-join-hero__title">{{ $practice->title() }}</h2>
        @if($practice->summary())
            <p class="st-join-hero__meta">{{ $practice->summary() }}</p>
        @else
            <p class="st-join-hero__meta">
                {{ $path->title() }}
                @if($practice->unit)
                    · {{ $practice->unit->title() }}
                @endif
            </p>
        @endif
    </div>
    <div class="st-lp-hero__side">
        <div class="st-join-hero__actions">
            @if($done)
                <span class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.path_practice_done') }}</span>
            @else
                <form method="POST" action="{{ route('student.learning-paths.complete', $path->slug) }}">
                    @csrf
                    <input type="hidden" name="item_type" value="practice">
                    <input type="hidden" name="item_id" value="{{ $practice->id }}">
                    <button type="submit" class="st-pill st-pill--solid st-pill--lg">{{ __('student_timeline.path_practice_complete') }}</button>
                </form>
            @endif
            <a href="{{ $pathUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.path_lesson_back') }}</a>
        </div>
    </div>
</section>

<article class="st-lp-reader st-lp-reader--practice">
    @if($practice->body())
        <div class="st-lp-reader__body">
            {!! $practice->body() !!}
        </div>
    @else
        <p class="st-lp-empty">{{ __('student_timeline.path_practice_empty') }}</p>
    @endif

    @if($practice->resource_url)
        <div class="st-lp-reader__links">
            <a href="{{ $practice->resource_url }}" target="_blank" rel="noopener" class="st-pill st-pill--solid">{{ __('student_timeline.path_practice_resource') }}</a>
        </div>
    @endif
</article>

<section class="st-lp-item-actions">
    <a href="{{ $pathUrl }}" class="st-pill st-pill--outline">{{ __('student_timeline.path_lesson_back') }}</a>
    @if(! $done)
        <form method="POST" action="{{ route('student.learning-paths.complete', $path->slug) }}">
            @csrf
            <input type="hidden" name="item_type" value="practice">
            <input type="hidden" name="item_id" value="{{ $practice->id }}">
            <button type="submit" class="st-pill st-pill--solid">{{ __('student_timeline.path_practice_complete') }}</button>
        </form>
    @else
        <a href="{{ $pathUrl }}" class="st-pill st-pill--solid">{{ __('student_timeline.path_practice_next') }}</a>
    @endif
</section>
@endsection

@section('events')
<div class="st-events__top">
    <h2>{{ __('student_timeline.quick_links') }}</h2>
</div>
<a href="{{ $pathUrl }}" class="st-event-card st-event-card--orange">
    <h3>{{ __('student_timeline.path_lesson_back') }}</h3>
    <p class="st-event-card__sub">{{ $path->title() }}</p>
</a>
@if($practice->resource_url)
    <a href="{{ $practice->resource_url }}" target="_blank" rel="noopener" class="st-event-card st-event-card--blue">
        <h3>{{ __('student_timeline.path_practice_resource') }}</h3>
        <p class="st-event-card__sub">{{ $practice->typeLabel() }}</p>
    </a>
@endif
<a href="{{ route('student.learning-paths.index') }}" class="st-event-card st-event-card--green">
    <h3>{{ __('student_timeline.paths_title') }}</h3>
    <p class="st-event-card__sub">{{ __('student_timeline.paths_list_hint') }}</p>
</a>
<div class="st-events__see">
    <a href="{{ route('dashboard') }}">{{ __('student_timeline.school_gate') }}</a>
</div>
@endsection
