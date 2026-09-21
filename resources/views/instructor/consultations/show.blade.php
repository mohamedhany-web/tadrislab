@extends('layouts.instructor-timeline')

@section('title', __('instructor.cons_show_title'))
@section('page_title', __('instructor.cons_show_title'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
    $studentName = $consultation->student->name ?? __('instructor.pm_student_fallback');
@endphp

<section class="st-join-hero" aria-label="{{ __('instructor.cons_show_title') }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">{{ $consultation->statusLabel() }}</p>
        <h2 class="st-join-hero__title">{{ __('instructor.cons_show_heading', ['name' => $studentName]) }}</h2>
        <p class="st-join-hero__meta">
            {{ $consultation->service?->title() ?? ($consultation->consultation_type ?: ($isRtl ? 'استشارة مهنية' : 'Professional consultation')) }}
        </p>
    </div>
    <div class="st-join-hero__actions">
        <a href="{{ route('instructor.consultations.index') }}" class="st-pill st-pill--outline">
            {{ __('instructor.cons_back') }}
        </a>
        @if($consultation->isScheduled() && $consultation->classroomMeeting && !$consultation->classroomMeeting->ended_at)
            <a href="{{ \App\Services\ClassroomMeetingAccessService::platformEnterUrl($consultation->classroomMeeting) }}" class="st-pill st-pill--solid">
                {{ __('instructor.cons_enter_room') }}
            </a>
        @endif
    </div>
</section>

@if(session('success'))
    <div class="st-flash st-flash--ok">{{ session('success') }}</div>
@endif

<section class="st-stats st-stats--classes" aria-label="{{ __('instructor.cons_show_title') }}">
    <article class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.cons_amount') }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ number_format($consultation->price_amount, 2) }} $</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'قيمة الجلسة' : 'Session fee' }}</p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.cons_duration') }}</p>
        <p class="st-stat-card__value">{{ (int) $consultation->duration_minutes }}</p>
        <p class="st-stat-card__hint">{{ __('instructor.o1o_minutes') }}</p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.cons_status') }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ $consultation->statusLabel() }}</p>
        <p class="st-stat-card__hint">{{ $consultation->briefStatusLabel() }}</p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.cons_when') }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">
            @if($consultation->scheduled_at)
                <x-app-datetime :at="$consultation->scheduled_at" pattern="D j M" />
            @else
                —
            @endif
        </p>
        <p class="st-stat-card__hint">
            @if($consultation->scheduled_at)
                <x-app-datetime :at="$consultation->scheduled_at" pattern="g:i A" />
            @else
                {{ $isRtl ? 'لم يُحدَّد بعد' : 'Not scheduled' }}
            @endif
        </p>
    </article>
</section>

<section class="st-panel st-cons-detail">
    <div class="st-section-head">
        <h2>{{ $isRtl ? 'تفاصيل الطلب' : 'Request details' }}</h2>
        <p>{{ $studentName }}</p>
    </div>

    @if($consultation->student_message)
        <div class="st-cons-detail__msg">
            <h3>{{ __('instructor.cons_student_request') }}</h3>
            <p>{{ $consultation->student_message }}</p>
        </div>
    @else
        <p class="st-cons-detail__quiet">{{ $isRtl ? 'لا توجد رسالة إضافية من المعلّم.' : 'No extra message from the teacher.' }}</p>
    @endif

    @if($consultation->isScheduled() && $consultation->classroomMeeting)
        @php
            $m = $consultation->classroomMeeting;
            $enterUrl = \App\Services\ClassroomMeetingAccessService::platformEnterUrl($m);
        @endphp
        <div class="st-cons-detail__room">
            <div>
                <h3>{{ __('instructor.cons_appointment') }}</h3>
                <p>
                    <x-app-datetime :at="$consultation->scheduled_at" />
                    · {{ $consultation->briefStatusLabel() }}
                </p>
            </div>
            <div class="st-cons-detail__room-actions">
                <a href="{{ route('instructor.classroom.show', $m) }}" class="st-pill st-pill--outline">
                    {{ __('instructor.cons_room_settings') }}
                </a>
                @if(!$m->ended_at)
                    <a href="{{ $enterUrl }}" class="st-pill st-pill--solid">
                        {{ __('instructor.cons_enter_room') }}
                    </a>
                @endif
            </div>
        </div>
    @endif
</section>
@endsection
