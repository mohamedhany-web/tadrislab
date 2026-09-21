@extends('layouts.instructor-timeline')

@section('title', __('instructor.edit_exam'))
@section('page_title', __('instructor.edit_exam'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.exams.index') }}">{{ __('instructor.my_exams') }}</a>
                <span>/</span>
                <a href="{{ route('instructor.exams.show', $exam) }}">{{ Str::limit($exam->title, 40) }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('common.edit') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-edit su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.edit_exam') }}
            </h1>
            <p class="su-page-head__sub">{{ $exam->title }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.exams.show', $exam) }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <form action="{{ route('instructor.exams.update', $exam) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="su-detail-grid">
            <div style="display:flex;flex-direction:column;gap:16px;min-width:0">
                <section class="su-card">
                    <h2 class="su-card__title"><i class="fas fa-info-circle" aria-hidden="true"></i> {{ __('instructor.exam_info') }}</h2>
                    <div class="su-form-grid" style="grid-template-columns:1fr 1fr">
                        <div class="su-field" style="grid-column:1 / -1">
                            <label for="title">{{ __('instructor.title') }} <span style="color:#b91c1c">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $exam->title) }}" required class="su-input"
                                   placeholder="{{ __('instructor.exam_title_placeholder') }}">
                            @error('title')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field">
                            <label for="advanced_course_id">{{ __('instructor.course_label') }}</label>
                            <select name="advanced_course_id" id="advanced_course_id" onchange="loadLessons()" class="su-select">
                                <option value="">{{ __('instructor.choose_course') }}</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('advanced_course_id', $exam->advanced_course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                                @endforeach
                            </select>
                            @error('advanced_course_id')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field">
                            <label for="course_lesson_id">{{ __('instructor.lesson_optional') }}</label>
                            <select name="course_lesson_id" id="course_lesson_id" class="su-select">
                                <option value="">{{ __('instructor.general_exam') }}</option>
                                @foreach($lessons as $lesson)
                                    <option value="{{ $lesson->id }}" {{ old('course_lesson_id', $exam->course_lesson_id) == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="su-field" style="grid-column:1 / -1">
                            <label for="description">{{ __('instructor.description') }}</label>
                            <textarea name="description" id="description" rows="3" class="su-input" style="min-height:88px;resize:vertical">{{ old('description', $exam->description) }}</textarea>
                        </div>
                        <div class="su-field" style="grid-column:1 / -1">
                            <label for="instructions">{{ __('instructor.exam_instructions') }}</label>
                            <textarea name="instructions" id="instructions" rows="4" class="su-input" style="min-height:110px;resize:vertical">{{ old('instructions', $exam->instructions) }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="su-card">
                    <h2 class="su-card__title"><i class="fas fa-clock" aria-hidden="true"></i> {{ __('instructor.time_and_marks') }}</h2>
                    <div class="su-form-grid" style="grid-template-columns:1fr 1fr 1fr">
                        <div class="su-field">
                            <label for="duration_minutes">{{ __('instructor.duration_minutes') }} ({{ __('instructor.minute_unit') }}) <span style="color:#b91c1c">*</span></label>
                            <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="5" max="480" required class="su-input">
                        </div>
                        <div class="su-field">
                            <label for="total_marks">{{ __('instructor.total_marks') }} <span style="color:#b91c1c">*</span></label>
                            <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" min="1" required class="su-input">
                        </div>
                        <div class="su-field">
                            <label for="passing_marks">{{ __('instructor.passing_marks') }} <span style="color:#b91c1c">*</span></label>
                            <input type="number" name="passing_marks" id="passing_marks" value="{{ old('passing_marks', $exam->passing_marks) }}" min="0" step="0.5" required class="su-input">
                        </div>
                        <div class="su-field">
                            <label for="attempts_allowed">{{ __('instructor.attempts_allowed') }} <span style="color:#b91c1c">*</span></label>
                            <input type="number" name="attempts_allowed" id="attempts_allowed" value="{{ old('attempts_allowed', $exam->attempts_allowed) }}" min="1" max="10" required class="su-input">
                            <p style="margin:6px 0 0;font-size:12px;color:var(--su-ink-40)">1–10</p>
                        </div>
                        <div class="su-field">
                            <label for="start_time">{{ __('instructor.start_time') }}</label>
                            <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}" class="su-input">
                        </div>
                        <div class="su-field">
                            <label for="end_time">{{ __('instructor.end_time') }}</label>
                            <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}" class="su-input">
                        </div>
                    </div>
                </section>

                <section class="su-card">
                    <h2 class="su-card__title"><i class="fas fa-sliders-h" aria-hidden="true"></i> {{ __('instructor.display_settings') }}</h2>
                    <div class="su-chip-row" style="flex-wrap:wrap;gap:10px;margin-bottom:16px">
                        @foreach([
                            'randomize_questions' => __('instructor.randomize_questions'),
                            'randomize_options' => __('instructor.randomize_options'),
                            'show_results_immediately' => __('instructor.show_results_immediately'),
                            'show_correct_answers' => __('instructor.show_correct_answers'),
                            'show_explanations' => __('instructor.show_explanations'),
                            'allow_review' => __('instructor.allow_review'),
                            'is_active' => __('instructor.exam_active'),
                            'show_in_sidebar' => __('instructor.show_in_sidebar'),
                        ] as $name => $label)
                            <label class="su-chip" style="cursor:pointer;height:auto;padding:10px 12px">
                                <input type="checkbox" name="{{ $name }}" value="1" {{ old($name, $exam->$name) ? 'checked' : '' }} style="margin-inline-end:8px">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <div class="su-field" style="max-width:12rem">
                        <label for="sidebar_position">{{ __('instructor.sidebar_position') }}</label>
                        <input type="number" name="sidebar_position" id="sidebar_position" value="{{ old('sidebar_position', $exam->sidebar_position ?? 1) }}" min="1" max="10" class="su-input">
                    </div>
                </section>
            </div>

            <aside style="display:flex;flex-direction:column;gap:16px;min-width:0">
                <div class="su-card">
                    <h2 class="su-card__title"><i class="fas fa-lightbulb" aria-hidden="true"></i> {{ __('instructor.tips') }}</h2>
                    <ul class="su-meta-list" style="font-size:13px;color:var(--su-ink-40)">
                        <li>{{ __('instructor.edit_exam_tip_1') }}</li>
                        <li>{{ __('instructor.edit_exam_tip_2') }}</li>
                        <li>{{ __('instructor.edit_exam_tip_3') }}</li>
                    </ul>
                </div>
                <div class="su-card" style="display:flex;flex-direction:column;gap:8px">
                    <button type="submit" class="su-btn su-btn--primary" style="justify-content:center">
                        <i class="fas fa-save" aria-hidden="true"></i>
                        {{ __('instructor.save_changes') }}
                    </button>
                    <a href="{{ route('instructor.exams.show', $exam) }}" class="su-btn" style="justify-content:center">{{ __('common.cancel') }}</a>
                </div>
            </aside>
        </div>
    </form>
</div>

@push('scripts')
<script>
function loadLessons() {
    var courseId = document.getElementById('advanced_course_id').value;
    var lessonSelect = document.getElementById('course_lesson_id');
    lessonSelect.innerHTML = '<option value="">' + @json(__('instructor.general_exam')) + '</option>';
    if (courseId) {
        fetch('/instructor/api/courses/' + courseId + '/lessons-list')
            .then(function(r) { return r.json(); })
            .then(function(lessons) {
                (Array.isArray(lessons) ? lessons : []).forEach(function(lesson) {
                    var option = document.createElement('option');
                    option.value = lesson.id;
                    option.textContent = lesson.title;
                    @if($exam->course_lesson_id)
                    if (lesson.id == {{ $exam->course_lesson_id }}) option.selected = true;
                    @endif
                    lessonSelect.appendChild(option);
                });
            })
            .catch(function(e) { console.error('Error loading lessons:', e); });
    }
}
document.addEventListener('DOMContentLoaded', function() {
    var courseId = document.getElementById('advanced_course_id').value;
    if (courseId) loadLessons();
});
</script>
@endpush
@endsection
