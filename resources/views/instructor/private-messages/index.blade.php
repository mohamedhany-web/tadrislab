@extends('layouts.instructor-timeline')

@section('title', __('instructor.pm_title'))
@section('page_title', __('instructor.pm_title'))

@section('content')
@php
    $avatarFallback = \App\Models\User::placeholderAvatarUrl();
@endphp

<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-comments su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.pm_title') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.pm_subtitle') }}</p>
        </div>
    </div>

    <section class="su-card" style="margin-bottom:20px">
        <form method="GET" class="su-form-grid" style="grid-template-columns:minmax(0,1fr) auto">
            <div class="su-field">
                <label for="pm-q">{{ __('common.search') }}</label>
                <input type="search" name="q" id="pm-q" value="{{ $searchQuery }}"
                       placeholder="{{ __('instructor.pm_search_placeholder') }}"
                       class="su-input">
            </div>
            <div class="su-form-actions">
                <button type="submit" class="su-btn su-btn--primary" style="height:40px">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    {{ __('common.search') }}
                </button>
            </div>
        </form>
    </section>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            {{ session('success') }}
        </div>
    @endif

    <section class="su-list">
        @forelse($threads as $thread)
            @php
                $student = $thread->student;
                $name = $student?->name ?: __('instructor.pm_student_fallback');
                $preview = optional($thread->messages->first())->body;
                $avatar = ($student && $student->profile_image) ? $student->profile_image_url : $avatarFallback;
            @endphp
            <a href="{{ route('instructor.private-messages.show', $thread) }}" class="su-list-item" style="text-decoration:none;color:inherit">
                <img src="{{ $avatar }}" alt="" width="40" height="40" style="width:40px;height:40px;border-radius:12px;object-fit:cover;flex-shrink:0">
                <div class="su-list-item__body">
                    <div class="su-list-item__title">{{ $name }}</div>
                    <div class="su-list-item__meta">
                        {{ \Illuminate\Support\Str::limit($preview ?: __('instructor.pm_start_chat'), 80) }}
                    </div>
                </div>
                <span style="font-size:11px;color:var(--su-ink-40);flex-shrink:0">
                    {{ $thread->last_message_at?->diffForHumans() }}
                </span>
            </a>
        @empty
            <div class="su-card">
                <div class="su-empty" style="padding:48px 16px">
                    <i class="fas fa-comments" aria-hidden="true"></i>
                    <p>{{ __('instructor.pm_empty') }}</p>
                </div>
            </div>
        @endforelse
    </section>

    @if(method_exists($threads, 'hasPages') && $threads->hasPages())
        <div class="su-pager">{{ $threads->links() }}</div>
    @endif
</div>
@endsection
