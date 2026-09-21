@extends('layouts.instructor-timeline')

@section('title', __('instructor.ls_create_title') . ' - ' . config('app.name'))
@section('page_title', __('instructor.ls_create_title'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp

<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.live-sessions.index') }}">{{ __('instructor.ls_title') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('instructor.ls_create_title') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-broadcast-tower su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.ls_create_title') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.ls_create_subtitle') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.live-sessions.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.ls_back_list') }}
            </a>
        </div>
    </div>

    <section class="su-card">
        <h2 class="su-card__title">
            <i class="fas fa-info-circle" aria-hidden="true"></i>
            {{ __('instructor.ls_session_info') }}
        </h2>

        <form method="POST" action="{{ route('instructor.live-sessions.store') }}">
            @csrf

            <div class="su-field" style="margin-bottom:14px">
                <label for="live_title">{{ __('instructor.ls_session_title') }} <span style="color:#b91c1c">*</span></label>
                <input type="text" name="title" id="live_title" value="{{ old('title') }}" required
                       class="su-input"
                       placeholder="{{ $isRtl ? 'مثال: مراجعة الوحدة الثانية — جلسة تفاعلية' : 'e.g. Unit 2 review — interactive session' }}">
                @error('title')
                    <p style="margin:6px 0 0;font-size:12px;color:#b91c1c">{{ $message }}</p>
                @enderror
            </div>

            <div class="su-field" style="margin-bottom:14px">
                <label for="course_id">{{ __('instructor.ls_course_optional') }}</label>
                <select name="course_id" id="course_id" class="su-select">
                    <option value="">{{ __('instructor.ls_general_session') }}</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
                <p style="margin:6px 0 0;font-size:11px;color:var(--su-ink-40)">{{ __('instructor.ls_course_hint') }}</p>
                @error('course_id')
                    <p style="margin:6px 0 0;font-size:12px;color:#b91c1c">{{ $message }}</p>
                @enderror
            </div>

            <div class="su-form-grid" style="margin-bottom:14px;align-items:start">
                <div class="su-field">
                    @include('partials.timezone-select', [
                        'value' => old('timezone', auth()->user()?->timezoneCode()),
                        'class' => 'su-select',
                        'labelClass' => '',
                        'hint' => null,
                    ])
                </div>
                <div class="su-field">
                    <label for="scheduled_at">{{ __('instructor.ls_scheduled_at') }} <span style="color:#b91c1c">*</span></label>
                    <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at') }}" required
                           class="su-input">
                    @error('scheduled_at')
                        <p style="margin:6px 0 0;font-size:12px;color:#b91c1c">{{ $message }}</p>
                    @enderror
                </div>
                <div class="su-field">
                    <label for="max_participants">{{ __('instructor.ls_max_participants') }}</label>
                    <input type="number" name="max_participants" id="max_participants" value="{{ old('max_participants', 100) }}" min="2" max="500"
                           class="su-input">
                    @error('max_participants')
                        <p style="margin:6px 0 0;font-size:12px;color:#b91c1c">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="su-field" style="margin-bottom:14px">
                <label for="password">{{ __('instructor.ls_password_optional') }}</label>
                <input type="text" name="password" id="password" value="{{ old('password') }}" autocomplete="off"
                       class="su-input"
                       placeholder="{{ __('instructor.ls_password_ph') }}">
                @error('password')
                    <p style="margin:6px 0 0;font-size:12px;color:#b91c1c">{{ $message }}</p>
                @enderror
            </div>

            <div class="su-field" style="margin-bottom:20px">
                <label for="description">{{ __('instructor.ls_description') }}</label>
                <textarea name="description" id="description" rows="4"
                          class="su-input" style="height:auto;min-height:100px;padding:10px 12px;resize:vertical"
                          placeholder="{{ __('instructor.ls_description_ph') }}">{{ old('description') }}</textarea>
                @error('description')
                    <p style="margin:6px 0 0;font-size:12px;color:#b91c1c">{{ $message }}</p>
                @enderror
            </div>

            <div class="su-form-actions" style="justify-content:flex-end;padding-top:16px;border-top:0.5px solid var(--su-line)">
                <a href="{{ route('instructor.live-sessions.index') }}" class="su-btn" style="height:40px">
                    <i class="fas fa-times" aria-hidden="true"></i>
                    {{ __('instructor.ls_cancel') }}
                </a>
                <button type="submit" class="su-btn su-btn--primary" style="height:40px">
                    <i class="fas fa-broadcast-tower" aria-hidden="true"></i>
                    {{ __('instructor.ls_create') }}
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
