<section class="lasles-features" id="features">
  <div class="lasles-container lasles-features__grid">
    <div class="lasles-features__art">
      <img src="{{ $img('features-illustration.png') }}" width="508" height="414" alt="" loading="lazy" decoding="async">
    </div>
    <div>
      <h2 class="lasles-section-title">{{ __('landing.home.features_title') }}</h2>
      <p class="lasles-section-lead">{{ __('landing.home.features_lead') }}</p>
      <ul class="lasles-checklist">
        @foreach(__('landing.home.features') as $item)
          <li><img src="{{ $img('check-green.svg') }}" width="24" height="24" alt="">{{ $item }}</li>
        @endforeach
      </ul>
      <div class="lasles-hero__actions" style="margin-top:1.25rem">
        <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('landing.home.cta_start_journey') }}</a>
        <a href="{{ route('public.learning-paths.index') }}" class="lasles-btn-outline">{{ __('landing.home.cta_explore_programs') }}</a>
      </div>
    </div>
  </div>
</section>
