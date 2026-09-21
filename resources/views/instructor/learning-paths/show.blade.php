@extends('layouts.instructor-timeline')

@section('title', $path->title())
@section('page_title', $path->title())

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
    $unitsCount = $path->units->count();
    $lessonsCount = $path->units->sum(fn ($u) => $u->lessons->count());
    $practicesCount = $path->units->sum(fn ($u) => $u->practices->count());
    $enrollCount = $path->enrollments->count();
    $tones = ['blue', 'pink', 'orange', 'purple'];
@endphp

<section class="st-join-hero" aria-label="{{ $path->title() }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">{{ $path->skillFocus() ?: 'TADRIS LAB' }}</p>
        <h2 class="st-join-hero__title">{{ $path->title() }}</h2>
        <p class="st-join-hero__meta">
            {{ $path->summary() ?: ($isRtl ? 'تفاصيل المسار والوحدات والمعلمين المسجّلين.' : 'Path details, units, and enrolled teachers.') }}
        </p>
    </div>
    <div class="st-join-hero__actions">
        <a href="{{ route('instructor.learning-paths.index') }}" class="st-pill st-pill--outline">
            {{ $isRtl ? 'كل المسارات' : 'All paths' }}
        </a>
        @if(Route::has('public.learning-paths.show') && $path->slug)
            <a href="{{ route('public.learning-paths.show', $path->slug) }}" class="st-pill st-pill--solid" target="_blank" rel="noopener">
                {{ $isRtl ? 'الصفحة العامة' : 'Public page' }}
            </a>
        @endif
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="{{ $isRtl ? 'ملخص المسار' : 'Path summary' }}">
    <article class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'الوحدات' : 'Units' }}</p>
        <p class="st-stat-card__value">{{ number_format($unitsCount) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'هيكل المسار' : 'Path structure' }}</p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'الدروس' : 'Lessons' }}</p>
        <p class="st-stat-card__value">{{ number_format($lessonsCount) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'محتوى نظري' : 'Theory content' }}</p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'التطبيقات' : 'Practices' }}</p>
        <p class="st-stat-card__value">{{ number_format($practicesCount) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'أدوات وأنشطة' : 'Tools & activities' }}</p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'المعلمون' : 'Teachers' }}</p>
        <p class="st-stat-card__value">{{ number_format($enrollCount) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'عبر الباقات' : 'Via packages' }}</p>
    </article>
</section>

@if($path->description())
    <section class="st-panel st-lp-desc">
        <div class="st-section-head">
            <h2>{{ $isRtl ? 'عن المسار' : 'About this path' }}</h2>
        </div>
        <p class="st-lp-desc__body">{{ $path->description() }}</p>
    </section>
@endif

<section class="st-msg-intro">
    <div>
        <h2>{{ $isRtl ? 'وحدات المسار' : 'Path units' }}</h2>
        <p>{{ $isRtl ? 'كل وحدة تحتوي دروسًا وتطبيقات عملية.' : 'Each unit holds lessons and practical applications.' }}</p>
    </div>
</section>

@forelse($path->units as $ui => $unit)
    @php $tone = $tones[$ui % count($tones)]; @endphp
    <article class="st-panel st-lp-unit st-lp-unit--{{ $tone }}">
        <header class="st-lp-unit__head">
            <span class="st-lp-unit__badge">{{ $isRtl ? 'وحدة' : 'Unit' }} {{ $ui + 1 }}</span>
            <h3 class="st-lp-unit__title">{{ $unit->title() }}</h3>
            @if($unit->summary())
                <p class="st-lp-unit__summary">{{ $unit->summary() }}</p>
            @endif
        </header>
        <div class="st-lp-unit__cols">
            <div class="st-lp-unit__col">
                <h4>
                    <i class="fas fa-book-open" aria-hidden="true"></i>
                    {{ $isRtl ? 'دروس' : 'Lessons' }}
                    <em>{{ $unit->lessons->count() }}</em>
                </h4>
                <ul>
                    @forelse($unit->lessons as $lesson)
                        <li>
                            <i class="fas fa-circle-play" aria-hidden="true"></i>
                            <span>{{ $lesson->title() }}</span>
                        </li>
                    @empty
                        <li class="is-empty">{{ $isRtl ? 'لا دروس بعد' : 'No lessons yet' }}</li>
                    @endforelse
                </ul>
            </div>
            <div class="st-lp-unit__col">
                <h4>
                    <i class="fas fa-screwdriver-wrench" aria-hidden="true"></i>
                    {{ $isRtl ? 'تطبيقات' : 'Practices' }}
                    <em>{{ $unit->practices->count() }}</em>
                </h4>
                <ul>
                    @forelse($unit->practices as $practice)
                        <li>
                            <i class="fas fa-check" aria-hidden="true"></i>
                            <span>{{ $practice->title() }}</span>
                        </li>
                    @empty
                        <li class="is-empty">{{ $isRtl ? 'لا تطبيقات بعد' : 'No practices yet' }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </article>
@empty
    <section class="st-panel st-lp-empty">
        <i class="fas fa-folder-open" aria-hidden="true"></i>
        <h3>{{ $isRtl ? 'لا وحدات بعد' : 'No units yet' }}</h3>
        <p>{{ $isRtl ? 'سيظهر هيكل المسار هنا بعد إضافته من الإدارة.' : 'Path structure will appear here once admin adds units.' }}</p>
    </section>
@endforelse

<section class="st-panel st-lp-enroll">
    <div class="st-section-head">
        <h2>{{ $isRtl ? 'معلمون مسجّلون' : 'Enrolled teachers' }}</h2>
        <p>{{ $isRtl ? 'تسجيلات عبر الباقات على هذا المسار' : 'Package enrollments on this path' }}</p>
    </div>
    @if($path->enrollments->isEmpty())
        <div class="st-lp-empty st-lp-empty--compact">
            <i class="fas fa-user-slash" aria-hidden="true"></i>
            <p>{{ $isRtl ? 'لا تسجيلات بعد.' : 'No enrollments yet.' }}</p>
        </div>
    @else
        <ul class="st-lp-enroll__list">
            @foreach($path->enrollments as $enr)
                <li class="st-lp-enroll__item">
                    <span class="st-lp-enroll__avatar" aria-hidden="true">{{ mb_substr($enr->user?->name ?? 'م', 0, 1) }}</span>
                    <span class="st-lp-enroll__body">
                        <strong>{{ $enr->user?->name ?? '—' }}</strong>
                        <em>{{ $enr->user?->email }}</em>
                    </span>
                    <time class="st-lp-enroll__date">{{ optional($enr->enrolled_at)->translatedFormat($isRtl ? 'j M Y' : 'M j, Y') ?? '—' }}</time>
                </li>
            @endforeach
        </ul>
    @endif
</section>
@endsection
