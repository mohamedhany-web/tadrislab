@extends('layouts.instructor-timeline')

@section('title', __('instructor.course_details') . ' - ' . $course->title)
@section('page_title', $course->title)

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
    $currency = platform_currency();
    $levelLabel = match ($course->level) {
        'beginner' => __('instructor.beginner'),
        'intermediate' => __('instructor.intermediate'),
        'advanced' => __('instructor.advanced'),
        default => __('instructor.level_unspecified'),
    };
    $priceLabel = (!$course->is_free && $course->effectivePurchasePrice() > 0)
        ? number_format($course->effectivePurchasePrice(), 2).' '.$currency
        : __('instructor.free');
@endphp

<section class="st-join-hero" aria-label="{{ $course->title }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">
            {{ $course->is_active ? __('instructor.active_status') : __('instructor.inactive_status') }}
            @if($course->is_featured) · {{ __('instructor.featured') }} @endif
        </p>
        <h2 class="st-join-hero__title">{{ $course->title }}</h2>
        <p class="st-join-hero__meta">
            @if($course->instructor) {{ $course->instructor->name }} · @endif
            {{ $levelLabel }} · {{ $priceLabel }}
            @if($course->academicYear) · {{ $course->academicYear->name }} @endif
            @if($course->academicSubject) · {{ $course->academicSubject->name }} @endif
        </p>
    </div>
    <div class="st-join-hero__actions">
        @if(Route::has('instructor.courses.curriculum'))
            <a href="{{ route('instructor.courses.curriculum', $course) }}" class="st-pill st-pill--solid st-pill--lg">
                {{ __('instructor.build_curriculum') }}
            </a>
        @endif
        <a href="{{ route('instructor.courses.index') }}" class="st-pill st-pill--outline">
            {{ __('instructor.back') }}
        </a>
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="{{ __('instructor.statistics') }}">
    <a href="#course-lectures" class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.lectures') }}</p>
        <p class="st-stat-card__value">{{ number_format($stats['total_lectures'] ?? 0) }}</p>
        <p class="st-stat-card__hint">{{ ($stats['upcoming_lectures'] ?? 0) }} {{ __('instructor.upcoming') }}</p>
    </a>
    <a href="#course-exams" class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.exams') }}</p>
        <p class="st-stat-card__value">{{ number_format($stats['total_exams'] ?? 0) }}</p>
        <p class="st-stat-card__hint">{{ ($stats['active_exams'] ?? 0) }} {{ __('instructor.active') }}</p>
    </a>
    <a href="#course-assignments" class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.assignments') }}</p>
        <p class="st-stat-card__value">{{ number_format($stats['total_assignments'] ?? 0) }}</p>
        <p class="st-stat-card__hint">{{ ($stats['pending_submissions'] ?? 0) }} {{ __('instructor.need_grading') }}</p>
    </a>
    <a href="#course-students" class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.students') }}</p>
        <p class="st-stat-card__value">{{ number_format($stats['total_students'] ?? 0) }}</p>
        <p class="st-stat-card__hint">{{ ($stats['total_attendance_records'] ?? 0) }} {{ __('instructor.attendance_records') }}</p>
    </a>
</section>

<nav class="st-course-jump" aria-label="{{ $isRtl ? 'أقسام الصفحة' : 'Page sections' }}">
    <a href="#course-overview">{{ __('instructor.overview') }}</a>
    <a href="#course-lectures">{{ __('instructor.lectures') }}</a>
    <a href="#course-exams">{{ __('instructor.exams') }}</a>
    <a href="#course-assignments">{{ __('instructor.assignments') }}</a>
    <a href="#course-students">{{ __('instructor.students') }}</a>
    <a href="#course-attendance">{{ __('instructor.attendance') }}</a>
</nav>

