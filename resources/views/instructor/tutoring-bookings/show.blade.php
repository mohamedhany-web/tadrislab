@extends('layouts.instructor-timeline')

@section('title', __('instructor.tb_show_title'))
@section('page_title', __('instructor.tb_show_title'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $status = $booking->status;
    $chip = match ($status) {
        'confirmed' => 'su-chip--ok',
        'pending' => 'su-chip--warn',
        'cancelled' => 'su-chip--off',
        'completed' => 'su-soft-3',
        default => '',
    };
@endphp

<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.tutoring-bookings.index') }}">{{ __('instructor.group_bookings') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('instructor.tb_show_title') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-calendar-check su-page-head__ico" aria-hidden="true"></i>
                {{ $booking->tutoringGroup?->title ?? __('instructor.tb_show_title') }}
            </h1>
            <div class="su-chip-row" style="margin-top:8px">
                <span class="su-chip {{ $chip }}">{{ $booking->statusLabel() }}</span>
            </div>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.tutoring-bookings.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.tb_back') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:14px 18px;background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.25);color:#15803d;font-size:13px;font-weight:600">
            <i class="fas fa-check-circle" aria-hidden="true"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="su-card" style="margin-bottom:16px;padding:14px 18px;background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.25);color:#b91c1c;font-size:13px;font-weight:600">
            <i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ session('error') }}
        </div>
    @endif

    <section class="su-card" style="max-width:640px">
        <h2 class="su-card__title">
            <i class="fas fa-info-circle" aria-hidden="true"></i>
            {{ __('instructor.tb_show_title') }}
        </h2>

        <div class="su-dl">
            <div class="su-dl__item">
                <label>{{ __('instructor.tb_student') }}</label>
                <div>{{ $booking->contactName() }}</div>
            </div>
            <div class="su-dl__item">
                <label>{{ __('instructor.tb_when') }}</label>
                <div>
                    @if($booking->starts_at)
                        <x-app-datetime :at="$booking->starts_at" pattern="Y-m-d H:i" />
                    @else
                        —
                    @endif
                </div>
            </div>
            <div class="su-dl__item">
                <label>{{ __('instructor.tb_ends') }}</label>
                <div>
                    @if($booking->ends_at)
                        <x-app-datetime :at="$booking->ends_at" pattern="Y-m-d H:i" />
                    @else
                        —
                    @endif
                </div>
            </div>
            <div class="su-dl__item">
                <label>{{ __('instructor.tb_status') }}</label>
                <div><span class="su-chip {{ $chip }}">{{ $booking->statusLabel() }}</span></div>
            </div>
            <div class="su-dl__item">
                <label>{{ __('instructor.tb_source') }}</label>
                <div>
                    {{ $booking->student_service_entitlement_id
                        ? __('instructor.tb_source_credit')
                        : __('instructor.tb_source_direct') }}
                </div>
            </div>
            @if($booking->student_notes)
                <div class="su-dl__item" style="grid-column:1 / -1">
                    <label>{{ __('instructor.tb_student_notes') }}</label>
                    <div>{{ $booking->student_notes }}</div>
                </div>
            @endif
        </div>

        @if($booking->classroomMeeting && $booking->status === \App\Models\TutoringGroupBooking::STATUS_CONFIRMED)
            <form method="POST" action="{{ route('instructor.classroom.start-meeting', $booking->classroomMeeting) }}" style="margin-top:20px">
                @csrf
                <button type="submit" class="su-btn su-btn--primary" style="width:100%;height:44px;justify-content:center">
                    <i class="fas fa-video" aria-hidden="true"></i>
                    {{ $booking->classroomMeeting->started_at && ! $booking->classroomMeeting->ended_at
                        ? __('instructor.tb_enter_live')
                        : __('instructor.tb_start_live') }}
                </button>
            </form>
            <p style="margin:10px 0 0;text-align:center;font-size:12px;color:var(--su-ink-40)">
                {{ __('instructor.tb_start_first_hint') }}
                @if($booking->classroomMeeting->code)
                    · {{ __('instructor.tb_room_code') }}:
                    <span class="tabular-nums" style="font-weight:700" dir="ltr">{{ $booking->classroomMeeting->code }}</span>
                @endif
            </p>
            <div class="su-form-actions" style="margin-top:10px;align-items:stretch">
                <input type="text" readonly value="{{ url('classroom/join/'.$booking->classroomMeeting->code) }}"
                       class="su-input" style="flex:1;font-size:12px" dir="ltr"
                       id="tb-guest-link">
                <button type="button"
                        onclick="navigator.clipboard.writeText(document.getElementById('tb-guest-link').value)"
                        class="su-btn" style="height:40px">
                    {{ __('instructor.tb_copy_guest_link') }}
                </button>
            </div>
        @endif

        @if($booking->status === \App\Models\TutoringGroupBooking::STATUS_CONFIRMED)
            <p style="margin:16px 0 8px;text-align:center;font-size:12px;color:var(--su-ink-40)">
                {{ __('instructor.tb_complete_hint') }}
            </p>
            <form method="POST" action="{{ route('instructor.tutoring-bookings.complete', $booking) }}"
                  onsubmit="return confirm(@json(__('instructor.tb_complete_confirm')));">
                @csrf
                <button type="submit" class="su-btn" style="width:100%;height:44px;justify-content:center;background:rgba(34,197,94,.12);color:#15803d;border-color:rgba(34,197,94,.3)">
                    <i class="fas fa-circle-check" aria-hidden="true"></i>
                    {{ __('instructor.tb_complete_manual') }}
                </button>
            </form>
        @elseif($booking->status === \App\Models\TutoringGroupBooking::STATUS_COMPLETED)
            <div style="margin-top:16px;padding:14px;border-radius:var(--su-r-12);background:rgba(34,197,94,.1);text-align:center;font-size:13px;font-weight:600;color:#15803d">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
                {{ __('instructor.tb_completed_msg') }}
            </div>
        @endif
    </section>
</div>
@endsection
