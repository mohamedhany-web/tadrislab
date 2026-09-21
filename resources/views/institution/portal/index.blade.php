@extends('layouts.student-timeline')

@section('title', 'جهاتي')

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $isRtl ? 'جهاتي' : 'My institutions',
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => $isRtl ? 'جهاتي' : 'My institutions', 'url' => null],
    ],
])

<section class="st-join-hero" aria-label="{{ $isRtl ? 'جهاتي' : 'My institutions' }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">TADRIS LAB</p>
        <h2 class="st-join-hero__title">{{ $isRtl ? 'المدارس والمؤسسات' : 'Schools & institutions' }}</h2>
        <p class="st-join-hero__meta">{{ $isRtl ? 'اختر جهة لعرض البرامج والأعضاء.' : 'Choose an institution to view programs and members.' }}</p>
    </div>
    <div class="st-join-hero__actions">
        <a href="{{ route('dashboard') }}" class="st-pill st-pill--outline">{{ $isRtl ? 'لوحة التحكم' : 'Dashboard' }}</a>
    </div>
</section>

<section class="st-stats st-stats--classes">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ $isRtl ? 'الجهات' : 'Institutions' }}</p>
        <p class="st-stat-card__value">{{ $memberships->count() }}</p>
    </article>
</section>

<section class="st-inst-grid" aria-label="{{ $isRtl ? 'جهاتي' : 'My institutions' }}">
    @foreach($memberships as $m)
        <a href="{{ route('institution.portal.show', $m->institution) }}" class="st-panel st-inst-card">
            <p class="st-inst-card__role">{{ $m->roleLabel() }}</p>
            <h3>{{ $m->institution?->name() ?? '—' }}</h3>
            <p class="st-inst-card__meta">{{ $m->institution?->orgTypeLabel() }}</p>
            <span class="st-pill st-pill--outline st-pill--sm">{{ $isRtl ? 'فتح البوابة' : 'Open portal' }}</span>
        </a>
    @endforeach
</section>
@endsection
