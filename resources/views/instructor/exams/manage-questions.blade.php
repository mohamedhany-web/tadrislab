@extends('layouts.instructor-timeline')

@section('title', __('instructor.manage_questions'))
@section('page_title', __('instructor.manage_questions'))

@push('styles')
<style>[x-cloak]{display:none!important}</style>
@endpush

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp
<div class="su-page" x-data="{ activeTab: 'current', showAddModal: false, showCreateModal: false }">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.exams.index') }}">{{ __('instructor.exams') }}</a>
                <span>/</span>
                <a href="{{ route('instructor.exams.show', $exam) }}">{{ Str::limit($exam->title, 40) }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('instructor.questions') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-list su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.manage_questions') }}
            </h1>
            <p class="su-page-head__sub">{{ $exam->title }}</p>
        </div>
        <div class="su-page-head__actions">
            <button type="button" @click="showAddModal = true" class="su-btn su-btn--primary">
                <i class="fas fa-database" aria-hidden="true"></i>
                {{ __('instructor.add_from_bank') }}
            </button>
            <button type="button" @click="showCreateModal = true" class="su-btn">
                <i class="fas fa-plus-circle" aria-hidden="true"></i>
                {{ __('instructor.new_question') }}
            </button>
            <a href="{{ route('instructor.exams.show', $exam) }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08)">
            <p style="margin:0;font-size:13px;color:#15803d"><i class="fas fa-check-circle" aria-hidden="true"></i> {{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="su-card" style="margin-bottom:16px;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08)">
            <p style="margin:0;font-size:13px;color:#b91c1c"><i class="fas fa-exclamation-circle" aria-hidden="true"></i> {{ session('error') }}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="su-card" style="margin-bottom:16px;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08)">
            <p style="margin:0 0 8px;font-weight:600;color:#b91c1c">{{ __('instructor.fix_fix_errors') }}</p>
            <ul style="margin:0;padding-inline-start:1.25rem;font-size:13px;color:#b91c1c">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <section class="su-card" style="padding:16px">
        <div class="su-tabs-bar" role="tablist">
            <button type="button" class="su-tab" :class="{ 'is-on': activeTab === 'current' }" @click="activeTab = 'current'">
                <i class="fas fa-list" aria-hidden="true"></i>
                {{ __('instructor.current_questions') }} ({{ $exam->questions->count() }})
            </button>
            <button type="button" class="su-tab" :class="{ 'is-on': activeTab === 'bank' }" @click="activeTab = 'bank'">
                <i class="fas fa-database" aria-hidden="true"></i>
                {{ __('instructor.question_bank') }} ({{ $availableQuestions->count() }})
            </button>
        </div>

        <div style="padding:16px 4px 4px">
            <div x-show="activeTab === 'current'" x-cloak>
                @if($exam->questions->count() > 0)
                    <div class="su-list" id="questions-list">
                        @foreach($exam->questions as $index => $question)
                            <article class="su-list-item">
                                <span class="su-list-item__ico su-soft-1" style="font-weight:700">{{ $index + 1 }}</span>
                                <div class="su-list-item__body">
                                    <div class="su-list-item__title" style="font-size:14px">{{ $question->question }}</div>
                                    <div class="su-list-item__meta">
                                        {{ $question->getTypeLabel() }} ·
                                        {{ $question->pivot->marks ?? 1 }} {{ __('instructor.point_unit') }} ·
                                        {{ $question->getDifficultyLabel() }}
                                    </div>
                                </div>
                                <div class="su-list-item__actions">
                                    <form action="{{ route('instructor.exams.questions.remove', [$exam, $question->id]) }}" method="POST" onsubmit="return confirm(@json(__('instructor.confirm_remove_question')));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="su-icon-link" style="color:#b91c1c" title="{{ __('common.delete') }}">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="su-empty">
                        <i class="fas fa-question-circle" aria-hidden="true"></i>
                        <p><strong>{{ __('instructor.no_questions') }}</strong></p>
                        <p>{{ __('instructor.add_questions_hint') }}</p>
                        <div class="su-page-head__actions" style="justify-content:center;margin-top:12px">
                            <button type="button" @click="showAddModal = true" class="su-btn su-btn--primary">{{ __('instructor.add_from_bank') }}</button>
                            <button type="button" @click="showCreateModal = true" class="su-btn">{{ __('instructor.new_question') }}</button>
                        </div>
                    </div>
                @endif
            </div>

            <div x-show="activeTab === 'bank'" x-cloak>
                @if($availableQuestions->count() > 0)
                    <div class="su-course-grid">
                        @foreach($availableQuestions as $question)
                            <article class="su-course-card">
                                <div class="su-course-card__body">
                                    <p class="su-course-card__title" style="font-size:14px">{{ Str::limit($question->question, 100) }}</p>
                                    <div class="su-chip-row" style="margin:8px 0">
                                        <span class="su-chip">{{ $question->getTypeLabel() }}</span>
                                        <span class="su-chip">{{ $question->getDifficultyLabel() }}</span>
                                        @if(!$question->is_active)
                                            <span class="su-chip su-chip--warn">{{ __('instructor.inactive') }}</span>
                                        @endif
                                        @if($question->questionBank)
                                            <span class="su-chip su-soft-1">{{ $question->questionBank->title }}</span>
                                        @endif
                                    </div>
                                    <form action="{{ route('instructor.exams.questions.add-from-bank', $exam) }}" method="POST" class="su-form-actions">
                                        @csrf
                                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                                        <input type="number" name="marks" value="{{ $question->points ?? 1 }}" min="0.5" step="0.5" required class="su-input" style="width:5rem;height:36px">
                                        <button type="submit" class="su-btn su-btn--primary" style="flex:1;justify-content:center;height:36px">
                                            <i class="fas fa-plus" aria-hidden="true"></i> {{ __('instructor.add_short') }}
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="su-empty">
                        <i class="fas fa-database" aria-hidden="true"></i>
                        <p><strong>{{ __('instructor.no_bank_questions') }}</strong></p>
                        <p>{{ __('instructor.create_bank_first') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Create question modal --}}
    <div x-show="showCreateModal" x-cloak
         style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.45)"
         @click.self="showCreateModal = false">
        <div class="su-card" style="width:100%;max-width:40rem;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;margin:0;padding:0" @click.stop>
            <div class="su-section-head" style="padding:16px 20px;border-bottom:1px solid var(--su-line);margin:0">
                <h3>{{ __('instructor.create_new_question') }}</h3>
                <button type="button" @click="showCreateModal = false" class="su-icon-link su-icon-link--ghost"><i class="fas fa-times" aria-hidden="true"></i></button>
            </div>
            @if($questionBanks->isEmpty())
                <div class="su-empty" style="padding:24px">
                    <p>{{ __('instructor.need_question_bank_first') }}</p>
                    <a href="{{ route('instructor.question-banks.index') }}" class="su-btn su-btn--primary" style="margin-top:12px">
                        <i class="fas fa-database" aria-hidden="true"></i> {{ __('instructor.question_banks') }}
                    </a>
                </div>
            @else
                <form action="{{ route('instructor.exams.questions.create-new', $exam) }}" method="POST" style="padding:20px;overflow-y:auto;flex:1">
                    @csrf
                    <div class="su-form-grid" style="grid-template-columns:1fr 1fr">
                        <div class="su-field" style="grid-column:1 / -1">
                            <label>{{ __('instructor.question_bank') }} <span style="color:#b91c1c">*</span></label>
                            <select name="question_bank_id" required class="su-select">
                                <option value="">{{ __('instructor.choose_question_bank') }}</option>
                                @foreach($questionBanks as $bank)
                                    <option value="{{ $bank->id }}" {{ old('question_bank_id') == $bank->id ? 'selected' : '' }}>{{ $bank->title }}</option>
                                @endforeach
                            </select>
                            @error('question_bank_id')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field" style="grid-column:1 / -1">
                            <label>{{ __('instructor.question_type') }} <span style="color:#b91c1c">*</span></label>
                            <select name="type" id="question_type" required onchange="updateQuestionForm()" class="su-select">
                                <option value="">{{ __('instructor.choose_type') }}</option>
                                <option value="multiple_choice">{{ __('instructor.type_multiple_choice') }}</option>
                                <option value="true_false">{{ __('instructor.type_true_false') }}</option>
                            </select>
                        </div>
                        <div class="su-field" style="grid-column:1 / -1">
                            <label>{{ __('instructor.question_text') }} <span style="color:#b91c1c">*</span></label>
                            <textarea name="question" rows="3" required class="su-input" style="min-height:88px;resize:vertical" placeholder="{{ __('instructor.question_text_ph') }}"></textarea>
                        </div>
                        <div class="su-field" style="grid-column:1 / -1;display:none" id="options_field">
                            <label>{{ __('instructor.options_one_per_line') }}</label>
                            <textarea name="options_text" rows="3" class="su-input" style="min-height:88px;resize:vertical" placeholder="{{ __('instructor.options_ph') }}"></textarea>
                        </div>
                        <div class="su-field" style="grid-column:1 / -1">
                            <label>{{ __('instructor.correct_answer') }} <span style="color:#b91c1c">*</span></label>
                            <input type="text" name="correct_answer" required class="su-input" placeholder="{{ __('instructor.correct_answer') }}">
                        </div>
                        <div class="su-field" style="grid-column:1 / -1">
                            <label>{{ __('instructor.explanation') }}</label>
                            <textarea name="explanation" rows="2" class="su-input" style="min-height:72px;resize:vertical"></textarea>
                        </div>
                        <div class="su-field">
                            <label>{{ __('instructor.points') }} <span style="color:#b91c1c">*</span></label>
                            <input type="number" name="points" value="1" min="0.5" step="0.5" required class="su-input">
                        </div>
                        <div class="su-field">
                            <label>{{ __('instructor.difficulty') }} <span style="color:#b91c1c">*</span></label>
                            <select name="difficulty_level" required class="su-select">
                                <option value="easy">{{ __('instructor.easy') }}</option>
                                <option value="medium" selected>{{ __('instructor.medium') }}</option>
                                <option value="hard">{{ __('instructor.hard') }}</option>
                            </select>
                        </div>
                        <div class="su-field">
                            <label>{{ __('instructor.exam_marks') }} <span style="color:#b91c1c">*</span></label>
                            <input type="number" name="marks" value="1" min="0.5" step="0.5" required class="su-input">
                        </div>
                    </div>
                    <div class="su-page-head__actions" style="justify-content:flex-end;margin-top:16px;border-top:1px solid var(--su-line);padding-top:16px">
                        <button type="button" @click="showCreateModal = false" class="su-btn">{{ __('common.cancel') }}</button>
                        <button type="submit" class="su-btn su-btn--primary">
                            <i class="fas fa-save" aria-hidden="true"></i> {{ __('instructor.create_and_add') }}
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- Add from bank modal --}}
    <div x-show="showAddModal" x-cloak
         style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(15,23,42,.45)"
         @click.self="showAddModal = false">
        <div class="su-card" style="width:100%;max-width:40rem;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;margin:0;padding:0" @click.stop>
            <div class="su-section-head" style="padding:16px 20px;border-bottom:1px solid var(--su-line);margin:0">
                <h3>{{ __('instructor.add_from_bank') }}</h3>
                <button type="button" @click="showAddModal = false" class="su-icon-link su-icon-link--ghost"><i class="fas fa-times" aria-hidden="true"></i></button>
            </div>
            <div style="padding:20px;overflow-y:auto;flex:1">
                <p style="margin:0 0 12px;font-size:13px;color:var(--su-ink-40)">{{ __('instructor.add_from_bank_hint') }}</p>
                @if($availableQuestions->isEmpty())
                    <div class="su-empty">
                        <i class="fas fa-database" aria-hidden="true"></i>
                        <p>{{ __('instructor.no_bank_questions') }}</p>
                        <a href="{{ route('instructor.question-banks.index') }}" class="su-btn" style="margin-top:8px">{{ __('instructor.question_banks') }}</a>
                    </div>
                @else
                    <div class="su-list">
                        @foreach($availableQuestions as $question)
                            <form action="{{ route('instructor.exams.questions.add-from-bank', $exam) }}" method="POST" class="su-list-item">
                                @csrf
                                <input type="hidden" name="question_id" value="{{ $question->id }}">
                                <div class="su-list-item__body">
                                    <div class="su-list-item__title" style="font-size:13px">{{ Str::limit($question->question, 70) }}</div>
                                    <div class="su-list-item__meta">
                                        {{ $question->getTypeLabel() }} · {{ $question->getDifficultyLabel() }}
                                        @if(!$question->is_active) · {{ __('instructor.inactive') }} @endif
                                    </div>
                                </div>
                                <div class="su-list-item__actions">
                                    <input type="number" name="marks" value="{{ $question->points ?? 1 }}" min="0.5" step="0.5" required class="su-input" style="width:4.5rem;height:32px">
                                    <button type="submit" class="su-btn su-btn--primary" style="height:32px">
                                        <i class="fas fa-plus" aria-hidden="true"></i> {{ __('instructor.add_short') }}
                                    </button>
                                </div>
                            </form>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateQuestionForm() {
    var type = document.getElementById('question_type').value;
    var el = document.getElementById('options_field');
    el.style.display = type === 'multiple_choice' ? 'block' : 'none';
}
document.querySelectorAll('form[action*="create-new"]').forEach(function(form) {
    form.addEventListener('submit', function() {
        var optionsText = form.querySelector('textarea[name="options_text"]');
        if (optionsText && optionsText.value) {
            var options = optionsText.value.split('\n').filter(function(opt) { return opt.trim(); });
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'options';
            input.value = JSON.stringify(options);
            form.appendChild(input);
        }
    });
});
</script>
@endpush
@endsection