<div class="st-course-layout" id="course-overview">
    <section class="st-panel st-course-info">
        <div class="st-section-head">
            <h2>{{ __('instructor.course_info') }}</h2>
            <p>{{ $isRtl ? 'بيانات الكورس الظاهرة للمتعلمين' : 'Course details visible to learners' }}</p>
        </div>

        @if($course->thumbnail)
            <img src="{{ storage_asset($course->thumbnail) }}" alt="{{ $course->title }}" class="st-course-cover">
        @endif

        <dl class="st-course-dl">
            <div>
                <dt>{{ __('instructor.title') }}</dt>
                <dd>{{ $course->title }}</dd>
            </div>
            @if($course->instructor)
                <div>
                    <dt>{{ __('instructor.instructor_label') }}</dt>
                    <dd>{{ $course->instructor->name }}</dd>
                </div>
            @endif
            <div>
                <dt>{{ __('instructor.level') }}</dt>
                <dd>{{ $levelLabel }}</dd>
            </div>
            <div>
                <dt>{{ __('instructor.price') }}</dt>
                <dd class="tabular-nums">
                    @if(!$course->is_free && $course->effectivePurchasePrice() > 0)
                        @if($course->hasPromotionalPrice())
                            <span class="st-course-dl__strike">{{ number_format($course->listPriceAmount(), 2) }} {{ $currency }}</span>
                        @endif
                        {{ number_format($course->effectivePurchasePrice(), 2) }} {{ $currency }}
                    @else
                        <span class="st-course-dl__free">{{ __('instructor.free') }}</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt>{{ __('instructor.course_duration') }}</dt>
                <dd>
                    {{ (int) ($course->duration_hours ?? 0) }} {{ __('instructor.hour') }}
                    @if(($course->duration_minutes ?? 0) > 0)
                        {{ __('instructor.and') }} {{ (int) $course->duration_minutes }} {{ __('instructor.minutes') }}
                    @endif
                </dd>
            </div>
            @if($course->programming_language)
                <div>
                    <dt>{{ __('instructor.programming_language') }}</dt>
                    <dd>{{ $course->programming_language }}</dd>
                </div>
            @endif
            @if($course->academicYear)
                <div>
                    <dt>{{ __('instructor.year') }}</dt>
                    <dd>{{ $course->academicYear->name }}</dd>
                </div>
            @endif
            @if($course->academicSubject)
                <div>
                    <dt>{{ __('instructor.subject') }}</dt>
                    <dd>{{ $course->academicSubject->name }}</dd>
                </div>
            @endif
            <div>
                <dt>{{ __('common.status') }}</dt>
                <dd>{{ $course->is_active ? __('instructor.active_status') : __('instructor.inactive_status') }}</dd>
            </div>
        </dl>

        @if($course->description)
            <div class="st-course-prose">
                <h3>{{ __('instructor.description') }}</h3>
                <p>{{ $course->description }}</p>
            </div>
        @endif
        @if($course->objectives)
            <div class="st-course-prose">
                <h3>{{ __('instructor.objectives') }}</h3>
                <p>{{ $course->objectives }}</p>
            </div>
        @endif
    </section>

    <aside class="st-course-side">
        <section class="st-panel">
            <div class="st-section-head">
                <h2>{{ __('instructor.quick_actions') }}</h2>
            </div>
            <div class="st-course-actions">
                <a href="{{ route('instructor.lectures.create', ['course_id' => $course->id]) }}" class="st-course-action">
                    <i class="fas fa-video" aria-hidden="true"></i>
                    <strong>{{ __('instructor.add_lecture') }}</strong>
                </a>
                <a href="{{ route('instructor.exams.create', ['course_id' => $course->id]) }}" class="st-course-action">
                    <i class="fas fa-clipboard-check" aria-hidden="true"></i>
                    <strong>{{ __('instructor.create_exam') }}</strong>
                </a>
                <a href="{{ route('instructor.assignments.create', ['course_id' => $course->id]) }}" class="st-course-action">
                    <i class="fas fa-tasks" aria-hidden="true"></i>
                    <strong>{{ __('instructor.create_assignment') }}</strong>
                </a>
                @if(Route::has('instructor.courses.curriculum'))
                    <a href="{{ route('instructor.courses.curriculum', $course) }}" class="st-course-action">
                        <i class="fas fa-sitemap" aria-hidden="true"></i>
                        <strong>{{ __('instructor.build_curriculum') }}</strong>
                    </a>
                @endif
            </div>
        </section>

        <section class="st-panel">
            <div class="st-section-head">
                <h2>{{ __('instructor.statistics') }}</h2>
            </div>
            <ul class="st-course-statlines">
                <li><span>{{ __('instructor.upcoming_lectures') }}</span><strong>{{ $stats['upcoming_lectures'] ?? 0 }}</strong></li>
                <li><span>{{ __('instructor.active_exams') }}</span><strong>{{ $stats['active_exams'] ?? 0 }}</strong></li>
                <li><span>{{ __('instructor.pending_submissions') }}</span><strong>{{ $stats['pending_submissions'] ?? 0 }}</strong></li>
                <li><span>{{ __('instructor.attendance_records') }}</span><strong>{{ $stats['total_attendance_records'] ?? 0 }}</strong></li>
            </ul>
        </section>
    </aside>
