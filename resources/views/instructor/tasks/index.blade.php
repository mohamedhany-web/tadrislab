@extends('layouts.instructor-timeline')

@section('title', __('instructor.tasks_from_management') . ' - ' . config('app.name'))
@section('page_title', __('instructor.tasks_from_management'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-check-square su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.tasks_from_management') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.tasks_assigned_by_management') }}</p>
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.total_tasks') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-check-square" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.pending') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['pending'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-clock" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.in_progress') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['in_progress'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-spinner" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.completed_attempts') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['completed'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-check-double" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <section class="su-card" style="margin-bottom:20px">
        <form method="GET" class="su-form-grid">
            <div class="su-field">
                <label for="search">{{ __('common.search') }}</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="{{ __('instructor.search_in_tasks') }}" class="su-input">
            </div>
            <div class="su-field">
                <label for="status">{{ __('common.status') }}</label>
                <select name="status" id="status" class="su-select">
                    <option value="">{{ __('instructor.all_statuses') }}</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('instructor.pending') }}</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>{{ __('instructor.in_progress') }}</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('instructor.completed_attempts') }}</option>
                </select>
            </div>
            <div class="su-field">
                <label for="priority">{{ __('instructor.priority') }}</label>
                <select name="priority" id="priority" class="su-select">
                    <option value="">{{ __('instructor.all_priorities') }}</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>{{ __('instructor.low') }}</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>{{ __('instructor.medium') }}</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>{{ __('instructor.high') }}</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>{{ __('instructor.urgent') }}</option>
                </select>
            </div>
            <div class="su-form-actions">
                <button type="submit" class="su-btn su-btn--primary" style="flex:1;justify-content:center;height:40px">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    {{ __('common.search') }}
                </button>
                @if(request()->anyFilled(['search', 'status', 'priority']))
                    <a href="{{ route('instructor.tasks.index') }}" class="su-btn" style="height:40px;width:40px;padding:0;justify-content:center">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </form>
    </section>

    @if($tasks->count() > 0)
        <div class="su-list">
            @foreach($tasks as $task)
                @php
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
                        'completed' => __('instructor.completed_attempts'),
                        'in_progress' => __('instructor.in_progress'),
                        default => __('instructor.pending'),
                    };
                @endphp
                <div class="su-list-item su-card" style="margin-bottom:12px">
                    <div class="su-list-item__body">
                        <div class="su-list-item__title" style="display:flex;flex-wrap:wrap;gap:6px;align-items:center">
                            {{ $task->title }}
                            @if($task->assigner)
                                <span class="su-chip">{{ __('instructor.from_management') }}</span>
                            @endif
                            <span class="su-chip {{ $prioChip }}">{{ $prioLabel }}</span>
                            <span class="su-chip {{ $stChip }}">{{ $stLabel }}</span>
                        </div>
                        @if($task->description)
                            <p style="margin:6px 0 0;font-size:13px;color:var(--su-ink-40)">{{ Str::limit($task->description, 160) }}</p>
                        @endif
                        <div class="su-list-item__meta">
                            @if($task->relatedCourse)
                                <span><i class="fas fa-book" aria-hidden="true"></i> {{ $task->relatedCourse->title ?? '—' }}</span>
                            @endif
                            @if($task->relatedLecture)
                                <span><i class="fas fa-chalkboard-teacher" aria-hidden="true"></i> {{ $task->relatedLecture->title ?? '—' }}</span>
                            @endif
                            @if($task->due_date)
                                <span><i class="fas fa-calendar" aria-hidden="true"></i> {{ $task->due_date->format('Y/m/d') }}</span>
                                @if($task->due_date->isPast() && $task->status != 'completed')
                                    <span class="su-chip su-chip--off">{{ __('instructor.late') }}</span>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="su-list-item__actions">
                        <a href="{{ route('instructor.tasks.show', $task) }}" class="su-btn su-btn--primary" style="height:32px">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                            {{ __('instructor.view_and_submit') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        @if($tasks->hasPages())
            <div class="su-pager" style="margin-top:16px">{{ $tasks->appends(request()->query())->links() }}</div>
        @endif
    @else
        <section class="su-card">
            <div class="su-empty">
                <i class="fas fa-check-square" aria-hidden="true"></i>
                <p>{{ __('instructor.no_tasks_from_management') }}</p>
                <p style="color:var(--su-ink-40);font-size:13px;margin:0">{{ __('instructor.no_tasks_description') }}</p>
            </div>
        </section>
    @endif
</div>
@endsection
