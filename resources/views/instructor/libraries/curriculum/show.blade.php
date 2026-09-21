@extends('layouts.instructor-timeline')

@section('title', $item->title . ' — ' . __('instructor.lib_curriculum_title'))
@section('page_title', $item->title)

@section('content')
@php
    $hasSections = isset($sectionTree) && $sectionTree->isNotEmpty();
    $hasLegacyFiles = $item->files && $item->files->isNotEmpty();
    $hasHtmlContent = filled($item->content ?? null);
@endphp
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.libraries.curriculum.index') }}">{{ __('instructor.lib_curriculum_title') }}</a>
                @if($item->category)
                    <span>/</span>
                    <span>{{ $item->category->name }}</span>
                @endif
                @if($item->subject)
                    <span>/</span>
                    <span>{{ $item->subject }}</span>
                @endif
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-book-open su-page-head__ico" aria-hidden="true"></i>
                {{ $item->title }}
            </h1>
            @if($item->description)
                <p class="su-page-head__sub">{{ $item->description }}</p>
            @endif
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.libraries.curriculum.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08);color:#b91c1c;font-size:13px">
            {{ session('error') }}
        </div>
    @endif

    @if($hasSections)
        <div class="su-list" style="margin-bottom:20px">
            @include('instructor.libraries.curriculum._section-node', ['sections' => $sectionTree, 'item' => $item, 'depth' => 0])
        </div>
    @endif

    @if($hasLegacyFiles)
        <section class="su-card su-card--flush" style="margin-bottom:20px">
            <div style="padding:14px 16px;border-bottom:0.5px solid var(--su-line)">
                <h3 class="su-card__title" style="margin:0">{{ __('instructor.lib_curriculum_legacy_files') }}</h3>
            </div>
            <div class="su-list" style="padding:12px">
                @foreach($item->files as $file)
                    <div class="su-list-item">
                        <span class="su-list-item__ico su-soft-3"><i class="fas fa-file" aria-hidden="true"></i></span>
                        <div class="su-list-item__body">
                            <div class="su-list-item__title">{{ $file->label ?: $file->file_type }}</div>
                        </div>
                        <div class="su-list-item__actions">
                            @if($file->file_type === 'html')
                                <a href="{{ route('curriculum-library.file.view', [$item, $file]) }}" target="_blank" rel="noopener" class="su-btn su-btn--primary" style="height:32px">{{ __('common.view') }}</a>
                            @elseif($file->file_type === 'presentation')
                                <a href="{{ route('curriculum-library.file.presentation', [$item, $file]) }}" target="_blank" rel="noopener" class="su-btn su-btn--primary" style="height:32px">{{ __('instructor.lib_curriculum_interactive_view') }}</a>
                            @elseif($file->file_type === 'pdf')
                                <a href="{{ route('curriculum-library.file.pdf', [$item, $file]) }}" target="_blank" rel="noopener" class="su-btn" style="height:32px">{{ __('common.view') }}</a>
                                <a href="{{ route('curriculum-library.file.download', [$item, $file]) }}" class="su-btn su-btn--primary" style="height:32px">{{ __('instructor.download') }}</a>
                            @else
                                <a href="{{ route('curriculum-library.file.download', [$item, $file]) }}" class="su-btn su-btn--primary" style="height:32px">{{ __('instructor.download') }}</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if($hasHtmlContent)
        <article class="su-card su-prose-box" style="margin-bottom:20px">
            <div class="su-prose-body">{!! $item->content !!}</div>
        </article>
    @endif

    @if(! $hasSections && ! $hasLegacyFiles && ! $hasHtmlContent)
        <div class="su-empty">
            <i class="fas fa-inbox" aria-hidden="true"></i>
            <p>{{ __('instructor.lib_curriculum_empty_content') }}</p>
        </div>
    @endif
</div>
@endsection
