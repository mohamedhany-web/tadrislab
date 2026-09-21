@extends('layouts.instructor-timeline')

@section('title', $assignment->title . ' - ' . config('app.name'))
@section('page_title', $assignment->title)

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.assignments.index') }}">{{ __('instructor.assignments') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ $assignment->title }}</strong>
            </nav>
            <h1 class="su-page-head__title">{{ $assignment->title }}</h1>
            <p class="su-page-head__sub">{{ $assignment->course->title ?? '—' }}</p>
            <div class="su-chip-row">
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
                <span class="su-chip {{ $chip }}">{{ $statusLabel }}</span>
                <span class="su-chip su-soft-1">{{ $assignment->max_score }} {{ __('instructor.score_marks') }}</span>
                @if($assignment->due_date)
                    <span class="su-chip su-soft-2">{{ $assignment->due_date->format('Y/m/d H:i') }}</span>
                @endif
            </div>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.assignments.edit', $assignment) }}" class="su-btn">
                <i class="fas fa-edit" aria-hidden="true"></i>
                {{ __('common.edit') }}
            </a>
            <a href="{{ route('instructor.assignments.submissions', $assignment) }}" class="su-btn su-btn--primary">
                <i class="fas fa-inbox" aria-hidden="true"></i>
                {{ __('instructor.submissions_title') }} ({{ $submissionStats['total'] ?? 0 }})
            </a>
            <a href="{{ route('instructor.assignments.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    @if($assignment->description)
        <section class="su-card" style="margin-bottom:16px">
            <h2 class="su-card__title">
                <i class="fas fa-align-left" aria-hidden="true"></i>
                {{ __('instructor.description') }}
            </h2>
            <div class="su-prose-body">{{ $assignment->description }}</div>
        </section>
    @endif

    @php
        $instrRes = is_array($assignment->resource_attachments) ? $assignment->resource_attachments : [];
    @endphp
    @if(count($instrRes) > 0)
        <section class="su-card" style="margin-bottom:16px">
            <h2 class="su-card__title">
                <i class="fas fa-paperclip" aria-hidden="true"></i>
                {{ __('instructor.assignment_attachments_students') }}
            </h2>
            <ul class="su-meta-list">
                @foreach($instrRes as $att)
                    @php
                        $p = is_array($att) ? ($att['path'] ?? '') : '';
                        $u = $p ? (\App\Services\AssignmentFileStorage::publicUrl($p) ?? '#') : '#';
                        $lb = is_array($att) ? ($att['original_name'] ?? basename($p)) : '';
                    @endphp
                    <li class="su-meta-row">
                        <span class="su-meta-ico su-soft-1"><i class="fas fa-file" aria-hidden="true"></i></span>
                        <a href="{{ $u }}" target="_blank" rel="noopener" style="color:var(--su-accent);font-weight:600">{{ $lb }}</a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="su-card su-card--flush">
        <div class="su-section-head" style="padding:12px 16px;margin:0;border-bottom:1px solid var(--su-line)">
            <h3>{{ __('instructor.last_submissions') }}</h3>
        </div>
        <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
            @if($submissions->count() > 0)
                <table class="su-table">
                    <thead>
                        <tr>
                            <th>{{ __('instructor.student') }}</th>
                            <th>{{ __('instructor.submission_date') }}</th>
                            <th>{{ __('common.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $sub)
                            <tr>
                                <td><strong style="font-weight:600">{{ $sub->student->name ?? '—' }}</strong></td>
                                <td class="tabular-nums" style="color:var(--su-ink-40)">{{ $sub->submitted_at?->format('Y/m/d H:i') }}</td>
                                <td>
                                    <span class="su-chip {{ $sub->status === 'graded' ? 'su-chip--ok' : 'su-chip--warn' }}">{{ $sub->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(method_exists($submissions, 'links') && $submissions->hasPages())
                    <div class="su-pager" style="padding:12px">{{ $submissions->links() }}</div>
                @endif
            @else
                <div class="su-empty">
                    <i class="fas fa-inbox" aria-hidden="true"></i>
                    <p>{{ __('instructor.no_submissions') }}</p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
