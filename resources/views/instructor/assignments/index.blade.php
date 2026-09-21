@extends('layouts.instructor-timeline')

@section('title', __('instructor.assignments'))
@section('page_title', __('instructor.assignments'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-tasks su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.assignments') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.manage_assignments_submissions') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.courses.index') }}" class="su-btn">
                <i class="fas fa-book" aria-hidden="true"></i>
                {{ __('instructor.courses') }}
            </a>
            <button type="button" onclick="openCreateModal()" class="su-btn su-btn--primary">
                <i class="fas fa-plus" aria-hidden="true"></i>
                {{ __('instructor.create_assignment') }}
            </button>
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.total') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-tasks" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.published') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['published'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.draft') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['draft'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-file-alt" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.submissions') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total_submissions'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-file-upload" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <section class="su-card" style="margin-bottom:20px">
        <form method="GET" class="su-form-grid">
            <div class="su-field">
                <label for="course_id">{{ __('instructor.courses') }}</label>
                <select name="course_id" id="course_id" class="su-select">
                    <option value="">{{ __('instructor.all_courses') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="su-field">
                <label for="status">{{ __('common.status') }}</label>
                <select name="status" id="status" class="su-select">
                    <option value="">{{ __('instructor.all') }}</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>{{ __('instructor.published') }}</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('instructor.draft') }}</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>{{ __('instructor.archived') }}</option>
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
                @if(request()->anyFilled(['course_id', 'status', 'search']))
                    <a href="{{ route('instructor.assignments.index') }}" class="su-btn" style="height:40px;width:40px;padding:0;justify-content:center" title="{{ __('common.reset') ?? 'Reset' }}">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </form>
    </section>

    @if($assignments->count() > 0)
        <div class="su-list">
            @foreach($assignments as $assignment)
                @php
                    $chip = match ($assignment->status) {
                        'published' => 'su-chip--ok',
                        'draft' => 'su-chip--warn',
                        default => 'su-chip--off',
                    };
                    $statusLabel = match ($assignment->status) {
                        'published' => __('instructor.published'),
                        'draft' => __('instructor.draft'),
                        default => __('instructor.archived'),
                    };
                @endphp
                <article class="su-list-item">
                    <span class="su-list-item__ico su-soft-1">
                        <i class="fas fa-tasks" aria-hidden="true"></i>
                    </span>
                    <div class="su-list-item__body">
                        <div class="su-chip-row" style="margin:0 0 6px">
                            <span class="su-chip {{ $chip }}">{{ $statusLabel }}</span>
                            @if($assignment->course)
                                <span class="su-chip">{{ Str::limit($assignment->course->title, 40) }}</span>
                            @endif
                        </div>
                        <div class="su-list-item__title">{{ $assignment->title }}</div>
                        @if($assignment->description)
                            <p style="margin:4px 0 0;font-size:13px;color:var(--su-ink-40)">{{ Str::limit($assignment->description, 120) }}</p>
                        @endif
                        <div class="su-list-item__meta">
                            @if($assignment->due_date)
                                {{ $assignment->due_date->format('Y/m/d') }} ·
                            @endif
                            {{ $assignment->submissions_count }} {{ __('instructor.submission_single') }} ·
                            {{ $assignment->max_score }} {{ __('instructor.score_marks') }}
                        </div>
                    </div>
                    <div class="su-list-item__actions">
                        <a href="{{ route('instructor.assignments.submissions', $assignment) }}" class="su-btn" style="height:32px">
                            <i class="fas fa-list" aria-hidden="true"></i>
                            {{ __('instructor.submissions') }}
                        </a>
                        <a href="{{ route('instructor.assignments.show', $assignment) }}" class="su-btn su-btn--primary" style="height:32px">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                            {{ __('common.view') }}
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
        @if(method_exists($assignments, 'links') && $assignments->hasPages())
            <div class="su-pager" style="margin-top:16px">{{ $assignments->links() }}</div>
        @endif
    @else
        <div class="su-empty">
            <i class="fas fa-tasks" aria-hidden="true"></i>
            <p><strong>{{ __('instructor.no_assignments') }}</strong></p>
            <p>{{ __('instructor.no_assignments_description') }}</p>
            <button type="button" onclick="openCreateModal()" class="su-btn su-btn--primary" style="margin-top:12px">
                <i class="fas fa-plus" aria-hidden="true"></i>
                {{ __('instructor.create_assignment') }}
            </button>
        </div>
    @endif
</div>

{{-- Create assignment modal --}}
<div id="createAssignmentModal" class="su-modal hidden" role="dialog" aria-modal="true" aria-labelledby="modal-title" style="position:fixed;inset:0;z-index:50;overflow-y:auto">
    <div style="display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px">
        <div style="position:fixed;inset:0;background:rgba(15,23,42,.45)" onclick="closeCreateModal()" id="modalOverlay"></div>
        <div class="su-card" style="position:relative;width:100%;max-width:56rem;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;margin:0;padding:0" id="modalPanel" onclick="event.stopPropagation()">
            <div class="su-section-head" style="padding:16px 20px;border-bottom:1px solid var(--su-line);margin:0">
                <h3 id="modal-title" style="display:flex;align-items:center;gap:10px;margin:0">
                    <span class="su-meta-ico su-soft-1"><i class="fas fa-tasks" aria-hidden="true"></i></span>
                    {{ __('instructor.create_assignment_modal_title') }}
                </h3>
                <button type="button" onclick="closeCreateModal()" class="su-icon-link su-icon-link--ghost" aria-label="{{ __('common.cancel') }}">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <p style="padding:0 20px;margin:8px 0 0;font-size:13px;color:var(--su-ink-40)">{{ __('instructor.create_assignment_modal_subtitle') }}</p>
            <div style="overflow-y:auto;flex:1;padding:20px">
                @include('instructor.assignments.create-form', ['courses' => $courses, 'isModal' => true])
            </div>
        </div>
    </div>
</div>

<script>
function openCreateModal() {
    var modal = document.getElementById('createAssignmentModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(function() {
            if (typeof updateLessonsOnCourseChange === 'function') updateLessonsOnCourseChange();
        }, 100);
    }
}
function closeCreateModal() {
    var modal = document.getElementById('createAssignmentModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        var form = document.getElementById('assignmentForm');
        if (form) {
            form.reset();
            var lessonSelect = document.getElementById('lesson_id');
            if (lessonSelect && lessonSelect.children.length > 1) {
                while (lessonSelect.children.length > 1) lessonSelect.removeChild(lessonSelect.lastChild);
            }
        }
    }
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeCreateModal(); });
</script>
@endsection
