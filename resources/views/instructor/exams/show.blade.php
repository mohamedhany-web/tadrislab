@extends('layouts.instructor-timeline')

@section('title', __('instructor.exam_details'))
@section('page_title', __('instructor.exam_details') . ': ' . $exam->title)

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page" x-data="{ activeTab: 'questions' }">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.exams.index') }}">{{ __('instructor.exams') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ $exam->title }}</strong>
            </nav>
            <h1 class="su-page-head__title">{{ $exam->title }}</h1>
            <p class="su-page-head__sub">{{ __('instructor.exam_details_subtitle') }}</p>
            <div class="su-chip-row">
                <span class="su-chip {{ $exam->is_active ? 'su-chip--ok' : 'su-chip--warn' }}">
                    <i class="fas {{ $exam->is_active ? 'fa-check-circle' : 'fa-ban' }}" aria-hidden="true"></i>
                    {{ $exam->is_active ? __('instructor.active') : __('instructor.inactive') }}
                </span>
            </div>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.exams.questions.manage', $exam) }}" class="su-btn su-btn--primary">
                <i class="fas fa-cogs" aria-hidden="true"></i>
                {{ __('instructor.manage_questions') }}
            </a>
            <a href="{{ route('instructor.exams.edit', $exam) }}" class="su-btn">
                <i class="fas fa-edit" aria-hidden="true"></i>
                {{ __('common.edit') }}
            </a>
            <a href="{{ route('instructor.exams.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.questions_count') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($exam->questions->count()) }}</div>
                <div class="su-kpi__d"><i class="fas fa-question-circle" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.attempts_count') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($attemptStats['total'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-users" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.completed_count') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($attemptStats['completed'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-check-double" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.average_score_label') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($attemptStats['average_score'] ?? 0, 1) }}</div>
                <div class="su-kpi__d"><i class="fas fa-star" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <section class="su-card" style="margin-bottom:16px">
        <div class="su-section-head" style="margin:0 0 16px">
            <h3>{{ __('instructor.exam_info') }}</h3>
        </div>
        <div class="su-dl" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div class="su-dl__item">
                <label>{{ __('instructor.title') }}</label>
                <div>{{ $exam->title }}</div>
            </div>
            <div class="su-dl__item">
                <label>{{ __('instructor.course_label') }}</label>
                <div>{{ $exam->advancedCourse->title ?? '—' }}</div>
                @if($exam->advancedCourse && $exam->advancedCourse->academicSubject)
                    <div style="font-size:12px;color:var(--su-ink-40)">{{ $exam->advancedCourse->academicSubject->name }}</div>
                @endif
            </div>
            @if($exam->lesson)
                <div class="su-dl__item">
                    <label>{{ __('instructor.lesson_label') }}</label>
                    <div>{{ $exam->lesson->title }}</div>
                </div>
            @endif
            <div class="su-dl__item">
                <label>{{ __('instructor.duration_minutes') }}</label>
                <div class="tabular-nums">{{ $exam->duration_minutes }} {{ __('instructor.minute_unit') }}</div>
            </div>
            <div class="su-dl__item">
                <label>{{ __('instructor.total_score_label') }}</label>
                <div class="tabular-nums">{{ $exam->total_marks }} {{ __('instructor.point_unit') }}</div>
            </div>
            <div class="su-dl__item">
                <label>{{ __('instructor.passing_marks_label') }}</label>
                <div class="tabular-nums">{{ $exam->passing_marks }} {{ __('instructor.point_unit') }}</div>
            </div>
            <div class="su-dl__item">
                <label>{{ __('instructor.attempts_allowed_label') }}</label>
                <div>{{ $exam->attempts_allowed == 0 ? __('instructor.unlimited') : $exam->attempts_allowed }}</div>
            </div>
        </div>
        @if($exam->description)
            <div class="su-prose-box" style="margin-top:16px">
                <label>{{ __('instructor.description') }}</label>
                <div class="su-prose-body">{{ $exam->description }}</div>
            </div>
        @endif
        @if($exam->instructions)
            <div class="su-prose-box" style="margin-top:16px">
                <label>{{ __('instructor.instructions_label') }}</label>
                <div class="su-prose-body" style="white-space:pre-wrap">{{ $exam->instructions }}</div>
            </div>
        @endif
    </section>

    <section class="su-card" style="padding:16px">
        <div class="su-tabs-bar" role="tablist">
            <button type="button" class="su-tab" :class="{ 'is-on': activeTab === 'questions' }" @click="activeTab = 'questions'">
                <i class="fas fa-question-circle" aria-hidden="true"></i> {{ __('instructor.questions_tab') }} ({{ $exam->questions->count() }})
            </button>
            <button type="button" class="su-tab" :class="{ 'is-on': activeTab === 'attempts' }" @click="activeTab = 'attempts'">
                <i class="fas fa-users" aria-hidden="true"></i> {{ __('instructor.attempts_tab') }} ({{ $attempts->total() }})
            </button>
            <button type="button" class="su-tab" :class="{ 'is-on': activeTab === 'settings' }" @click="activeTab = 'settings'">
                <i class="fas fa-cogs" aria-hidden="true"></i> {{ __('instructor.settings_tab') }}
            </button>
        </div>

        <div style="padding:16px 4px 4px">
            <div x-show="activeTab === 'questions'" x-cloak>
                <div class="su-section-head" style="margin:0 0 12px">
                    <h3>{{ __('instructor.exam_questions_title') }}</h3>
                    <a href="{{ route('instructor.exams.questions.manage', $exam) }}" class="su-btn" style="height:32px">
                        <i class="fas fa-cogs" aria-hidden="true"></i>
                        {{ __('instructor.manage_questions') }}
                    </a>
                </div>
                @if($exam->questions->count() > 0)
                    <div class="su-list">
                        @foreach($exam->questions as $index => $question)
                            <article class="su-list-item">
                                <span class="su-list-item__ico su-soft-1" style="font-weight:700">{{ $index + 1 }}</span>
                                <div class="su-list-item__body">
                                    <div class="su-list-item__title" style="font-size:14px">{{ Str::limit($question->question, 80) }}</div>
                                    <div class="su-list-item__meta">
                                        {{ $question->pivot->marks ?? 1 }} {{ __('instructor.point_unit') }}
                                        @if($question->type) · {{ $question->type }} @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="su-empty">
                        <i class="fas fa-question-circle" aria-hidden="true"></i>
                        <p><strong>{{ __('instructor.no_questions') }}</strong></p>
                        <p>{{ __('instructor.add_questions_hint') }}</p>
                        <a href="{{ route('instructor.exams.questions.manage', $exam) }}" class="su-btn su-btn--primary" style="margin-top:12px">
                            <i class="fas fa-cogs" aria-hidden="true"></i>
                            {{ __('instructor.manage_questions') }}
                        </a>
                    </div>
                @endif
            </div>

            <div x-show="activeTab === 'attempts'" x-cloak>
                <div class="su-section-head" style="margin:0 0 12px">
                    <h3>{{ __('instructor.student_attempts_title') }}</h3>
                </div>
                @if($attempts->count() > 0)
                    <div class="su-table-wrap">
                        <table class="su-table">
                            <thead>
                                <tr>
                                    <th>{{ __('instructor.students') }}</th>
                                    <th>{{ __('instructor.result_label') }}</th>
                                    <th>{{ __('common.status') }}</th>
                                    <th>{{ __('common.date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attempts as $attempt)
                                    <tr>
                                        <td>
                                            <strong style="font-weight:600">{{ $attempt->user->name ?? '—' }}</strong>
                                            <div style="font-size:12px;color:var(--su-ink-40)">{{ $attempt->user->email ?? '—' }}</div>
                                        </td>
                                        <td class="tabular-nums">
                                            @if($attempt->status === 'completed' && $attempt->score !== null)
                                                <strong>{{ number_format($attempt->score, 1) }} / {{ $exam->total_marks }}</strong>
                                                <div style="font-size:12px;color:var(--su-ink-40)">{{ number_format(($attempt->score / max($exam->total_marks, 1)) * 100, 1) }}%</div>
                                            @else
                                                <span style="color:var(--su-ink-40)">{{ __('instructor.not_completed') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $chip = match ($attempt->status) {
                                                    'completed' => 'su-chip--ok',
                                                    'in_progress' => 'su-chip--warn',
                                                    default => 'su-chip--off',
                                                };
                                                $label = match ($attempt->status) {
                                                    'completed' => __('instructor.completed_status'),
                                                    'in_progress' => __('instructor.in_progress_status'),
                                                    default => __('instructor.not_completed'),
                                                };
                                            @endphp
                                            <span class="su-chip {{ $chip }}">{{ $label }}</span>
                                        </td>
                                        <td class="tabular-nums" style="color:var(--su-ink-40)">
                                            {{ $attempt->submitted_at ? $attempt->submitted_at->format('Y-m-d H:i') : $attempt->created_at->format('Y-m-d H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(method_exists($attempts, 'links') && $attempts->hasPages())
                        <div class="su-pager" style="margin-top:12px">{{ $attempts->links() }}</div>
                    @endif
                @else
                    <div class="su-empty">
                        <i class="fas fa-users" aria-hidden="true"></i>
                        <p><strong>{{ __('instructor.no_attempts') }}</strong></p>
                        <p>{{ __('instructor.no_attempts_desc') }}</p>
                    </div>
                @endif
            </div>

            <div x-show="activeTab === 'settings'" x-cloak>
                <div class="su-section-head" style="margin:0 0 12px">
                    <h3>{{ __('instructor.exam_settings_title') }}</h3>
                </div>
                <div class="su-meta-list">
                    @foreach([
                        ['randomize_questions', __('instructor.randomize_questions')],
                        ['randomize_options', __('instructor.randomize_options')],
                        ['show_results_immediately', __('instructor.show_results_immediately')],
                        ['show_correct_answers', __('instructor.show_correct_answers')],
                        ['show_explanations', __('instructor.show_explanations')],
                        ['allow_review', __('instructor.allow_review')],
                    ] as $item)
                        @php $attr = $item[0]; $name = $item[1]; @endphp
                        <div class="su-meta-row" style="justify-content:space-between">
                            <span>{{ $name }}</span>
                            <span class="su-chip {{ $exam->$attr ? 'su-chip--ok' : 'su-chip--off' }}">
                                {{ $exam->$attr ? __('instructor.enabled') : __('instructor.inactive') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
