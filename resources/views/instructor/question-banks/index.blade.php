@extends('layouts.instructor-timeline')

@section('title', __('instructor.question_banks'))
@section('page_title', __('instructor.question_banks'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-database su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.question_banks') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.question_banks_manage_desc') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.courses.index') }}" class="su-btn">
                <i class="fas fa-book" aria-hidden="true"></i>
                {{ __('instructor.courses') }}
            </a>
            <a href="{{ route('instructor.question-banks.create') }}" class="su-btn su-btn--primary">
                <i class="fas fa-plus" aria-hidden="true"></i>
                {{ __('instructor.create_question_bank') }}
            </a>
        </div>
    </div>

    @if($questionBanks->count() > 0)
        <div class="su-course-grid">
            @foreach($questionBanks as $bank)
                <article class="su-course-card">
                    <div class="su-course-card__head">
                        <h3 class="su-course-card__title">{{ $bank->title }}</h3>
                        <span class="su-chip {{ $bank->is_active ? 'su-chip--ok' : 'su-chip--warn' }}">
                            {{ $bank->is_active ? __('instructor.active_status') : __('instructor.inactive_status') }}
                        </span>
                    </div>
                    <div class="su-course-card__body">
                        @if($bank->description)
                            <p class="su-course-card__desc">{{ Str::limit($bank->description, 100) }}</p>
                        @endif
                        <div class="su-meta-list">
                            <div class="su-meta-row">
                                <span class="su-meta-ico su-soft-1"><i class="fas fa-question-circle" aria-hidden="true"></i></span>
                                <span>{{ __('instructor.question_single') }}:</span>
                                <strong class="tabular-nums">{{ $bank->questions_count }}</strong>
                            </div>
                            @if($bank->difficulty)
                                <div class="su-meta-row">
                                    <span class="su-meta-ico su-soft-2"><i class="fas fa-signal" aria-hidden="true"></i></span>
                                    <span>{{ __('instructor.difficulty') }}:</span>
                                    <strong>
                                        @if($bank->difficulty === 'easy') {{ __('instructor.difficulty_easy') }}
                                        @elseif($bank->difficulty === 'medium') {{ __('instructor.medium') }}
                                        @else {{ __('instructor.hard') }}
                                        @endif
                                    </strong>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="su-course-card__foot" style="display:flex;gap:8px">
                        <a href="{{ route('instructor.question-banks.show', $bank) }}" class="su-btn su-btn--primary" style="flex:1;justify-content:center">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                            {{ __('common.view') }}
                        </a>
                        <a href="{{ route('instructor.question-banks.edit', $bank) }}" class="su-btn" style="height:36px;width:36px;padding:0;justify-content:center" title="{{ __('common.edit') }}">
                            <i class="fas fa-edit" aria-hidden="true"></i>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
        @if(method_exists($questionBanks, 'links') && $questionBanks->hasPages())
            <div class="su-pager" style="margin-top:16px">{{ $questionBanks->links() }}</div>
        @endif
    @else
        <section class="su-card">
            <div class="su-empty">
                <i class="fas fa-database" aria-hidden="true"></i>
                <p>{{ __('instructor.no_question_banks') }}</p>
                <p style="color:var(--su-ink-40);font-size:13px;margin:0 0 12px">{{ __('instructor.no_question_banks_description') }}</p>
                <a href="{{ route('instructor.question-banks.create') }}" class="su-btn su-btn--primary">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    {{ __('instructor.create_question_bank') }}
                </a>
            </div>
        </section>
    @endif
</div>
@endsection
