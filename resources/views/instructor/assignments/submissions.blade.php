@extends('layouts.instructor-timeline')

@section('title', __('instructor.submissions_of') . ': ' . $assignment->title . ' - ' . config('app.name'))
@section('page_title', __('instructor.submissions_title'))

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
                <a href="{{ route('instructor.assignments.show', $assignment) }}">{{ Str::limit($assignment->title, 40) }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('instructor.submissions_title') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-inbox su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.submissions_of') }}: {{ $assignment->title }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.max_score_points') }}: {{ $assignment->max_score }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.assignments.show', $assignment) }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back_to_assignment') }}
            </a>
        </div>
    </div>

    <section class="su-card su-card--flush">
        <div class="su-section-head" style="padding:12px 16px;margin:0;border-bottom:1px solid var(--su-line)">
            <h3>{{ __('instructor.submissions_list') }}</h3>
        </div>
        <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
            @if($submissions->count() > 0)
                <table class="su-table">
                    <thead>
                        <tr>
                            <th>{{ __('instructor.student') }}</th>
                            <th>{{ __('instructor.submission_date') }}</th>
                            <th>{{ __('instructor.score_label') }}</th>
                            <th>{{ __('common.status') }}</th>
                            <th>{{ __('instructor.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submissions as $sub)
                            <tr>
                                <td><strong style="font-weight:600">{{ $sub->student->name ?? '—' }}</strong></td>
                                <td class="tabular-nums" style="color:var(--su-ink-40)">{{ $sub->submitted_at?->format('Y/m/d H:i') }}</td>
                                <td class="tabular-nums">
                                    @if($sub->score !== null)
                                        <strong>{{ $sub->score }}/{{ $assignment->max_score }}</strong>
                                    @else
                                        <span style="color:var(--su-ink-40)">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sub->status === 'graded')
                                        <span class="su-chip su-chip--ok">{{ __('instructor.graded_status') }}</span>
                                    @elseif($sub->status === 'returned')
                                        <span class="su-chip su-soft-1">{{ __('instructor.returned_status') }}</span>
                                    @else
                                        <span class="su-chip su-chip--warn">{{ __('instructor.pending_review') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" onclick="toggleDetail({{ $sub->id }})" class="su-btn" style="height:32px">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                        {{ __('instructor.view_grade') }}
                                    </button>
                                </td>
                            </tr>
                            <tr id="detail-{{ $sub->id }}" class="hidden">
                                <td colspan="5" style="background:var(--su-bg)">
                                    <div style="display:flex;flex-direction:column;gap:16px;max-width:48rem;padding:8px 0">
                                        @if($sub->content)
                                            <div>
                                                <div class="su-section-head" style="margin:0 0 8px">
                                                    <h3 style="font-size:13px">{{ __('instructor.content_label') }}</h3>
                                                </div>
                                                <div class="su-prose-box"><div class="su-prose-body" style="white-space:pre-wrap">{{ $sub->content }}</div></div>
                                            </div>
                                        @endif
                                        @if($sub->attachments && count($sub->attachments) > 0)
                                            <div>
                                                <div class="su-section-head" style="margin:0 0 8px">
                                                    <h3 style="font-size:13px">{{ __('instructor.attachments_label') }}</h3>
                                                </div>
                                                <ul class="su-meta-list">
                                                    @foreach($sub->attachments as $att)
                                                        @php
                                                            $path = is_string($att) ? $att : ($att['path'] ?? $att['url'] ?? null);
                                                            $url = $path ? (\App\Services\AssignmentFileStorage::publicUrl($path) ?? (str_starts_with((string) $path, 'http') ? $path : url('storage/'.$path))) : '#';
                                                            $label = is_array($att) ? ($att['original_name'] ?? $att['name'] ?? basename($path ?? __('instructor.attachment_fallback'))) : basename($att);
                                                        @endphp
                                                        <li class="su-meta-row">
                                                            <span class="su-meta-ico su-soft-1"><i class="fas fa-file" aria-hidden="true"></i></span>
                                                            <a href="{{ $url }}" target="_blank" rel="noopener" style="color:var(--su-accent);font-weight:600">{{ $label }}</a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        @if($sub->feedback)
                                            <div>
                                                <div class="su-section-head" style="margin:0 0 8px">
                                                    <h3 style="font-size:13px">{{ __('instructor.feedback_label') }}</h3>
                                                </div>
                                                <p style="margin:0;font-size:13px;color:var(--su-ink-40)">{{ $sub->feedback }}</p>
                                            </div>
                                        @endif
                                        <form action="{{ route('instructor.assignments.grade', [$assignment, $sub]) }}" method="POST" class="su-form-grid" style="grid-template-columns:auto 1fr auto auto;align-items:end;border-top:1px solid var(--su-line);padding-top:12px">
                                            @csrf
                                            <div class="su-field">
                                                <label>{{ __('instructor.score_label') }} (0–{{ $assignment->max_score }})</label>
                                                <input type="number" name="score" min="0" max="{{ $assignment->max_score }}" value="{{ old('score', $sub->score) }}" class="su-input" style="width:6rem">
                                            </div>
                                            <div class="su-field">
                                                <label>{{ __('instructor.feedback_label') }}</label>
                                                <input type="text" name="feedback" value="{{ old('feedback', $sub->feedback) }}" placeholder="{{ __('instructor.optional_comment') }}" class="su-input">
                                            </div>
                                            <div class="su-field">
                                                <label>{{ __('common.status') }}</label>
                                                <select name="status" class="su-select">
                                                    <option value="submitted" {{ $sub->status === 'submitted' ? 'selected' : '' }}>{{ __('instructor.pending_review') }}</option>
                                                    <option value="graded" {{ $sub->status === 'graded' ? 'selected' : '' }}>{{ __('instructor.graded_status') }}</option>
                                                    <option value="returned" {{ $sub->status === 'returned' ? 'selected' : '' }}>{{ __('instructor.returned_status') }}</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="su-btn su-btn--primary" style="height:40px">
                                                <i class="fas fa-check" aria-hidden="true"></i>
                                                {{ __('instructor.save_grade') }}
                                            </button>
                                        </form>
                                    </div>
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
                    <p>{{ __('instructor.no_submissions_yet') }}</p>
                </div>
            @endif
        </div>
    </section>
</div>

@if($submissions->count() > 0)
<script>
function toggleDetail(id) {
    const row = document.getElementById('detail-' + id);
    if (!row) return;
    row.classList.toggle('hidden');
}
</script>
@endif
@endsection
