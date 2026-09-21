@php
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
    $img = $img ?? fn (string $file) => lasles_img($file);
@endphp
<section class="lasles-paths-hero" aria-labelledby="paths-hero-title">
  <div class="lasles-container lasles-paths-hero__grid">
    <div class="lasles-paths-hero__copy">
      <p class="lasles-path-kicker">{{ __('landing.learning_paths.kicker') }}</p>
      <p class="lasles-paths-hero__brand">{{ 'TADRIS LAB' }}</p>
      <h1 id="paths-hero-title" class="lasles-paths-hero__title">
        {{ __('landing.learning_paths.hero_title_before') }}
        <em>{{ __('landing.learning_paths.hero_title_strong') }}</em>
      </h1>
      <p class="lasles-paths-hero__lead">{{ __('landing.learning_paths.hero_lead') }}</p>
      <p class="lasles-paths-hero__note">{{ __('landing.learning_paths.hero_structure') }}</p>
      <div class="lasles-hero__actions">
        <a href="#paths-catalog" class="lasles-btn-primary">{{ __('landing.learning_paths.catalog_title') }}</a>
        <a href="{{ route('register') }}" class="lasles-btn-outline">{{ __('landing.home.cta_start_journey') }}</a>
      </div>
    </div>
    <div class="lasles-paths-hero__art">
      @include('partials.landing.lasles.page-art', ['variant' => 'features', 'img' => $img])
    </div>
  </div>
</section>
