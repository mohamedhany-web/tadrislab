@extends('layouts.instructor-timeline')

@section('title', __('instructor.lecture_recordings'))
@section('page_title', __('instructor.lecture_recordings'))

@section('content')
<div class="su-page su-lr-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-video su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.lecture_recordings') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.lr_subtitle') }}</p>
        </div>
        <div class="su-page-head__actions">
            @if(Route::has('instructor.lectures.index'))
                <a href="{{ route('instructor.lectures.index') }}" class="su-btn">
                    <i class="fas fa-chalkboard" aria-hidden="true"></i>
                    {{ __('instructor.lectures') }}
                </a>
            @endif
        </div>
    </div>

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.lr_stat_total') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['total'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-chalkboard-teacher" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.lr_recorded') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['recorded'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.lr_no_recording') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['missing'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-exclamation-circle" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.lr_live_recordings') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['live'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-broadcast-tower" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="su-alert su-alert--ok" role="status">
            <i class="fas fa-check-circle" aria-hidden="true"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="su-alert su-alert--err" role="alert">
            <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="su-lr-stack">
        @forelse($lectures as $lecture)
            @php
                $hasRec = filled($lecture->recording_url) || filled($lecture->recording_file_path);
            @endphp
            <article class="su-card su-lr-card">
                <div class="su-lr-card__head">
                    <div class="su-lr-card__identity">
                        <span class="su-list-item__ico {{ $hasRec ? 'su-soft-3' : 'su-soft-4' }}">
                            <i class="fas {{ $hasRec ? 'fa-film' : 'fa-video-slash' }}" aria-hidden="true"></i>
                        </span>
                        <div class="min-w-0">
                            <h2 class="su-lr-card__title">{{ $lecture->title }}</h2>
                            <p class="su-lr-card__meta">
                                {{ $lecture->course?->title ?? '—' }}
                                @if($lecture->scheduled_at)
                                    · <x-app-datetime :at="$lecture->scheduled_at" pattern="Y/m/d H:i" />
                                @endif
                            </p>
                            <div class="su-chip-row">
                                @if($hasRec)
                                    <span class="su-chip su-chip--ok">
                                        <i class="fas fa-check" aria-hidden="true"></i>
                                        {{ __('instructor.lr_recorded') }}
                                    </span>
                                @else
                                    <span class="su-chip su-chip--warn">
                                        <i class="fas fa-clock" aria-hidden="true"></i>
                                        {{ __('instructor.lr_no_recording') }}
                                    </span>
                                @endif
                                @if($lecture->recording_file_path)
                                    <span class="su-chip su-soft-1">
                                        <i class="fas fa-file-video" aria-hidden="true"></i>
                                        {{ __('instructor.lr_has_file') }}
                                    </span>
                                @endif
                                @if($lecture->recording_url)
                                    <span class="su-chip su-soft-2">
                                        <i class="fas fa-link" aria-hidden="true"></i>
                                        {{ __('instructor.lr_has_url') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('instructor.lecture-recordings.preview', $lecture) }}" class="su-btn">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                        {{ __('instructor.lr_preview') }}
                    </a>
                </div>

                <form method="POST"
                      action="{{ route('instructor.lecture-recordings.update', $lecture) }}"
                      enctype="multipart/form-data"
                      class="su-lr-form">
                    @csrf
                    @method('PUT')

                    <div class="su-field">
                        <label for="recording_url_{{ $lecture->id }}">{{ __('instructor.lr_video_url') }}</label>
                        <input type="url"
                               name="recording_url"
                               id="recording_url_{{ $lecture->id }}"
                               value="{{ old('recording_url', $lecture->recording_url) }}"
                               placeholder="{{ __('instructor.lr_video_url_ph') }}"
                               class="su-input"
                               dir="ltr">
                        <p class="su-field-hint">{{ __('instructor.lr_url_hint') }}</p>
                    </div>

                    <div class="su-lr-form__row">
                        <div class="su-field">
                            <label for="recording_file_{{ $lecture->id }}">{{ __('instructor.lr_upload_file') }}</label>
                            <input type="file"
                                   name="recording_file"
                                   id="recording_file_{{ $lecture->id }}"
                                   accept="video/*"
                                   class="su-input su-input--file">
                        </div>
                        <label class="su-check">
                            <input type="checkbox" name="clear_file" value="1">
                            <span>{{ __('instructor.lr_clear_file') }}</span>
                        </label>
                    </div>

                    <div class="su-lr-form__actions">
                        <button type="submit" class="su-btn su-btn--primary">
                            <i class="fas fa-save" aria-hidden="true"></i>
                            {{ __('instructor.lr_save') }}
                        </button>
                    </div>
                </form>
            </article>
        @empty
            <div class="su-card">
                <div class="su-empty" style="padding:48px 16px">
                    <i class="fas fa-video" aria-hidden="true"></i>
                    <h3 style="margin:0;font-size:16px;font-weight:600;color:var(--su-ink)">{{ __('instructor.lr_empty') }}</h3>
                    <p>{{ __('instructor.lr_empty_hint') }}</p>
                    @if(Route::has('instructor.lectures.create'))
                        <a href="{{ route('instructor.lectures.create') }}" class="su-btn su-btn--primary">
                            <i class="fas fa-plus" aria-hidden="true"></i>
                            {{ __('instructor.add_new_lecture') }}
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    @if(method_exists($lectures, 'links') && $lectures->hasPages())
        <div class="su-pager">{{ $lectures->links() }}</div>
    @endif

    @if($liveRecordings->isNotEmpty())
        <section class="su-card" style="margin-top:20px">
            <h2 class="su-card__title">
                <i class="fas fa-broadcast-tower" aria-hidden="true"></i>
                {{ __('instructor.lr_live_recordings') }}
            </h2>
            <div class="su-list">
                @foreach($liveRecordings as $rec)
                    <div class="su-list-item">
                        <span class="su-list-item__ico su-soft-2">
                            <i class="fas fa-circle-play" aria-hidden="true"></i>
                        </span>
                        <div class="su-list-item__body">
                            <div class="su-list-item__title">
                                {{ $rec->title ?: __('instructor.lr_recording_num', ['id' => $rec->id]) }}
                            </div>
                        </div>
                        <span class="su-chip {{ $rec->is_published ? 'su-chip--ok' : 'su-chip--warn' }}">
                            {{ $rec->is_published ? __('instructor.published') : __('instructor.draft') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
