@extends('layouts.instructor-timeline')

@section('title', __('instructor.request_details_title') . ' - ' . config('app.name'))
@section('page_title', __('instructor.request_details_title'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $chip = match ($request->status) {
        'pending' => 'su-chip--warn',
        'approved' => 'su-chip--ok',
        default => 'su-chip--off',
    };
    $label = match ($request->status) {
        'pending' => __('instructor.pending_review'),
        'approved' => __('instructor.approved'),
        default => __('instructor.rejected'),
    };
@endphp
<div class="su-page" style="max-width:48rem">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.management-requests.index') }}">{{ __('instructor.my_requests_to_management') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ Str::limit($request->subject, 40) }}</strong>
            </nav>
            <h1 class="su-page-head__title">{{ $request->subject }}</h1>
            <div class="su-chip-row">
                <span class="su-chip {{ $chip }}">{{ $label }}</span>
                <span class="su-chip su-soft-2">{{ $request->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.management-requests.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back_to_list') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            {{ session('success') }}
        </div>
    @endif

    <section class="su-card" style="margin-bottom:16px">
        <h2 class="su-card__title"><i class="fas fa-align-left" aria-hidden="true"></i> {{ __('instructor.request_text_label') }}</h2>
        <div class="su-prose-body" style="white-space:pre-wrap">{{ $request->message }}</div>
    </section>

    @if($request->admin_reply)
        <section class="su-card" style="border-color:rgba(59,130,246,.25);background:rgba(59,130,246,.06)">
            <h2 class="su-card__title"><i class="fas fa-reply" aria-hidden="true"></i> {{ __('instructor.admin_response_label') }}</h2>
            <div class="su-prose-body" style="white-space:pre-wrap">{{ $request->admin_reply }}</div>
            <p style="margin:12px 0 0;font-size:12px;color:var(--su-ink-40)">
                {{ $request->replied_at?->format('Y-m-d H:i') }}
                @if($request->repliedByUser)
                    — {{ $request->repliedByUser->name }}
                @endif
            </p>
        </section>
    @endif
</div>
@endsection
