<section class="lasles-institutions" id="institutions">
  <div class="lasles-container lasles-features__grid">
    <div>
      <h2 class="lasles-section-title">{{ __('landing.home.institutions_title') }}</h2>
      <p class="lasles-institutions__outcome">{{ __('landing.home.institutions_outcome') }}</p>
      <p class="lasles-section-lead">{{ __('landing.home.institutions_lead') }}</p>
      <ul class="lasles-checklist">
        @foreach(__('landing.home.institutions_points') as $item)
          <li><img src="{{ $img('check-green.svg') }}" width="24" height="24" alt="">{{ $item }}</li>
        @endforeach
      </ul>
      <div class="lasles-hero__actions" style="margin-top:1.35rem">
        <a href="{{ route('public.institutions.inquiry') }}" class="lasles-btn-primary">{{ __('landing.home.institutions_cta') }}</a>
        <a href="{{ route('public.contact') }}" class="lasles-btn-outline">{{ __('landing.home.cta_contact') }}</a>
      </div>
    </div>
    <div class="lasles-institutions__panel" aria-hidden="true">
      <div class="lasles-institutions__panel-head">
        <p>{{ __('landing.home.institutions_panel_title') }}</p>
        <span class="lasles-institutions__chip">{{ __('landing.home.institutions_panel_chip') }}</span>
      </div>
      <div class="lasles-institutions__metrics">
        <div class="lasles-institutions__metric">
          <strong>{{ __('landing.home.institutions_metric_1_value') }}</strong>
          <span>{{ __('landing.home.institutions_metric_1_label') }}</span>
        </div>
        <div class="lasles-institutions__metric">
          <strong>{{ __('landing.home.institutions_metric_2_value') }}</strong>
          <span>{{ __('landing.home.institutions_metric_2_label') }}</span>
        </div>
        <div class="lasles-institutions__metric">
          <strong>{{ __('landing.home.institutions_metric_3_value') }}</strong>
          <span>{{ __('landing.home.institutions_metric_3_label') }}</span>
        </div>
      </div>
      <div class="lasles-institutions__art">
        <img src="{{ $img('map-global.svg') }}" width="508" height="220" alt="" loading="lazy" decoding="async">
      </div>
    </div>
  </div>
</section>
