@extends('layouts.instructor-timeline')

@section('title', 'مساراتي المسندة')
@section('page_title', 'مساراتي المسندة')

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
    $tones = ['blue', 'pink', 'orange', 'purple'];
    $pathCount = $paths->count();
    $unitsTotal = (int) $paths->sum('units_count');
    $lessonsTotal = (int) $paths->sum('lessons_count');
    $enrollTotal = (int) $paths->sum('enrollments_count');
@endphp

<section class="st-join-hero" aria-label="مساراتي المسندة">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">TADRIS LAB</p>
        <h2 class="st-join-hero__title">{{ $isRtl ? 'مساراتي المسندة' : 'Assigned learning paths' }}</h2>
        <p class="st-join-hero__meta">
            {{ $isRtl
                ? 'المسارات المسموح لك بتقديمها للمعلمين — مسار → وحدات → دروس → تطبيقات.'
                : 'Paths you can coach teachers through — path → units → lessons → practices.' }}
        </p>
    </div>
    <div class="st-join-hero__actions">
        @if(Route::has('instructor.calendar'))
            <a href="{{ route('instructor.calendar') }}" class="st-pill st-pill--outline">{{ __('instructor.my_calendar') }}</a>
        @endif
        @if(Route::has('instructor.consultations.index') && instructor_ui('show_consultations', true))
            <a href="{{ route('instructor.consultations.index') }}" class="st-pill st-pill--solid">استشاراتي</a>
        @endif
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="{{ $isRtl ? 'ملخص المسارات' : 'Paths summary' }}">
    <article class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'المسارات' : 'Paths' }}</p>
        <p class="st-stat-card__value">{{ number_format($pathCount) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'مسندة إليك' : 'Assigned to you' }}</p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'الوحدات' : 'Units' }}</p>
        <p class="st-stat-card__value">{{ number_format($unitsTotal) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'عبر كل المسارات' : 'Across all paths' }}</p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'الدروس' : 'Lessons' }}</p>
        <p class="st-stat-card__value">{{ number_format($lessonsTotal) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'محتوى تدريبي' : 'Training content' }}</p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'المعلمون' : 'Teachers' }}</p>
        <p class="st-stat-card__value">{{ number_format($enrollTotal) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'تسجيلات نشطة' : 'Active enrollments' }}</p>
    </article>
</section>

<section class="st-msg-intro">
    <div>
        <h2>{{ $isRtl ? 'كتالوج المسارات' : 'Path catalog' }}</h2>
        <p>{{ $isRtl ? 'افتح مسارًا لمراجعة الوحدات والدروس والمعلمين المسجّلين.' : 'Open a path to review units, lessons, and enrolled teachers.' }}</p>
    </div>
</section>

@if($paths->isEmpty())
    <section class="st-panel st-lp-empty">
        <i class="fas fa-route" aria-hidden="true"></i>
        <h3>{{ $isRtl ? 'لا مسارات ممنوحة بعد' : 'No paths assigned yet' }}</h3>
        <p>{{ $isRtl
            ? 'اطلب من الإدارة تفعيل خدمة «المسارات التعليمية» وربط المسارات بحسابك.'
            : 'Ask admin to enable learning paths and assign them to your account.' }}</p>
    </section>
@else
    <section class="st-lp-grid" aria-label="{{ $isRtl ? 'المسارات' : 'Paths' }}">
        @foreach($paths as $i => $path)
            @php $tone = $tones[$i % count($tones)]; @endphp
            <a href="{{ route('instructor.learning-paths.show', $path) }}" class="st-lp-card st-lp-card--{{ $tone }}">
                <img class="st-lp-card__blob" src="{{ $i % 2 ? $subjMask2 : $subjMask1 }}" alt="" width="120" height="120">
                <span class="st-lp-card__index">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="st-lp-card__title">{{ $path->title() }}</h3>
                <p class="st-lp-card__focus">{{ $path->skillFocus() ?: '—' }}</p>
                <ul class="st-lp-card__meta">
                    <li><i class="fas fa-layer-group" aria-hidden="true"></i> {{ $path->units_count }} {{ $isRtl ? 'وحدة' : 'units' }}</li>
                    <li><i class="fas fa-book-open" aria-hidden="true"></i> {{ $path->lessons_count }} {{ $isRtl ? 'درس' : 'lessons' }}</li>
                    <li><i class="fas fa-screwdriver-wrench" aria-hidden="true"></i> {{ $path->practices_count }} {{ $isRtl ? 'تطبيق' : 'practices' }}</li>
                    <li><i class="fas fa-user-graduate" aria-hidden="true"></i> {{ $path->enrollments_count }} {{ $isRtl ? 'معلّم' : 'teachers' }}</li>
                </ul>
                <span class="st-lp-card__cta">
                    {{ $isRtl ? 'عرض المسار' : 'View path' }}
                    <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                </span>
            </a>
        @endforeach
    </section>
@endif
@endsection
