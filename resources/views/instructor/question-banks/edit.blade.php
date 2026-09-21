@extends('layouts.instructor-timeline')

@section('title', __('instructor.edit_question_bank') . ' - ' . config('app.name'))
@section('page_title', __('instructor.edit_question_bank'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.question-banks.index') }}">{{ __('instructor.question_banks') }}</a>
                <span>/</span>
                <a href="{{ route('instructor.question-banks.show', $questionBank) }}">{{ Str::limit($questionBank->title, 40) }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('common.edit') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-edit su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.edit_question_bank') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.edit_question_bank_sub') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.question-banks.show', $questionBank) }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <form action="{{ route('instructor.question-banks.update', $questionBank) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="su-detail-grid">
            <div style="display:flex;flex-direction:column;gap:16px;min-width:0">
                <section class="su-card">
                    <h2 class="su-card__title"><i class="fas fa-info-circle" aria-hidden="true"></i> {{ __('instructor.question_bank_info') }}</h2>
                    <div class="su-form-grid" style="grid-template-columns:1fr">
                        <div class="su-field">
                            <label for="title">{{ __('instructor.title_required') }} <span style="color:#b91c1c">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $questionBank->title) }}" required class="su-input"
                                   placeholder="{{ __('instructor.question_bank_title_placeholder') }}">
                            @error('title')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field">
                            <label for="description">{{ __('instructor.description') }}</label>
                            <textarea name="description" id="description" rows="4" class="su-input" style="min-height:100px;resize:vertical"
                                      placeholder="{{ __('instructor.description_placeholder') }}">{{ old('description', $questionBank->description) }}</textarea>
                        </div>
                        <div class="su-field" style="max-width:16rem">
                            <label for="difficulty">{{ __('instructor.difficulty') }}</label>
                            <select name="difficulty" id="difficulty" class="su-select">
                                <option value="">{{ __('instructor.optional_label') }}</option>
                                <option value="easy" {{ old('difficulty', $questionBank->difficulty) == 'easy' ? 'selected' : '' }}>{{ __('instructor.easy') }}</option>
                                <option value="medium" {{ old('difficulty', $questionBank->difficulty) == 'medium' ? 'selected' : '' }}>{{ __('instructor.medium') }}</option>
                                <option value="hard" {{ old('difficulty', $questionBank->difficulty) == 'hard' ? 'selected' : '' }}>{{ __('instructor.hard') }}</option>
                            </select>
                        </div>
                    </div>
                </section>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px">
                <section class="su-card">
                    <h2 class="su-card__title"><i class="fas fa-lightbulb" aria-hidden="true"></i> {{ __('instructor.tips') }}</h2>
                    <ul class="su-meta-list" style="font-size:13px;color:var(--su-ink-40)">
                        <li>• {{ __('instructor.tip_edit_bank_1') }}</li>
                        <li>• {{ __('instructor.tip_edit_bank_2') }}</li>
                        <li>• {{ __('instructor.tip_edit_bank_3') }}</li>
                    </ul>
                </section>
                <section class="su-card">
                    <h2 class="su-card__title"><i class="fas fa-toggle-on" aria-hidden="true"></i> {{ __('instructor.status_label') }}</h2>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $questionBank->is_active) ? 'checked' : '' }}>
                        <span>{{ __('instructor.bank_active') }}</span>
                    </label>
                </section>
                <section class="su-card">
                    <div style="display:flex;flex-direction:column;gap:8px">
                        <button type="submit" class="su-btn su-btn--primary" style="justify-content:center">
                            <i class="fas fa-save" aria-hidden="true"></i>
                            {{ __('instructor.save_changes') }}
                        </button>
                        <a href="{{ route('instructor.question-banks.show', $questionBank) }}" class="su-btn" style="justify-content:center">
                            {{ __('common.cancel') }}
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </form>
</div>
@endsection
