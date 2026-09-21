@extends('layouts.instructor-timeline')

@section('title', __('instructor.exams'))
@section('page_title', __('instructor.exams'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-clipboard-check su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.exams') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.manage_exams_attempts') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.courses.index') }}" class="su-btn">
                <i class="fas fa-book" aria-hidden="true"></i>
                {{ __('instructor.courses') }}
            </a>
            <a href="{{ route('instructor.exams.create') }}" class="su-btn su-btn--primary">
                <i class="fas fa-plus" aria-hidden="true"></i>
                {{ __('instructor.create_exam') }}
            </a>
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.total') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-clipboard-check" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.active') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['active'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.attempts') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total_attempts'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-redo" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.completed_attempts') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['completed_attempts'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-check-double" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <section class="su-card" style="margin-bottom:20px">
        <form method="GET" class="su-form-grid">
            <div class="su-field">
                <label for="course_id">{{ __('instructor.online_course') }}</label>
                <select name="course_id" id="course_id" class="su-select">
                    <option value="">{{ __('instructor.all_online_courses') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="su-field">
                <label for="is_active">{{ __('common.status') }}</label>
                <select name="is_active" id="is_active" class="su-select">
                    <option value="">{{ __('instructor.all') }}</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>{{ __('instructor.active') }}</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>{{ __('instructor.inactive') }}</option>
                </select>
            </div>
            <div class="su-field">
                <label for="search">{{ __('common.search') }}</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="{{ __('instructor.search_placeholder') }}" class="su-input">
            </div>
            <div class="su-form-actions">
                <button type="submit" class="su-btn su-btn--primary" style="flex:1;justify-content:center;height:40px">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    {{ __('common.search') }}
                </button>
                @if(request()->anyFilled(['course_id', 'is_active', 'search']))
                    <a href="{{ route('instructor.exams.index') }}" class="su-btn" style="height:40px;width:40px;padding:0;justify-content:center" title="{{ __('common.reset') ?? 'Reset' }}">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </form>
    </section>

    @if($exams->count() > 0)
        <div class="su-list">
            @foreach($exams as $exam)
                <article class="su-list-item">
                    <span class="su-list-item__ico {{ $exam->is_active ? 'su-soft-2' : 'su-soft-4' }}">
                        <i class="fas fa-clipboard-check" aria-hidden="true"></i>
                    </span>
                    <div class="su-list-item__body">
                        <div class="su-chip-row" style="margin:0 0 6px">
                            <span class="su-chip {{ $exam->is_active ? 'su-chip--ok' : 'su-chip--warn' }}">
                                {{ $exam->is_active ? __('instructor.active_status') : __('instructor.inactive_status') }}
                            </span>
                            @if($exam->advancedCourse)
                                <span class="su-chip">{{ Str::limit($exam->advancedCourse->title, 36) }}</span>
                            @endif
                        </div>
                        <div class="su-list-item__title">{{ $exam->title }}</div>
                        @if($exam->description)
                            <p style="margin:4px 0 0;font-size:13px;color:var(--su-ink-40)">{{ Str::limit($exam->description, 120) }}</p>
                        @endif
                        <div class="su-list-item__meta">
                            {{ $exam->duration_minutes }} {{ __('instructor.minutes') }} ·
                            {{ $exam->total_marks }} {{ __('instructor.marks') }} ·
                            {{ $exam->questions_count }} {{ __('instructor.question_single') }} ·
                            {{ $exam->attempts_count }} {{ __('instructor.attempt_single') }}
                        </div>
                    </div>
                    <div class="su-list-item__actions">
                        <a href="{{ route('instructor.exams.questions.manage', $exam) }}" class="su-btn" style="height:32px">
                            <i class="fas fa-list" aria-hidden="true"></i>
                            {{ __('instructor.questions') }}
                        </a>
                        <a href="{{ route('instructor.exams.show', $exam) }}" class="su-btn su-btn--primary" style="height:32px">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                            {{ __('common.view') }}
                        </a>
                        <form action="{{ route('instructor.exams.destroy', $exam) }}" method="POST" onsubmit="return confirm(@json(__('instructor.confirm_delete_exam')));">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="su-icon-link" style="color:#b91c1c" title="{{ __('instructor.delete_exam_title') }}">
                                <i class="fas fa-trash-alt" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
        @if(method_exists($exams, 'links') && $exams->hasPages())
            <div class="su-pager" style="margin-top:16px">{{ $exams->links() }}</div>
        @endif
    @else
        <div class="su-empty">
            <i class="fas fa-clipboard-check" aria-hidden="true"></i>
            <p><strong>{{ __('instructor.no_exams') }}</strong></p>
            <p>{{ __('instructor.no_exams_description') }}</p>
            <a href="{{ route('instructor.exams.create') }}" class="su-btn su-btn--primary" style="margin-top:12px">
                <i class="fas fa-plus" aria-hidden="true"></i>
                {{ __('instructor.create_exam') }}
            </a>
        </div>
    @endif
</div>
@endsection
