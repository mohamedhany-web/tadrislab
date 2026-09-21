@extends('layouts.instructor-timeline')

@section('title', __('instructor.edit_assignment_title') . ' - ' . $assignment->title)
@section('page_title', __('instructor.edit_assignment_title'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page" style="max-width:48rem">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.assignments.index') }}">{{ __('instructor.assignments') }}</a>
                <span>/</span>
                <a href="{{ route('instructor.assignments.show', $assignment) }}">{{ Str::limit($assignment->title, 40) }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('common.edit') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-edit su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.edit_assignment_title') }}
            </h1>
            <p class="su-page-head__sub">{{ $assignment->title }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.assignments.show', $assignment) }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <form action="{{ route('instructor.assignments.update', $assignment) }}" method="POST" enctype="multipart/form-data" class="su-card">
        @csrf
        @method('PUT')
        <input type="hidden" name="advanced_course_id" value="{{ $assignment->advanced_course_id ?? $assignment->course_id }}">

        <div class="su-form-grid" style="grid-template-columns:1fr 1fr">
            <div class="su-field" style="grid-column:1 / -1">
                <label for="title">{{ __('instructor.assignment_title_required') }} <span style="color:#b91c1c">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $assignment->title) }}" required class="su-input">
                @error('title')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
            <div class="su-field" style="grid-column:1 / -1">
                <label for="description">{{ __('instructor.description') }}</label>
                <textarea name="description" id="description" rows="3" class="su-input" style="min-height:88px;resize:vertical">{{ old('description', $assignment->description) }}</textarea>
                @error('description')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
            <div class="su-field" style="grid-column:1 / -1">
                <label for="instructions">{{ __('instructor.instructions_label') }}</label>
                <textarea name="instructions" id="instructions" rows="4" class="su-input" style="min-height:110px;resize:vertical">{{ old('instructions', $assignment->instructions) }}</textarea>
                @error('instructions')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
            @php
                $resAtt = is_array($assignment->resource_attachments) ? $assignment->resource_attachments : [];
            @endphp
            @if(count($resAtt) > 0)
                <div class="su-field" style="grid-column:1 / -1">
                    <label>{{ __('instructor.current_attachments_remove') }}</label>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:8px">
                        @foreach($resAtt as $idx => $att)
                            @php
                                $p = is_array($att) ? ($att['path'] ?? '') : '';
                                $url = $p ? (\App\Services\AssignmentFileStorage::publicUrl($p) ?? '#') : '#';
                                $on = is_array($att) ? ($att['original_name'] ?? basename($p)) : '';
                            @endphp
                            <li class="su-chip-row" style="justify-content:space-between;padding:10px 12px;border:1px solid var(--su-line);border-radius:12px;margin:0">
                                <a href="{{ $url }}" target="_blank" rel="noopener" style="color:var(--su-accent);font-weight:600;overflow:hidden;text-overflow:ellipsis">{{ $on }}</a>
                                <label class="su-chip su-chip--off" style="cursor:pointer">
                                    <input type="checkbox" name="remove_resource_indices[]" value="{{ $idx }}" style="margin-inline-end:6px">
                                    {{ __('common.delete') }}
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="su-field" style="grid-column:1 / -1">
                <label>{{ __('instructor.add_new_attachments') }}</label>
                <input type="file" name="resource_files[]" multiple accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png,.gif,.webp,.ppt,.pptx,.txt" class="su-input">
                @error('resource_files')<p class="su-field-error">{{ $message }}</p>@enderror
                @error('resource_files.*')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
            <div class="su-field">
                <label for="due_date">{{ __('instructor.due_date') }}</label>
                <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date', $assignment->due_date?->format('Y-m-d\TH:i')) }}" class="su-input">
                @error('due_date')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
            <div class="su-field">
                <label for="max_score">{{ __('instructor.total_score_label') }} <span style="color:#b91c1c">*</span></label>
                <input type="number" name="max_score" id="max_score" value="{{ old('max_score', $assignment->max_score) }}" min="1" max="1000" required class="su-input">
                @error('max_score')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
            <div class="su-field" style="grid-column:1 / -1">
                <label class="su-chip" style="cursor:pointer;justify-content:flex-start;height:auto;padding:12px 14px;width:100%">
                    <input type="checkbox" name="allow_late_submission" value="1" {{ old('allow_late_submission', $assignment->allow_late_submission) ? 'checked' : '' }} style="margin-inline-end:10px">
                    {{ __('instructor.allow_late_submission_label') }}
                </label>
            </div>
            <div class="su-field" style="grid-column:1 / -1">
                <label for="status">{{ __('common.status') }} <span style="color:#b91c1c">*</span></label>
                <select name="status" id="status" required class="su-select">
                    <option value="draft" {{ old('status', $assignment->status) == 'draft' ? 'selected' : '' }}>{{ __('instructor.draft') }}</option>
                    <option value="published" {{ old('status', $assignment->status) == 'published' ? 'selected' : '' }}>{{ __('instructor.published') }}</option>
                    <option value="archived" {{ old('status', $assignment->status) == 'archived' ? 'selected' : '' }}>{{ __('instructor.archived') }}</option>
                </select>
                @error('status')<p class="su-field-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="su-page-head__actions" style="margin-top:20px;justify-content:flex-end;border-top:1px solid var(--su-line);padding-top:16px">
            <a href="{{ route('instructor.assignments.index') }}" class="su-btn">{{ __('common.cancel') }}</a>
            <button type="submit" class="su-btn su-btn--primary">
                <i class="fas fa-save" aria-hidden="true"></i>
                {{ __('instructor.save_changes') }}
            </button>
        </div>
    </form>
</div>
@endsection
