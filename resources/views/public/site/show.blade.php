@extends('layouts.lasles-public')

@section('content')
@php
  $isRtl = app()->getLocale() === 'ar';
  $children = $node['children'] ?? [];
  $parent = $node['parent'] ?? null;
  $sections = $content['sections'] ?? [];
  $outcomes = $content['outcomes'] ?? [];
  $packages = $packages ?? collect();
  $relatedPages = $relatedPages ?? [];
  $catalogUrl = $catalogUrl ?? null;
  $deliveryKey = $deliveryKey ?? 'course';
  $deliveryCopy = __('site.delivery.'.$deliveryKey);
  if (! is_array($deliveryCopy)) {
      $deliveryCopy = [];
  }

  $ctaPrimary = route('public.pricing');
  $ctaPrimaryLabel = __('site.cta.packages');
  if (str_starts_with($pageKey, 'account')) {
      $ctaPrimary = auth()->check() ? url('/dashboard') : route('login');
      $ctaPrimaryLabel = auth()->check() ? __('auth.dashboard') : __('site.cta.login');
  } elseif (in_array($deliveryKey, ['consultation', 'institutional'], true)) {
      $ctaPrimary = route('public.contact', ['topic' => $pageKey]);
      $ctaPrimaryLabel = __('site.cta.consult');
  } elseif ($catalogUrl) {
      $ctaPrimary = $catalogUrl;
      $ctaPrimaryLabel = __('site.cta.explore_catalog');
  }
@endphp

<section class="lasles-site-hero">
  <div class="lasles-container lasles-site-hero__inner">
    @if($parent)
      <p class="lasles-site-breadcrumb">
        <a href="{{ route($parent['route']) }}">{{ $parent['label'] }}</a>
        <span aria-hidden="true">/</span>
        <span>{{ $node['label'] }}</span>
      </p>
    @endif
    @if(!empty($content['kicker']))
      <p class="lasles-path-kicker">{{ $content['kicker'] }}</p>
    @endif
    <h1 class="lasles-site-hero__title">{{ $content['title'] ?? $node['label'] }}</h1>
    @if(!empty($content['lead']))
      <p class="lasles-site-hero__lead">{{ $content['lead'] }}</p>
    @endif
    <div class="lasles-site-actors">
      <span class="lasles-site-chip">{{ __('site.actors.learner_chip') }}</span>
      <span class="lasles-site-chip lasles-site-chip--gold">{{ __('site.actors.instructor_chip') }}</span>
      @if(!empty($content['loop_step']))
        <span class="lasles-site-chip">{{ $content['loop_step'] }}</span>
      @endif
    </div>
    <div class="lasles-path-hero__actions">
      <a href="{{ $ctaPrimary }}" class="lasles-btn-primary">{{ $ctaPrimaryLabel }}</a>
      <a href="{{ route('register') }}" class="lasles-btn-outline">{{ __('site.cta.register') }}</a>
    </div>
    @if(!empty($content['auth_note']))
      <p class="lasles-site-note">{{ $content['auth_note'] }}</p>
    @else
      <p class="lasles-site-note">{{ __('site.actors.learner_note') }}</p>
    @endif
  </div>
</section>

<section class="lasles-site-roles">
  <div class="lasles-container lasles-site-roles__grid">
    <article class="lasles-site-role">
      <p class="lasles-site-role__eyebrow">{{ __('site.actors.for_learner') }}</p>
      <h2>{{ $content['audience'] ?? __('site.actors.learner_title') }}</h2>
      <p>{{ $content['for_teacher'] ?? __('site.actors.learner_body') }}</p>
    </article>
    <article class="lasles-site-role lasles-site-role--instructor">
      <p class="lasles-site-role__eyebrow">{{ __('site.actors.for_instructor') }}</p>
      <h2>{{ $deliveryCopy['title'] ?? __('site.actors.instructor_title') }}</h2>
      <p>{{ $content['delivered_by'] ?? ($deliveryCopy['body'] ?? __('site.actors.instructor_body')) }}</p>
      @if(!empty($content['how_described']) || !empty($deliveryCopy['how_described']))
        <p class="lasles-site-role__meta">
          <strong>{{ __('site.actors.how_described_label') }}</strong>
          {{ $content['how_described'] ?? $deliveryCopy['how_described'] }}
        </p>
      @endif
    </article>
  </div>
