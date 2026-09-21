@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $laslesCss = public_path('css/landing/lasles.css');
    $laslesVer = is_file($laslesCss) ? (string) filemtime($laslesCss) : (string) time();
    $brand = 'TADRIS LAB';
    $brandAr = 'TADRIS LAB';
    $img = function (string $file) {
        return lasles_img($file);
    };
    $langSwitch = fn (string $lang) => request()->fullUrlWithQuery(array_merge(request()->query(), ['lang' => $lang]));
    $laslesNavActive = $laslesNavActive ?? '';
    $pageTitle = $pageTitle ?? ($brand);
    $pageDescription = $pageDescription ?? __('landing.meta.description');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.seo-meta', [
        'title' => $pageTitle,
        'description' => $pageDescription,
        'keywords' => __('landing.meta.keywords'),
        'image' => \App\Services\SeoAssets::ogImageUrl(),
        'imageAlt' => $pageTitle,
        'url' => url()->current(),
        'type' => 'website',
    ])
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Lato:wght@400;700;900&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="theme-color" content="#1E4E8C">
    <link rel="stylesheet" href="{{ route('assets.landing.css', ['sheet' => 'lasles']) }}?v={{ $laslesVer }}">
    @stack('head')
</head>
<body class="lasles-home {{ $bodyClass ?? '' }}">
@include('partials.landing.lasles.nav')
<main>
    @yield('content')
</main>
@include('partials.landing.lasles.footer')
@include('partials.landing.lasles.scripts')
@stack('scripts')
</body>
</html>
