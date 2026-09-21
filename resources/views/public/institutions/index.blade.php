@extends('layouts.lasles-public')

@section('content')
@php
  $isRtl = app()->getLocale() === 'ar';
  $img = fn (string $file) => lasles_img($file);
  $content = is_array($content ?? null) ? $content : [];
  $children = $node['children'] ?? [];
  $how = $content['how'] ?? [];
  $points = $content['points'] ?? [];
  $inquiryUrl = route('public.institutions.inquiry');
  $packages = $packages ?? collect();
@endphp

<section class="lasles-inst-hero" aria-labelledby="inst-hero-title">
  <div class="lasles-container lasles-inst-hero__grid">
    <div class="lasles-inst-hero__copy">
      <p class="lasles-path-kicker">{{ $content['kicker'] ?? '' }}</p>
      <p class="lasles-inst-hero__brand">{{ $isRtl ? 'تدريس لاب' : 'TADRIS LAB' }}</p>
      <h1 id="inst-hero-title" class="lasles-inst-hero__title">
        {{ $content['title_before'] ?? ($content['title'] ?? '') }}
        @if(!empty($content['title_strong']))
          <em>{{ $content['title_strong'] }}</em>
        @endif
      </h1>
      <p class="lasles-inst-hero__lead">{{ $content['lead'] ?? '' }}</p>
      <p class="lasles-inst-hero__outcome">{{ $content['outcome'] ?? '' }}</p>
      <div class="lasles-hero__actions">
        <a href="{{ $inquiryUrl }}" class="lasles-btn-primary">{{ $content['cta_inquiry'] ?? __('landing.home.institutions_cta') }}</a>
        <a href="{{ route('public.contact', ['topic' => 'institutional']) }}" class="lasles-btn-outline">{{ __('landing.home.cta_contact') }}</a>
      </div>
    </div>
    <div class="lasles-inst-hero__art">
      @include('partials.landing.lasles.page-art', ['variant' => 'map', 'img' => $img])
    </div>
  </div>
</section>

<section class="lasles-inst-story">
  <div class="lasles-container lasles-inst-story__grid">
    <div>
      <h2 class="lasles-section-title">{{ $content['story_title'] ?? '' }}</h2>
      <p class="lasles-section-lead">{{ $content['story_body'] ?? '' }}</p>
      @if(is_array($points) && count($points))
        <ul class="lasles-checklist lasles-inst-story__list">
          @foreach($points as $item)
            <li><img src="{{ $img('check-green.svg') }}" width="22" height="22" alt="">{{ $item }}</li>
          @endforeach
        </ul>
      @endif
    </div>
    <aside class="lasles-inst-panel" aria-hidden="true">
      <div class="lasles-inst-panel__head">
        <p>{{ $content['panel_title'] ?? __('landing.home.institutions_panel_title') }}</p>
        <span>{{ $content['panel_chip'] ?? __('landing.home.institutions_panel_chip') }}</span>
      </div>
      <div class="lasles-inst-panel__metrics">
        <div>
          <strong>{{ __('landing.home.institutions_metric_1_value') }}</strong>
          <span>{{ __('landing.home.institutions_metric_1_label') }}</span>
        </div>
        <div>
          <strong>{{ __('landing.home.institutions_metric_2_value') }}</strong>
          <span>{{ __('landing.home.institutions_metric_2_label') }}</span>
        </div>
        <div>
          <strong>{{ __('landing.home.institutions_metric_3_value') }}</strong>
          <span>{{ __('landing.home.institutions_metric_3_label') }}</span>
        </div>
      </div>
    </aside>
  </div>
</section>

@if(is_array($how) && count($how))
<section class="lasles-paths-guide lasles-inst-guide">
  <div class="lasles-container">
    <div class="lasles-paths-guide__head">
      <h2 class="lasles-section-title lasles-section-title--center">{{ $content['how_title'] ?? '' }}</h2>
      <p class="lasles-section-lead lasles-section-lead--center">{{ $content['how_lead'] ?? '' }}</p>
    </div>
    <ol class="lasles-paths-guide__grid">
      @foreach($how as $i => $item)
        <li class="lasles-paths-guide__item">
          <span class="lasles-paths-guide__num" aria-hidden="true">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
          <h3>{{ $item['title'] ?? '' }}</h3>
          <p>{{ $item['body'] ?? '' }}</p>
        </li>
      @endforeach
    </ol>
  </div>
</section>
@endif

@if(count($children))
<section class="lasles-inst-types" id="programs">
  <div class="lasles-container">
    <h2 class="lasles-section-title lasles-section-title--center">{{ $content['types_title'] ?? __('site.sections.explore') }}</h2>
    <p class="lasles-section-lead lasles-section-lead--center">{{ $content['types_lead'] ?? '' }}</p>
    <div class="lasles-inst-types__grid">
      @foreach($children as $child)
        @php
          $childContent = __('site.pages.'.$child['key']);
          $childLead = is_array($childContent) ? ($childContent['lead'] ?? '') : '';
          $isInquiry = ($child['key'] ?? '') === 'institutional-inquiry';
        @endphp
        <a class="lasles-inst-type {{ $isInquiry ? 'is-featured' : '' }}" href="{{ route($child['route']) }}">
          <h3>{{ $child['label'] }}</h3>
          @if($childLead !== '')
            <p>{{ $childLead }}</p>
          @elseif($isInquiry)
            <p>{{ $content['inquiry_lead'] ?? '' }}</p>
          @endif
          <span>{{ __('site.cta.explore') }} <span aria-hidden="true">→</span></span>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

@if($packages->isNotEmpty())
<section class="lasles-inst-packages">
  <div class="lasles-container">
    <h2 class="lasles-section-title lasles-section-title--center">{{ $content['packages_title'] ?? __('site.sections.packages_title') }}</h2>
    <p class="lasles-section-lead lasles-section-lead--center">{{ $content['package_hint'] ?? __('site.sections.packages_lead') }}</p>
    <div class="lasles-inst-packages__grid">
      @foreach($packages->take(2) as $package)
        <article class="lasles-inst-package">
          <p class="lasles-inst-package__type">{{ $package->typeLabel() }}</p>
          <h3>{{ $package->name }}</h3>
          <p>{{ $package->card_summary ?: $package->formattedPrice() }}</p>
          <p class="lasles-inst-package__price">{{ $package->formattedPrice() }}</p>
          <a href="{{ $package->ctaUrl() }}" class="lasles-btn-outline">{{ $package->ctaLabel() }}</a>
        </article>
      @endforeach
    </div>
    <p class="lasles-inst-packages__all">
      <a href="{{ route('public.pricing') }}" class="lasles-btn-primary">{{ __('site.cta.all_packages') }}</a>
    </p>
  </div>
</section>
@endif

<section class="lasles-container lasles-subscribe-wrap lasles-inst-cta">
  <div class="lasles-subscribe">
    <div>
      <h2 class="lasles-subscribe__title">{{ $content['cta_title'] ?? '' }}</h2>
      <p class="lasles-subscribe__lead">{{ $content['cta_lead'] ?? '' }}</p>
    </div>
    <div class="lasles-subscribe__actions">
      <a href="{{ $inquiryUrl }}" class="lasles-btn-primary">{{ $content['cta_inquiry'] ?? __('landing.home.institutions_cta') }}</a>
      <a href="{{ route('public.contact') }}" class="lasles-btn-outline">{{ __('landing.home.cta_contact') }}</a>
    </div>
  </div>
</section>
@endsection