</section>

@if(count($children))
<section class="lasles-site-cards">
  <div class="lasles-container">
    <h2 class="lasles-section-title lasles-section-title--center">{{ __('site.sections.explore') }}</h2>
    <div class="lasles-site-grid">
      @foreach($children as $child)
        @php $childContent = __('site.pages.'.$child['key']); @endphp
        <a class="lasles-site-card" href="{{ route($child['route']) }}">
          <h3>{{ $child['label'] }}</h3>
          <p>{{ is_array($childContent) ? ($childContent['lead'] ?? '') : '' }}</p>
          <span>{{ __('site.cta.explore') }} →</span>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

@if(count($sections) || count($outcomes))
<section class="lasles-site-sections">
  <div class="lasles-container">
    @if(count($sections))
      <div class="lasles-site-sections__grid">
        @foreach($sections as $section)
          <article class="lasles-site-block">
            <h2>{{ $section['title'] ?? '' }}</h2>
            <p>{{ $section['body'] ?? '' }}</p>
          </article>
        @endforeach
      </div>
    @endif
    @if(count($outcomes))
      <div class="lasles-site-outcomes">
        <h2 class="lasles-section-title">{{ __('site.sections.outcomes') }}</h2>
        <ul>
          @foreach($outcomes as $outcome)
            <li>{{ $outcome }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </div>
</section>
@endif

@if($packages->isNotEmpty())
<section class="lasles-site-packages" id="packages">
  <div class="lasles-container">
    <h2 class="lasles-section-title">{{ __('site.sections.packages_title') }}</h2>
    <p class="lasles-section-lead">{{ $content['package_hint'] ?? __('site.sections.packages_lead') }}</p>
    <div class="lasles-site-packages__grid">
      @foreach($packages as $package)
        <article class="lasles-site-package">
          <p class="lasles-site-package__type">{{ $package->typeLabel() }}</p>
          <h3>{{ $package->name }}</h3>
          <p>{{ $package->card_summary ?: $package->formattedPrice() }}</p>
          <p class="lasles-site-package__price">{{ $package->formattedPrice() }}</p>
          <a href="{{ $package->ctaUrl() }}" class="lasles-btn-outline">{{ $package->ctaLabel() }}</a>
        </article>
      @endforeach
    </div>
    <p class="lasles-site-packages__all">
      <a href="{{ route('public.pricing') }}" class="lasles-btn-primary">{{ __('site.cta.all_packages') }}</a>
    </p>
  </div>
</section>
@else
<section class="lasles-site-packages lasles-site-packages--empty" id="packages">
  <div class="lasles-container">
    <h2 class="lasles-section-title">{{ __('site.sections.packages_title') }}</h2>
    <p class="lasles-section-lead">{{ $content['package_hint'] ?? __('site.sections.packages_lead') }}</p>
    <a href="{{ route('public.pricing') }}" class="lasles-btn-primary">{{ __('site.cta.all_packages') }}</a>
  </div>
</section>
@endif

@if(count($relatedPages))
<section class="lasles-site-related">
  <div class="lasles-container">
    <h2 class="lasles-section-title lasles-section-title--center">{{ __('site.sections.related') }}</h2>
    <div class="lasles-site-grid">
      @foreach($relatedPages as $related)
        <a class="lasles-site-card" href="{{ $related['url'] }}">
          <h3>{{ $related['label'] }}</h3>
          <p>{{ \Illuminate\Support\Str::limit($related['lead'], 110) }}</p>
          <span>{{ __('site.cta.explore') }} →</span>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="lasles-path-promise">
  <div class="lasles-container lasles-path-promise__box">
    <div>
      <h2 class="lasles-section-title">{{ __('site.sections.next_title') }}</h2>
      <p class="lasles-section-lead">{{ __('site.sections.next_lead') }}</p>
    </div>
    <div class="lasles-path-promise__cta">
      <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('site.cta.register') }}</a>
      <a href="{{ route('public.pricing') }}" class="lasles-path-promise__login">{{ __('site.cta.packages') }}</a>
      <a href="{{ route('public.contact') }}" class="lasles-path-promise__login">{{ __('site.cta.contact') }}</a>
    </div>
  </div>
</section>
@endsection
