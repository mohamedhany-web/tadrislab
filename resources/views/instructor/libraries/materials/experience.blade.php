@extends('layouts.instructor-timeline')

@section('title', $material->title ?: $material->file_name)
@section('page_title', $material->title ?: $material->file_name)

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.libraries.materials.index') }}">{{ __('instructor.lib_materials_title') }}</a>
                <span>/</span>
                <a href="{{ route('instructor.libraries.materials.show', $folder) }}">{{ $folder->displayName() }}</a>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-play-circle su-page-head__ico" aria-hidden="true"></i>
                {{ $material->title ?: $material->file_name }}
            </h1>
            <p class="su-page-head__sub">
                {{ $isGame ? __('instructor.lib_materials_play_in') : __('instructor.lib_materials_view_in') }}
            </p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.libraries.materials.show', $folder) }}" class="su-btn">
                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
            <a href="{{ route('instructor.libraries.materials.download', [$folder, $material]) }}" class="su-btn su-btn--primary">
                <i class="fas fa-download" aria-hidden="true"></i>
                {{ __('instructor.download') }}
            </a>
        </div>
    </div>

    <section class="su-card su-card--flush" style="overflow:hidden">
        <iframe
            src="{{ $frameUrl }}"
            title="{{ $material->title ?: $material->file_name }}"
            style="display:block;width:100%;height:75vh;border:0;background:#fff"
            sandbox="allow-scripts allow-same-origin allow-forms"
        ></iframe>
    </section>
</div>
@endsection
