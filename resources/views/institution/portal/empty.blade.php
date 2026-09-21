@extends('layouts.student-timeline')

@section('title', 'بوابة الجهة')

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $isRtl ? 'بوابة الجهة' : 'Institution portal',
    'crumbs' => [
        ['label' => __('student_timeline.school_gate'), 'url' => route('dashboard')],
        ['label' => $isRtl ? 'بوابة الجهة' : 'Institution portal', 'url' => null],
    ],
])

<section class="st-join-hero st-join-hero--muted">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">TADRIS LAB</p>
        <h2 class="st-join-hero__title">{{ $isRtl ? 'لست مرتبطًا بعضوية جهة نشطة' : 'No active institution membership' }}</h2>
        <p class="st-join-hero__meta">
            {{ $isRtl
                ? 'تواصل مع الإدارة أو أرسل استفسار مدرسة/مؤسسة للانضمام.'
                : 'Contact admin or send a school/institution inquiry to join.' }}
        </p>
    </div>
    <div class="st-join-hero__actions">
        @if(Route::has('public.institutions.inquiry'))
            <a href="{{ route('public.institutions.inquiry') }}" class="st-pill st-pill--solid st-pill--lg">{{ $isRtl ? 'استفسار مؤسسة' : 'Institution inquiry' }}</a>
        @endif
        <a href="{{ route('dashboard') }}" class="st-pill st-pill--outline">{{ $isRtl ? 'لوحة التحكم' : 'Dashboard' }}</a>
    </div>
</section>
@endsection
