@extends('layouts.lasles-public')

@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'TADRIS LAB');
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
    $thumbUrl = $course->thumbnail_url;
    $introVideoUrl = trim((string) ($course->video_url ?? ''));
    $introEmbedUrl = \App\Helpers\VideoHelper::getEmbedUrl($introVideoUrl);
    $introDirectVideo = \App\Helpers\VideoHelper::getDirectVideoUrl($introVideoUrl);
    $categoryDisplay = $course->courseCategory?->name ?? __('public.course_category_not_set');
    $isMonthly = $course->isMonthlyBilling();
    $checkoutPrice = $course->effectiveCheckoutPrice();
    $isPaid = $checkoutPrice > 0 && ! ($course->is_free ?? false);
    $hasPromo = $isPaid && $course->hasPromotionalPrice();
    $listPrice = $hasPromo ? $course->listPriceAmount() : 0;
    $savedAmount = $hasPromo ? max(0, $listPrice - $checkoutPrice) : 0;
    $discountPct = ($hasPromo && $listPrice > 0)
        ? (int) round((1 - ($checkoutPrice / $listPrice)) * 100)
        : 0;
    $instructorApproved = $course->instructor
        && \App\Models\InstructorProfile::where('user_id', $course->instructor->id)->where('status', 'approved')->exists();
    $subjectName = $course->academicSubject->name ?? __('public.course_category_not_set');
    $learnPoints = $course->what_you_learn
        ? array_values(array_filter(array_map('trim', explode("\n", $course->what_you_learn))))
        : [];
    $isOneToOne = $course->isOneToOne();
    $from = $from ?? ($isOneToOne ? 'one_to_one' : 'groups');
    $catalogUrl = route('public.courses');
    $deliveryLabel = $isOneToOne
        ? ($isRtl ? 'فردي 1:1' : '1:1 private')
        : ($isRtl ? 'جماعي' : 'Group');
    $heroCover = $thumbUrl ?: asset('img/lasles/features-illustration.png');
    $pageTitle = ($course->title ?? __('public.course_detail_title')).' — '.($isRtl ? 'تدريس لاب' : $brand);
    $pageDescription = \Illuminate\Support\Str::limit(strip_tags($course->description ?? ''), 160);
    $bodyClass = 'lasles-course-detail-page';
    $laslesNavActive = 'courses';

    $primaryCtaHref = null;
    $primaryCtaLabel = __('landing.learning_paths.cta_start');
    $primaryIsForm = false;
    if (auth()->check()) {
        if ($isEnrolled ?? false) {
            $primaryCtaHref = route('my-courses.show', $course);
            $primaryCtaLabel = __('public.start_learning_now');
        } elseif ($isPaid) {
            $primaryCtaHref = route('public.course.checkout', $course->id);
            $primaryCtaLabel = __('public.buy_now');
        } else {
            $primaryIsForm = true;
            $primaryCtaLabel = __('public.register_free');
        }
    } else {
        $redirect = $isPaid
            ? route('public.course.checkout', $course->id)
            : route('public.course.show', $course->id);
        $primaryCtaHref = route('register', ['redirect' => $redirect]);
        $primaryCtaLabel = $isPaid ? __('public.buy_now') : __('public.register_free');
    }
@endphp

@section('content')
@if (session('success') || session('info') || session('error'))
  <div class="lasles-container lasles-course-flash-wrap">
    @if (session('success'))
      <div class="lasles-course-flash lasles-course-flash--ok" data-flash>
        <p>{{ session('success') }}</p>
        <button type="button" data-flash-close aria-label="{{ $isRtl ? 'إغلاق' : 'Close' }}">×</button>
      </div>
    @endif
    @if (session('info'))
      <div class="lasles-course-flash lasles-course-flash--info" data-flash>
        <p>{{ session('info') }}</p>
        <button type="button" data-flash-close aria-label="{{ $isRtl ? 'إغلاق' : 'Close' }}">×</button>
      </div>
    @endif
    @if (session('error'))
      <div class="lasles-course-flash lasles-course-flash--err" data-flash>
        <p>{{ session('error') }}</p>
        <button type="button" data-flash-close aria-label="{{ $isRtl ? 'إغلاق' : 'Close' }}">×</button>
      </div>
    @endif
  </div>
@endif

