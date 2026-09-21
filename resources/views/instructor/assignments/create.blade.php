@extends('layouts.instructor-timeline')

@section('title', __('instructor.create_assignment') . ' - ' . config('app.name'))
@section('page_title', __('instructor.create_assignment'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page" style="max-width:56rem">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.assignments.index') }}">{{ __('instructor.assignments') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('instructor.create_assignment') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-tasks su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.create_assignment') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.add_assignment_for_course') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.assignments.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <section class="su-card">
        @include('instructor.assignments.create-form', ['courses' => $courses])
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('advanced_course_id');
    if (!courseSelect) return;

    const newCourseSelect = courseSelect.cloneNode(true);
    courseSelect.parentNode.replaceChild(newCourseSelect, courseSelect);

    newCourseSelect.addEventListener('change', function() {
        const courseId = this.value;
        const lessonSelect = document.getElementById('lesson_id');
        if (!lessonSelect) return;

        while (lessonSelect.children.length > 1) lessonSelect.removeChild(lessonSelect.lastChild);
        if (!courseId) return;

        const loadingOption = document.createElement('option');
        loadingOption.value = '';
        loadingOption.textContent = @json(__('instructor.loading_text'));
        loadingOption.disabled = true;
        lessonSelect.appendChild(loadingOption);
        lessonSelect.disabled = true;

        fetch('/instructor/api/courses/' + courseId + '/lessons-list', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
            .then(function(r) { return r.ok ? r.json() : Promise.reject(); })
            .then(function(data) {
                loadingOption.remove();
                var lessons = Array.isArray(data) ? data : (data.lessons || []);
                if (lessons.length > 0) {
                    lessons.forEach(function(lesson) {
                        var opt = document.createElement('option');
                        opt.value = lesson.id;
                        opt.textContent = lesson.title || 'درس ' + (lesson.order || '');
                        lessonSelect.appendChild(opt);
                    });
                } else {
                    var noOpt = document.createElement('option');
                    noOpt.value = '';
                    noOpt.textContent = @json(__('instructor.no_lessons_in_course'));
                    noOpt.disabled = true;
                    lessonSelect.appendChild(noOpt);
                }
                lessonSelect.disabled = false;
            })
            .catch(function() {
                loadingOption.remove();
                var errOpt = document.createElement('option');
                errOpt.value = '';
                errOpt.textContent = @json(__('instructor.error_occurred'));
                errOpt.disabled = true;
                lessonSelect.appendChild(errOpt);
                lessonSelect.disabled = false;
            });
    });

    if (typeof updateGroupOptions === 'function') {
        updateGroupOptions(newCourseSelect.value);
    }
});
</script>
@endsection
