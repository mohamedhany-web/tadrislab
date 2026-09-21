{{-- Shared page art: isolated Lasles illustrations (home-style), different asset per page --}}
@php
    $img = $img ?? fn (string $file) => asset('img/lasles/'.$file);
    $variant = $variant ?? 'person'; // person | features | map | tools
@endphp
@if($variant === 'person')
  <div class="lasles-page-art lasles-page-art--person" aria-hidden="true">
    <img src="{{ $img('hero-illustration.svg') }}" width="611" height="382" alt="" decoding="async" fetchpriority="high">
  </div>
@elseif($variant === 'features')
  <div class="lasles-page-art lasles-page-art--features" aria-hidden="true">
    <img src="{{ $img('features-illustration.png') }}" width="508" height="414" alt="" decoding="async" fetchpriority="high">
  </div>
@elseif($variant === 'map')
  <div class="lasles-page-art lasles-page-art--map" aria-hidden="true">
    <img src="{{ $img('map-global.svg') }}" width="508" height="280" alt="" loading="lazy" decoding="async">
  </div>
@elseif($variant === 'tools')
  {{-- Different asset from home hero; same isolated treatment --}}
  <div class="lasles-page-art lasles-page-art--tools" aria-hidden="true">
    <img src="{{ $img('features-illustration.png') }}" width="508" height="414" alt="" decoding="async" fetchpriority="high">
  </div>
@else
  <div class="lasles-page-art lasles-page-art--person" aria-hidden="true">
    <img src="{{ $img('hero-illustration.svg') }}" width="611" height="382" alt="" decoding="async">
  </div>
@endif
