@extends('layouts.instructor-timeline')

@section('title', __('instructor.submit_request_title') . ' - ' . config('app.name'))
@section('page_title', __('instructor.submit_request_title'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page" style="max-width:40rem">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.management-requests.index') }}">{{ __('instructor.my_requests_to_management') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('instructor.new_request') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-paper-plane su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.submit_new_request_title') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.submit_request_desc') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.management-requests.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <section class="su-card">
        <form action="{{ route('instructor.management-requests.store') }}" method="POST">
            @csrf
            <div class="su-form-grid" style="grid-template-columns:1fr">
                <div class="su-field">
                    <label>{{ __('instructor.request_subject_required') }}</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required class="su-input"
                           placeholder="{{ __('instructor.subject_placeholder') }}">
                    @error('subject')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
                <div class="su-field">
                    <label>{{ __('instructor.request_details_required') }}</label>
                    <textarea name="message" rows="6" required class="su-input" style="min-height:140px;resize:vertical"
                              placeholder="{{ __('instructor.message_placeholder') }}">{{ old('message') }}</textarea>
                    @error('message')<p class="su-field-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="su-form-actions" style="margin-top:16px;justify-content:flex-end;gap:8px">
                <a href="{{ route('instructor.management-requests.index') }}" class="su-btn">{{ __('common.cancel') }}</a>
                <button type="submit" class="su-btn su-btn--primary">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i>
                    {{ __('instructor.send_request') }}
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
