@extends('layouts.lasles-public')

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $thumb = filled($path->thumbnail)
        ? (str_starts_with((string) $path->thumbnail, 'http') ? $path->thumbnail : asset('storage/'.$path->thumbnail))
        : lasles_img('features-illustration.png');
    $unitsCount = $path->units->count();
    $lessonsCount = $path->units->sum(fn ($u) => $u->lessons->count());
    $practicesCount = $path->units->sum(fn ($u) => $u->practices->count());
    $price = $path->price !== null ? (float) $path->price : null;
    $isFree = $price !== null && $price <= 0;
    $relatedPaths = $relatedPaths ?? collect();
@endphp

<section class="lasles-path-detail-hero" aria-labelledby="path-detail-title">
  <div class="lasles-path-detail-hero__bg" style="--path-cover: url('{{ $thumb }}')" aria-hidden="true"></div>
  <div class="lasles-path-detail-hero__veil" aria-hidden="true"></div>
  <div class="lasles-container lasles-path-detail-hero__inner">
    <p class="lasles-path-detail-hero__crumb">
      <a href="{{ route('home') }}">{{ __('site.nav_short.home') }}</a>
      <span aria-hidden="true">/</span>
      <a href="{{ route('public.learning-paths.index') }}">{{ __('landing.learning_paths.nav') }}</a>
      <span aria-hidden="true">/</span>
      <span>{{ $path->title() }}</span>
    </p>

    <div class="lasles-path-detail-hero__copy">
      <p class="lasles-path-detail-hero__kicker">{{ __('landing.learning_paths.kicker') }}</p>
      <h1 id="path-detail-title" class="lasles-path-detail-hero__title">{{ $path->title() }}</h1>
      @if($path->summary())
        <p class="lasles-path-detail-hero__lead">{{ $path->summary() }}</p>
      @endif

      <div class="lasles-path-detail-hero__chips">
        @if($path->skillFocus())
          <span class="lasles-path-chip lasles-path-chip--gold">{{ $path->skillFocus() }}</span>
        @endif
        <span class="lasles-path-chip">{{ __('landing.learning_paths.units_count', ['count' => $unitsCount]) }}</span>
        @if($path->estimated_minutes)
          <span class="lasles-path-chip">{{ $path->estimated_minutes }} {{ __('landing.learning_paths.minutes') }}</span>
        @endif
        @if($lessonsCount > 0)
          <span class="lasles-path-chip">{{ $lessonsCount }} {{ __('landing.learning_paths.stat_lessons') }}</span>
        @endif
        @if($practicesCount > 0)
          <span class="lasles-path-chip">{{ $practicesCount }} {{ __('landing.learning_paths.stat_tools') }}</span>
        @endif
        @if($isFree)
          <span class="lasles-path-chip lasles-path-chip--blue">{{ __('landing.learning_paths.price_free') }}</span>
        @elseif($price !== null)
          <span class="lasles-path-chip lasles-path-chip--blue">{{ number_format($price, 0) }} {{ $path->currency ?: 'QAR' }}</span>
        @endif
      </div>

      <p class="lasles-path-detail-hero__promise">{{ __('landing.learning_paths.detail_promise') }}</p>

      <div class="lasles-path-detail-hero__actions">
        @if($path->isStandaloneProduct())
          <a href="{{ route('public.learning-paths.checkout', $path->slug) }}" class="lasles-btn-primary">
            {{ $isFree ? __('landing.learning_paths.cta_start') : __('landing.learning_paths.cta_buy') }}
          </a>
        @else
          <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('landing.learning_paths.cta_start') }}</a>
        @endif
        <a href="{{ route('public.pricing') }}" class="lasles-btn-outline lasles-btn-outline--on-dark">{{ __('landing.learning_paths.cta_packages') }}</a>
      </div>
    </div>
  </div>
</section>

