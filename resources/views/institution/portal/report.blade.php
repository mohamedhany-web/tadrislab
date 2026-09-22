@extends('layouts.student-timeline')

@section('title', ($isRtl ?? app()->getLocale() === 'ar') ? 'تقرير المتابعة' : 'Progress report')

@section('content')
@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
@endphp

@include('partials.student-timeline-top', [
    'locale' => $locale,
    'pageTitle' => $isRtl ? 'تقرير المتابعة' : 'Progress report',
    'crumbs' => [
        ['label' => $institution->name(), 'url' => route('institution.portal.show', $institution)],
        ['label' => $isRtl ? 'التقرير' : 'Report', 'url' => null],
    ],
])

<section class="st-join-hero">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">{{ $institution->engagementModeLabel() }}</p>
        <h2 class="st-join-hero__title">{{ $isRtl ? 'متابعة المشاركين والتقدّم' : 'Participants & progress' }}</h2>
        <p class="st-join-hero__meta">{{ $institution->name() }}</p>
    </div>
    <div class="st-join-hero__actions">
        <a href="{{ route('institution.portal.show', $institution) }}" class="st-pill st-pill--outline">{{ $isRtl ? 'رجوع' : 'Back' }}</a>
    </div>
</section>

<section class="st-stats st-stats--classes">
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ $isRtl ? 'البرامج' : 'Programs' }}</p>
        <p class="st-stat-card__value">{{ $report['programs'] }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ $isRtl ? 'المشاركون' : 'Participants' }}</p>
        <p class="st-stat-card__value">{{ $report['participants'] }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ $isRtl ? 'مكتمل' : 'Completed' }}</p>
        <p class="st-stat-card__value">{{ $report['completed'] }}</p>
    </article>
    <article class="st-stat-card">
        <p class="st-stat-card__label">{{ $isRtl ? 'متوسط التقدّم' : 'Avg progress' }}</p>
        <p class="st-stat-card__value">{{ $report['avg_progress'] }}%</p>
    </article>
</section>

<section class="st-panel" style="overflow-x:auto">
    <table class="st-table" style="width:100%;border-collapse:collapse;font-size:.9rem">
        <thead>
            <tr style="text-align:start;border-bottom:1px solid #e2e8f0">
                <th style="padding:.6rem">{{ $isRtl ? 'البرنامج' : 'Program' }}</th>
                <th style="padding:.6rem">{{ $isRtl ? 'المسار' : 'Mode' }}</th>
                <th style="padding:.6rem">{{ $isRtl ? 'المشارك' : 'Participant' }}</th>
                <th style="padding:.6rem">{{ $isRtl ? 'الحالة' : 'Status' }}</th>
                <th style="padding:.6rem">{{ $isRtl ? 'التقدّم' : 'Progress' }}</th>
                <th style="padding:.6rem">{{ $isRtl ? 'المدرب' : 'Coach' }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report['rows'] as $row)
                <tr style="border-bottom:1px solid #f1f5f9">
                    <td style="padding:.6rem">{{ $row['program'] }}</td>
                    <td style="padding:.6rem">{{ $row['mode_label'] }}</td>
                    <td style="padding:.6rem">
                        {{ $row['name'] }}
                        @if($row['email'])<br><span style="font-size:.75rem;opacity:.7">{{ $row['email'] }}</span>@endif
                    </td>
                    <td style="padding:.6rem">{{ $row['status'] }}</td>
                    <td style="padding:.6rem">{{ $row['progress'] }}%</td>
                    <td style="padding:.6rem">{{ $row['coach'] ?: '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding:1.5rem;text-align:center;opacity:.7">
                        {{ $isRtl ? 'لا بيانات متابعة بعد.' : 'No tracking data yet.' }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
