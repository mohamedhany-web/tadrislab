@php
    $publicLocale = app()->getLocale();
    $publicRtl = $publicLocale === 'ar';
    $laslesCss = public_path('css/landing/lasles.css');
    $laslesVer = is_file($laslesCss) ? (string) filemtime($laslesCss) : (string) time();
    $brand = config('app.name', 'TADRIS LAB');
    $img = fn (string $file) => lasles_img($file);
    $langSwitch = fn (string $lang) => request()->fullUrlWithQuery(array_merge(request()->query(), ['lang' => $lang]));
@endphp
<!DOCTYPE html>
<html lang="{{ $publicLocale }}" dir="{{ $publicRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $seoTitle = trim($__env->yieldContent('title')) ?: ($brand.' — '.('TADRIS LAB'));
        $seoDescription = trim($__env->yieldContent('meta_description')) ?: __('landing.meta.description');
        $seoKeywords = trim($__env->yieldContent('meta_keywords')) ?: __('landing.meta.keywords');
        $seoImage = trim($__env->yieldContent('meta_image')) ?: \App\Services\SeoAssets::ogImageUrl();
        $seoType = trim($__env->yieldContent('meta_type')) ?: 'website';
        $seoCanonical = trim($__env->yieldContent('canonical_url')) ?: url()->current();
        $seoAltBase = url()->current();
    @endphp
    @include('components.seo-meta', [
        'title' => $seoTitle,
        'description' => $seoDescription,
        'keywords' => $seoKeywords,
        'image' => $seoImage,
        'type' => $seoType,
        'url' => $seoCanonical,
    ])
    <link rel="alternate" hreflang="ar" href="{{ $seoAltBase }}?lang=ar">
    <link rel="alternate" hreflang="en" href="{{ $seoAltBase }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ $seoAltBase }}">
    <meta name="theme-color" content="#1E4E8C">
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Lato:wght@400;700;900&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ route('assets.landing.css', ['sheet' => 'lasles']) }}?v={{ $laslesVer }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
      /* Light shell for legacy dark public content blocks */
      .lasles-public-legacy {
        color: var(--lasles-navy, #1A3558);
        background: #fff;
        min-height: 50vh;
        padding: 1.5rem 0 3rem;
      }
      .lasles-public-legacy .text-white { color: var(--lasles-navy, #1A3558) !important; }
      .lasles-public-legacy .text-white\/70,
      .lasles-public-legacy .text-white\/80,
      .lasles-public-legacy .text-white\/60 { color: var(--lasles-muted, #5A6472) !important; }
      .lasles-public-legacy .bg-\[\#0B132A\],
      .lasles-public-legacy .bg-slate-900,
      .lasles-public-legacy .bg-navy { background: transparent !important; }
    </style>
    @stack('styles')
    @stack('head')
    @include('partials.seo-jsonld', ['jsonldType' => 'website'])
</head>
<body class="lasles-home page-academy font-sans antialiased"
      x-data="{ mobileMenu: false, searchQuery: '' }"
      :class="{ 'overflow-hidden': mobileMenu }">

    @include('partials.landing.lasles.nav')

    <main class="flex-1 w-full lasles-public-legacy">
        <div class="lasles-container">
            @yield('content')
        </div>
    </main>

    @include('partials.landing.lasles.footer')
    @include('partials.landing.lasles.scripts')
    @stack('scripts')
</body>
</html>
