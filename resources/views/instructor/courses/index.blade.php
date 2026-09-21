@extends('layouts.instructor-timeline')

@section('title', __('instructor.my_courses') . ' - ' . config('app.name'))
@section('page_title', __('instructor.my_courses'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
    $currency = platform_currency();
    $tones = ['blue', 'pink', 'orange', 'purple'];
@endphp

<section class="st-join-hero" aria-label="{{ __('instructor.my_courses') }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">TADRIS LAB</p>
        <h2 class="st-join-hero__title">{{ __('instructor.my_courses') }}</h2>
        <p class="st-join-hero__meta">{{ __('instructor.courses_assigned_to_you') }}</p>
    </div>
    <div class="st-join-hero__actions">
        @if(Route::has('instructor.lectures.index'))
            <a href="{{ route('instructor.lectures.index') }}" class="st-pill st-pill--outline">{{ __('instructor.lectures') }}</a>
        @endif
        @if(Route::has('instructor.calendar'))
            <a href="{{ route('instructor.calendar') }}" class="st-pill st-pill--solid">{{ __('instructor.my_calendar') }}</a>
        @endif
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="{{ __('instructor.statistics') }}">
    <article class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.total_courses') }}</p>
        <p class="st-stat-card__value">{{ number_format($stats['total'] ?? 0) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'مسندة إليك' : 'Assigned to you' }}</p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.active') }}</p>
        <p class="st-stat-card__value">{{ number_format($stats['active'] ?? 0) }}</p>
        <p class="st-stat-card__hint">{{ __('instructor.active_status') }}</p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.inactive') }}</p>
        <p class="st-stat-card__value">{{ number_format($stats['inactive'] ?? 0) }}</p>
        <p class="st-stat-card__hint">{{ __('instructor.inactive_status') }}</p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.total_students') }}</p>
        <p class="st-stat-card__value">{{ number_format($stats['total_students'] ?? 0) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'تسجيلات نشطة' : 'Active enrollments' }}</p>
    </article>
</section>

<section class="st-panel st-course-filters">
    <form method="GET" class="st-course-filters__form">
        <div class="st-course-filters__field">
            <label for="search">{{ __('common.search') }}</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                   placeholder="{{ __('instructor.search_in_course_titles') }}">
        </div>
        <div class="st-course-filters__field">
            <label for="status">{{ __('common.status') }}</label>
            <select name="status" id="status">
                <option value="">{{ __('instructor.all_statuses') }}</option>
                <option value="active" @selected(request('status') === 'active')>{{ __('instructor.active_status') }}</option>
                <option value="inactive" @selected(request('status') === 'inactive')>{{ __('instructor.inactive_status') }}</option>
            </select>
        </div>
        <div class="st-course-filters__actions">
            <button type="submit" class="st-pill st-pill--solid">{{ __('common.search') }}</button>
            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('instructor.courses.index') }}" class="st-pill st-pill--outline">{{ __('common.reset') ?? 'Reset' }}</a>
            @endif
        </div>
    </form>
</section>

@if($courses->count() > 0)
    <section class="st-lp-grid" aria-label="{{ __('instructor.my_courses') }}">
        @foreach($courses as $i => $course)
            @php $tone = $tones[$i % count($tones)]; @endphp
            <article class="st-lp-card st-lp-card--{{ $tone }}">
                <img class="st-lp-card__blob" src="{{ $i % 2 ? $subjMask2 : $subjMask1 }}" alt="" width="120" height="120">
                <div class="st-lp-card__top">
                    <span class="st-lp-card__index">{{ str_pad((string) ($i + 1 + (($courses->currentPage() - 1) * $courses->perPage())), 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="st-course-row__chip {{ $course->is_active ? 'is-ok' : 'is-off' }}">
                        {{ $course->is_active ? __('instructor.active_status') : __('instructor.inactive_status') }}
                    </span>
                </div>
                <h3 class="st-lp-card__title">{{ $course->title }}</h3>
                @if($course->description)
                    <p class="st-lp-card__focus">{{ Str::limit($course->description, 110) }}</p>
                @endif
                <ul class="st-lp-card__meta">
                    <li><i class="fas fa-chalkboard-teacher" aria-hidden="true"></i> {{ $course->lectures_count ?? 0 }} {{ __('instructor.lecture_single') }}</li>
                    <li><i class="fas fa-user-graduate" aria-hidden="true"></i> {{ $course->enrollments_count ?? 0 }} {{ __('instructor.student_single') }}</li>
                    <li>
                        <i class="fas fa-tag" aria-hidden="true"></i>
                        @if(!$course->is_free && $course->effectivePurchasePrice() > 0)
                            {{ number_format($course->effectivePurchasePrice(), 2) }} {{ $currency }}
                        @else
                            {{ __('instructor.free') }}
                        @endif
                    </li>
                    @if($course->level)
                        <li>
                            <i class="fas fa-signal" aria-hidden="true"></i>
                            @if($course->level == 'beginner') {{ __('instructor.beginner') }}
                            @elseif($course->level == 'intermediate') {{ __('instructor.intermediate') }}
                            @else {{ __('instructor.advanced') }}
                            @endif
                        </li>
                    @endif
                </ul>
                <div class="st-lp-card__foot">
                    <a href="{{ route('instructor.courses.show', $course) }}" class="st-lp-card__cta">
                        {{ __('instructor.view_details') }}
                        <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                    </a>
                    @if(Route::has('instructor.courses.curriculum'))
                        <a href="{{ route('instructor.courses.curriculum', $course) }}" class="st-lp-card__cta st-lp-card__cta--muted">
                            {{ __('instructor.build_curriculum') }}
                        </a>
                    @endif
                </div>
            </article>
        @endforeach
    </section>
    <div class="st-cons-pager">{{ $courses->links() }}</div>
@else
    <section class="st-panel st-lp-empty">
        <i class="fas fa-book-open" aria-hidden="true"></i>
        <h3>{{ __('instructor.no_courses') }}</h3>
        <p>{{ __('instructor.courses_description_empty') }}</p>
    </section>
@endif
@endsection
