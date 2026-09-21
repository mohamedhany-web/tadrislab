@extends('layouts.instructor-timeline')

@section('title', __('instructor.create_exam_new') . ' - ' . config('app.name'))
@section('page_title', __('instructor.create_exam_new'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page" style="max-width:56rem">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.exams.index') }}">{{ __('instructor.my_exams') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('instructor.create_exam_new') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-clipboard-list su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.create_exam_new') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.add_exam_to_course') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.exams.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <form action="{{ route('instructor.exams.store') }}" method="POST" class="su-card">
        @csrf

        <div class="su-section-head" style="margin:0 0 16px">
            <h3><i class="fas fa-info-circle" aria-hidden="true"></i> {{ __('instructor.exam_info') }}</h3>
        </div>
        <div class="su-form-grid" style="grid-template-columns:1fr 1fr;margin-bottom:24px">
            <div class="su-field" style="grid-column:1 / -1">
                <label for="title">{{ __('instructor.title') }} <span style="color:#b91c1c">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required class="su-input"
                       placeholder="{{ __('instructor.exam_title_placeholder') }}">
                @error('title')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
            <div class="su-field">
                <label for="advanced_course_id">{{ __('instructor.online_course') }} <span style="color:#b91c1c">*</span></label>
                <select name="advanced_course_id" id="advanced_course_id" onchange="loadLessons()" class="su-select">
                    <option value="">{{ __('instructor.choose_course') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('advanced_course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
                @error('advanced_course_id')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
            <div class="su-field" id="lesson-wrap">
                <label for="course_lesson_id">{{ __('instructor.lesson_optional') }}</label>
                <select name="course_lesson_id" id="course_lesson_id" class="su-select">
                    <option value="">{{ __('instructor.general_exam') }}</option>
                </select>
            </div>
            <div class="su-field" style="grid-column:1 / -1">
                <label for="description">{{ __('instructor.description') }}</label>
                <textarea name="description" id="description" rows="3" class="su-input" style="min-height:88px;resize:vertical" placeholder="{{ __('instructor.description_placeholder') }}">{{ old('description') }}</textarea>
            </div>
            <div class="su-field" style="grid-column:1 / -1">
                <label for="instructions">{{ __('instructor.exam_instructions') }}</label>
                <textarea name="instructions" id="instructions" rows="4" class="su-input" style="min-height:110px;resize:vertical" placeholder="{{ __('instructor.instructions_placeholder') }}">{{ old('instructions') }}</textarea>
            </div>
        </div>

        <div class="su-section-head" style="margin:0 0 16px;border-top:1px solid var(--su-line);padding-top:16px">
            <h3><i class="fas fa-clock" aria-hidden="true"></i> {{ __('instructor.time_and_marks') }}</h3>
        </div>
        <div class="su-form-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px">
            <div class="su-field">
                <label for="duration_minutes">{{ __('instructor.duration_minutes') }} ({{ __('instructor.minute_unit') }}) <span style="color:#b91c1c">*</span></label>
                <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="5" max="480" required class="su-input">
            </div>
            <div class="su-field">
                <label for="total_marks">{{ __('instructor.total_marks') }} <span style="color:#b91c1c">*</span></label>
                <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', 100) }}" min="1" required class="su-input">
            </div>
            <div class="su-field">
                <label for="passing_marks">{{ __('instructor.passing_marks') }} <span style="color:#b91c1c">*</span></label>
                <input type="number" name="passing_marks" id="passing_marks" value="{{ old('passing_marks', 60) }}" min="0" step="0.5" required class="su-input">
            </div>
            <div class="su-field">
                <label for="attempts_allowed">{{ __('instructor.attempts_allowed') }} <span style="color:#b91c1c">*</span></label>
                <input type="number" name="attempts_allowed" id="attempts_allowed" value="{{ old('attempts_allowed', 1) }}" min="1" max="10" required class="su-input">
            </div>
            <div class="su-field">
                <label for="start_time">{{ __('instructor.start_time') }}</label>
                <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}" class="su-input">
            </div>
            <div class="su-field">
                <label for="end_time">{{ __('instructor.end_time') }}</label>
                <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}" class="su-input">
            </div>
        </div>

        <div class="su-section-head" style="margin:0 0 16px;border-top:1px solid var(--su-line);padding-top:16px">
            <h3><i class="fas fa-sliders-h" aria-hidden="true"></i> {{ __('instructor.display_settings') }}</h3>
        </div>
        <div class="su-chip-row" style="margin-bottom:16px;flex-wrap:wrap;gap:10px">
            @foreach([
                ['randomize_questions', __('instructor.randomize_questions'), false],
                ['randomize_options', __('instructor.randomize_options'), false],
                ['show_results_immediately', __('instructor.show_results_immediately'), true],
                ['show_correct_answers', __('instructor.show_correct_answers'), false],
                ['show_explanations', __('instructor.show_explanations'), false],
                ['allow_review', __('instructor.allow_review'), true],
                ['is_active', __('instructor.exam_active'), true],
                ['show_in_sidebar', __('instructor.show_in_sidebar'), true],
            ] as $opt)
                <label class="su-chip" style="cursor:pointer;height:auto;padding:10px 12px">
                    <input type="checkbox" name="{{ $opt[0] }}" value="1" {{ old($opt[0], $opt[2]) ? 'checked' : '' }} style="margin-inline-end:8px">
                    {{ $opt[1] }}
                </label>
            @endforeach
        </div>
        <div class="su-field" style="max-width:12rem;margin-bottom:20px">
            <label for="sidebar_position">{{ __('instructor.sidebar_position') }}</label>
            <input type="number" name="sidebar_position" id="sidebar_position" value="{{ old('sidebar_position', 1) }}" min="1" max="10" class="su-input">
        </div>

        <div class="su-page-head__actions" style="justify-content:space-between;border-top:1px solid var(--su-line);padding-top:16px;flex-wrap:wrap;gap:12px">
            <div class="su-chip su-soft-1" style="height:auto;padding:10px 14px;max-width:28rem">
                <strong>{{ __('instructor.tips') }}:</strong> {{ __('instructor.tip_after_create_exam') }}
            </div>
            <div style="display:flex;gap:8px">
                <a href="{{ route('instructor.exams.index') }}" class="su-btn">{{ __('common.cancel') }}</a>
                <button type="submit" class="su-btn su-btn--primary">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    {{ __('instructor.create_exam_btn') }}
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function loadLessons() {
    const courseId = document.getElementById('advanced_course_id').value;
    const lessonSelect = document.getElementById('course_lesson_id');
    lessonSelect.innerHTML = '<option value="">' + @json(__('instructor.general_exam')) + '</option>';
    if (courseId) {
        fetch(`/instructor/api/courses/${courseId}/lessons-list`)
            .then(response => response.json())
            .then(lessons => {
                (Array.isArray(lessons) ? lessons : []).forEach(lesson => {
                    const option = document.createElement('option');
                    option.value = lesson.id;
                    option.textContent = lesson.title || ('درس ' + (lesson.order || ''));
                    lessonSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading lessons:', error));
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const courseId = document.getElementById('advanced_course_id').value;
    if (courseId) loadLessons();
});
</script>
@endpush
@endsection
