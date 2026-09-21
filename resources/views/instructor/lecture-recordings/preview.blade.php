@extends('layouts.instructor-timeline')

@section('title', __('instructor.lr_preview_title') . ' — ' . $lecture->title)
@section('page_title', __('instructor.lr_preview_title'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page" style="max-width:56rem">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.lecture-recordings.index') }}">{{ __('instructor.lecture_recordings') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ $lecture->title }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-play-circle su-page-head__ico" aria-hidden="true"></i>
                {{ $lecture->title }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.lr_preview_title') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.lecture-recordings.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <section class="su-card" style="padding:0;overflow:hidden">
        <div style="aspect-ratio:16/9;background:#0f172a">
            @if($embedUrl)
                <iframe src="{{ $embedUrl }}" style="width:100%;height:100%;border:0" allowfullscreen allow="accelerometer; autoplay; encrypted-media; picture-in-picture"></iframe>
            @elseif($directUrl || $fileUrl)
                <video style="width:100%;height:100%" controls playsinline controlsList="nodownload" oncontextmenu="return false;">
                    <source src="{{ $directUrl ?: $fileUrl }}">
                </video>
            @else
                <div class="su-empty" style="height:100%;color:rgba(255,255,255,.6)">
                    <i class="fas fa-video-slash" aria-hidden="true"></i>
                    <p>{{ __('instructor.lr_no_preview') }}</p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
