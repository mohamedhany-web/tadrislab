@php
    $isRtl = app()->getLocale() === 'ar';
    $brand = config('app.name', 'TADRIS LAB');
    $langSwitch = fn (string $lang) => request()->fullUrlWithQuery(array_merge(request()->query(), ['lang' => $lang]));
    $authCss = public_path('css/landing/lasles-auth.css');
    $authVer = is_file($authCss) ? (string) filemtime($authCss) : (string) time();
    $logo = asset('img/lasles/logo-mark.png');
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title') — {{ $brand }}</title>
  <meta name="robots" content="noindex, nofollow">
  <meta name="theme-color" content="#1E4E8C">
  @include('partials.favicon-links')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Lato:wght@400;700;900&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="{{ route('assets.landing.css', ['sheet' => 'lasles-auth']) }}?v={{ $authVer }}">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  @stack('head')
</head>
<body class="lasles-auth" @yield('body_attrs')>
  <div class="lasles-auth-shell">
    <div class="lasles-auth-bg" aria-hidden="true"><span class="lasles-auth-bg__grid"></span></div>
    <div class="lasles-auth-layer">
      <nav class="lasles-auth-nav">
        <a href="{{ route('home') }}" class="lasles-auth-logo">
          <img src="{{ $logo }}" width="32" height="32" alt="">
          <span>@if($isRtl)<b>تدريس لاب</b>@else<b>TADRIS</b> <em>LAB</em>@endif</span>
        </a>
        <div class="lasles-auth-nav__actions">
          <div class="lasles-auth-lang" role="group" aria-label="{{ $isRtl ? 'اللغة' : 'Language' }}">
            <a href="{{ $langSwitch('ar') }}" class="{{ $isRtl ? 'is-on' : '' }}" hreflang="ar">عربي</a>
            <a href="{{ $langSwitch('en') }}" class="{{ ! $isRtl ? 'is-on' : '' }}" hreflang="en">EN</a>
          </div>
          @hasSection('nav_action')
            @yield('nav_action')
          @else
            <a href="{{ route('home') }}" class="lasles-auth-nav-link">{{ __('auth.back_to_home') }}</a>
          @endif
        </div>
      </nav>
      <div class="lasles-auth-main @yield('main_class')">
        @include('partials.auth.lasles.aside')
        <div class="lasles-auth-panel">
          @yield('content')
        </div>
      </div>
    </div>
  </div>
  @stack('scripts')
</body>
</html>
