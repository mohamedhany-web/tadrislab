@extends('layouts.lasles-public')

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

<section class="lasles-paths-hero">
  <div class="lasles-container lasles-paths-hero__inner">
    <p class="lasles-site-breadcrumb" style="margin-bottom:.85rem">
      <a href="{{ route('public.tools.index') }}">{{ __('landing.tools.catalog_title') }}</a>
      <span aria-hidden="true">/</span>
      <span>{{ $tool->title() }}</span>
    </p>
    <p class="lasles-paths-hero__brand">{{ $typeLabels[$tool->tool_type] ?? $tool->tool_type }}</p>
    <h1 class="lasles-paths-hero__title">{{ $tool->title() }}</h1>
    @if($tool->summary())
      <p class="lasles-paths-hero__lead">{{ $tool->summary() }}</p>
    @endif
  </div>
</section>

<section class="lasles-container" style="padding-block:2.5rem 4rem">
  @if(session('error'))
    <div class="lasles-paths-empty" style="margin-bottom:1.5rem"><p>{{ session('error') }}</p></div>
  @endif

  @if($tool->description())
    <div class="lasles-section-lead" style="max-width:42rem;white-space:pre-line;margin-bottom:1.75rem">{{ $tool->description() }}</div>
  @endif

  <div class="lasles-hero__actions" style="justify-content:flex-start;flex-wrap:wrap;gap:.75rem">
    @if($canAccess && ($tool->hasDownloadableFile() || $tool->hasExternalLink()))
      <a href="{{ route('public.tools.download', $tool->slug) }}" class="lasles-btn-primary">{{ __('landing.tools.cta_download') }}</a>
    @elseif(! $canAccess)
      <a href="{{ route('login') }}" class="lasles-btn-primary">{{ __('landing.tools.cta_login') }}</a>
      <a href="{{ route('public.pricing') }}" class="lasles-btn-outline">{{ __('landing.tools.cta_packages') }}</a>
    @endif
    <a href="{{ route('public.tools.index') }}" class="lasles-btn-outline">{{ __('landing.tools.cta_back') }}</a>
  </div>

  @if($tool->learningPaths->isNotEmpty() || $tool->packages->isNotEmpty())
    <div style="margin-top:2.5rem;display:grid;gap:1.5rem">
      @if($tool->learningPaths->isNotEmpty())
        <div>
          <h2 class="lasles-section-title" style="font-size:1.25rem">{{ __('landing.tools.linked_paths') }}</h2>
          <ul style="margin-top:1rem;display:grid;gap:.5rem">
            @foreach($tool->learningPaths as $path)
              <li><a href="{{ route('public.learning-paths.show', $path->slug) }}">{{ $path->title() }}</a></li>
            @endforeach
          </ul>
        </div>
      @endif
      @if($tool->packages->isNotEmpty())
        <div>
          <h2 class="lasles-section-title" style="font-size:1.25rem">{{ __('landing.tools.linked_packages') }}</h2>
          <ul style="margin-top:1rem;display:grid;gap:.5rem">
            @foreach($tool->packages as $package)
              <li><a href="{{ route('public.pricing') }}">{{ $package->name }}</a></li>
            @endforeach
          </ul>
        </div>
      @endif
    </div>
  @endif
</section>
@endsection
