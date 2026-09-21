@extends('layouts.instructor-timeline')

@section('title', $thread->student?->name ?: __('instructor.pm_chat_fallback'))
@section('page_title', $thread->student?->name ?: __('instructor.pm_chat_fallback'))

@section('content')
@php
    $student = $thread->student;
    $name = $student?->name ?: __('instructor.pm_student_fallback');
    $avatarFallback = \App\Models\User::placeholderAvatarUrl();
    $avatar = ($student && $student->profile_image) ? $student->profile_image_url : $avatarFallback;
    $messages = $thread->messages->where('is_internal_note', false)->values();
    $meId = (int) auth()->id();
@endphp

<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.private-messages.index') }}">{{ __('instructor.pm_all_messages') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ $name }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-comment-dots su-page-head__ico" aria-hidden="true"></i>
                {{ $name }}
            </h1>
            <p class="su-page-head__sub">{{ $thread->subject ?: __('instructor.pm_private_chat') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.notifications.index') }}" class="su-btn">
                <i class="fas fa-bell" aria-hidden="true"></i>
                {{ __('instructor.notifications') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08);color:#b91c1c;font-size:13px">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="su-card su-card--flush" style="padding:0;overflow:hidden">
        <header style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-bottom:0.5px solid var(--su-line)">
            <img src="{{ $avatar }}" alt="" width="40" height="40" style="width:40px;height:40px;border-radius:12px;object-fit:cover">
            <div class="min-w-0">
                <div style="font-size:14px;font-weight:600;color:var(--su-ink)">{{ $name }}</div>
                <div style="font-size:12px;color:var(--su-ink-40)">{{ $thread->subject ?: __('instructor.pm_private_chat') }}</div>
            </div>
        </header>

        <div style="max-height:28rem;overflow-y:auto;padding:16px;background:var(--su-bg);display:flex;flex-direction:column;gap:10px">
            @forelse($messages as $msg)
                @php $mine = (int) $msg->sender_id === $meId; @endphp
                <div style="display:flex;{{ $mine ? 'justify-content:flex-end' : 'justify-content:flex-start' }}">
                    <div style="max-width:85%;border-radius:16px;padding:10px 14px;font-size:13px;{{ $mine ? 'background:var(--su-ink);color:var(--su-bg)' : 'background:var(--su-bg-2);border:0.5px solid var(--su-line);color:var(--su-ink)' }}">
                        <p style="margin:0 0 4px;font-size:11px;font-weight:600;opacity:.7">
                            {{ $msg->sender->name ?? '' }} · {{ $msg->created_at?->diffForHumans() }}
                        </p>
                        <p style="margin:0;white-space:pre-wrap">{{ $msg->body }}</p>
                    </div>
                </div>
            @empty
                <div class="su-empty" style="padding:32px 8px">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i>
                    <p>{{ __('instructor.pm_empty_thread') }}</p>
                </div>
            @endforelse
        </div>

        <form method="post" action="{{ route('instructor.private-messages.send', $thread) }}" style="padding:16px;border-top:0.5px solid var(--su-line);display:flex;flex-direction:column;gap:12px">
            @csrf
            <div class="su-field" style="margin:0">
                <label for="pm-body">{{ __('instructor.pm_write_placeholder') }}</label>
                <textarea name="body" id="pm-body" rows="3" required maxlength="5000"
                          class="su-input" style="height:auto;min-height:88px;padding:10px 12px;resize:vertical"
                          placeholder="{{ __('instructor.pm_write_placeholder') }}">{{ old('body') }}</textarea>
            </div>
            <div>
                <button type="submit" class="su-btn su-btn--primary">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i>
                    {{ __('instructor.pm_send') }}
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
