@php
  $isRtl = app()->getLocale() === 'ar';
  $pricingCss = public_path('css/landing/lasles-pricing.css');
  $pricingVer = is_file($pricingCss) ? (string) filemtime($pricingCss) : (string) time();
  $pageTitle = __('public.pricing_page_title').' — '.($isRtl ? 'تدريس لاب' : 'TADRIS LAB');
  $pageDescription = __('public.pricing_meta_description');
  $bodyClass = 'lasles-pricing-page';
  $laslesNavActive = 'pricing';
  $packages = $packages ?? collect();
  $img = function (string $file) {
      $path = public_path('img/lasles/'.$file);
      $url = lasles_img($file);

      return is_file($path) ? $url.'?v='.filemtime($path) : $url;
  };
  $teacherPackages = $packages->filter(fn ($p) => in_array($p->package_type, ['free', 'individual', 'advanced'], true))->values();
  $orgPackages = $packages->filter(fn ($p) => in_array($p->package_type, ['school_institution', 'custom'], true))->values();
  $otherPackages = $packages->reject(fn ($p) => in_array($p->package_type, ['free', 'individual', 'advanced', 'school_institution', 'custom'], true))->values();
@endphp
@extends('layouts.lasles-public')

@push('head')
  <link rel="stylesheet" href="{{ route('assets.landing.css', ['sheet' => 'lasles-pricing']) }}?v={{ $pricingVer }}">
  <link rel="canonical" href="{{ route('public.pricing') }}">
@endpush

@section('content')
<section class="lasles-pricing-hero" aria-labelledby="pricing-hero-title">
  <div class="lasles-container lasles-pricing-hero__inner">
    <p class="lasles-pricing-hero__crumb">
      <a href="{{ route('home') }}">{{ __('site.nav_short.home') }}</a>
      <span aria-hidden="true">/</span>
      <span>{{ __('public.pricing_page_title') }}</span>
    </p>
    <div class="lasles-pricing-hero__copy">
      <p class="lasles-pricing-hero__kicker">{{ __('public.pricing_hero_kicker') }}</p>
      <h1 id="pricing-hero-title" class="lasles-pricing-hero__title">{{ __('public.pricing_hero_title') }}</h1>
      <p class="lasles-pricing-hero__lead">{{ __('public.pricing_hero_sub') }}</p>
      <p class="lasles-pricing-hero__note">{{ __('public.pricing_hero_note') }}</p>
      <div class="lasles-pricing-hero__meta">
        <span class="lasles-pricing-chip">{{ __('public.pricing_chip_paths') }}</span>
        <span class="lasles-pricing-chip">{{ __('public.pricing_chip_tools') }}</span>
        <span class="lasles-pricing-chip">{{ __('public.pricing_chip_consult') }}</span>
        <span class="lasles-pricing-chip">{{ __('public.pricing_chip_schools') }}</span>
      </div>
    </div>
  </div>
</section>

<section class="lasles-pricing-catalog" id="plans" aria-labelledby="pricing-catalog-title">
  <div class="lasles-container">
    <div class="lasles-pricing-catalog__head">
      <p class="lasles-path-kicker">{{ __('public.pricing_packages_kicker') }}</p>
      <h2 id="pricing-catalog-title" class="lasles-section-title">{{ __('public.pricing_packages_title') }}</h2>
      <p class="lasles-section-lead">{{ __('public.pricing_packages_sub') }}</p>
    </div>

    @if($packages->isEmpty())
      <div class="lasles-pricing-empty">
        <h3>{{ __('public.pricing_no_packages') }}</h3>
        <p>{{ __('public.pricing_empty_body') }}</p>
        <div class="lasles-pricing-empty__actions">
          <a href="{{ route('public.contact') }}" class="lasles-btn-primary">{{ __('public.pricing_footer_contact') }}</a>
          <a href="{{ route('public.courses') }}" class="lasles-btn-outline">{{ __('site.nav_short.courses') }}</a>
        </div>
      </div>
    @else
      @if($teacherPackages->isNotEmpty())
        <div class="lasles-pricing-group">
          <h3 class="lasles-pricing-group__title">{{ __('public.pricing_group_teacher') }}</h3>
          <div class="lasles-plans lasles-plans--pricing lasles-plans--teacher">
            @foreach($teacherPackages as $package)
              @include('partials.landing.lasles.package-card', ['package' => $package, 'img' => $img])
            @endforeach
          </div>
        </div>
      @endif

      @if($orgPackages->isNotEmpty())
        <div class="lasles-pricing-group">
          <h3 class="lasles-pricing-group__title">{{ __('public.pricing_group_org') }}</h3>
          <div class="lasles-plans lasles-plans--pricing lasles-plans--org">
            @foreach($orgPackages as $package)
              @include('partials.landing.lasles.package-card', ['package' => $package, 'img' => $img])
            @endforeach
          </div>
        </div>
      @endif

      @if($otherPackages->isNotEmpty())
        <div class="lasles-pricing-group">
          <div class="lasles-plans lasles-plans--pricing">
            @foreach($otherPackages as $package)
              @include('partials.landing.lasles.package-card', ['package' => $package, 'img' => $img])
            @endforeach
          </div>
        </div>
      @endif
    @endif
  </div>
</section>

<section class="lasles-pricing-compare" aria-labelledby="pricing-compare-title">
  <div class="lasles-container">
    <div class="lasles-pricing-compare__head">
      <p class="lasles-path-kicker">{{ __('public.pricing_compare_kicker') }}</p>
      <h2 id="pricing-compare-title" class="lasles-section-title">{{ __('public.pricing_compare_title') }}</h2>
      <p class="lasles-section-lead">{{ __('public.pricing_compare_sub') }}</p>
    </div>
    <ol class="lasles-pricing-compare__list">
      <li>
        <span class="lasles-pricing-compare__num" aria-hidden="true">01</span>
        <div>
          <strong>{{ __('public.pricing_compare_free') }}</strong>
          <span>{{ __('public.pricing_compare_free_body') }}</span>
        </div>
      </li>
      <li>
        <span class="lasles-pricing-compare__num" aria-hidden="true">02</span>
        <div>
          <strong>{{ __('public.pricing_compare_individual') }}</strong>
          <span>{{ __('public.pricing_compare_individual_body') }}</span>
        </div>
      </li>
      <li>
        <span class="lasles-pricing-compare__num" aria-hidden="true">03</span>
        <div>
          <strong>{{ __('public.pricing_compare_advanced') }}</strong>
          <span>{{ __('public.pricing_compare_advanced_body') }}</span>
        </div>
      </li>
      <li>
        <span class="lasles-pricing-compare__num" aria-hidden="true">04</span>
        <div>
          <strong>{{ __('public.pricing_compare_school') }}</strong>
          <span>{{ __('public.pricing_compare_school_body') }}</span>
        </div>
      </li>
    </ol>
  </div>
</section>

<section class="lasles-pricing-cta">
  <div class="lasles-container">
    <div class="lasles-pricing-cta__inner">
      <div class="lasles-pricing-cta__copy">
        <h2>{{ __('public.pricing_footer_cta_title') }}</h2>
        <p>{{ __('public.pricing_footer_cta_sub') }}</p>
      </div>
      <div class="lasles-pricing-cta__actions">
        <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('public.pricing_cta_register') }}</a>
        <a href="{{ route('public.contact', ['topic' => 'package']) }}" class="lasles-btn-outline">{{ __('public.pricing_footer_contact') }}</a>
      </div>
    </div>
  </div>
</section>
@endsection
