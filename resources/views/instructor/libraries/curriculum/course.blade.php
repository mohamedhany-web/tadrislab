@extends('layouts.instructor-timeline')

@section('title', __('instructor.lib_curriculum_title') . ': ' . $course->title)
@section('page_title', $course->title)

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.libraries.curriculum.index') }}">{{ __('instructor.lib_curriculum_title') }}</a>
                <span>/</span>
                <span>{{ $course->academicSubject?->academicYear?->name }}</span>
                <span>/</span>
                <span>{{ $course->academicSubject?->name }}</span>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-layer-group su-page-head__ico" aria-hidden="true"></i>
                {{ $course->title }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.lib_curriculum_course_sub') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.libraries.curriculum.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <section class="su-kpi-row su-kpi-row--3" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.lib_curriculum_kpi_sections') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($course->sections->count()) }}</div>
                <div class="su-kpi__d"><i class="fas fa-folder" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.lib_curriculum_kpi_items') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($course->sections->sum(fn ($s) => $s->items->count())) }}</div>
                <div class="su-kpi__d"><i class="fas fa-list" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.lib_curriculum_kpi_lectures') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($course->lectures->count()) }}</div>
                <div class="su-kpi__d"><i class="fas fa-chalkboard-teacher" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    @forelse($course->sections as $section)
        <section class="su-card su-card--flush" style="margin-bottom:16px">
            <div style="padding:14px 16px;border-bottom:0.5px solid var(--su-line)">
                <h3 class="su-card__title" style="margin:0">{{ $section->title }}</h3>
                @if($section->description)
                    <p style="margin:4px 0 0;font-size:12px;color:var(--su-ink-40)">{{ $section->description }}</p>
                @endif
            </div>
            <div class="su-list" style="padding:12px">
                @forelse($section->items as $item)
                    @php
                        $related = $item->item;
                        $label = $related->title
                            ?? $related->name
                            ?? (class_basename((string) $item->item_type).' #'.$item->item_id);
                    @endphp
                    <div class="su-list-item">
                        <span class="su-list-item__ico su-soft-1"><i class="fas fa-puzzle-piece" aria-hidden="true"></i></span>
                        <div class="su-list-item__body">
                            <div class="su-list-item__title">{{ $label }}</div>
                            <div class="su-list-item__meta">
                                <span class="su-chip" style="height:22px;font-size:10px;text-transform:uppercase">{{ class_basename((string) $item->item_type) }}</span>
                                {{ __('instructor.lib_curriculum_order', ['order' => $item->order]) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="su-empty" style="padding:24px">
                        <p>{{ __('instructor.lib_curriculum_no_section_items') }}</p>
                    </div>
                @endforelse
            </div>
        </section>
    @empty
        <div class="su-empty">
            <i class="fas fa-folder-open" aria-hidden="true"></i>
            <p>{{ __('instructor.lib_curriculum_no_sections') }}</p>
        </div>
    @endforelse
</div>
@endsection