<section class="lasles-course-detail-hero" aria-labelledby="course-detail-title">
  <div class="lasles-course-detail-hero__bg" style="--course-cover: url('{{ $heroCover }}')" aria-hidden="true"></div>
  <div class="lasles-course-detail-hero__veil" aria-hidden="true"></div>
  <div class="lasles-container lasles-course-detail-hero__inner">
    <p class="lasles-course-detail-hero__crumb">
      <a href="{{ route('home') }}">{{ __('site.nav_short.home') }}</a>
      <span aria-hidden="true">/</span>
      <a href="{{ $catalogUrl }}">{{ __('landing.course_detail.crumb_courses') }}</a>
      <span aria-hidden="true">/</span>
      <span>{{ \Illuminate\Support\Str::limit($course->title ?? '', 48) }}</span>
    </p>

    <div class="lasles-course-detail-hero__copy">
      <p class="lasles-course-detail-hero__kicker">{{ __('landing.course_detail.kicker') }}</p>
      <h1 id="course-detail-title" class="lasles-course-detail-hero__title">{{ $course->title ?? __('public.course_title_fallback') }}</h1>
      @if ($course->description)
        <p class="lasles-course-detail-hero__lead">{{ \Illuminate\Support\Str::limit(strip_tags($course->description), 180) }}</p>
      @endif

      <div class="lasles-course-detail-hero__chips">
        <span class="lasles-path-chip {{ $isOneToOne ? 'lasles-path-chip--gold' : '' }}">{{ $deliveryLabel }}</span>
        @if ($course->is_featured ?? false)
          <span class="lasles-path-chip lasles-path-chip--gold">{{ __('public.featured_course_badge') }}</span>
        @endif
        @if (($course->duration_hours ?? 0) > 0)
          <span class="lasles-path-chip">{{ $course->duration_hours }} {{ __('public.hours') }}</span>
        @endif
        @if (($course->lessons_count ?? 0) > 0)
          <span class="lasles-path-chip">{{ $course->lessons_count }} {{ __('landing.learning_paths.stat_lessons') }}</span>
        @endif
        @if ($discountPct > 0)
          <span class="lasles-path-chip lasles-path-chip--blue">{{ $isRtl ? "خصم {$discountPct}%" : "{$discountPct}% off" }}</span>
        @elseif (! $isPaid)
          <span class="lasles-path-chip lasles-path-chip--blue">{{ __('public.free_price') }}</span>
        @endif
      </div>

      <p class="lasles-course-detail-hero__promise">{{ __('landing.course_detail.promise') }}</p>

      <div class="lasles-course-detail-hero__actions">
        @if ($primaryIsForm)
          <form action="{{ route('public.course.enroll.free', $course->id) }}" method="POST">
            @csrf
            <button type="submit" class="lasles-btn-primary">{{ $primaryCtaLabel }}</button>
          </form>
        @else
          <a href="{{ $primaryCtaHref }}" class="lasles-btn-primary">{{ $primaryCtaLabel }}</a>
        @endif
        <a href="{{ $catalogUrl }}" class="lasles-btn-outline lasles-btn-outline--on-dark">{{ __('landing.course_detail.cta_explore') }}</a>
      </div>
    </div>
  </div>
</section>

