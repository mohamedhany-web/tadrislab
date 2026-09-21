@extends('layouts.instructor-timeline')

@section('title', __('instructor.create_task') . ' - ' . config('app.name'))
@section('page_title', __('instructor.create_task'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page" style="max-width:56rem">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.tasks.index') }}">{{ __('instructor.tasks_from_management') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('instructor.create_task') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-plus su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.create_task') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.create_task_desc') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.tasks.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <section class="su-card">
        <form action="{{ route('instructor.tasks.store') }}" method="POST">
            @csrf
            <div class="su-form-grid" style="grid-template-columns:1fr 1fr">
                <div class="su-field" style="grid-column:1 / -1">
                    <label for="title">{{ __('instructor.task_title_required') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="su-input"
                           placeholder="{{ __('instructor.task_title_placeholder') }}">
                    @error('title')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field" style="grid-column:1 / -1">
                    <label for="description">{{ __('instructor.description') }}</label>
                    <textarea name="description" id="description" rows="4" class="su-input" style="min-height:100px;resize:vertical"
                              placeholder="{{ __('instructor.task_description_placeholder') }}">{{ old('description') }}</textarea>
                    @error('description')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field">
                    <label for="priority">{{ __('instructor.priority') }} <span style="color:#b91c1c">*</span></label>
                    <select name="priority" id="priority" required class="su-select">
                        <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>{{ __('instructor.low') }}</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>{{ __('instructor.medium') }}</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>{{ __('instructor.high') }}</option>
                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>{{ __('instructor.urgent') }}</option>
                    </select>
                    @error('priority')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field">
                    <label for="due_date">{{ __('instructor.due_date') }}</label>
                    <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date') }}" class="su-input">
                    @error('due_date')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field">
                    <label for="related_course_id">{{ __('instructor.course_optional') }}</label>
                    <select name="related_course_id" id="related_course_id" class="su-select">
                        <option value="">{{ __('instructor.choose_course') }}</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('related_course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                    @error('related_course_id')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field">
                    <label for="related_lecture_id">{{ __('instructor.lecture_optional') }}</label>
                    <select name="related_lecture_id" id="related_lecture_id" class="su-select">
                        <option value="">{{ __('instructor.choose_lecture') }}</option>
                        @foreach($lectures as $lecture)
                            <option value="{{ $lecture->id }}" {{ old('related_lecture_id') == $lecture->id ? 'selected' : '' }}>
                                {{ $lecture->title }} - {{ $lecture->scheduled_at->format('Y/m/d') }}
                            </option>
                        @endforeach
                    </select>
                    @error('related_lecture_id')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="su-card" style="margin:16px 0;padding:12px 16px;background:var(--su-soft-1,rgba(59,130,246,.08));border-color:transparent">
                <span style="font-size:13px;font-weight:600">{{ __('instructor.priority_preview') }}:</span>
                <span id="priority-preview" class="su-chip" style="margin-inline-start:8px">{{ __('instructor.medium') }}</span>
            </div>

            <div class="su-form-actions" style="justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid var(--su-line,rgba(0,0,0,.06))">
                <a href="{{ route('instructor.tasks.index') }}" class="su-btn">{{ __('common.cancel') }}</a>
                <button type="submit" class="su-btn su-btn--primary">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    {{ __('instructor.save_task') }}
                </button>
            </div>
        </form>
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('related_course_id');
    const lectureSelect = document.getElementById('related_lecture_id');
    const prioritySelect = document.getElementById('priority');
    const priorityPreview = document.getElementById('priority-preview');
    const labels = {
        low: @json(__('instructor.low')),
        medium: @json(__('instructor.medium')),
        high: @json(__('instructor.high')),
        urgent: @json(__('instructor.urgent')),
    };
    const chooseLecture = @json(__('instructor.choose_lecture'));

    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        lectureSelect.innerHTML = '<option value="">' + chooseLecture + '</option>';
        if (courseId) {
            fetch(`{{ route('instructor.tasks.lectures') }}?course_id=${courseId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                data.forEach(lecture => {
                    const option = document.createElement('option');
                    option.value = lecture.id;
                    option.textContent = `${lecture.title} - ${new Date(lecture.scheduled_at).toLocaleDateString()}`;
                    lectureSelect.appendChild(option);
                });
            })
            .catch(() => {});
        }
    });

    function updatePriorityPreview() {
        const priority = prioritySelect.value;
        priorityPreview.textContent = labels[priority] || labels.medium;
        priorityPreview.className = 'su-chip ' + (
            priority === 'urgent' ? 'su-chip--off' :
            priority === 'high' ? 'su-chip--warn' :
            priority === 'medium' ? 'su-soft-1' : ''
        );
    }
    prioritySelect.addEventListener('change', updatePriorityPreview);
    updatePriorityPreview();
});
</script>
@endpush
@endsection
