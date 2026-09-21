@extends('layouts.instructor-timeline')

@section('title', __('instructor.o1o_schedule_session'))
@section('page_title', __('instructor.o1o_schedule_session'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.one-to-one-sessions.index') }}">{{ __('instructor.o1o_title') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ $session->course->title ?? '—' }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-chalkboard-teacher su-page-head__ico" aria-hidden="true"></i>
                {{ $session->course->title ?? '—' }}
            </h1>
            <p class="su-page-head__sub">
                {{ $session->student->name ?? __('instructor.pm_student_fallback') }}
                — {{ __('instructor.o1o_session_number', ['n' => $session->session_number]) }}
            </p>
        </div>
        <div class="su-page-head__actions">
            <span class="su-chip">{{ $session->statusLabel() }}</span>
            <a href="{{ route('instructor.one-to-one-sessions.index') }}" class="su-btn">
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
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(185,28,28,.35);background:rgba(185,28,28,.08);color:#b91c1c;font-size:13px">
            {{ session('error') }}
        </div>
    @endif

    <section class="su-card">
        @if($session->status === \App\Models\OneToOneSession::STATUS_SCHEDULED && $session->classroomMeeting)
            @php $m = $session->classroomMeeting; @endphp
            <div class="su-card su-soft-3" style="padding:16px;margin-bottom:16px">
                <p style="margin:0 0 12px;font-size:14px;font-weight:600;color:var(--su-ink)">
                    {{ __('instructor.cons_appointment') }}:
                    <x-app-datetime :at="$session->scheduled_at" />
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    <a href="{{ route('instructor.classroom.show', $m) }}" class="su-btn">
                        <i class="fas fa-cog" aria-hidden="true"></i>
                        {{ __('instructor.cons_room_settings') }}
                    </a>
                    @if(!$m->ended_at)
                        <a href="{{ route('instructor.classroom.room', $m) }}" class="su-btn su-btn--ok">
                            <i class="fas fa-video" aria-hidden="true"></i>
                            {{ __('instructor.cons_enter_room') }}
                        </a>
                    @endif
                </div>
            </div>
            @php
                $canMarkComplete = $m->started_at
                    && $session->scheduled_at
                    && $session->scheduled_at->lte(now()->addMinutes(15));
            @endphp
            @if($canMarkComplete)
                <form method="POST" action="{{ route('instructor.one-to-one-sessions.complete', $session) }}" onsubmit="return confirm(@json(__('instructor.o1o_complete_confirm')))">
                    @csrf
                    <button type="submit" class="su-btn su-btn--primary">
                        <i class="fas fa-check" aria-hidden="true"></i>
                        {{ __('instructor.o1o_mark_complete') }}
                    </button>
                </form>
            @else
                <div class="su-card su-soft-4" style="padding:12px 16px;font-size:13px;color:var(--su-ink)">
                    <i class="fas fa-info-circle" aria-hidden="true"></i>
                    {{ __('instructor.o1o_complete_requires_room') }}
                </div>
            @endif
        @elseif($session->status === \App\Models\OneToOneSession::STATUS_PENDING)
            <div class="su-card su-soft-4" style="padding:16px;margin-bottom:16px">
                <p style="margin:0;font-size:13px;color:var(--su-ink)">{{ __('instructor.o1o_pending_hint') }}</p>
                <a href="{{ route('instructor.one-to-one-availability.index') }}" class="su-btn" style="margin-top:12px;height:32px">
                    <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                    {{ __('instructor.o1a_title') }}
                </a>
            </div>
            <form method="POST" action="{{ route('instructor.one-to-one-sessions.schedule', $session) }}" style="display:flex;flex-direction:column;gap:14px;padding-top:4px;border-top:0.5px solid var(--su-line)">
                @csrf
                @php $tzCurrent = old('timezone', auth()->user()?->timezoneCode()); @endphp
                @include('partials.timezone-select', [
                    'value' => $tzCurrent,
                    'class' => 'su-select',
                    'labelClass' => 'block text-[12px] font-medium mb-1.5',
                ])
                <div class="su-field">
                    <label for="scheduled_at">{{ __('instructor.o1o_datetime_label') }}</label>
                    <input type="datetime-local" name="scheduled_at" id="scheduled_at" required
                           class="su-input"
                           min="{{ now()->timezone($tzCurrent)->addHour()->format('Y-m-d\TH:i') }}">
                    @error('scheduled_at')
                        <p style="margin:6px 0 0;font-size:12px;color:#b91c1c">{{ $message }}</p>
                    @enderror
                </div>
                <div class="su-field">
                    <label for="duration_minutes">{{ __('instructor.o1o_duration_label') }}</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="50" min="50" max="50" readonly class="su-input">
                    <p style="margin:6px 0 0;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.o1o_duration_fixed') }}</p>
                </div>
                <div>
                    <button type="submit" class="su-btn su-btn--primary">
                        <i class="fas fa-calendar-plus" aria-hidden="true"></i>
                        {{ __('instructor.o1o_schedule_session') }}
                    </button>
                </div>
            </form>
        @elseif($session->status === \App\Models\OneToOneSession::STATUS_COMPLETED && $session->classroomMeeting)
            @php $m = $session->classroomMeeting; @endphp
            <div class="su-card su-soft-3" style="padding:16px;margin-bottom:16px">
                <p style="margin:0 0 8px;font-size:14px;font-weight:600;color:var(--su-ink)">
                    {{ __('instructor.cons_appointment') }}:
                    @if($session->scheduled_at)
                        <x-app-datetime :at="$session->scheduled_at" />
                    @endif
                </p>
                @if($m->hasBrowserRecording())
                    <p style="margin:0 0 12px;font-size:13px;color:#15803d">
                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                        تم رفع التسجيل — متاح للطالب من صفحة الحصة.
                    </p>
                    @if($m->recording_download_url || $m->recording_audio_download_url)
                        <a href="{{ $m->recording_download_url ?: $m->recording_audio_download_url }}" target="_blank" rel="noopener" class="su-btn su-btn--ok">
                            <i class="fas fa-play-circle" aria-hidden="true"></i>
                            مشاهدة التسجيل
                        </a>
                    @endif
                @else
                    <p style="margin:0;font-size:13px;color:var(--su-ink-40)">
                        لا يوجد تسجيل مرفوع لهذه الحصة بعد.
                    </p>
                @endif
            </div>
        @endif
    </section>
</div>
@endsection