<section class="lasles-course-detail-body">
  <div class="lasles-container lasles-course-detail-body__grid">
    <div class="lasles-course-detail-body__main">
      @if ($introEmbedUrl || $introDirectVideo || $thumbUrl)
        <div class="lasles-course-detail-media">
          <p class="lasles-path-kicker">{{ __('landing.course_detail.media_title') }}</p>
          <div class="lasles-course-detail-media__frame">
            @if ($introEmbedUrl)
              <iframe src="{{ $introEmbedUrl }}" title="{{ __('public.course_intro_video') }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen; web-share" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
            @elseif ($introDirectVideo)
              <video src="{{ $introDirectVideo }}" controls playsinline preload="metadata" poster="{{ $thumbUrl }}">{{ __('public.course_intro_video_unsupported') }}</video>
            @else
              <img src="{{ $thumbUrl }}" alt="{{ $course->title }}" width="960" height="540" loading="lazy" decoding="async">
            @endif
          </div>
        </div>
      @endif

      <div class="lasles-course-detail-about">
        <p class="lasles-path-kicker">{{ __('landing.course_detail.about_kicker') }}</p>
        <h2 class="lasles-course-detail-about__title">{{ __('landing.course_detail.about_title') }}</h2>
        <p class="lasles-course-detail-about__text">{{ $course->description ?? __('public.course_desc_fallback') }}</p>

        <div class="lasles-course-detail-delivery {{ $isOneToOne ? 'is-solo' : '' }}">
          <strong>{{ $isOneToOne ? __('landing.groups_page.solo_label') : __('landing.groups_page.group_label') }}</strong>
          <p>
            {{ $isOneToOne ? __('landing.course_detail.solo_blurb') : __('landing.course_detail.group_blurb') }}
            @if ($course->instructor)
              {{ __('landing.course_detail.tutor_label') }}: <b>{{ $course->instructor->name }}</b>.
            @endif
          </p>
        </div>
      </div>

      @if ($course->objectives)
        <div class="lasles-course-detail-block">
          <h2>{{ __('landing.course_detail.objectives_title') }}</h2>
          <div class="lasles-course-detail-block__box">{{ $course->objectives }}</div>
        </div>
      @endif

      @if (count($learnPoints))
        <div class="lasles-course-detail-block">
          <h2>{{ __('landing.course_detail.learn_title') }}</h2>
          <ul class="lasles-course-detail-learn">
            @foreach ($learnPoints as $point)
              <li><span aria-hidden="true"></span>{{ $point }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @if ($course->requirements)
        <div class="lasles-course-detail-block">
          <h2>{{ __('landing.course_detail.requirements_title') }}</h2>
          <div class="lasles-course-detail-block__box">{{ $course->requirements }}</div>
        </div>
      @endif

      <div class="lasles-course-detail-block">
        <h2>{{ __('landing.course_detail.details_title') }}</h2>
        <dl class="lasles-course-detail-facts">
          <div><dt>{{ __('landing.course_detail.delivery') }}</dt><dd>{{ $deliveryLabel }}</dd></div>
          <div><dt>{{ __('public.course_category_label') }}</dt><dd>{{ $categoryDisplay }}</dd></div>
          <div><dt>{{ __('public.subject_label') }}</dt><dd>{{ $subjectName }}</dd></div>
          <div><dt>{{ __('public.duration') }}</dt><dd>{{ $course->duration_hours ?? 0 }} {{ __('public.hours') }}</dd></div>
          <div><dt>{{ __('public.lectures_count_label') }}</dt><dd>{{ $course->lessons_count ?? 0 }}</dd></div>
          @if ($course->instructor)
            <div>
              <dt>{{ __('public.instructor_label') }}</dt>
              <dd>
                @if ($instructorApproved)
                  <a href="{{ route('public.instructors.show', $course->instructor) }}">{{ $course->instructor->name }}</a>
                @else
                  {{ $course->instructor->name }}
                @endif
              </dd>
            </div>
          @endif
          <div>
            <dt>{{ __('landing.course_detail.billing') }}</dt>
            <dd>
              @if (! $isPaid)
                {{ __('public.free_price') }}
              @elseif ($isMonthly)
                {{ __('public.checkout_monthly_price_label') }}
              @else
                {{ __('public.checkout_benefit_lifetime') }}
              @endif
            </dd>
          </div>
        </dl>
      </div>
    </div>

    <aside class="lasles-course-detail-side">
      <div class="lasles-course-detail-side__card">
        <p class="lasles-course-detail-side__kicker">{{ __('landing.course_detail.side_kicker') }}</p>
        <h2>{{ __('landing.course_detail.side_title') }}</h2>

        @if ($course->instructor)
          <p class="lasles-course-detail-side__tutor">
            @if ($instructorApproved)
              <a href="{{ route('public.instructors.show', $course->instructor) }}">{{ $course->instructor->name }}</a>
            @else
              {{ $course->instructor->name }}
            @endif
          </p>
        @endif

        <div class="lasles-course-detail-side__price">
          @if ($isPaid)
            <p class="lasles-course-detail-side__amount">
              {{ number_format($checkoutPrice, 0) }}
              <small>{{ __('public.currency_egp') }}@if ($isMonthly) / {{ __('public.per_month') }}@endif</small>
            </p>
            @if ($hasPromo)
              <p class="lasles-course-detail-side__old">{{ number_format($listPrice, 0) }} {{ __('public.currency_egp') }}</p>
              @if ($savedAmount > 0)
                <span class="lasles-course-detail-side__save">{{ __('landing.course_detail.save_amount', ['amount' => number_format($savedAmount, 0).' '.__('public.currency_egp')]) }}</span>
              @endif
            @endif
          @else
            <p class="lasles-course-detail-side__amount is-free">{{ __('public.free_price') }}</p>
          @endif
          <p class="lasles-course-detail-side__access">{{ __('landing.course_detail.access_note') }}</p>
        </div>

        <dl class="lasles-course-detail-side__specs">
          <div><dt>{{ __('landing.course_detail.delivery') }}</dt><dd>{{ $deliveryLabel }}</dd></div>
          <div><dt>{{ __('public.duration') }}</dt><dd>{{ $course->duration_hours ?? 0 }} {{ __('public.hours') }}</dd></div>
          <div><dt>{{ __('public.lectures_count_label') }}</dt><dd>{{ $course->lessons_count ?? 0 }}</dd></div>
          <div><dt>{{ __('public.course_category_label') }}</dt><dd>{{ $categoryDisplay }}</dd></div>
        </dl>

        <div class="lasles-course-detail-side__actions">
          @if ($primaryIsForm)
            <form action="{{ route('public.course.enroll.free', $course->id) }}" method="POST">
              @csrf
              <button type="submit" class="lasles-btn-primary lasles-course-detail-side__cta">{{ $primaryCtaLabel }}</button>
            </form>
          @else
            <a href="{{ $primaryCtaHref }}" class="lasles-btn-primary lasles-course-detail-side__cta">{{ $primaryCtaLabel }}</a>
          @endif
          <a href="{{ $waUrl }}" class="lasles-course-detail-side__wa" target="_blank" rel="noopener">{{ __('landing.course_detail.whatsapp') }}</a>
          <a href="{{ $catalogUrl }}" class="lasles-course-detail-side__link">{{ __('landing.course_detail.cta_explore') }}</a>
        </div>

        <div class="lasles-course-detail-trust">
          <div><strong>{{ __('landing.course_detail.trust_secure') }}</strong></div>
          <div><strong>{{ __('landing.course_detail.trust_fast') }}</strong></div>
          <div><strong>{{ __('landing.course_detail.trust_cert') }}</strong></div>
        </div>
      </div>
    </aside>
  </div>
</section>

@if (isset($relatedCourses) && $relatedCourses->isNotEmpty())
<section class="lasles-course-detail-related" aria-labelledby="related-courses-title">
  <div class="lasles-container">
    <div class="lasles-showcase__head">
      <div class="lasles-showcase__intro">
        <p class="lasles-path-kicker">{{ __('landing.course_detail.related_kicker') }}</p>
        <h2 id="related-courses-title" class="lasles-section-title">{{ __('landing.course_detail.related_title') }}</h2>
      </div>
      <a href="{{ $catalogUrl }}" class="lasles-showcase__more lasles-btn-outline">{{ __('public.all_courses') }}</a>
    </div>
    <div class="lasles-showcase__grid lasles-showcase__grid--paths">
      @foreach ($relatedCourses->take(3) as $i => $related)
        @php
          $rThumb = $related->thumbnail_url;
        @endphp
        <a href="{{ route('public.course.show', $related->id) }}" class="lasles-path-card-pro">
          <div class="lasles-path-card-pro__media {{ $rThumb ? '' : 'lasles-path-card-pro__media--accent' }}">
            @if ($rThumb)
              <img src="{{ $rThumb }}" alt="" loading="lazy" decoding="async">
              <span class="lasles-path-card-pro__shade" aria-hidden="true"></span>
            @endif
            <span class="lasles-path-card-pro__index {{ $rThumb ? 'lasles-path-card-pro__index--on-media' : '' }}">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
          </div>
          <div class="lasles-path-card-pro__body">
            <h3 class="lasles-path-card-pro__title">{{ $related->title }}</h3>
            <p class="lasles-path-card-pro__summary">{{ $related->instructor->name ?? __('landing.course_detail.tutor_label') }}</p>
            <span class="lasles-path-card-pro__cta">{{ __('public.course_detail_title') }} <span aria-hidden="true">→</span></span>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<section class="lasles-path-detail-cta">
  <div class="lasles-container lasles-path-detail-cta__inner">
    <div>
      <h2 class="lasles-section-title">{{ __('landing.course_detail.cta_title') }}</h2>
      <p class="lasles-section-lead">{{ __('landing.course_detail.cta_lead') }}</p>
    </div>
    <div class="lasles-path-detail-cta__actions">
      @if ($primaryIsForm)
        <form action="{{ route('public.course.enroll.free', $course->id) }}" method="POST">
          @csrf
          <button type="submit" class="lasles-btn-primary">{{ $primaryCtaLabel }}</button>
        </form>
      @else
        <a href="{{ $primaryCtaHref }}" class="lasles-btn-primary">{{ $primaryCtaLabel }}</a>
      @endif
      <a href="{{ route('public.learning-paths.index') }}" class="lasles-btn-outline">{{ __('landing.course_detail.cta_explore') }}</a>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-flash-close]').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var wrap = btn.closest('[data-flash]');
    if (wrap) wrap.remove();
  });
});
</script>
@endpush
