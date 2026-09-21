@php $content = $content ?? []; @endphp
<section class="lasles-container lasles-subscribe-wrap lasles-about-cta">
  <div class="lasles-subscribe">
    <div>
      <h2 class="lasles-subscribe__title">{{ $content['cta_title'] ?? '' }}</h2>
      <p class="lasles-subscribe__lead">{{ $content['cta_lead'] ?? '' }}</p>
    </div>
    <div class="lasles-subscribe__actions">
      <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('landing.home.cta_start_journey') }}</a>
      <a href="{{ route('public.learning-paths.index') }}" class="lasles-btn-outline">{{ __('landing.home.cta_explore_programs') }}</a>
    </div>
  </div>
</section>
