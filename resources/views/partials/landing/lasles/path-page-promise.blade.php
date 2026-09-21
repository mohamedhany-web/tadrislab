@php
    $img = $img ?? fn (string $file) => asset('img/lasles/'.$file);
@endphp
<section class="lasles-path-promise" aria-labelledby="path-promise-title">
  <div class="lasles-container">
    <div class="lasles-path-promise__box">
      <div class="lasles-path-promise__copy">
        <h2 id="path-promise-title" class="lasles-section-title">{{ __('landing.path.promise_title') }}</h2>
        <p class="lasles-section-lead">{{ __('landing.path.promise_lead') }}</p>
        <ul class="lasles-checklist">
          <li><img src="{{ $img('check-green.svg') }}" width="24" height="24" alt="">{{ __('landing.path.promise_1') }}</li>
          <li><img src="{{ $img('check-green.svg') }}" width="24" height="24" alt="">{{ __('landing.path.promise_2') }}</li>
          <li><img src="{{ $img('check-green.svg') }}" width="24" height="24" alt="">{{ __('landing.path.promise_3') }}</li>
        </ul>
      </div>
      <div class="lasles-path-promise__cta">
        <p>{{ __('landing.path.promise_cta_note') }}</p>
        <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('landing.path.cta_primary') }}</a>
        <a href="{{ route('login') }}" class="lasles-path-promise__login">{{ __('landing.path.cta_login') }}</a>
        <a href="{{ route('public.pricing') }}" class="lasles-path-promise__link">{{ __('landing.path.cta_pricing') }}</a>
      </div>
    </div>
  </div>
</section>