</div>

{{-- Lectures --}}
<section class="st-panel st-course-block" id="course-lectures">
    <div class="st-course-block__head">
        <div>
            <h2>{{ __('instructor.lectures') }} ({{ $lectures->total() }})</h2>
            <p>{{ $isRtl ? 'محاضرات هذا الكورس' : 'Lectures for this course' }}</p>
        </div>
        <a href="{{ route('instructor.lectures.create', ['course_id' => $course->id]) }}" class="st-pill st-pill--solid">
            {{ __('instructor.add_lecture') }}
        </a>
    </div>
    @forelse($lectures as $lecture)
        <a href="{{ route('instructor.lectures.show', $lecture) }}" class="st-course-row">
            <span class="st-course-row__ico"><i class="fas fa-video" aria-hidden="true"></i></span>
            <span class="st-course-row__body">
                <strong>{{ $lecture->title }}</strong>
                <em>
                    @if($lecture->scheduled_at)
                        <x-app-datetime :at="$lecture->scheduled_at" pattern="Y/m/d H:i" />
                    @else
                        —
                    @endif
                    @if($lecture->lesson) · {{ $lecture->lesson->title }} @endif
                </em>
            </span>
            <span class="st-course-row__chip">
                @if($lecture->status == 'scheduled') {{ __('instructor.scheduled_lecture') }}
                @elseif($lecture->status == 'in_progress') {{ __('instructor.in_progress') }}
                @elseif($lecture->status == 'completed') {{ __('instructor.completed') }}
                @else {{ __('instructor.cancelled_lecture') }}
                @endif
            </span>
        </a>
    @empty
        <div class="st-lp-empty st-lp-empty--compact">
            <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
            <p>{{ __('instructor.no_lectures') }}</p>
        </div>
    @endforelse
    @if($lectures->hasPages())
        <div class="st-cons-pager">{{ $lectures->links() }}</div>
    @endif
</section>

{{-- Exams --}}
<section class="st-panel st-course-block" id="course-exams">
    <div class="st-course-block__head">
        <div>
            <h2>{{ __('instructor.exams') }} ({{ $exams->total() }})</h2>
            <p>{{ $isRtl ? 'اختبارات مرتبطة بالكورس' : 'Exams linked to this course' }}</p>
        </div>
        <a href="{{ route('instructor.exams.create', ['course_id' => $course->id]) }}" class="st-pill st-pill--solid">
            {{ __('instructor.create_exam') }}
        </a>
    </div>
    @forelse($exams as $exam)
        <a href="{{ route('instructor.exams.show', $exam) }}" class="st-course-row">
            <span class="st-course-row__ico st-course-row__ico--gold"><i class="fas fa-clipboard-check" aria-hidden="true"></i></span>
            <span class="st-course-row__body">
                <strong>{{ $exam->title }}</strong>
                <em>
                    {{ $exam->duration_minutes }} {{ __('instructor.minutes') }}
                    · {{ $exam->questions_count }} {{ __('instructor.question_single') }}
                    @if($exam->lesson) · {{ $exam->lesson->title }} @endif
                </em>
            </span>
            <span class="st-course-row__chip">{{ $exam->is_active ? __('instructor.active_status') : __('instructor.inactive_status') }}</span>
        </a>
    @empty
        <div class="st-lp-empty st-lp-empty--compact">
            <i class="fas fa-clipboard-check" aria-hidden="true"></i>
            <p>{{ __('instructor.no_exams') }}</p>
        </div>
    @endforelse
    @if($exams->hasPages())
        <div class="st-cons-pager">{{ $exams->links() }}</div>
    @endif
</section>

{{-- Assignments --}}
<section class="st-panel st-course-block" id="course-assignments">
    <div class="st-course-block__head">
        <div>
            <h2>{{ __('instructor.assignments') }} ({{ $assignments->total() }})</h2>
            <p>{{ $isRtl ? 'واجبات وتسليمات الطلاب' : 'Assignments and submissions' }}</p>
        </div>
        <a href="{{ route('instructor.assignments.create', ['course_id' => $course->id]) }}" class="st-pill st-pill--solid">
            {{ __('instructor.create_assignment') }}
        </a>
    </div>
    @forelse($assignments as $assignment)
        <a href="{{ route('instructor.assignments.show', $assignment) }}" class="st-course-row">
            <span class="st-course-row__ico"><i class="fas fa-tasks" aria-hidden="true"></i></span>
            <span class="st-course-row__body">
                <strong>{{ $assignment->title }}</strong>
                <em>
                    {{ $assignment->submissions_count }} {{ __('instructor.submission_single') }}
                    @if($assignment->due_date)
                        · <x-app-datetime :at="$assignment->due_date" pattern="Y/m/d" />
                    @endif
                </em>
            </span>
            <span class="st-course-row__chip">
                @if($assignment->status == 'published') {{ __('instructor.published') }}
                @elseif($assignment->status == 'draft') {{ __('instructor.draft') }}
                @else {{ __('instructor.archived') }}
                @endif
            </span>
        </a>
    @empty
        <div class="st-lp-empty st-lp-empty--compact">
            <i class="fas fa-tasks" aria-hidden="true"></i>
            <p>{{ __('instructor.no_assignments') }}</p>
        </div>
    @endforelse
    @if($assignments->hasPages())
        <div class="st-cons-pager">{{ $assignments->links() }}</div>
    @endif
