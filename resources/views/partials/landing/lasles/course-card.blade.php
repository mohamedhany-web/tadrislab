@php
  $isRtl = app()->getLocale() === 'ar';
  $thumb = $course->thumbnail_url
    ?: asset('img/lasles/features-illustration.png');
  $list = (float) ($course->price ?? 0);
  $pay = (float) ($course->price_after_discount ?? $list);
  if ($pay <= 0) {
      $pay = $list;
  }
  $isFree = ($course->is_free ?? false) || $pay <= 0;
  $hasDiscount = ! $isFree && $list > $pay && $pay > 0;
  $categoryLabel = $course->courseCategory->name
    ?? $course->academicSubject->name
    ?? $course->category
    ?? null;
  $excerpt = \Illuminate\Support\Str::limit(strip_tags((string) ($course->description ?? '')), 110);
  $instructor = $course->instructor->name ?? null;
  $lessonsCount = (int) ($course->lessons_count ?? 0);
@endphp
<a href="{{ route('public.course.show', $course->id) }}" class="lasles-course-card">
  <div class="lasles-course-card__media">
    <img src="{{ $thumb }}" alt="" loading="lazy" decoding="async">
    @if($course->is_featured)
      <span class="lasles-course-card__badge lasles-course-card__badge--gold">{{ __('public.courses_badge_featured') }}</span>
    @elseif($isFree)
      <span class="lasles-course-card__badge">{{ __('public.courses_badge_free') }}</span>
    @endif
  </div>
  <div class="lasles-course-card__body">
    @if($categoryLabel)
      <p class="lasles-course-card__cat">{{ $categoryLabel }}</p>
    @endif
    <h3 class="lasles-course-card__title">{{ $course->title }}</h3>
    @if($excerpt !== '')
      <p class="lasles-course-card__excerpt">{{ $excerpt }}</p>
    @endif
    <div class="lasles-course-card__meta">
      @if($course->level)
        <span>{{ $course->level }}</span>
      @endif
      @if($lessonsCount > 0)
        <span>{{ trans_choice('public.courses_lessons_count', $lessonsCount, ['count' => $lessonsCount]) }}</span>
      @endif
      @if($instructor)
        <span>{{ $instructor }}</span>
      @endif
    </div>
    <div class="lasles-course-card__foot">
      <p class="lasles-course-card__price">
        @if($isFree)
          {{ __('public.courses_badge_free') }}
        @else
          @if($hasDiscount)<s>{{ number_format($list, 0) }}</s>@endif
          {{ number_format($pay, 0) }} {{ $isRtl ? 'ر.ق' : 'QAR' }}
        @endif
      </p>
      <span class="lasles-course-card__cta">{{ __('public.courses_card_cta') }} →</span>
    </div>
  </div>
</a>
