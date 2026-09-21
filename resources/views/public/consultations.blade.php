@extends('layouts.lasles-public')

@section('content')
@php
  $isRtl = app()->getLocale() === 'ar';
  $img = fn (string $file) => lasles_img($file);
  $content = is_array($content ?? null) ? $content : [];
  $children = $node['children'] ?? [];
  $how = $content['how'] ?? [];
  $bookUrl = \Illuminate\Support\Facades\Route::has('public.consultations.book')
      ? route('public.consultations.book')
      : route('public.contact', ['topic' => 'consultations']);
@endphp

<section class="lasles-consult-hero" aria-labelledby="consult-hero-title">
  <div class="lasles-container lasles-consult-hero__grid">
    <div class="lasles-consult-hero__copy">
      <p class="lasles-path-kicker">{{ $content['kicker'] ?? '' }}</p>
      <p class="lasles-consult-hero__brand">{{ $isRtl ? 'تدريس لاب' : 'TADRIS LAB' }}</p>
      <h1 id="consult-hero-title" class="lasles-consult-hero__title">
        {{ $content['title_before'] ?? ($content['title'] ?? '') }}
        @if(!empty($content['title_strong']))
          <em>{{ $content['title_strong'] }}</em>
        @endif
      </h1>
      <p class="lasles-consult-hero__lead">{{ $content['lead'] ?? '' }}</p>
      <div class="lasles-hero__actions">
        <a href="{{ $bookUrl }}" class="lasles-btn-primary">{{ $content['cta_book'] ?? __('site.cta.consult') }}</a>
        <a href="{{ route('register') }}" class="lasles-btn-outline">{{ __('landing.home.cta_start_journey') }}</a>
      </div>
    </div>
    <div class="lasles-consult-hero__art">
      @include('partials.landing.lasles.page-art', ['variant' => 'map', 'img' => $img])
    </div>
  </div>
</section>

<section class="lasles-consult-story">
  <div class="lasles-container lasles-consult-story__inner">
    <h2 class="lasles-section-title">{{ $content['story_title'] ?? '' }}</h2>
    <p class="lasles-section-lead">{{ $content['story_body'] ?? '' }}</p>
  </div>
</section>

@if(is_array($how) && count($how))
<section class="lasles-paths-guide">
  <div class="lasles-container">
    <h2 class="lasles-section-title lasles-section-title--center">{{ $content['how_title'] ?? '' }}</h2>
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
<section class="lasles-consult-types" id="types">
  <div class="lasles-container">
    <h2 class="lasles-section-title lasles-section-title--center">{{ $content['types_title'] ?? __('site.sections.explore') }}</h2>
    <p class="lasles-section-lead lasles-section-lead--center">{{ $content['types_lead'] ?? '' }}</p>
    <div class="lasles-consult-types__grid">
      @foreach($children as $child)
        @php
          $childContent = __('site.pages.'.$child['key']);
          $childLead = is_array($childContent) ? ($childContent['lead'] ?? '') : '';
        @endphp
        <a class="lasles-consult-type" href="{{ route($child['route']) }}">
          <h3>{{ $child['label'] }}</h3>
          @if($childLead)
            <p>{{ $childLead }}</p>
          @endif
          <span>{{ __('site.cta.explore') }} <span aria-hidden="true">→</span></span>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="lasles-container lasles-subscribe-wrap lasles-consult-cta">
  <div class="lasles-subscribe">
    <div>
      <h2 class="lasles-subscribe__title">{{ $content['cta_title'] ?? '' }}</h2>
      <p class="lasles-subscribe__lead">{{ $content['cta_lead'] ?? '' }}</p>
    </div>
    <div class="lasles-subscribe__actions">
      <a href="{{ $bookUrl }}" class="lasles-btn-primary">{{ $content['cta_book'] ?? __('site.cta.consult') }}</a>
      <a href="{{ route('register') }}" class="lasles-btn-outline">{{ __('landing.home.cta_start_journey') }}</a>
    </div>
  </div>
</section>
@endsection
