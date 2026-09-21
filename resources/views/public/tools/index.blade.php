@extends('layouts.lasles-public')

@section('content')
@php
  $isRtl = app()->getLocale() === 'ar';
  $img = fn (string $file) => lasles_img($file);
  $accessLabels = \App\Models\TeacherTool::accessModeLabels();
  $activeType = request('type');
  $guide = __('landing.tools.guide');
  $guide = is_array($guide) ? $guide : [];
@endphp

<section class="lasles-tools-hero" aria-labelledby="tools-hero-title">
  <div class="lasles-container lasles-tools-hero__grid">
    <div class="lasles-tools-hero__copy">
      <p class="lasles-path-kicker">{{ __('landing.tools.kicker') }}</p>
      <p class="lasles-tools-hero__brand">{{ $isRtl ? 'تدريس لاب' : 'TADRIS LAB' }}</p>
      <h1 id="tools-hero-title" class="lasles-tools-hero__title">
        {{ __('landing.tools.hero_title_before') }}
        <em>{{ __('landing.tools.hero_title_strong') }}</em>
      </h1>
      <p class="lasles-tools-hero__lead">{{ __('landing.tools.hero_lead') }}</p>
      <p class="lasles-tools-hero__note">{{ __('landing.tools.hero_note') }}</p>
      <div class="lasles-hero__actions">
        <a href="#tools-catalog" class="lasles-btn-primary">{{ __('landing.tools.catalog_title') }}</a>
        <a href="{{ route('register') }}" class="lasles-btn-outline">{{ __('landing.home.cta_start_journey') }}</a>
      </div>
    </div>
    <div class="lasles-tools-hero__art">
      @include('partials.landing.lasles.page-art', ['variant' => 'tools', 'img' => $img])
    </div>
  </div>
</section>

<section class="lasles-paths-guide lasles-tools-guide">
  <div class="lasles-container">
    <div class="lasles-paths-guide__head">
      <h2 class="lasles-section-title lasles-section-title--center">{{ __('landing.tools.guide_title') }}</h2>
      <p class="lasles-section-lead lasles-section-lead--center">{{ __('landing.tools.guide_lead') }}</p>
    </div>
    <ol class="lasles-paths-guide__grid">
      @foreach($guide as $i => $item)
        <li class="lasles-paths-guide__item">
          <span class="lasles-paths-guide__num" aria-hidden="true">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
          <h3>{{ $item['title'] ?? '' }}</h3>
          <p>{{ $item['body'] ?? '' }}</p>
        </li>
      @endforeach
    </ol>
  </div>
</section>

<section class="lasles-tools-catalog" id="tools-catalog" aria-labelledby="tools-catalog-title">
  <div class="lasles-container">
    <div class="lasles-paths-catalog__head">
      <div>
        <h2 id="tools-catalog-title" class="lasles-section-title">{{ __('landing.tools.catalog_title') }}</h2>
        <p class="lasles-section-lead">{{ __('landing.tools.catalog_lead') }}</p>
      </div>
    </div>

    <div class="lasles-tools-filters" role="navigation" aria-label="{{ __('landing.tools.filter_all') }}">
      <a href="{{ route('public.tools.index') }}" class="lasles-tools-filter {{ blank($activeType) ? 'is-active' : '' }}">{{ __('landing.tools.filter_all') }}</a>
      @foreach($typeLabels as $key => $label)
        <a href="{{ route('public.tools.index', ['type' => $key]) }}" class="lasles-tools-filter {{ $activeType === $key ? 'is-active' : '' }}">{{ $label }}</a>
      @endforeach
    </div>

    @if($tools->isEmpty())
      <div class="lasles-paths-empty">
        <h3>{{ __('landing.tools.empty_title') }}</h3>
        <p>{{ __('landing.tools.empty_body') }}</p>
        <a href="{{ route('public.pricing') }}" class="lasles-btn-outline">{{ __('landing.tools.empty_cta') }}</a>
      </div>
    @else
      <div class="lasles-paths-grid">
        @foreach($tools as $i => $tool)
          <a href="{{ route('public.tools.show', $tool->slug) }}" class="lasles-path-tile">
            <div class="lasles-path-tile__top">
              <span class="lasles-path-tile__index" aria-hidden="true">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
              <span class="lasles-path-tile__skill">{{ $typeLabels[$tool->tool_type] ?? $tool->tool_type }}</span>
            </div>
            <h3 class="lasles-path-tile__title">{{ $tool->title() }}</h3>
            @if($tool->summary())
              <p class="lasles-path-tile__summary">{{ $tool->summary() }}</p>
            @endif
            <span class="lasles-path-tile__meta">{{ $accessLabels[$tool->access_mode] ?? $tool->access_mode }}</span>
            <span class="lasles-path-tile__cta">{{ __('landing.tools.view_tool') }} <span aria-hidden="true">→</span></span>
          </a>
        @endforeach
      </div>
    @endif
  </div>
</section>

<section class="lasles-container lasles-subscribe-wrap lasles-tools-cta">
  <div class="lasles-subscribe">
    <div>
      <h2 class="lasles-subscribe__title">{{ __('landing.tools.cta_title') }}</h2>
      <p class="lasles-subscribe__lead">{{ __('landing.tools.cta_lead') }}</p>
    </div>
    <div class="lasles-subscribe__actions">
      <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('landing.home.cta_start_journey') }}</a>
      <a href="{{ route('public.pricing') }}" class="lasles-btn-outline">{{ __('landing.tools.cta_packages') }}</a>
    </div>
  </div>
</section>
@endsection
