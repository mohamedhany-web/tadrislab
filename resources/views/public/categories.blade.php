@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'TADRIS LAB');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ __('landing.nav.categories') }} — {{ $brand }}</title>
  <meta name="description" content="{{ $isRtl ? 'تصفّح تصنيفات تدريس لاب.' : 'Browse TADRIS LAB categories.' }}">
  <link rel="canonical" href="{{ route('public.categories') }}">
  @include('partials.favicon-links')
  @include('partials.landing.head', ['landingCss' => []])
</head>
<body class="lasles-home">
@include('partials.landing.lasles.nav')

<main>
  <section class="lasles-container" style="padding:2.5rem 0 3.5rem">
    <nav class="lasles-site-breadcrumb" aria-label="{{ $isRtl ? 'مسار التنقل' : 'Breadcrumb' }}" style="margin-bottom:1.25rem">
      <a href="{{ url('/') }}">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a>
      <span aria-hidden="true">/</span>
      <span>{{ __('landing.nav.categories') }}</span>
    </nav>
    <h1 class="lasles-site-hero__title" style="margin:0 0 .75rem">{{ __('landing.nav.categories') }}</h1>
    <p class="lasles-site-hero__lead" style="margin:0 0 2rem;max-width:40rem">
      {{ $isRtl ? 'مسارات واضحة تقودك للمحتوى المناسب.' : 'Clear paths to the right learning content.' }}
    </p>

    <div class="lasles-site-grid">
      @forelse($categories as $category)
        <a class="lasles-site-card" href="{{ $category['url'] }}">
          @if(!empty($category['thumb_url']))
            <img src="{{ $category['thumb_url'] }}" alt="" style="width:100%;height:140px;object-fit:cover;border-radius:8px;margin-bottom:.85rem" loading="lazy">
          @endif
          <h3>{{ $category['name'] }}</h3>
          @if(!empty($category['desc']))
            <p>{{ $category['desc'] }}</p>
          @endif
          <span>{{ __('site.cta.explore') }} →</span>
        </a>
      @empty
        <p>{{ $isRtl ? 'لا توجد تصنيفات حالياً.' : 'No categories yet.' }}</p>
      @endforelse
    </div>

    @if(!empty($featuredCourses) && count($featuredCourses))
      <h2 class="lasles-section-title" style="margin-top:3rem">{{ $isRtl ? 'مختارات' : 'Featured' }}</h2>
      <div class="lasles-site-grid">
        @foreach($featuredCourses as $course)
          <a class="lasles-site-card" href="{{ route('public.course.show', $course->id) }}">
            <h3>{{ $course->title }}</h3>
            <span>{{ __('site.cta.explore') }} →</span>
          </a>
        @endforeach
      </div>
    @endif
  </section>
</main>

@include('partials.landing.lasles.footer')
@include('partials.landing.lasles.scripts')
</body>
</html>
