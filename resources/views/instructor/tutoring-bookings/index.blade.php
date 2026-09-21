@extends('layouts.instructor-timeline')

@section('title', __('instructor.group_bookings'))
@section('page_title', __('instructor.group_bookings'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-calendar-check su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.group_bookings') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.tb_subtitle') }}</p>
        </div>
        @if(Route::has('instructor.tutoring-cohorts.index'))
            <div class="su-page-head__actions">
                <a href="{{ route('instructor.tutoring-cohorts.index') }}" class="su-btn">
                    <i class="fas fa-layer-group" aria-hidden="true"></i>
                    {{ __('instructor.class_command') }}
                </a>
            </div>
        @endif
    </div>

    <section class="su-kpi-row su-kpi-row--2" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.tb_upcoming_confirmed') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['upcoming'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-calendar-check" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.tb_pending_review') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($stats['pending'] ?? 0) }}</div>
                <div class="su-kpi__d"><i class="fas fa-hourglass-half" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <section class="su-card su-card--flush">
        <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
            <table class="su-table">
                <thead>
                    <tr>
                        <th>{{ __('instructor.tb_group') }}</th>
                        <th>{{ __('instructor.tb_student') }}</th>
                        <th>{{ __('instructor.tb_when') }}</th>
                        <th>{{ __('instructor.tb_status') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        @php
                            $chip = match ($booking->status) {
                                'confirmed' => 'su-chip--ok',
                                'pending' => 'su-chip--warn',
                                'cancelled' => 'su-chip--off',
                                'completed' => 'su-soft-3',
                                default => '',
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong style="font-weight:600;display:block">{{ $booking->tutoringGroup?->title ?? '—' }}</strong>
                                @if($booking->cohort?->title)
                                    <span style="font-size:11px;color:var(--su-ink-40)">{{ $booking->cohort->title }}</span>
                                @elseif($booking->tutoringGroup?->schoolYear?->name)
                                    <span style="font-size:11px;color:var(--su-ink-40)">{{ $booking->tutoringGroup->schoolYear->name }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="su-person">
                                    <span class="su-avatar">{{ mb_substr($booking->contactName(), 0, 1) }}</span>
                                    <strong>{{ $booking->contactName() }}</strong>
                                </div>
                            </td>
                            <td class="tabular-nums" style="color:var(--su-ink-40);white-space:nowrap">
                                @if($booking->starts_at)
                                    <x-app-datetime :at="$booking->starts_at" pattern="Y-m-d H:i" />
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="su-chip {{ $chip }}">{{ $booking->statusLabel() }}</span>
                            </td>
                            <td style="text-align:end">
                                <a href="{{ route('instructor.tutoring-bookings.show', $booking) }}" class="su-btn" style="height:32px">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                    {{ __('instructor.tb_view') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="su-empty">
                                    <i class="fas fa-calendar-check" aria-hidden="true"></i>
                                    <p>{{ __('instructor.tb_empty') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($bookings, 'links'))
            <div class="su-pager" style="padding:12px">{{ $bookings->links() }}</div>
        @endif
    </section>
</div>
@endsection
