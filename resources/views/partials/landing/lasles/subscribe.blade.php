<section class="lasles-container lasles-subscribe-wrap">
  <div class="lasles-subscribe">
    <div>
      <h2 class="lasles-subscribe__title">{{ __('landing.home.cta_title') }}</h2>
      <p class="lasles-subscribe__lead">{{ __('landing.home.cta_lead') }}</p>
    </div>
    <div class="lasles-subscribe__actions">
      <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('landing.home.cta_start_journey') }}</a>
      <a href="{{ route('public.learning-paths.index') }}" class="lasles-btn-outline">{{ __('landing.home.cta_explore_programs') }}</a>
    </div>
  </div>
</section>
