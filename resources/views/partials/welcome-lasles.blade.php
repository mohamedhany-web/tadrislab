@php
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
    $brand = 'TADRIS LAB';
    $brandAr = 'TADRIS LAB';
    $img = fn (string $file) => lasles_img($file);
    $langSwitch = fn (string $lang) => request()->fullUrlWithQuery(array_merge(request()->query(), ['lang' => $lang]));
@endphp

@include('partials.landing.lasles.nav')
<main>
  @include('partials.landing.lasles.hero')
  @include('partials.landing.lasles.stats')
  @include('partials.landing.lasles.path-section')
  @include('partials.landing.lasles.home-paths')
  @include('partials.landing.lasles.home-courses')
  @include('partials.landing.lasles.pillars')
  @include('partials.landing.lasles.features')
  @include('partials.landing.lasles.pricing')
  @include('partials.landing.lasles.institutions')
  @include('partials.landing.lasles.testimonials')
  @include('partials.landing.lasles.subscribe')
</main>
@include('partials.landing.lasles.footer')
@include('partials.landing.lasles.scripts')
