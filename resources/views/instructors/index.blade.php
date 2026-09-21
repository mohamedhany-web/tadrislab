@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'TADRIS LAB');
    $profiles = $profiles ?? collect();
    $featuredCourses = $featuredCourses ?? collect();
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
  <title>{{ __('public.instructors_page_title') }} — {{ $brand }}</title>
  <meta name="description" content="{{ __('public.instructors_subtitle') }}">
  <meta name="theme-color" content="#0B3D91">
  <link rel="canonical" href="{{ route('public.instructors.index') }}">
  @include('partials.favicon-links')
  @include('partials.seo-jsonld', ['jsonldType' => 'website'])
  @include('partials.landing.head', ['landingCss' => ['theme', 'courses-catalog', 'instructors-catalog']])
</head>
<body class="sana-home sana-courses-page sana-instructors-page">
@include('partials.landing.navbar', ['navActive' => 'instructors'])

<main class="sana-cat-page">
  <section class="sana-cat-hero" id="inst-hero">
    <div class="sana-cat-hero__dots"></div>
    <div class="sana-container sana-cat-hero__inner">
      <nav class="sana-cat-hero__breadcrumb" aria-label="{{ $isRtl ? 'مسار التنقل' : 'Breadcrumb' }}">
        <a href="{{ url('/') }}">{{ $isRtl ? 'الرئيسية' : 'Home' }}</a>
        <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }}" style="font-size:0.6rem;opacity:0.5"></i>
        <span>{{ __('landing.nav.instructors') }}</span>
      </nav>

      <span class="sana-inst-hero__eyebrow"><i class="fas fa-chalkboard-user"></i> {{ $isRtl ? 'معلمون مختصون' : 'Specialist teachers' }}</span>
      <h1 class="sana-cat-hero__title">
        {{ $isRtl ? 'اختر' : 'Choose' }} <span class="hl">{{ $isRtl ? 'المعلم المناسب' : 'the right teacher' }}</span>
      </h1>
      <p class="sana-cat-hero__desc">{{ __('public.instructors_subtitle') }}</p>

      <div class="sana-cat-hero__stats sana-reveal">
        <span class="sana-cat-hero__stat"><i class="fas fa-chalkboard-teacher"></i> {{ number_format($profiles->count()) }} {{ $isRtl ? 'معلم' : 'teachers' }}</span>
        <span class="sana-cat-hero__stat"><i class="fas fa-book-open"></i> {{ number_format((int) $profiles->sum('courses_count')) }} {{ $isRtl ? 'كورس' : 'courses' }}</span>
      </div>

      <div class="sana-inst-hero__actions sana-reveal">
        <a href="#teachers-list" class="sana-btn sana-btn--white-outline sana-btn--sm"><i class="fas fa-user-graduate"></i> {{ $isRtl ? 'تصفّح المعلمين' : 'Browse teachers' }}</a>
        <a href="{{ route('public.tutor.apply') }}" class="sana-btn sana-btn--white-outline sana-btn--sm"><i class="fas fa-chalkboard-teacher"></i> {{ $isRtl ? 'انضم كمعلم' : 'Become a teacher' }}</a>
      </div>
    </div>
  </section>

  <section class="sana-section sana-section--white" id="teachers-list">
    <div class="sana-container">
      <div class="sana-head-row sana-reveal" style="margin-bottom:28px">
        <div class="sana-head">
          <h2 class="sana-head__title">{{ $isRtl ? 'المعلمون' : 'Teachers' }} <span class="hl">{{ $isRtl ? 'جاهزون للحجز' : 'ready to book' }}</span></h2>
          <span class="sana-head__line"></span>
        </div>
      </div>

      @if($profiles->isNotEmpty())
        <div class="sana-inst-grid-v2">
          @foreach($profiles as $p)
            @php
              $url = route('public.instructors.show', $p->user);
              $name = $p->user->name ?? __('public.instructor_fallback');
              $headline = $p->headline_clean ?: __('public.instructor_fallback');
              $skills = array_slice($p->skills_list ?? [], 0, 3);
              $initial = mb_substr($name, 0, 1);
            @endphp
            <article class="sana-inst-card-v2 sana-reveal">
              <a href="{{ $url }}" class="sana-inst-card-v2__main">
                <div class="sana-inst-card-v2__ring">
                  @if($p->photo_url)
                    <img src="{{ $p->photo_url }}" alt="{{ $name }}" loading="lazy">
                  @else
                    <span class="av">{{ $initial }}</span>
                  @endif
                </div>
                <h3>{{ $name }}</h3>
                <p class="sana-inst-card-v2__role">{{ $headline }}</p>
                @if(count($skills) > 0)
                  <div class="sana-inst-card-v2__tags">
                    @foreach($skills as $skill)
                      <span>{{ $skill }}</span>
                    @endforeach
                  </div>
                @endif
                <div class="sana-inst-card-v2__badges">
                  <span><i class="fas fa-book-open"></i> {{ (int) ($p->courses_count ?? 0) }} {{ __('public.instructors_course_many') }}</span>
                  <span class="is-book"><i class="fas fa-calendar-check"></i> {{ $isRtl ? 'متاح للعرض' : 'View profile' }}</span>
                </div>
                <span class="sana-inst-card-v2__link">{{ $isRtl ? 'عرض الملف الشخصي' : 'View profile' }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i></span>
              </a>
              <a href="{{ $url }}" class="sana-btn sana-btn--yellow sana-btn--sm sana-inst-card-v2__book">
                <i class="fas fa-calendar-plus"></i> {{ $isRtl ? 'عرض الجدول والحجز' : 'Schedule & book' }}
              </a>
            </article>
          @endforeach
        </div>
      @else
        <p class="sana-reveal" style="text-align:center;color:var(--muted);font-weight:700">{{ __('public.instructors_empty_hint') }}</p>
      @endif
    </div>
  </section>

  <section class="sana-cat-cta">
    <div class="sana-container">
      <div class="sana-cat-cta__inner sana-inst-cta">
        <div>
          <h2>{{ __('public.instructors_cta_title') }}</h2>
          <p>{{ __('public.instructors_cta_desc') }}</p>
        </div>
        <div class="sana-cat-cta__actions">
          <div class="sana-site-cta">
            <a href="{{ url('/?open_trial=1') }}" class="sana-btn sana-btn--yellow sana-btn--lg"><i class="fas fa-clipboard-check"></i> {{ __('landing.academy.free_trial_cta') }}</a>
            <a href="{{ $waUrl }}" class="sana-btn sana-btn--wa sana-btn--lg" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> {{ $isRtl ? 'واتساب' : 'WhatsApp' }}</a>
          </div>
          <a href="{{ route('public.tutor.apply') }}" class="sana-btn sana-btn--purple-outline"><i class="fas fa-chalkboard-teacher"></i> {{ __('public.instructors_cta_register') }}</a>
        </div>
      </div>
    </div>
  </section>
</main>

@include('partials.landing.footer')
@if(request('focus') === 'private')
<script>
document.getElementById('teachers-list')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
</script>
@endif
</body>
</html>
