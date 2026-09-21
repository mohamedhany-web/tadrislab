@php
  $isRtl = app()->getLocale() === 'ar';
  $coursesCss = public_path('css/landing/lasles-courses.css');
  $coursesVer = is_file($coursesCss) ? (string) filemtime($coursesCss) : (string) time();
  $pageTitle = __('public.courses_page_title').' — TADRIS LAB';
  $pageDescription = __('public.courses_subtitle');
  $bodyClass = 'lasles-courses-page';

  $filterUrl = function (array $overrides = []) use ($filters) {
      $query = array_filter(array_merge([
          'q' => $filters['q'] ?: null,
          'category' => $filters['category'] ?: null,
          'level' => $filters['level'] ?: null,
          'featured' => ! empty($filters['featured']) ? 1 : null,
      ], $overrides), fn ($v) => $v !== null && $v !== '' && $v !== false);

      return route('public.courses', $query);
  };

  $activeFilters = 0;
  if (! empty($filters['q'])) { $activeFilters++; }
  if (! empty($filters['category'])) { $activeFilters++; }
  if (! empty($filters['level'])) { $activeFilters++; }
  if (! empty($filters['featured'])) { $activeFilters++; }
@endphp
@extends('layouts.lasles-public')

@push('head')
  <link rel="stylesheet" href="{{ route('assets.landing.css', ['sheet' => 'lasles-courses']) }}?v={{ $coursesVer }}">
  <link rel="canonical" href="{{ route('public.courses') }}">
@endpush

@section('content')
<section class="lasles-courses-hero">
  <div class="lasles-container lasles-courses-hero__inner">
    <p class="lasles-site-breadcrumb" style="margin-bottom:.85rem">
      <a href="{{ route('home') }}">{{ __('site.nav_short.home') }}</a>
      <span aria-hidden="true">/</span>
      <span>{{ __('site.nav_short.courses') }}</span>
    </p>
    <h1 class="lasles-courses-hero__title">{{ __('public.courses_hero') }}</h1>
    <p class="lasles-courses-hero__lead">{{ __('public.courses_subtitle') }}</p>
    <div class="lasles-courses-hero__meta">
      <span class="lasles-courses-chip">{{ number_format($courses->total()) }} {{ __('public.courses_stats_available') }}</span>
      <span class="lasles-courses-chip">{{ number_format($totalActive) }} {{ __('public.courses_catalog_total') }}</span>
      <a class="lasles-courses-chip" href="{{ route('public.learning-paths.index') }}">{{ __('site.nav.learning_paths') }}</a>
    </div>
  </div>
</section>

<section class="lasles-container lasles-courses-toolbar">
  <form action="{{ route('public.courses') }}" method="get" class="lasles-courses-search" role="search">
    @if(!empty($filters['category']))
      <input type="hidden" name="category" value="{{ $filters['category'] }}">
    @endif
    @if(!empty($filters['level']))
      <input type="hidden" name="level" value="{{ $filters['level'] }}">
    @endif
    @if(!empty($filters['featured']))
      <input type="hidden" name="featured" value="1">
    @endif
    <input
      type="search"
      name="q"
      value="{{ $filters['q'] }}"
      placeholder="{{ __('public.courses_search_placeholder') }}"
      aria-label="{{ __('public.courses_search_placeholder') }}"
    >
    <button type="submit" class="lasles-btn-primary">{{ $isRtl ? 'بحث' : 'Search' }}</button>
  </form>

  <div class="lasles-courses-filters" aria-label="{{ __('public.courses_filters_label') }}">
    <a href="{{ $filterUrl(['category' => null, 'level' => null, 'featured' => null]) }}" class="{{ empty($filters['category']) && empty($filters['level']) && empty($filters['featured']) ? 'is-on' : '' }}">
      {{ __('public.courses_filter_all') }}
    </a>
    <a href="{{ $filterUrl(['featured' => empty($filters['featured']) ? 1 : null]) }}" class="{{ !empty($filters['featured']) ? 'is-on' : '' }}">
      {{ __('public.courses_filter_featured') }}
    </a>
    @foreach($categories as $category)
      <a href="{{ $filterUrl(['category' => (int) $filters['category'] === (int) $category->id ? null : $category->id]) }}"
         class="{{ (int) ($filters['category'] ?? 0) === (int) $category->id ? 'is-on' : '' }}">
        {{ $category->name }}
      </a>
    @endforeach
    @foreach($levels as $lvl)
      <a href="{{ $filterUrl(['level' => ($filters['level'] ?? '') === $lvl ? null : $lvl]) }}"
         class="{{ ($filters['level'] ?? '') === $lvl ? 'is-on' : '' }}">
        {{ $lvl }}
      </a>
    @endforeach
    @if($activeFilters > 0)
      <a href="{{ route('public.courses') }}" class="lasles-courses-clear">{{ __('public.courses_reset_search') }}</a>
    @endif
  </div>
</section>

<section class="lasles-container">
  @if($courses->isEmpty())
    <div class="lasles-courses-empty">
      <h2>{{ __('public.courses_empty_title') }}</h2>
      <p>{{ __('public.courses_empty_body') }}</p>
      <div class="lasles-hero__actions" style="justify-content:center;margin-top:1.25rem">
        <a href="{{ route('public.courses') }}" class="lasles-btn-outline">{{ __('public.courses_reset_search') }}</a>
        <a href="{{ route('public.learning-paths.index') }}" class="lasles-btn-primary">{{ __('site.nav.learning_paths') }}</a>
      </div>
    </div>
  @else
    <div class="lasles-courses-grid">
      @foreach($courses as $course)
        @include('partials.landing.lasles.course-card', ['course' => $course])
      @endforeach
    </div>
    @if($courses->hasPages())
      <div class="lasles-courses-pager">
        {{ $courses->onEachSide(1)->links() }}
      </div>
    @endif
  @endif

  <div class="lasles-courses-cta">
    <div>
      <h2>{{ __('public.courses_cta_title') }}</h2>
      <p>{{ __('public.courses_cta_desc') }}</p>
    </div>
    <div class="lasles-courses-cta__actions">
      <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('public.courses_cta_register') }}</a>
      <a href="{{ route('public.contact') }}" class="lasles-btn-outline">{{ __('site.cta.contact') }}</a>
    </div>
  </div>
</section>
@endsection