<section class="lasles-path-detail-body" aria-labelledby="path-outline-title">
  <div class="lasles-container lasles-path-detail-body__grid">
    <div class="lasles-path-detail-body__main">
      @if($path->description())
        <div class="lasles-path-detail-about">
          <p class="lasles-path-kicker">{{ __('landing.learning_paths.overview_label') }}</p>
          <h2 class="lasles-path-detail-about__title">{{ __('landing.learning_paths.about_title') }}</h2>
          <p class="lasles-path-detail-about__text">{{ $path->description() }}</p>
        </div>
      @endif

      @if($path->skillFocus())
        <div class="lasles-path-detail-about" style="margin-top:1.5rem">
          <p class="lasles-path-kicker">{{ $isRtl ? 'ماذا ستتعلم' : 'What you will learn' }}</p>
          <h2 class="lasles-path-detail-about__title">{{ $isRtl ? 'المهارة المستهدفة' : 'Target skill' }}</h2>
          <p class="lasles-path-detail-about__text">{{ $path->skillFocus() }}</p>
        </div>
      @endif

      <div class="lasles-path-detail-body__head">
        <p class="lasles-path-kicker">{{ __('landing.learning_paths.outline_kicker') }}</p>
        <h2 id="path-outline-title" class="lasles-path-detail-body__title">{{ __('landing.learning_paths.outline_title') }}</h2>
        <p class="lasles-path-detail-body__lead">{{ __('landing.learning_paths.outline_lead') }}</p>
      </div>

      @forelse($path->units as $unit)
        @php
          $unitLessons = $unit->lessons->count();
          $unitPractices = $unit->practices->count();
        @endphp
        <article class="lasles-path-unit-card">
          <header class="lasles-path-unit-card__head">
            <span class="lasles-path-unit-card__index" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
            <div class="lasles-path-unit-card__intro">
              <h3 class="lasles-path-unit-card__title">{{ $unit->title() }}</h3>
              @if($unit->summary())
                <p class="lasles-path-unit-card__summary">{{ $unit->summary() }}</p>
              @endif
              <div class="lasles-path-unit-card__meta">
                <span>{{ $unitLessons }} {{ __('landing.learning_paths.stat_lessons') }}</span>
                <span aria-hidden="true">·</span>
                <span>{{ $unitPractices }} {{ __('landing.learning_paths.stat_tools') }}</span>
              </div>
            </div>
          </header>

          <div class="lasles-path-unit-card__cols">
            <div class="lasles-path-unit-card__col">
              <h4 class="lasles-path-unit-card__label">{{ __('landing.learning_paths.lessons_label') }}</h4>
              @if($unit->lessons->isEmpty())
                <p class="lasles-path-unit-card__empty">{{ __('landing.learning_paths.content_soon') }}</p>
              @else
                <ul class="lasles-path-unit-card__list">
                  @foreach($unit->lessons as $lesson)
                    <li>
                      <span class="lasles-path-unit-card__dot" aria-hidden="true"></span>
                      <span>{{ $lesson->title() }}</span>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
            <div class="lasles-path-unit-card__col">
              <h4 class="lasles-path-unit-card__label">{{ __('landing.learning_paths.practices_label') }}</h4>
              @if($unit->practices->isEmpty())
                <p class="lasles-path-unit-card__empty">{{ __('landing.learning_paths.content_soon') }}</p>
              @else
                <ul class="lasles-path-unit-card__list">
                  @foreach($unit->practices as $practice)
                    <li>
                      <span class="lasles-path-unit-card__type">{{ $practice->typeLabel() }}</span>
                      <span>{{ $practice->title() }}</span>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
          </div>
        </article>
      @empty
        <div class="lasles-paths-empty">
          <p>{{ __('landing.learning_paths.units_soon') }}</p>
        </div>
      @endforelse
    </div>

    <aside class="lasles-path-detail-side">
      <div class="lasles-path-detail-side__card">
        <p class="lasles-path-detail-side__kicker">{{ __('landing.learning_paths.side_kicker') }}</p>
        <h3>{{ __('landing.learning_paths.side_title') }}</h3>
        <p>{{ __('landing.learning_paths.side_lead') }}</p>
        <ol class="lasles-path-detail-side__steps">
          <li>{{ __('landing.learning_paths.side_step_1') }}</li>
          <li>{{ __('landing.learning_paths.side_step_2') }}</li>
          <li>{{ __('landing.learning_paths.side_step_3') }}</li>
        </ol>
        @if($path->isStandaloneProduct())
          <a href="{{ route('public.learning-paths.checkout', $path->slug) }}" class="lasles-btn-primary lasles-path-detail-side__cta">
            {{ $isFree ? __('landing.learning_paths.cta_start') : __('landing.learning_paths.cta_buy') }}
          </a>
        @else
          <a href="{{ route('register') }}" class="lasles-btn-primary lasles-path-detail-side__cta">{{ __('landing.learning_paths.cta_start') }}</a>
        @endif
        <a href="{{ route('public.pricing') }}" class="lasles-path-detail-side__link">{{ __('landing.learning_paths.cta_packages') }}</a>
      </div>
    </aside>
  </div>
