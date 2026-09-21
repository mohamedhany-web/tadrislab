@extends('layouts.instructor-timeline')

@section('title', __('instructor.tc_title'))
@section('page_title', __('instructor.tc_title'))

@section('content')
@php
    $instructorName = $overview['instructor_name'] ?? auth()->user()->name;
@endphp

<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-chalkboard-teacher su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.tc_title') }}
            </h1>
            <p class="su-page-head__sub">
                {{ __('instructor.tc_hello', ['name' => $instructorName]) }}
                — {{ __('instructor.tc_subtitle') }}
            </p>
        </div>
    </div>

    <section class="su-kpi-row su-kpi-row--3" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.tc_cohorts') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($overview['cohorts_count'] ?? $cohorts->total()) }}</div>
                <div class="su-kpi__d"><i class="fas fa-layer-group" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.tc_students') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($overview['students_count'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-user-graduate" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.tc_sessions_today') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($overview['sessions_today'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-calendar-day" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <section class="su-card su-card--flush">
        <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
            <table class="su-table">
                <thead>
                    <tr>
                        <th>{{ __('instructor.tc_col_cohort') }}</th>
                        <th>{{ __('instructor.tc_col_group') }}</th>
                        <th>{{ __('instructor.tc_students') }}</th>
                        <th>{{ __('instructor.tc_col_starts') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cohorts as $cohort)
                        <tr>
                            <td>
                                <strong style="font-weight:600">{{ $cohort->title }}</strong>
                            </td>
                            <td>
                                <div style="font-weight:500">{{ $cohort->tutoringGroup?->title ?? '—' }}</div>
                                @if($cohort->tutoringGroup?->schoolYear)
                                    <div style="font-size:11px;color:var(--su-ink-40);margin-top:2px">
                                        {{ $cohort->tutoringGroup->schoolYear->name }}
                                    </div>
                                @endif
                            </td>
                            <td class="tabular-nums">
                                <span class="su-chip su-soft-1">
                                    {{ $cohort->students_count ?? $cohort->enrolled_count }}/{{ $cohort->capacity }}
                                </span>
                            </td>
                            <td class="tabular-nums" style="color:var(--su-ink-40)">
                                @if($cohort->starts_at)
                                    <x-app-datetime :at="$cohort->starts_at" pattern="Y-m-d" />
                                @else
                                    —
                                @endif
                            </td>
                            <td style="text-align:end">
                                <a href="{{ route('instructor.tutoring-cohorts.show', $cohort) }}" class="su-btn su-btn--primary" style="height:32px">
                                    {{ __('instructor.tc_open') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="su-empty">
                                    <i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
                                    <p>{{ __('instructor.tc_empty') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($cohorts, 'links'))
            <div class="su-pager" style="padding:12px">{{ $cohorts->links() }}</div>
        @endif
    </section>
</div>
@endsection
