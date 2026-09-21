<section class="lasles-container lasles-subscribe-wrap lasles-paths-cta">
  <div class="lasles-subscribe">
    <div>
      <h2 class="lasles-subscribe__title">{{ __('landing.learning_paths.cta_title') }}</h2>
      <p class="lasles-subscribe__lead">{{ __('landing.learning_paths.cta_lead') }}</p>
    </div>
    <div class="lasles-subscribe__actions">
      <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('landing.home.cta_start_journey') }}</a>
      <a href="{{ route('public.pricing') }}" class="lasles-btn-outline">{{ __('landing.learning_paths.cta_packages') }}</a>
    </div>
  </div>
</section>
