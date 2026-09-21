@extends('layouts.instructor-timeline')

@section('title', __('instructor.lectures') . ' - ' . config('app.name'))
@section('page_title', __('instructor.lectures'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-chalkboard-teacher su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.lectures') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.manage_lectures_curriculum') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.courses.index') }}" class="su-btn">
                <i class="fas fa-book" aria-hidden="true"></i>
                {{ __('instructor.courses') }}
            </a>
            <a href="{{ route('instructor.lectures.create') }}" class="su-btn su-btn--primary">
                <i class="fas fa-plus" aria-hidden="true"></i>
                {{ __('instructor.add_new_lecture') }}
            </a>
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.total_courses') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total_courses'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-book" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.active_courses') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['active_courses'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.total_lectures') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total_lectures'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-chalkboard-teacher" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.scheduled_lectures') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['scheduled_lectures'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-calendar-alt" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.total_students') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total_students'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-users" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <section class="su-card" style="margin-bottom:20px">
        <form method="GET" class="su-form-grid">
            <div class="su-field">
                <label for="search">{{ __('common.search') }}</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="{{ __('instructor.search_in_courses') }}"
                       class="su-input">
            </div>
            <div class="su-field">
                <label for="status">{{ __('common.status') }}</label>
                <select name="status" id="status" class="su-select">
                    <option value="">{{ __('instructor.all_statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('instructor.active_status') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('instructor.not_active') }}</option>
                </select>
            </div>
            <div class="su-form-actions">
                <button type="submit" class="su-btn su-btn--primary" style="flex:1;justify-content:center;height:40px">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    {{ __('common.search') }}
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('instructor.lectures.index') }}" class="su-btn" style="height:40px;width:40px;padding:0;justify-content:center" title="{{ __('common.reset') ?? 'Reset' }}">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </form>
    </section>

    @if($courses->count() > 0)
        <div class="su-course-grid">
            @foreach($courses as $course)
                <article class="su-course-card">
                    <div class="su-course-card__head">
                        <h3 class="su-course-card__title">{{ $course->title }}</h3>
                        <span class="su-chip {{ $course->is_active ? 'su-chip--ok' : 'su-chip--off' }}">
                            <i class="fas {{ $course->is_active ? 'fa-check-circle' : 'fa-times-circle' }}" aria-hidden="true"></i>
                            {{ $course->is_active ? __('instructor.active_status') : __('instructor.not_active') }}
                        </span>
                    </div>
                    <div class="su-course-card__body">
                        @if($course->academicYear)
                            <p class="su-course-card__desc">{{ $course->academicYear->name }}</p>
                        @endif
                        <div class="su-meta-list">
                            <div class="su-meta-row">
                                <span class="su-meta-ico su-soft-1"><i class="fas fa-book-open" aria-hidden="true"></i></span>
                                <span>{{ __('instructor.sections_label') }}:</span>
                                <strong>{{ $course->sections_count ?? 0 }}</strong>
                            </div>
                            <div class="su-meta-row">
                                <span class="su-meta-ico su-soft-2"><i class="fas fa-chalkboard-teacher" aria-hidden="true"></i></span>
                                <span>{{ __('instructor.lectures_label') }}:</span>
                                <strong>{{ $course->lectures_count ?? 0 }}</strong>
                            </div>
                            <div class="su-meta-row">
                                <span class="su-meta-ico su-soft-3"><i class="fas fa-users" aria-hidden="true"></i></span>
                                <span>{{ __('instructor.students_label') }}:</span>
                                <strong>{{ $course->enrollments_count ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="su-course-card__foot" style="display:flex;flex-direction:column;gap:8px">
                        <a href="{{ route('instructor.courses.curriculum', $course) }}" class="su-btn su-btn--primary" style="justify-content:center">
                            <i class="fas fa-sitemap" aria-hidden="true"></i>
                            {{ __('instructor.build_curriculum') }}
                        </a>
                        <a href="{{ route('instructor.courses.show', $course) }}" class="su-btn" style="justify-content:center">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                            {{ __('instructor.view_details') }}
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        @if(method_exists($courses, 'links') && $courses->hasPages())
            <div class="su-pager" style="margin-top:16px">{{ $courses->links() }}</div>
        @endif
    @else
        <div class="su-empty">
            <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
            <p><strong>{{ __('instructor.no_courses') }}</strong></p>
            <p>{{ __('instructor.no_courses_lectures_desc') }}</p>
        </div>
    @endif
</div>
@endsection
