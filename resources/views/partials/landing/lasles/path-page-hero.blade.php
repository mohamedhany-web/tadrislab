@php
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
    $img = $img ?? fn (string $file) => lasles_img($file);
@endphp
<section class="lasles-path-hero" aria-labelledby="path-hero-title">
  <div class="lasles-container lasles-path-hero__grid">
    <div class="lasles-path-hero__copy">
      <p class="lasles-path-hero__brand">{{ $isRtl ? 'تدريس لاب' : 'TADRIS LAB' }}</p>
      <h1 id="path-hero-title" class="lasles-path-hero__title">{!! __('landing.path.title_html') !!}</h1>
      <p class="lasles-path-hero__lead">{{ __('landing.path.lead') }}</p>
      <div class="lasles-path-hero__actions">
        <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('landing.path.cta_primary') }}</a>
        <a href="{{ route('public.courses') }}" class="lasles-btn-outline">{{ __('landing.path.cta_secondary') }}</a>
      </div>
    </div>
    <div class="lasles-path-hero__art" aria-hidden="true">
      <img src="{{ $img('features-illustration.png') }}" width="580" height="400" alt="" decoding="async" fetchpriority="high">
    </div>
  </div>
</section>
