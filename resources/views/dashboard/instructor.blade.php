@extends('layouts.instructor-timeline')

@section('title', __('instructor.dashboard_title'))
@section('page_title', __('instructor.overview'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $showCourses = instructor_ui('show_courses', false);
    $showTutoring = instructor_ui('show_tutoring', false);
    $showLive = instructor_ui('show_live_broadcast', false);
    $pending = (int) ($stats['pending_submissions'] ?? 0);
    $students = (int) ($stats['total_students'] ?? 0);
    $courses = (int) ($stats['my_courses'] ?? 0);
    $lectures = (int) ($stats['total_lectures'] ?? 0);
    $upcomingTutoring = (int) ($stats['upcoming_tutoring'] ?? 0);
    $upcomingLectures = (int) ($stats['upcoming_lectures'] ?? 0);
    $exams = (int) ($stats['total_exams'] ?? 0);
    $assignments = (int) ($stats['total_assignments'] ?? 0);
    $pathCount = (int) ($stats['learning_paths_count'] ?? auth()->user()?->teachingLearningPathIds()->count() ?? 0);
    $consultCount = (int) ($stats['consultations_count'] ?? 0);
    $upcomingConsult = (int) ($stats['upcoming_consultations'] ?? 0);
    $bookingsHref = Route::has('instructor.tutoring-bookings.index')
        ? route('instructor.tutoring-bookings.index')
        : route('dashboard');
    $cohortsHref = Route::has('instructor.tutoring-cohorts.index')
        ? route('instructor.tutoring-cohorts.index')
        : $bookingsHref;
    $liveHref = Route::has('instructor.live-sessions.index')
        ? route('instructor.live-sessions.index')
        : route('dashboard');
    $calendarHref = Route::has('instructor.calendar')
        ? route('instructor.calendar')
        : route('dashboard');
    $pathsHref = Route::has('instructor.learning-paths.index')
        ? route('instructor.learning-paths.index')
        : route('dashboard');
    $consultHref = Route::has('instructor.consultations.index')
        ? route('instructor.consultations.index')
        : route('dashboard');

    if ($showCourses) {
        $workload = [
            ['label' => __('instructor.my_courses'), 'value' => $courses, 'href' => route('instructor.courses.index')],
            ['label' => __('instructor.total_students'), 'value' => $students, 'href' => route('instructor.courses.index')],
            ['label' => __('instructor.upcoming_lectures'), 'value' => $upcomingLectures, 'href' => route('instructor.lectures.index')],
            ['label' => __('instructor.group_bookings'), 'value' => $upcomingTutoring, 'href' => $bookingsHref],
            ['label' => __('instructor.need_grading'), 'value' => $pending, 'href' => route('instructor.assignments.index')],
            ['label' => __('instructor.exams'), 'value' => $exams, 'href' => route('instructor.exams.index')],
        ];
    } else {
        $workload = [
            ['label' => 'مساراتي المسندة', 'value' => $pathCount, 'href' => $pathsHref],
            ['label' => 'استشاراتي', 'value' => $consultCount, 'href' => $consultHref],
            ['label' => __('instructor.upcoming'), 'value' => $upcomingConsult, 'href' => $consultHref],
            ['label' => __('instructor.my_calendar'), 'value' => $upcomingLectures + $upcomingTutoring + $upcomingConsult, 'href' => $calendarHref],
        ];
    }
    $maxWorkload = max(1, collect($workload)->max('value'));
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
@endphp

<section class="st-join-hero" aria-label="{{ __('instructor.overview') }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">TADRIS LAB</p>
        <h2 class="st-join-hero__title">{{ __('instructor.welcome') }}، {{ auth()->user()->name }}</h2>
        <p class="st-join-hero__meta">
            {{ $isRtl ? 'لوحة المدرب — مسارات واستشارات ومواعيد مسندة إليك.' : 'Coach panel — assigned paths, consultations, and slots.' }}
            · {{ now()->translatedFormat($isRtl ? 'l، j F' : 'D, M j') }}
        </p>
    </div>
    <div class="st-join-hero__actions">
        @if(Route::has('instructor.consultations.index') && instructor_ui('show_consultations', true))
            <a href="{{ route('instructor.consultations.index') }}" class="st-pill st-pill--solid st-pill--lg">استشاراتي</a>
        @endif
        @if(Route::has('instructor.learning-paths.index') && instructor_ui('show_learning_paths', true))
            <a href="{{ route('instructor.learning-paths.index') }}" class="st-pill st-pill--outline">مساراتي</a>
        @elseif(Route::has('instructor.calendar'))
            <a href="{{ route('instructor.calendar') }}" class="st-pill st-pill--outline">{{ __('instructor.my_calendar') }}</a>
        @endif
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="{{ __('instructor.overview') }}">
    @if($showCourses)
        <a href="{{ route('instructor.courses.index') }}" class="st-subject st-subject--blue st-stat-card">
            <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
            <p class="st-stat-card__label">{{ __('instructor.my_courses') }}</p>
            <p class="st-stat-card__value">{{ number_format($courses) }}</p>
            <p class="st-stat-card__hint">{{ $lectures }} {{ __('instructor.lectures') }}</p>
        </a>
        <a href="{{ route('instructor.courses.index') }}" class="st-subject st-subject--pink st-stat-card">
            <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
            <p class="st-stat-card__label">{{ __('instructor.total_students') }}</p>
            <p class="st-stat-card__value">{{ number_format($students) }}</p>
            <p class="st-stat-card__hint">{{ $isRtl ? 'طلاب مسجّلون' : 'Enrolled learners' }}</p>
        </a>
        <a href="{{ route('instructor.lectures.index') }}" class="st-subject st-subject--orange st-stat-card">
            <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
            <p class="st-stat-card__label">{{ __('instructor.upcoming_lectures') }}</p>
            <p class="st-stat-card__value">{{ number_format($upcomingLectures) }}</p>
            <p class="st-stat-card__hint">{{ __('instructor.upcoming') }}</p>
        </a>
        <a href="{{ route('instructor.assignments.index') }}" class="st-subject st-subject--purple st-stat-card">
            <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
            <p class="st-stat-card__label">{{ __('instructor.need_grading') }}</p>
            <p class="st-stat-card__value">{{ number_format($pending) }}</p>
            <p class="st-stat-card__hint">{{ __('instructor.assignments') }}</p>
        </a>
    @else
        <a href="{{ $pathsHref }}" class="st-subject st-subject--blue st-stat-card">
            <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
            <p class="st-stat-card__label">مساراتي المسندة</p>
            <p class="st-stat-card__value">{{ number_format($pathCount) }}</p>
            <p class="st-stat-card__hint">{{ $isRtl ? 'مسارات للتدريب' : 'Assigned paths' }}</p>
        </a>
        <a href="{{ $consultHref }}" class="st-subject st-subject--pink st-stat-card">
            <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
            <p class="st-stat-card__label">استشاراتي</p>
            <p class="st-stat-card__value">{{ number_format($consultCount) }}</p>
            <p class="st-stat-card__hint">{{ $isRtl ? 'إجمالي الطلبات' : 'Total requests' }}</p>
        </a>
        <a href="{{ $consultHref }}" class="st-subject st-subject--orange st-stat-card">
            <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
            <p class="st-stat-card__label">{{ __('instructor.upcoming') }}</p>
            <p class="st-stat-card__value">{{ number_format($upcomingConsult) }}</p>
            <p class="st-stat-card__hint">{{ $isRtl ? 'استشارات قادمة' : 'Upcoming consultations' }}</p>
        </a>
        <a href="{{ $calendarHref }}" class="st-subject st-subject--purple st-stat-card">
            <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
            <p class="st-stat-card__label">{{ __('instructor.my_calendar') }}</p>
            <p class="st-stat-card__value">{{ number_format($upcomingLectures + $upcomingTutoring + $upcomingConsult) }}</p>
            <p class="st-stat-card__hint">{{ $isRtl ? 'مواعيد قادمة' : 'Upcoming slots' }}</p>
        </a>
    @endif
</section>

<section class="st-msg-intro">
    <div>
        <h2>{{ __('instructor.overview') }}</h2>
        <p>{{ $isRtl ? 'اختصارات سريعة لما تحتاجه اليوم.' : 'Quick shortcuts for what you need today.' }}</p>
    </div>
</section>

<section class="su-quick-grid" style="margin-bottom:1.25rem">
    @if(Route::has('instructor.calendar'))
    <a href="{{ route('instructor.calendar') }}" class="su-quick">
        <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-calendar-alt"></i></span>
        <span>
            <strong>{{ __('instructor.my_calendar') }}</strong>
            <em>{{ ($showCourses ? $upcomingLectures : $upcomingConsult) + ($showTutoring ? $upcomingTutoring : 0) }} {{ __('instructor.upcoming') }}</em>
        </span>
    </a>
    @endif
    @if(instructor_ui('show_learning_paths', true) && Route::has('instructor.learning-paths.index'))
    <a href="{{ route('instructor.learning-paths.index') }}" class="su-quick">
        <span class="su-rail-ico su-rail-ico--a"><i class="fas fa-route"></i></span>
        <span>
            <strong>مساراتي المسندة</strong>
            <em>{{ $pathCount }} مسار</em>
        </span>
    </a>
    @endif
    @if(instructor_ui('show_consultations', true) && Route::has('instructor.consultations.index'))
    <a href="{{ route('instructor.consultations.index') }}" class="su-quick">
        <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-comments"></i></span>
        <span>
            <strong>استشاراتي</strong>
            <em>{{ $consultCount }} · {{ $upcomingConsult }} قادمة</em>
        </span>
    </a>
    @endif
    @if($showTutoring && Route::has('instructor.tutoring-bookings.index'))
    <a href="{{ route('instructor.tutoring-bookings.index') }}" class="su-quick">
        <span class="su-rail-ico su-rail-ico--a"><i class="fas fa-users"></i></span>
        <span>
            <strong>{{ __('instructor.group_bookings') }}</strong>
            <em>{{ $upcomingTutoring }} {{ __('instructor.upcoming') }}</em>
        </span>
    </a>
    @endif
    @if($showCourses)
    <a href="{{ route('instructor.assignments.index') }}" class="su-quick">
        <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-tasks"></i></span>
        <span>
            <strong>{{ __('instructor.assignments') }}</strong>
            <em>@if($pending > 0){{ $pending }} {{ __('instructor.need_grading') }}@else{{ __('instructor.all_assignments_graded') }}@endif</em>
        </span>
    </a>
    @endif
    @if($showLive && Route::has('instructor.live-sessions.index'))
    <a href="{{ route('instructor.live-sessions.index') }}" class="su-quick">
        <span class="su-rail-ico su-rail-ico--a"><i class="fas fa-broadcast-tower"></i></span>
        <span>
            <strong>{{ __('instructor.live_broadcast') }}</strong>
            <em>{{ __('instructor.manage_streams') }}</em>
        </span>
    </a>
    @endif
</section>

@if(!empty($upcomingTutoringBooking) && $showTutoring)
    <section class="st-join-hero st-join-hero--muted" style="margin-bottom:1.25rem">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">{{ __('instructor.next_live_session') }}</p>
            <h2 class="st-join-hero__title">{{ $upcomingTutoringBooking->tutoringGroup?->title ?? __('instructor.group_session') }}</h2>
            <p class="st-join-hero__meta">
                <x-app-datetime :at="$upcomingTutoringBooking->starts_at" pattern="D j M · g:i A" />
                @if($upcomingTutoringBooking->user) · {{ $upcomingTutoringBooking->user->name }} @endif
            </p>
        </div>
        <div class="st-join-hero__actions">
            @if($upcomingTutoringBooking->classroomMeeting)
                <form method="POST" action="{{ route('instructor.classroom.start-meeting', $upcomingTutoringBooking->classroomMeeting) }}">
                    @csrf
                    <button type="submit" class="st-pill st-pill--solid">{{ __('instructor.start_live') }}</button>
                </form>
            @endif
            @if(Route::has('instructor.tutoring-bookings.show'))
                <a href="{{ route('instructor.tutoring-bookings.show', $upcomingTutoringBooking) }}" class="st-pill st-pill--outline">{{ __('instructor.view_details') }}</a>
            @endif
        </div>
    </section>
@endif

<section class="su-bottom">
    @if($showCourses)
    <div class="su-block st-panel">
        <div class="flex items-center justify-between mb-3">
            <div class="su-block__title" style="margin:0">{{ __('instructor.upcoming_lectures') }}</div>
            <a href="{{ route('instructor.lectures.index') }}" class="su-rail-m">{{ __('instructor.view_all') }}</a>
        </div>
        <div class="space-y-1">
            @forelse(($upcoming_lectures ?? collect())->take(5) as $lecture)
                <a href="{{ route('instructor.lectures.show', $lecture) }}" class="su-rail-item">
                    <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-chalkboard"></i></span>
                    <div class="min-w-0">
                        <div class="su-rail-t truncate">{{ $lecture->title }}</div>
                        <div class="su-rail-m truncate">
                            {{ $lecture->course->title ?? __('instructor.not_specified') }}
                            · {{ $lecture->scheduled_at?->diffForHumans() }}
                        </div>
                    </div>
                </a>
            @empty
                <p class="su-rail-m" style="padding:16px;text-align:center">{{ __('instructor.no_lectures') }}</p>
            @endforelse
        </div>
    </div>
    @elseif(Route::has('instructor.consultations.index'))
    <div class="su-block st-panel">
        <div class="flex items-center justify-between mb-3">
            <div class="su-block__title" style="margin:0">استشارات قادمة</div>
            <a href="{{ $consultHref }}" class="su-rail-m">{{ __('instructor.view_all') }}</a>
        </div>
        <div class="space-y-1">
            @forelse(($upcoming_consultations ?? collect())->take(5) as $c)
                <a href="{{ route('instructor.consultations.show', $c) }}" class="su-rail-item">
                    <span class="su-rail-ico su-rail-ico--a"><i class="fas fa-comments"></i></span>
                    <div class="min-w-0">
                        <div class="su-rail-t truncate">{{ $c->contact_name ?? $c->student?->name ?? 'استشارة' }}</div>
                        <div class="su-rail-m truncate">{{ $c->scheduled_at?->diffForHumans() ?? $c->statusLabel() }}</div>
                    </div>
                </a>
            @empty
                <p class="su-rail-m" style="padding:16px;text-align:center">لا استشارات قادمة حاليًا</p>
            @endforelse
        </div>
    </div>
    @endif

    <div class="su-block st-panel">
        <div class="su-block__title">{{ __('instructor.workload_summary') }}</div>
        <div class="su-workload">
            @foreach($workload as $row)
                <a href="{{ $row['href'] }}" class="su-workload-row">
                    <span class="su-workload-label truncate">{{ $row['label'] }}</span>
                    <div class="su-workload-bar" aria-hidden="true">
                        <i style="width: {{ max(8, (int) round(($row['value'] / $maxWorkload) * 100)) }}%"></i>
                    </div>
                    <strong>{{ number_format($row['value']) }}</strong>
                </a>
            @endforeach
        </div>
    </div>
</section>

@if($showCourses)
<section class="su-bottom" style="margin-top:20px">
    <div class="su-block st-panel">
        <div class="flex items-center justify-between mb-3">
            <div class="su-block__title" style="margin:0">{{ __('instructor.my_recent_courses') }}</div>
            <a href="{{ route('instructor.courses.index') }}" class="su-rail-m">{{ __('instructor.view_all') }}</a>
        </div>
        <div class="space-y-1">
            @forelse(($my_courses ?? collect()) as $course)
                <a href="{{ route('instructor.courses.show', $course) }}" class="su-rail-item">
                    <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-book"></i></span>
                    <div class="min-w-0">
                        <div class="su-rail-t truncate">{{ $course->title }}</div>
                        <div class="su-rail-m">{{ $course->active_students_count ?? 0 }} {{ __('instructor.student_single') }}</div>
                    </div>
                </a>
            @empty
                <p class="su-rail-m" style="padding:16px;text-align:center">{{ __('instructor.no_courses_assigned') }}</p>
            @endforelse
        </div>
    </div>

    <div class="su-block st-panel">
        <div class="flex items-center justify-between mb-3">
            <div class="su-block__title" style="margin:0">{{ __('instructor.assignments_need_grading') }}</div>
            @if($pending > 0)<span class="su-link__badge">{{ $pending }}</span>@endif
        </div>
        <div class="space-y-1">
            @forelse(($pending_assignments ?? collect())->take(5) as $submission)
                <a href="{{ route('instructor.assignments.submissions', $submission->assignment) }}" class="su-rail-item">
                    <span class="su-rail-avatar">{{ mb_substr($submission->student->name ?? 'S', 0, 1) }}</span>
                    <div class="min-w-0">
                        <div class="su-rail-t truncate">{{ $submission->assignment->title ?? __('instructor.assignment_default') }}</div>
                        <div class="su-rail-m">{{ $submission->student->name ?? '' }} · {{ $submission->created_at->diffForHumans() }}</div>
                    </div>
                </a>
            @empty
                <p class="su-rail-m" style="padding:16px;text-align:center">{{ __('instructor.all_assignments_graded') }}</p>
            @endforelse
        </div>
    </div>
</section>
@endif
@endsection