</section>

@if($relatedPaths->isNotEmpty())
<section class="lasles-path-detail-related" aria-labelledby="related-paths-title">
  <div class="lasles-container">
    <div class="lasles-showcase__head">
      <div class="lasles-showcase__intro">
        <p class="lasles-path-kicker">{{ __('landing.learning_paths.related_kicker') }}</p>
        <h2 id="related-paths-title" class="lasles-section-title">{{ __('landing.learning_paths.related_title') }}</h2>
      </div>
      <a href="{{ route('public.learning-paths.index') }}" class="lasles-showcase__more lasles-btn-outline">
        {{ __('landing.home.showcase_paths_cta') }}
      </a>
    </div>
    <div class="lasles-showcase__grid lasles-showcase__grid--paths">
      @foreach($relatedPaths as $i => $rel)
        @php
          $relThumb = filled($rel->thumbnail)
            ? (str_starts_with((string) $rel->thumbnail, 'http') ? $rel->thumbnail : asset('storage/'.$rel->thumbnail))
            : null;
        @endphp
        <a href="{{ route('public.learning-paths.show', $rel->slug) }}" class="lasles-path-card-pro">
          <div class="lasles-path-card-pro__media {{ $relThumb ? '' : 'lasles-path-card-pro__media--accent' }}">
            @if($relThumb)
              <img src="{{ $relThumb }}" alt="" loading="lazy" decoding="async">
              <span class="lasles-path-card-pro__shade" aria-hidden="true"></span>
            @endif
            <span class="lasles-path-card-pro__index {{ $relThumb ? 'lasles-path-card-pro__index--on-media' : '' }}">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
          </div>
          <div class="lasles-path-card-pro__body">
            <h3 class="lasles-path-card-pro__title">{{ $rel->title() }}</h3>
            @if($rel->summary())
              <p class="lasles-path-card-pro__summary">{{ \Illuminate\Support\Str::limit($rel->summary(), 90) }}</p>
            @endif
            <span class="lasles-path-card-pro__cta">{{ __('landing.learning_paths.view_path') }} <span aria-hidden="true">→</span></span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="lasles-path-detail-cta">
  <div class="lasles-container lasles-path-detail-cta__inner">
    <div>
      <h2 class="lasles-section-title">{{ __('landing.learning_paths.cta_title') }}</h2>
      <p class="lasles-section-lead">{{ __('landing.learning_paths.cta_lead') }}</p>
    </div>
    <div class="lasles-path-detail-cta__actions">
      <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('landing.learning_paths.cta_start') }}</a>
      <a href="{{ route('public.learning-paths.index') }}" class="lasles-btn-outline">{{ __('landing.home.showcase_paths_cta') }}</a>
    </div>
  </div>
</section>
@endsection
