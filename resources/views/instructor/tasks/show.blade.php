@extends('layouts.instructor-timeline')

@section('title', $task->title . ' - ' . __('instructor.tasks_from_management'))
@section('page_title', __('instructor.task_details'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $prioChip = match ($task->priority) {
        'urgent' => 'su-chip--off',
        'high' => 'su-chip--warn',
        'medium' => 'su-soft-1',
        default => '',
    };
    $prioLabel = match ($task->priority) {
        'urgent' => __('instructor.urgent'),
        'high' => __('instructor.high'),
        'medium' => __('instructor.medium'),
        default => __('instructor.low'),
    };
    $stChip = match ($task->status) {
        'completed' => 'su-chip--ok',
        'in_progress' => 'su-soft-1',
        default => 'su-chip--warn',
    };
    $stLabel = match ($task->status) {
        'completed' => __('instructor.completed'),
        'in_progress' => __('instructor.in_progress'),
        default => __('instructor.pending'),
    };
@endphp
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.tasks.index') }}">{{ __('instructor.tasks_from_management') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ $task->title }}</strong>
            </nav>
            <h1 class="su-page-head__title">{{ $task->title }}</h1>
            <div class="su-chip-row">
                @if($task->assigner)
                    <span class="su-chip">{{ __('instructor.from_management') }}</span>
                @endif
                <span class="su-chip {{ $prioChip }}">{{ $prioLabel }}</span>
                <span class="su-chip {{ $stChip }}">{{ $stLabel }}</span>
            </div>
        </div>
        <div class="su-page-head__actions">
            @if(!$task->assigned_by)
                <a href="{{ route('instructor.tasks.edit', $task) }}" class="su-btn">
                    <i class="fas fa-edit" aria-hidden="true"></i>
                    {{ __('common.edit') }}
                </a>
            @endif
            <a href="{{ route('instructor.tasks.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    @if($task->description)
        <section class="su-card" style="margin-bottom:16px">
            <h2 class="su-card__title"><i class="fas fa-align-left" aria-hidden="true"></i> {{ __('instructor.description') }}</h2>
            <div class="su-prose-body" style="white-space:pre-wrap">{{ $task->description }}</div>
        </section>
    @endif

    <section class="su-card" style="margin-bottom:16px">
        <h2 class="su-card__title"><i class="fas fa-info-circle" aria-hidden="true"></i> {{ __('instructor.additional_details') }}</h2>
        <div class="su-meta-list">
            @if($task->relatedCourse)
                <div class="su-meta-row">
                    <span class="su-meta-ico su-soft-1"><i class="fas fa-book" aria-hidden="true"></i></span>
                    <span>{{ __('instructor.course') }}:</span>
                    <strong>{{ $task->relatedCourse->title }}</strong>
                </div>
            @endif
            @if($task->relatedLecture)
                <div class="su-meta-row">
                    <span class="su-meta-ico su-soft-2"><i class="fas fa-chalkboard-teacher" aria-hidden="true"></i></span>
                    <span>{{ __('instructor.lecture') }}:</span>
                    <strong>{{ $task->relatedLecture->title }}</strong>
                </div>
            @endif
            @if($task->due_date)
                <div class="su-meta-row">
                    <span class="su-meta-ico su-soft-3"><i class="fas fa-calendar-alt" aria-hidden="true"></i></span>
                    <span>{{ __('instructor.due_date') }}:</span>
                    <strong>{{ $task->due_date->format('Y-m-d H:i') }}</strong>
                    @if($task->due_date->isPast() && $task->status != 'completed')
                        <span class="su-chip su-chip--off">{{ __('instructor.late') }}</span>
                    @endif
                </div>
            @endif
            @if($task->completed_at)
                <div class="su-meta-row">
                    <span class="su-meta-ico su-soft-4"><i class="fas fa-check-double" aria-hidden="true"></i></span>
                    <span>{{ __('instructor.completed') }}:</span>
                    <strong>{{ $task->completed_at->format('Y-m-d H:i') }}</strong>
                </div>
            @endif
            @if($task->assigned_by && isset($task->progress))
                <div class="su-meta-row">
                    <span class="su-meta-ico su-soft-1"><i class="fas fa-chart-line" aria-hidden="true"></i></span>
                    <span>{{ __('instructor.progress_label') }}:</span>
                    <strong class="tabular-nums">{{ (int)($task->progress ?? 0) }}%</strong>
                </div>
            @endif
        </div>
    </section>

    @if($task->assigned_by)
        <section class="su-card" style="margin-bottom:16px">
            <h2 class="su-card__title"><i class="fas fa-tasks" aria-hidden="true"></i> {{ __('instructor.update_progress') }}</h2>
            <form action="{{ route('instructor.tasks.update-progress', $task) }}" method="POST" class="su-form-grid" style="grid-template-columns:1fr 1fr auto;align-items:end">
                @csrf
                @method('PUT')
                <div class="su-field">
                    <label>{{ __('common.status') }}</label>
                    <select name="status" class="su-select">
                        <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>{{ __('instructor.pending') }}</option>
                        <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>{{ __('instructor.in_progress') }}</option>
                        <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>{{ __('instructor.completed') }}</option>
                    </select>
                </div>
                <div class="su-field">
                    <label>{{ __('instructor.progress_percent') }}</label>
                    <input type="number" name="progress" min="0" max="100" value="{{ (int)($task->progress ?? 0) }}" class="su-input">
                </div>
                <button type="submit" class="su-btn su-btn--primary" style="height:40px">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    {{ __('instructor.save_progress') }}
                </button>
            </form>
        </section>

        <section class="su-card">
            <h2 class="su-card__title"><i class="fas fa-paper-plane" aria-hidden="true"></i> {{ __('instructor.my_submissions') }}</h2>
            @if($task->deliverables->count() > 0)
                <div class="su-list" style="margin-bottom:20px">
                    @foreach($task->deliverables as $d)
                        @php
                            $dChip = match ($d->status) {
                                'approved' => 'su-chip--ok',
                                'rejected', 'needs_revision' => 'su-chip--off',
                                default => 'su-soft-1',
                            };
                            $dLabel = match ($d->status) {
                                'approved' => __('instructor.approved'),
                                'rejected' => __('instructor.rejected'),
                                'needs_revision' => __('instructor.needs_revision'),
                                default => __('instructor.submitted_status'),
                            };
                        @endphp
                        <div class="su-list-item">
                            <div class="su-list-item__body">
                                <div class="su-list-item__title">{{ $d->title }}</div>
                                @if($d->description)
                                    <p style="margin:4px 0 0;font-size:13px;color:var(--su-ink-40)">{{ $d->description }}</p>
                                @endif
                                <div class="su-list-item__meta">
                                    <span>{{ $d->submitted_at?->format('Y-m-d H:i') }}</span>
                                    @if($d->delivery_type === 'link' && $d->link_url)
                                        · <a href="{{ $d->link_url }}" target="_blank" rel="noopener">{{ __('instructor.open_link') }}</a>
                                    @endif
                                    @if($d->file_path)
                                        · <a href="{{ Storage::url($d->file_path) }}" target="_blank" rel="noopener">{{ __('instructor.download_file') }}</a>
                                    @endif
                                </div>
                                @if($d->feedback)
                                    <p style="margin:8px 0 0;padding:8px 10px;border-radius:8px;background:rgba(245,158,11,.1);font-size:13px">
                                        <strong>{{ __('instructor.admin_notes_label') }}:</strong> {{ $d->feedback }}
                                    </p>
                                @endif
                            </div>
                            <span class="su-chip {{ $dChip }}">{{ $dLabel }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
            <form action="{{ route('instructor.tasks.submit-deliverable', $task) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="su-form-grid" style="grid-template-columns:1fr 1fr">
                    <div class="su-field">
                        <label>{{ __('instructor.submission_title_label') }} <span style="color:#b91c1c">*</span></label>
                        <input type="text" name="title" required maxlength="255" value="{{ old('title') }}" class="su-input"
                               placeholder="{{ __('instructor.submission_title_placeholder') }}">
                    </div>
                    <div class="su-field">
                        <label>{{ __('instructor.submission_type_label') }}</label>
                        <select name="delivery_type" id="delivery_type" class="su-select">
                            <option value="file">{{ __('instructor.file_type') }}</option>
                            <option value="image">{{ __('instructor.image_type') }}</option>
                            <option value="link">{{ __('instructor.link_type') }}</option>
                        </select>
                    </div>
                    <div class="su-field" style="grid-column:1 / -1">
                        <label>{{ __('instructor.description_optional') }}</label>
                        <textarea name="description" rows="2" class="su-input" style="min-height:64px;resize:vertical"
                                  placeholder="{{ __('instructor.submission_description_placeholder') }}">{{ old('description') }}</textarea>
                    </div>
                    <div class="su-field" id="file_input" style="grid-column:1 / -1">
                        <label>{{ __('instructor.file_label') }}</label>
                        <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,image/*" class="su-input">
                        <span style="font-size:12px;color:var(--su-ink-40)">{{ __('instructor.max_10mb') }}</span>
                    </div>
                    <div class="su-field" id="link_input" style="display:none;grid-column:1 / -1">
                        <label>{{ __('instructor.submission_link_label') }}</label>
                        <input type="url" name="link_url" value="{{ old('link_url') }}" placeholder="https://..." class="su-input">
                    </div>
                </div>
                <div class="su-form-actions" style="margin-top:12px">
                    <button type="submit" class="su-btn su-btn--primary">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i>
                        {{ __('instructor.submit_work') }}
                    </button>
                </div>
            </form>
        </section>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.getElementById('delivery_type')?.addEventListener('change', function() {
    var type = this.value;
    var fileInput = document.getElementById('file_input');
    var linkInput = document.getElementById('link_input');
    if (fileInput) fileInput.style.display = type === 'link' ? 'none' : '';
    if (linkInput) linkInput.style.display = type === 'link' ? '' : 'none';
});
</script>
@endpush
