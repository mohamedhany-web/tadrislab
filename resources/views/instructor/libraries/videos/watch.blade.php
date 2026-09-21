@extends('layouts.instructor-timeline')

@section('title', $libraryVideo->title)
@section('page_title', $libraryVideo->title)

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.libraries.videos.index') }}">{{ __('instructor.lib_videos_title') }}</a>
                <span>/</span>
                <span>{{ $isOwn ? __('instructor.lib_videos_own_badge') : __('instructor.lib_videos_academy_badge') }}</span>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-play-circle su-page-head__ico" aria-hidden="true"></i>
                {{ $libraryVideo->title }}
            </h1>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.libraries.videos.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
            @if($isOwn)
                <a href="{{ route('instructor.libraries.videos.edit', $libraryVideo) }}" class="su-btn su-btn--primary">
                    <i class="fas fa-edit" aria-hidden="true"></i>
                    {{ __('common.edit') }}
                </a>
            @endif
        </div>
    </div>

    <article class="su-card su-card--flush" style="overflow:hidden">
        <div style="aspect-ratio:16/9;background:#000">
            @if($embedUrl)
                <iframe
                    src="{{ $embedUrl }}"
                    title="{{ $libraryVideo->title }}"
                    style="display:block;width:100%;height:100%;border:0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen; web-share"
                    allowfullscreen
                    referrerpolicy="strict-origin-when-cross-origin"
                ></iframe>
            @elseif($directUrl)
                <video style="display:block;width:100%;height:100%" controls playsinline preload="metadata" @if($thumbnail) poster="{{ $thumbnail }}" @endif controlslist="nodownload">
                    <source src="{{ $directUrl }}" type="{{ $libraryVideo->mime_type ?: 'video/mp4' }}">
                </video>
            @else
                <div style="display:flex;align-items:center;justify-content:center;height:100%;color:rgba(255,255,255,.8);font-size:13px">
                    {{ __('instructor.lib_videos_cannot_play') }}
                </div>
            @endif
        </div>
        @if($libraryVideo->description)
            <p style="padding:14px 16px;margin:0;font-size:13px;color:var(--su-ink-40)">{{ $libraryVideo->description }}</p>
        @endif
    </article>
</div>
@endsection
