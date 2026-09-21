<section class="lasles-hero" id="top">
  <div class="lasles-hero__glow" aria-hidden="true"></div>
  <div class="lasles-container lasles-hero__grid">
    <div class="lasles-hero__copy">
      <p class="lasles-hero__brand">{{ __('landing.home.hero_brand') }}</p>
      <h1 class="lasles-hero__title">{{ __('landing.home.hero_title') }}</h1>
      <p class="lasles-hero__lead">{{ __('landing.home.hero_lead') }}</p>
      <div class="lasles-hero__actions">
        <a href="{{ route('register') }}" class="lasles-btn-primary lasles-btn-primary--hero">{{ __('landing.home.cta_start_journey') }}</a>
        <a href="{{ route('public.learning-paths.index') }}" class="lasles-btn-outline lasles-btn-outline--hero">{{ __('landing.home.cta_explore_programs') }}</a>
      </div>
    </div>
    <div class="lasles-hero__art">
      <div class="lasles-hero__art-frame">
        <img src="{{ $img('hero-illustration.svg') }}" width="611" height="382" alt="" decoding="async" fetchpriority="high">
      </div>
    </div>
  </div>
</section>
