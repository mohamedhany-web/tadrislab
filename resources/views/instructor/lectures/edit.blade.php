@extends('layouts.instructor-timeline')

@section('title', __('instructor.edit_lecture') . ' - ' . $lecture->title)
@section('page_title', __('instructor.edit_lecture'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page" style="max-width:56rem">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.lectures.index') }}">{{ __('instructor.lectures') }}</a>
                <span>/</span>
                <a href="{{ route('instructor.lectures.show', $lecture) }}">{{ Str::limit($lecture->title, 40) }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('common.edit') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-edit su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.edit_lecture') }}
            </h1>
            <p class="su-page-head__sub">{{ $lecture->title }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.lectures.show', $lecture) }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <form action="{{ route('instructor.lectures.update', $lecture) }}" method="POST" enctype="multipart/form-data" class="su-card">
        @csrf
        @method('PUT')

        <div class="su-form-grid" style="grid-template-columns:1fr 1fr 1fr;margin-bottom:16px">
            <div class="su-field">
                <label for="course_id">{{ __('instructor.course_label') }} <span style="color:#b91c1c">*</span></label>
                <select name="course_id" id="course_id" required class="su-select">
                    <option value="">{{ __('instructor.choose_course') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $lecture->course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
                @error('course_id')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
            <div class="su-field">
                <label for="course_lesson_id">{{ __('instructor.lesson_optional') }}</label>
                <select name="course_lesson_id" id="course_lesson_id" class="su-select">
                    <option value="">{{ __('instructor.no_lesson') }}</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" {{ old('course_lesson_id', $lecture->course_lesson_id) == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                    @endforeach
                </select>
                <p style="margin:6px 0 0;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.lesson_link_hint') }}</p>
                @error('course_lesson_id')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
            <div class="su-field">
                <label for="title">{{ __('instructor.lecture_title') }} <span style="color:#b91c1c">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $lecture->title) }}" required class="su-input">
                @error('title')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="su-field" style="margin-bottom:16px">
            <label for="description">{{ __('instructor.description') }}</label>
            <textarea name="description" id="description" rows="3" class="su-input" style="min-height:88px;resize:vertical">{{ old('description', $lecture->description) }}</textarea>
            @error('description')<p class="su-field-error">{{ $message }}</p>@enderror
        </div>

        <div class="su-form-grid" style="grid-template-columns:1fr 1fr;margin-bottom:16px">
            @include('partials.timezone-select', ['value' => old('timezone', auth()->user()?->timezoneCode())])
            <div class="su-field">
                <label for="scheduled_at">{{ __('instructor.date_time') }} <span style="color:#b91c1c">*</span></label>
                <input type="datetime-local" name="scheduled_at" id="scheduled_at"
                       value="{{ old('scheduled_at', \App\Support\AppTimezone::datetimeLocalValue($lecture->scheduled_at, old('timezone', auth()->user()?->timezoneCode()))) }}"
                       required class="su-input">
                @error('scheduled_at')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
            <div class="su-field">
                <label for="duration_minutes">{{ __('instructor.duration_minutes_label') }} <span style="color:#b91c1c">*</span></label>
                <input type="number" name="duration_minutes" id="duration_minutes"
                       value="{{ old('duration_minutes', $lecture->duration_minutes) }}" min="15" max="480" required class="su-input">
                @error('duration_minutes')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
            <div class="su-field">
                <label for="min_watch_percent_to_unlock_next">{{ __('instructor.min_watch_percent') }}</label>
                <input type="number" name="min_watch_percent_to_unlock_next" id="min_watch_percent_to_unlock_next"
                       value="{{ old('min_watch_percent_to_unlock_next', $lecture->min_watch_percent_to_unlock_next ?? 0) }}" min="0" max="100" class="su-input"
                       placeholder="{{ __('instructor.min_watch_percent_ph') }}">
                <p style="margin:6px 0 0;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.min_watch_percent_hint') }}</p>
            </div>
        </div>

        <div class="su-field" style="margin-bottom:16px">
            <label for="status">{{ __('common.status') }} <span style="color:#b91c1c">*</span></label>
            <select name="status" id="status" required class="su-select">
                <option value="scheduled" {{ old('status', $lecture->status) == 'scheduled' ? 'selected' : '' }}>{{ __('instructor.scheduled_lecture') }}</option>
                <option value="in_progress" {{ old('status', $lecture->status) == 'in_progress' ? 'selected' : '' }}>{{ __('instructor.in_progress_status') }}</option>
                <option value="completed" {{ old('status', $lecture->status) == 'completed' ? 'selected' : '' }}>{{ __('instructor.completed_status') }}</option>
                <option value="cancelled" {{ old('status', $lecture->status) == 'cancelled' ? 'selected' : '' }}>{{ __('instructor.cancelled_lecture') }}</option>
            </select>
            @error('status')<p class="su-field-error">{{ $message }}</p>@enderror
        </div>

        <div class="su-field" style="margin-bottom:16px">
            <label for="recording_url">{{ __('instructor.recording_link_section') }}</label>
            <input type="url" name="recording_url" id="recording_url" value="{{ old('recording_url', $lecture->recording_url) }}" class="su-input">
            @error('recording_url')<p class="su-field-error">{{ $message }}</p>@enderror
        </div>

        @php $lecture->load('materials'); @endphp
        <div class="su-field" style="margin-bottom:16px">
            <label>{{ __('instructor.lecture_materials') }}</label>
            @foreach($lecture->materials as $mat)
                <div class="su-chip-row" style="justify-content:space-between;padding:12px;border:1px solid var(--su-line);border-radius:12px;margin:0 0 8px">
                    <div class="min-w-0">
                        <strong style="font-weight:600">{{ $mat->title ?: $mat->file_name }}</strong>
                        <div style="font-size:12px;color:var(--su-ink-40)">{{ $mat->file_name }}</div>
                    </div>
                    <div class="su-chip-row" style="margin:0">
                        <label class="su-chip" style="cursor:pointer">
                            <input type="hidden" name="material_visible_old[{{ $mat->id }}]" value="0">
                            <input type="checkbox" name="material_visible_old[{{ $mat->id }}]" value="1" {{ $mat->is_visible_to_student ? 'checked' : '' }} style="margin-inline-end:6px">
                            {{ __('instructor.visible_to_student') }}
                        </label>
                        <label class="su-chip su-chip--off" style="cursor:pointer">
                            <input type="checkbox" name="material_delete_old[]" value="{{ $mat->id }}" style="margin-inline-end:6px">
                            {{ __('common.delete') }}
                        </label>
                    </div>
                </div>
            @endforeach
            <div id="edit-materials-new" style="display:flex;flex-direction:column;gap:8px;margin-bottom:8px"></div>
            <button type="button" id="edit-add-material" class="su-btn">
                <i class="fas fa-plus" aria-hidden="true"></i>
                {{ __('instructor.add_material') }}
            </button>
        </div>

        <div class="su-field" style="margin-bottom:16px">
            <label for="notes">{{ __('instructor.notes_section') }}</label>
            <textarea name="notes" id="notes" rows="3" class="su-input" style="min-height:88px;resize:vertical">{{ old('notes', $lecture->notes) }}</textarea>
            @error('notes')<p class="su-field-error">{{ $message }}</p>@enderror
        </div>

        <div class="su-section-head" style="margin:0 0 12px">
            <h3>{{ __('instructor.options_section') }}</h3>
        </div>
        <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px">
            <label class="su-chip" style="cursor:pointer;height:auto;padding:14px;width:100%;justify-content:flex-start;gap:12px">
                <input type="checkbox" name="has_attendance_tracking" value="1"
                       {{ old('has_attendance_tracking', $lecture->has_attendance_tracking) ? 'checked' : '' }}>
                <span>
                    <strong style="display:block">{{ __('instructor.attendance_tracking') }}</strong>
                    <span style="font-size:12px;color:var(--su-ink-40)">{{ __('instructor.attendance_tracking_desc') }}</span>
                </span>
            </label>
            <label class="su-chip" style="cursor:pointer;height:auto;padding:14px;width:100%;justify-content:flex-start;gap:12px">
                <input type="checkbox" name="has_assignment" value="1"
                       {{ old('has_assignment', $lecture->has_assignment) ? 'checked' : '' }}>
                <span>
                    <strong style="display:block">{{ __('instructor.has_assignment') }}</strong>
                    <span style="font-size:12px;color:var(--su-ink-40)">{{ __('instructor.has_assignment_desc') }}</span>
                </span>
            </label>
            <label class="su-chip" style="cursor:pointer;height:auto;padding:14px;width:100%;justify-content:flex-start;gap:12px">
                <input type="checkbox" name="has_evaluation" value="1"
                       {{ old('has_evaluation', $lecture->has_evaluation) ? 'checked' : '' }}>
                <span>
                    <strong style="display:block">{{ __('instructor.has_evaluation') }}</strong>
                    <span style="font-size:12px;color:var(--su-ink-40)">{{ __('instructor.has_evaluation_desc') }}</span>
                </span>
            </label>
        </div>

        <div class="su-page-head__actions" style="justify-content:flex-end;border-top:1px solid var(--su-line);padding-top:16px">
            <a href="{{ route('instructor.lectures.show', $lecture) }}" class="su-btn">{{ __('common.cancel') }}</a>
            <button type="submit" class="su-btn su-btn--primary">
                <i class="fas fa-save" aria-hidden="true"></i>
                {{ __('instructor.save_changes') }}
            </button>
        </div>
    </form>
</div>

<script>
(function() {
    var newMaterialsContainer = document.getElementById('edit-materials-new');
    var addBtn = document.getElementById('edit-add-material');
    if (newMaterialsContainer && addBtn) {
        var rowHtml = '<div class="edit-material-row su-chip-row" style="flex-wrap:wrap;align-items:flex-end;gap:12px;padding:14px;border:1px solid var(--su-line);border-radius:14px;margin:0">' +
            '<div style="flex:1;min-width:180px"><label style="display:block;font-size:12px;margin-bottom:4px">{{ __("instructor.file_label") }}</label>' +
            '<input type="file" name="material_files[]" class="su-input" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar,.png,.jpg,.jpeg"></div>' +
            '<div style="width:12rem"><label style="display:block;font-size:12px;margin-bottom:4px">{{ __("instructor.title_optional") }}</label>' +
            '<input type="text" name="material_titles[]" placeholder="{{ __("instructor.material_title_ph") }}" class="su-input"></div>' +
            '<label class="su-chip" style="cursor:pointer"><input type="hidden" name="material_visible[]" value="0"><input type="checkbox" name="material_visible[]" value="1" checked style="margin-inline-end:6px"><span>{{ __("instructor.visible_to_student") }}</span></label>' +
            '<button type="button" class="edit-remove-material su-btn" style="height:32px">{{ __("common.delete") }}</button></div>';
        addBtn.addEventListener('click', function() {
            var div = document.createElement('div');
            div.innerHTML = rowHtml;
            newMaterialsContainer.appendChild(div.firstElementChild);
        });
        newMaterialsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.edit-remove-material')) e.target.closest('.edit-material-row').remove();
        });
    }
    document.getElementById('course_id').addEventListener('change', function() {
        const courseId = this.value;
        const lessonSelect = document.getElementById('course_lesson_id');
        while (lessonSelect.children.length > 1) lessonSelect.removeChild(lessonSelect.lastChild);
        if (courseId) {
            fetch(`/api/courses/${courseId}/lessons`)
                .then(response => response.json())
                .then(data => {
                    if (data.lessons) {
                        data.lessons.forEach(lesson => {
                            const option = document.createElement('option');
                            option.value = lesson.id;
                            option.textContent = lesson.title;
                            lessonSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Error fetching lessons:', error));
        }
    });
})();
</script>
@endsection