</section>

{{-- Students --}}
<section class="st-panel st-course-block" id="course-students">
    <div class="st-course-block__head">
        <div>
            <h2>{{ __('instructor.enrolled_students') }} ({{ $enrollments->total() }})</h2>
            <p>{{ $isRtl ? 'المعلمون/الطلاب المسجّلون بنشاط' : 'Actively enrolled learners' }}</p>
        </div>
    </div>
    @forelse($enrollments as $enrollment)
        <div class="st-course-row st-course-row--static">
            <span class="st-cons-row__avatar" aria-hidden="true">{{ mb_substr($enrollment->user->name ?? 'م', 0, 1) }}</span>
            <span class="st-course-row__body">
                <strong>{{ $enrollment->user->name ?? __('instructor.not_specified') }}</strong>
                <em>
                    {{ $enrollment->user->email ?? '—' }}
                    @if($enrollment->user?->phone) · {{ $enrollment->user->phone }} @endif
                </em>
            </span>
            <span class="st-course-row__chip">{{ $enrollment->status ?? __('instructor.active_status') }}</span>
            <time class="st-course-row__when">
                {{ optional($enrollment->created_at)->translatedFormat($isRtl ? 'j M Y' : 'M j, Y') ?? '—' }}
            </time>
        </div>
    @empty
        <div class="st-lp-empty st-lp-empty--compact">
            <i class="fas fa-user-graduate" aria-hidden="true"></i>
            <p>{{ __('instructor.no_enrolled_students') }}</p>
            <p>{{ __('instructor.no_enrolled_description') }}</p>
        </div>
    @endforelse
    @if($enrollments->hasPages())
        <div class="st-cons-pager">{{ $enrollments->links() }}</div>
    @endif
</section>

{{-- Attendance --}}
<section class="st-panel st-course-block" id="course-attendance">
    <div class="st-course-block__head">
        <div>
            <h2>{{ __('instructor.attendance_absence') }}</h2>
            <p>{{ $isRtl ? 'آخر المحاضرات المكتملة وسجلات الحضور' : 'Recent completed lectures and attendance' }}</p>
        </div>
        <a href="{{ route('instructor.attendance.index', ['course_id' => $course->id]) }}" class="st-pill st-pill--outline">
            {{ __('instructor.view_all_records') }}
        </a>
    </div>
    @php
        $courseLectures = \App\Models\Lecture::where('course_id', $course->id)
            ->where('status', 'completed')
            ->withCount('attendanceRecords')
            ->orderByDesc('scheduled_at')
            ->take(10)
            ->get();
    @endphp
    @forelse($courseLectures as $lecture)
        <a href="{{ route('instructor.attendance.lecture', $lecture) }}" class="st-course-row">
            <span class="st-course-row__ico st-course-row__ico--gold"><i class="fas fa-clipboard-list" aria-hidden="true"></i></span>
            <span class="st-course-row__body">
                <strong>{{ $lecture->title }}</strong>
                <em>
                    @if($lecture->scheduled_at)
                        <x-app-datetime :at="$lecture->scheduled_at" pattern="Y/m/d H:i" />
                    @endif
                    · {{ $lecture->attendance_records_count }} {{ __('instructor.attendance_record_single') }}
                </em>
            </span>
            <span class="st-course-row__cta">{{ __('common.view') }}</span>
        </a>
    @empty
        <div class="st-lp-empty st-lp-empty--compact">
            <i class="fas fa-clipboard-list" aria-hidden="true"></i>
            <p>{{ __('instructor.no_attendance_records') }}</p>
            <p>{{ __('instructor.no_attendance_description') }}</p>
        </div>
    @endforelse
</section>
@endsection
