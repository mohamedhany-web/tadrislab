@extends('layouts.instructor-timeline')

@section('title', __('instructor.edit_task') . ' - ' . $task->title)
@section('page_title', __('instructor.edit_task'))

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
                <a href="{{ route('instructor.tasks.show', $task) }}">{{ Str::limit($task->title, 40) }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('common.edit') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-edit su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.edit_task') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.update_status_or_details') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.tasks.show', $task) }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <section class="su-card">
        <form action="{{ route('instructor.tasks.update', $task) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="su-form-grid" style="grid-template-columns:1fr 1fr">
                <div class="su-field" style="grid-column:1 / -1">
                    <label>{{ __('instructor.task_title_required') }}</label>
                    <input type="text" name="title" value="{{ old('title', $task->title) }}" required class="su-input">
                    @error('title')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field" style="grid-column:1 / -1">
                    <label>{{ __('instructor.description') }}</label>
                    <textarea name="description" rows="4" class="su-input" style="min-height:100px;resize:vertical">{{ old('description', $task->description) }}</textarea>
                    @error('description')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field">
                    <label>{{ __('instructor.priority') }}</label>
                    <select name="priority" class="su-select">
                        <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>{{ __('instructor.low') }}</option>
                        <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>{{ __('instructor.medium') }}</option>
                        <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>{{ __('instructor.high') }}</option>
                        <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>{{ __('instructor.urgent') }}</option>
                    </select>
                </div>
                <div class="su-field">
                    <label>{{ __('common.status') }}</label>
                    <select name="status" class="su-select">
                        <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>{{ __('instructor.pending') }}</option>
                        <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>{{ __('instructor.in_progress') }}</option>
                        <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>{{ __('instructor.completed') }}</option>
                        <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>{{ __('instructor.cancelled_lecture') }}</option>
                    </select>
                </div>
                <div class="su-field" style="grid-column:1 / -1">
                    <label>{{ __('instructor.due_date') }}</label>
                    <input type="datetime-local" name="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '') }}" class="su-input">
                    @error('due_date')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field">
                    <label>{{ __('instructor.course_optional') }}</label>
                    <select name="related_course_id" id="related_course_id" class="su-select">
                        <option value="">{{ __('instructor.none_option') }}</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('related_course_id', $task->related_course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="su-field">
                    <label>{{ __('instructor.lecture_optional') }}</label>
                    <select name="related_lecture_id" id="related_lecture_id" class="su-select">
                        <option value="">{{ __('instructor.none_option') }}</option>
                        @foreach($lectures as $lecture)
                            <option value="{{ $lecture->id }}" {{ old('related_lecture_id', $task->related_lecture_id) == $lecture->id ? 'selected' : '' }}>
                                {{ $lecture->title }} @if($lecture->scheduled_at) - {{ $lecture->scheduled_at->format('Y/m/d') }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="su-form-actions" style="margin-top:16px;padding-top:16px;border-top:1px solid var(--su-line,rgba(0,0,0,.06));justify-content:flex-end;gap:8px">
                <a href="{{ route('instructor.tasks.show', $task) }}" class="su-btn">{{ __('common.cancel') }}</a>
                <button type="submit" class="su-btn su-btn--primary">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    {{ __('instructor.save_changes') }}
                </button>
            </div>
        </form>
    </section>
</div>

@push('scripts')
<script>
document.getElementById('related_course_id').addEventListener('change', function() {
    const courseId = this.value;
    const lectureSelect = document.getElementById('related_lecture_id');
    const noneLabel = @json(__('instructor.none_option'));
    lectureSelect.innerHTML = '<option value="">' + noneLabel + '</option>';
    if (courseId) {
        fetch(`{{ route('instructor.tasks.lectures') }}?course_id=${courseId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            data.forEach(lecture => {
                const opt = document.createElement('option');
                opt.value = lecture.id;
                opt.textContent = lecture.title + (lecture.scheduled_at ? ' - ' + lecture.scheduled_at : '');
                lectureSelect.appendChild(opt);
            });
        })
        .catch(() => {});
    }
});
</script>
@endpush
@endsection
