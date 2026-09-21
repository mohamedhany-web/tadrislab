@extends('layouts.instructor-timeline')

@section('title', __('instructor.my_calendar'))
@section('page_title', __('instructor.my_calendar'))

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">
@endpush

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $viewerTz = $viewerTz ?? auth()->user()?->timezoneCode() ?? \App\Support\AppTimezone::academy();
    $fcLocale = $isRtl ? 'ar' : 'en';
    $upcoming = collect($events ?? [])->filter(fn ($e) => ($e->start_date ?? now()) >= now())->take(12);
    $next = $upcoming->first();
    $total = (int) ($stats['total'] ?? 0);
    $upcomingCount = (int) ($stats['upcoming'] ?? $upcoming->count());
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
@endphp

<section class="st-join-hero" aria-label="{{ __('instructor.my_calendar') }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">TADRIS LAB</p>
        @if($next)
            <h2 class="st-join-hero__title">{{ $next->title }}</h2>
            <p class="st-join-hero__meta">
                <x-app-datetime :at="$next->start_date" :timezone="$viewerTz" :pattern="$isRtl ? 'l، d M · g:i A' : 'D, M j · g:i A'" />
                · {{ \App\Support\AppTimezone::label($viewerTz) }}
            </p>
        @else
            <h2 class="st-join-hero__title">{{ __('instructor.my_calendar') }}</h2>
            <p class="st-join-hero__meta">
                {{ __('instructor.calendar_subtitle') }}
                <strong>{{ \App\Support\AppTimezone::label($viewerTz) }}</strong>
            </p>
        @endif
    </div>
    <div class="st-join-hero__actions">
        @if($next && !empty($next->url))
            <a href="{{ $next->url }}" class="st-pill st-pill--solid st-pill--lg">
                {{ $isRtl ? 'فتح الموعد' : 'Open event' }}
            </a>
        @endif
        @if(Route::has('instructor.consultations.index') && instructor_ui('show_consultations', true))
            <a href="{{ route('instructor.consultations.index') }}" class="st-pill st-pill--outline">استشاراتي</a>
        @endif
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="{{ __('instructor.my_calendar') }}">
    <article class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.calendar_total') }}</p>
        <p class="st-stat-card__value">{{ number_format($total) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'كل المواعيد في النطاق' : 'All events in range' }}</p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.upcoming') }}</p>
        <p class="st-stat-card__value">{{ number_format($upcomingCount) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'مواعيد قادمة' : 'Upcoming slots' }}</p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'منطقتك' : 'Your timezone' }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ \App\Support\AppTimezone::label($viewerTz) }}</p>
        <p class="st-stat-card__hint">{{ $viewerTz }}</p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'العرض الافتراضي' : 'Default view' }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ $isRtl ? 'أسبوعي' : 'Week' }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'شهر · يوم · قائمة' : 'Month · Day · List' }}</p>
    </article>
</section>

<section class="st-msg-intro">
    <div>
        <h2>{{ __('instructor.my_calendar') }}</h2>
        <p>{{ $isRtl ? 'مساراتك واستشاراتك ومحاضراتك في مكان واحد — بتوقيتك المحلي.' : 'Paths, consultations, and lectures in one place — on your local clock.' }}</p>
    </div>
</section>

<div class="st-cal-layout">
    <section class="st-panel st-fc su-fc" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" aria-label="{{ __('instructor.my_calendar') }}">
        <div id="calendar" class="su-fc__mount"></div>
        <div class="st-fc__legend">
            <span><i style="background:#7c3aed"></i> {{ __('instructor.cal_private') }}</span>
            <span><i style="background:#1E4E8C"></i> {{ __('instructor.cal_group') }}</span>
            <span><i style="background:#3B7BC4"></i> {{ __('instructor.cal_classroom') }}</span>
            <span><i style="background:#A88050"></i> {{ __('instructor.cal_consultation') }}</span>
            <span><i style="background:#EF4444"></i> {{ __('instructor.cal_live') }}</span>
        </div>
    </section>

    <aside class="st-panel st-cal-side" aria-label="{{ __('instructor.upcoming') }}">
        <div class="st-section-head">
            <h2>{{ __('instructor.upcoming') }}</h2>
            <p>{{ $isRtl ? 'أقرب المواعيد المسندة إليك' : 'Your next assigned slots' }}</p>
        </div>
        <div class="st-cal-upcoming">
            @forelse($upcoming as $event)
                <a href="{{ $event->url ?? '#' }}" class="st-cal-upcoming__item">
                    <span class="st-cal-upcoming__ico" aria-hidden="true">
                        <i class="fas fa-calendar-day"></i>
                    </span>
                    <span class="st-cal-upcoming__body">
                        <strong>{{ $event->title }}</strong>
                        <em>
                            <x-app-datetime :at="$event->start_date" :timezone="$viewerTz" pattern="D j M · g:i A" />
                        </em>
                    </span>
                    <i class="fas fa-chevron-{{ $isRtl ? 'left' : 'right' }} st-cal-upcoming__chev" aria-hidden="true"></i>
                </a>
            @empty
                <div class="st-cal-upcoming__empty">
                    <i class="fas fa-calendar-times" aria-hidden="true"></i>
                    <p>{{ __('instructor.calendar_no_upcoming') }}</p>
                </div>
            @endforelse
        </div>
    </aside>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/ar.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var isRtl = @json($isRtl);
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl || typeof FullCalendar === 'undefined') {
        if (calendarEl) {
            calendarEl.innerHTML = '<p style="padding:40px;text-align:center;color:#6b7a93;font-weight:700">{{ $isRtl ? 'تعذر تحميل التقويم' : 'Calendar failed to load' }}</p>';
        }
        return;
    }

    var isMobile = window.matchMedia('(max-width: 640px)').matches;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: @json($fcLocale),
        direction: isRtl ? 'rtl' : 'ltr',
        timeZone: @json($viewerTz),
        initialView: isMobile ? 'listWeek' : 'timeGridWeek',
        headerToolbar: isRtl
            ? { right: 'prev,next today', center: 'title', left: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' }
            : { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' },
        buttonText: isRtl
            ? { today: 'اليوم', month: 'شهر', week: 'أسبوع', day: 'يوم', list: 'قائمة' }
            : { today: 'Today', month: 'Month', week: 'Week', day: 'Day', list: 'List' },
        events: {
            url: @json(route('instructor.calendar.events')),
            failure: function () {
                console.error('Failed to load calendar events');
            }
        },
        eventClick: function (info) {
            if (info.event.url) {
                info.jsEvent.preventDefault();
                window.location.href = info.event.url;
            }
        },
        height: 'auto',
        contentHeight: isMobile ? 480 : 640,
        firstDay: isRtl ? 6 : 0,
        navLinks: true,
        dayMaxEvents: 3,
        nowIndicator: true,
        stickyHeaderDates: true
    });
    calendar.render();

    window.addEventListener('resize', function () {
        var mobile = window.matchMedia('(max-width: 640px)').matches;
        calendar.setOption('contentHeight', mobile ? 480 : 640);
    });
});
</script>
@endpush
