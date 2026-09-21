@extends('layouts.instructor-timeline')

@section('title', $lesson->title . ' — ' . $course->title)
@section('page_title', $lesson->title)

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.courses.index') }}">{{ __('instructor.courses') }}</a>
                <span>/</span>
                <a href="{{ route('instructor.courses.show', $course->id) }}">{{ $course->title }}</a>
                <span>/</span>
                <a href="{{ route('instructor.courses.lessons.index', $course->id) }}">{{ __('instructor.lessons_breadcrumb') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ $lesson->title }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-book-open su-page-head__ico" aria-hidden="true"></i>
                {{ $lesson->title }}
            </h1>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.courses.lessons.edit', [$course->id, $lesson->id]) }}" class="su-btn su-btn--primary">
                <i class="fas fa-edit" aria-hidden="true"></i>
                {{ __('common.edit') }}
            </a>
            <a href="{{ route('instructor.courses.lessons.index', $course->id) }}" class="su-btn">
                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.lessons_back_to_list') }}
            </a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr;gap:20px">
        <div style="display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1fr);gap:20px" class="su-lessons-show-grid">
            <section class="su-card">
                <h3 class="su-card__title" style="margin-bottom:14px">{{ __('instructor.lessons_details') }}</h3>
                @if($lesson->description)
                    <div class="su-prose-box" style="margin-bottom:14px">
                        <label>{{ __('instructor.description') }}</label>
                        <p>{{ $lesson->description }}</p>
                    </div>
                @endif
                @if($lesson->content)
                    <div class="su-prose-box" style="margin-bottom:14px">
                        <label>{{ __('instructor.lessons_content_label') }}</label>
                        <div class="su-prose-body">{!! nl2br(e($lesson->content)) !!}</div>
                    </div>
                @endif
                @if($lesson->type === 'video' && $lesson->video_url)
                    <div>
                        <div style="font-size:12px;font-weight:500;color:var(--su-ink-40);margin-bottom:6px">{{ __('instructor.lessons_video_url') }}</div>
                        <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener" class="su-btn" style="height:32px">
                            <i class="fas fa-external-link-alt" aria-hidden="true"></i>
                            {{ __('common.view') }}
                        </a>
                    </div>
                @endif
            </section>

            <aside style="display:flex;flex-direction:column;gap:16px">
                <section class="su-card">
                    <h3 class="su-card__title" style="margin-bottom:12px">{{ __('instructor.lessons_info') }}</h3>
                    <div class="su-meta-list">
                        <div class="su-meta-row">
                            <span class="su-meta-ico su-soft-1"><i class="fas fa-tag" aria-hidden="true"></i></span>
                            <span>{{ __('instructor.type') }}:</span>
                            <strong>
                                @if($lesson->type === 'video') {{ __('instructor.lessons_type_video') }}
                                @elseif($lesson->type === 'text') {{ __('instructor.lessons_type_text') }}
                                @elseif($lesson->type === 'document') {{ __('instructor.lessons_type_document') }}
                                @else {{ __('instructor.lessons_type_quiz') }}
                                @endif
                            </strong>
                        </div>
                        <div class="su-meta-row">
                            <span class="su-meta-ico su-soft-2"><i class="fas fa-sort" aria-hidden="true"></i></span>
                            <span>{{ __('instructor.lessons_col_order') }}:</span>
                            <strong>{{ $lesson->order }}</strong>
                        </div>
                        @if($lesson->duration_minutes)
                            <div class="su-meta-row">
                                <span class="su-meta-ico su-soft-3"><i class="fas fa-clock" aria-hidden="true"></i></span>
                                <span>{{ __('instructor.lessons_col_duration') }}:</span>
                                <strong>{{ $lesson->duration_minutes }} {{ __('instructor.minutes') }}</strong>
                            </div>
                        @endif
                        <div class="su-meta-row">
                            <span class="su-meta-ico su-soft-4"><i class="fas fa-toggle-on" aria-hidden="true"></i></span>
                            <span>{{ __('common.status') }}:</span>
                            <strong>
                                <span class="su-chip {{ $lesson->is_active ? 'su-chip--ok' : 'su-chip--off' }}">
                                    {{ $lesson->is_active ? __('instructor.active') : __('instructor.inactive') }}
                                </span>
                            </strong>
                        </div>
                        <div class="su-meta-row">
                            <span class="su-meta-ico su-soft-1"><i class="fas fa-gift" aria-hidden="true"></i></span>
                            <span>{{ __('instructor.free') }}:</span>
                            <strong>{{ $lesson->is_free ? __('common.yes') : __('common.no') }}</strong>
                        </div>
                    </div>
                </section>

                @php
                    $attachments = is_string($lesson->attachments) ? json_decode($lesson->attachments, true) : $lesson->attachments;
                    $attachments = is_array($attachments) ? $attachments : [];
                @endphp
                @if(count($attachments) > 0)
                    <section class="su-card">
                        <h3 class="su-card__title" style="margin-bottom:12px">{{ __('instructor.lessons_attachments') }}</h3>
                        <div class="su-list">
                            @foreach($attachments as $att)
                                @php $path = $att['path'] ?? null; $attUrl = $path ? (str_starts_with($path, 'http') ? $path : url('storage/' . $path)) : '#'; @endphp
                                <a href="{{ $attUrl }}" target="_blank" rel="noopener" class="su-list-item" style="text-decoration:none;color:inherit">
                                    <span class="su-list-item__ico su-soft-2"><i class="fas fa-paperclip" aria-hidden="true"></i></span>
                                    <div class="su-list-item__body">
                                        <div class="su-list-item__title">{{ $att['name'] ?? __('instructor.lib_curriculum_file_fallback') }}</div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </aside>
        </div>
    </div>
</div>
<style>
@media (max-width: 960px) {
  .su-lessons-show-grid { grid-template-columns: 1fr !important; }
}
</style>
@endsection
