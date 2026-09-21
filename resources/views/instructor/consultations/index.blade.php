@extends('layouts.instructor-timeline')

@section('title', __('instructor.cons_title'))
@section('page_title', __('instructor.cons_title'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
    $total = method_exists($requests, 'total') ? (int) $requests->total() : $requests->count();
    $upcomingCount = isset($upcoming) ? $upcoming->count() : 0;
@endphp

<section class="st-join-hero" aria-label="{{ __('instructor.cons_title') }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">TADRIS LAB</p>
        <h2 class="st-join-hero__title">{{ __('instructor.cons_title') }}</h2>
        <p class="st-join-hero__meta">{{ __('instructor.cons_subtitle') }}</p>
    </div>
    <div class="st-join-hero__actions">
        @if(Route::has('instructor.learning-paths.index'))
            <a href="{{ route('instructor.learning-paths.index') }}" class="st-pill st-pill--outline">مساراتي</a>
        @endif
        @if(Route::has('instructor.calendar'))
            <a href="{{ route('instructor.calendar') }}" class="st-pill st-pill--solid">{{ __('instructor.cons_my_calendar') }}</a>
        @endif
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="{{ __('instructor.cons_title') }}">
    <article class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'كل الطلبات' : 'All requests' }}</p>
        <p class="st-stat-card__value">{{ number_format($total) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'مسندة إليك' : 'Assigned to you' }}</p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.upcoming') }}</p>
        <p class="st-stat-card__value">{{ number_format($upcomingCount) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'جلسات قادمة' : 'Upcoming sessions' }}</p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'هذه الصفحة' : 'This page' }}</p>
        <p class="st-stat-card__value">{{ number_format($requests->count()) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'في القائمة الحالية' : 'In current list' }}</p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'التقويم' : 'Calendar' }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ $isRtl ? 'متزامن' : 'Synced' }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'مع مواعيدك' : 'With your slots' }}</p>
    </article>
</section>

@if(isset($upcoming) && $upcoming->isNotEmpty())
    <section class="st-panel st-cons-upcoming">
        <div class="st-section-head">
            <h2>{{ $isRtl ? 'الجلسات القادمة' : 'Upcoming sessions' }}</h2>
            <p>{{ $isRtl ? 'أقرب مواعيد الاستشارة' : 'Nearest consultation slots' }}</p>
        </div>
        <ul class="st-cons-upcoming__list">
            @foreach($upcoming as $u)
                <li>
                    <a href="{{ route('instructor.consultations.show', $u) }}" class="st-cons-upcoming__item">
                        <span class="st-cons-upcoming__ico"><i class="fas fa-comments" aria-hidden="true"></i></span>
                        <span class="st-cons-upcoming__body">
                            <strong>{{ $u->student->name ?? '—' }}</strong>
                            <em>{{ $u->service?->title() ?? $u->consultation_type }}</em>
                        </span>
                        <time>
                            <x-app-datetime :at="$u->preferred_slot_at" pattern="D j M · g:i A" />
                        </time>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
@endif

<section class="st-msg-intro">
    <div>
        <h2>{{ $isRtl ? 'كل الاستشارات' : 'All consultations' }}</h2>
        <p>{{ $isRtl ? 'راجع الحالة والموعد وافتح التفاصيل.' : 'Review status, schedule, and open details.' }}</p>
    </div>
</section>

<section class="st-panel st-cons-table">
    @forelse($requests as $r)
        <a href="{{ route('instructor.consultations.show', $r) }}" class="st-cons-row">
            <span class="st-cons-row__avatar" aria-hidden="true">{{ mb_substr($r->student->name ?? 'م', 0, 1) }}</span>
            <span class="st-cons-row__main">
                <strong>{{ $r->student->name ?? '—' }}</strong>
                <em>{{ $r->service?->title() ?? ($r->consultation_type ?: '—') }}</em>
            </span>
            <span class="st-cons-row__meta">
                <span class="st-cons-row__chip">{{ $r->statusLabel() }}</span>
                <span class="st-cons-row__amt">{{ number_format($r->price_amount, 2) }} $</span>
                <span class="st-cons-row__when">
                    @if($r->scheduled_at)
                        <x-app-datetime :at="$r->scheduled_at" pattern="D j M · g:i A" />
                    @else
                        —
                    @endif
                </span>
            </span>
            <span class="st-cons-row__cta">{{ __('instructor.cons_details') }}</span>
        </a>
    @empty
        <div class="st-lp-empty">
            <i class="fas fa-comments" aria-hidden="true"></i>
            <h3>{{ __('instructor.cons_empty') }}</h3>
            <p>{{ $isRtl ? 'ستظهر الطلبات هنا عند حجز المعلمون لاستشارة معك.' : 'Requests appear here when teachers book a consultation with you.' }}</p>
        </div>
    @endforelse

    @if(method_exists($requests, 'links') && $requests->hasPages())
        <div class="st-cons-pager">{{ $requests->links() }}</div>
    @endif
</section>
@endsection
