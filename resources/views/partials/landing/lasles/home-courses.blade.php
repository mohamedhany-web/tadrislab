@php
    $featuredCourses = $featuredCourses ?? collect();
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
@endphp
<section class="lasles-showcase lasles-showcase--courses" id="home-courses" aria-labelledby="home-courses-title">
  <div class="lasles-container">
    <div class="lasles-showcase__head">
      <div class="lasles-showcase__intro">
        <p class="lasles-path-kicker">{{ __('landing.home.showcase_courses_kicker') }}</p>
        <h2 id="home-courses-title" class="lasles-section-title">{{ __('landing.home.showcase_courses_title') }}</h2>
        <p class="lasles-section-lead">{{ __('landing.home.showcase_courses_lead') }}</p>
      </div>
      <a href="{{ route('public.courses') }}" class="lasles-showcase__more lasles-btn-outline">
        {{ __('landing.home.showcase_courses_cta') }}
        <span aria-hidden="true">{{ $isRtl ? '←' : '→' }}</span>
      </a>
    </div>

    @if($featuredCourses->isEmpty())
      <div class="lasles-showcase__empty">
        <p>{{ __('landing.home.showcase_courses_empty') }}</p>
        <a href="{{ route('public.courses') }}" class="lasles-btn-primary">{{ __('landing.home.showcase_courses_cta') }}</a>
      </div>
    @else
      <div class="lasles-showcase__grid lasles-showcase__grid--courses lasles-courses-grid">
        @foreach($featuredCourses as $course)
          @include('partials.landing.lasles.course-card', ['course' => $course])
        @endforeach
      </div>
      <div class="lasles-showcase__foot">
        <a href="{{ route('public.courses') }}" class="lasles-showcase__more-link">
          {{ __('landing.home.showcase_courses_cta') }}
          <span aria-hidden="true">{{ $isRtl ? '←' : '→' }}</span>
        </a>
      </div>
    @endif
  </div>
</section>
