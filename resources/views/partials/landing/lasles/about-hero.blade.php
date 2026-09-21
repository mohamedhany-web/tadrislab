@php
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
    $img = $img ?? fn (string $file) => lasles_img($file);
    $content = $content ?? [];
@endphp
<section class="lasles-about-hero" aria-labelledby="about-hero-title">
  <div class="lasles-container lasles-about-hero__grid">
    <div class="lasles-about-hero__copy">
      <p class="lasles-path-kicker">{{ $content['kicker'] ?? '' }}</p>
      <p class="lasles-about-hero__brand">{{ $isRtl ? 'تدريس لاب' : 'TADRIS LAB' }}</p>
      <h1 id="about-hero-title" class="lasles-about-hero__title">
        {{ $content['title_before'] ?? '' }}
        <em>{{ $content['title_strong'] ?? '' }}</em>
      </h1>
      <p class="lasles-about-hero__lead">{{ $content['lead'] ?? '' }}</p>
      <div class="lasles-hero__actions">
        <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('landing.home.cta_start_journey') }}</a>
        <a href="{{ route('public.learning-paths.index') }}" class="lasles-btn-outline">{{ __('landing.home.cta_explore_programs') }}</a>
      </div>
    </div>
    <div class="lasles-about-hero__art">
      @include('partials.landing.lasles.page-art', ['variant' => 'features', 'img' => $img])
    </div>
  </div>
</section>
