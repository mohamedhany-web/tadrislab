@extends('layouts.instructor-timeline')

@section('title', __('instructor.question_bank') . ': ' . $questionBank->title . ' - ' . config('app.name'))
@section('page_title', $questionBank->title)

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page" x-data="{ showCreateModal: false }">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.question-banks.index') }}">{{ __('instructor.question_banks') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ $questionBank->title }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-database su-page-head__ico" aria-hidden="true"></i>
                {{ $questionBank->title }}
            </h1>
            <p class="su-page-head__sub">{{ $questionBank->description ?? __('instructor.question_bank') }}</p>
        </div>
        <div class="su-page-head__actions">
            <button type="button" @click="showCreateModal = true" class="su-btn su-btn--primary">
                <i class="fas fa-plus" aria-hidden="true"></i>
                {{ __('instructor.add_question') }}
            </button>
            <a href="{{ route('instructor.question-banks.edit', $questionBank) }}" class="su-btn">
                <i class="fas fa-edit" aria-hidden="true"></i>
                {{ __('instructor.edit_question_bank') }}
            </a>
            <a href="{{ route('instructor.question-banks.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            <i class="fas fa-check-circle" aria-hidden="true"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08);color:#b91c1c;font-size:13px">
            <ul style="margin:0;padding-inline-start:1.2rem">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="su-kpi-row" style="margin-bottom:20px">
        <div class="su-kpi su-kpi--1">
            <div class="su-kpi__l">{{ __('instructor.total_questions') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($questionBank->questions->count()) }}</div>
                <div class="su-kpi__d"><i class="fas fa-list" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--2">
            <div class="su-kpi__l">{{ __('instructor.easy') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($questionBank->questions->where('difficulty_level', 'easy')->count()) }}</div>
                <div class="su-kpi__d"><i class="fas fa-smile" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--3">
            <div class="su-kpi__l">{{ __('instructor.medium') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($questionBank->questions->where('difficulty_level', 'medium')->count()) }}</div>
                <div class="su-kpi__d"><i class="fas fa-meh" aria-hidden="true"></i></div>
            </div>
        </div>
        <div class="su-kpi su-kpi--4">
            <div class="su-kpi__l">{{ __('instructor.hard') }}</div>
            <div class="su-kpi__row">
                <div class="su-kpi__v">{{ number_format($questionBank->questions->where('difficulty_level', 'hard')->count()) }}</div>
                <div class="su-kpi__d"><i class="fas fa-frown" aria-hidden="true"></i></div>
            </div>
        </div>
    </section>

    <section class="su-card su-card--flush">
        <div class="su-section-head" style="padding:14px 16px;border-bottom:1px solid var(--su-line,rgba(0,0,0,.06))">
            <h2 class="su-card__title" style="margin:0">
                {{ __('instructor.questions') }} ({{ $questionBank->questions->count() }})
            </h2>
            <button type="button" @click="showCreateModal = true" class="su-btn su-btn--primary" style="height:32px">
                <i class="fas fa-plus" aria-hidden="true"></i>
                {{ __('instructor.add_question') }}
            </button>
        </div>

        @if($questionBank->questions->count() > 0)
            <div class="su-list">
                @foreach($questionBank->questions as $index => $question)
                    @php
                        $typeSoft = match ($question->type) {
                            'multiple_choice' => 'su-soft-1',
                            'true_false' => 'su-soft-2',
                            'fill_blank' => 'su-soft-3',
                            'short_answer' => 'su-soft-4',
                            default => 'su-soft-1',
                        };
                    @endphp
                    <div class="su-list-item">
                        <span class="su-list-item__ico {{ $typeSoft }}" style="font-weight:700;font-size:12px">{{ $index + 1 }}</span>
                        <div class="su-list-item__body">
                            <div class="su-list-item__title">{{ Str::limit($question->question, 200) }}</div>
                            <div class="su-list-item__meta" style="display:flex;flex-wrap:wrap;gap:6px;align-items:center">
                                <span class="su-chip {{ $typeSoft }}">{{ $question->getTypeLabel() }}</span>
                                <span class="su-chip">{{ $question->points }} {{ __('instructor.points') }}</span>
                                <span class="su-chip">{{ $question->getDifficultyLabel() }}</span>
                                @if($question->category)
                                    <span class="su-chip">{{ $question->category->name }}</span>
                                @endif
                            </div>
                            @if($question->type == 'multiple_choice' && $question->options && is_array($question->options))
                                @php $normalizedCorrectAnswers = $question->normalizeMultipleChoiceCorrectAnswers(); @endphp
                                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px">
                                    @foreach($question->options as $optIndex => $opt)
                                        @php $isCorrect = in_array((int)$optIndex, $normalizedCorrectAnswers, true); @endphp
                                        <span class="su-chip {{ $isCorrect ? 'su-chip--ok' : '' }}">
                                            {{ $opt }} @if($isCorrect)<i class="fas fa-check" aria-hidden="true"></i>@endif
                                        </span>
                                    @endforeach
                                </div>
                            @elseif(in_array($question->type, ['true_false', 'short_answer', 'essay']))
                                <p style="margin:8px 0 0;font-size:13px;color:var(--su-ink-40)">
                                    {{ __('instructor.answer_label') }}:
                                    {{ is_array($question->correct_answer) ? ($question->correct_answer[0] ?? '—') : $question->correct_answer }}
                                </p>
                            @endif
                        </div>
                        <div class="su-list-item__actions">
                            <a href="{{ route('instructor.questions.edit', $question) }}" class="su-btn" style="height:32px;width:32px;padding:0;justify-content:center" title="{{ __('common.edit') }}">
                                <i class="fas fa-edit" aria-hidden="true"></i>
                            </a>
                            <form action="{{ route('instructor.questions.destroy', $question) }}" method="POST" onsubmit="return confirm(@json(__('instructor.confirm_delete_question')));" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="su-btn" style="height:32px;width:32px;padding:0;justify-content:center;color:#b91c1c" title="{{ __('common.delete') }}">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="su-empty" style="padding:48px 16px">
                <i class="fas fa-question-circle" aria-hidden="true"></i>
                <p>{{ __('instructor.no_questions_yet') }}</p>
                <p style="color:var(--su-ink-40);font-size:13px;margin:0 0 12px">{{ __('instructor.add_first_question') }}</p>
                <button type="button" @click="showCreateModal = true" class="su-btn su-btn--primary">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    {{ __('instructor.add_question') }}
                </button>
            </div>
        @endif
    </section>

    <div x-show="showCreateModal"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="su-modal-backdrop"
         style="position:fixed;inset:0;z-index:50;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.45)"
         @click.self="showCreateModal = false">
        <div class="su-card" style="width:100%;max-width:40rem;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;margin:0" @click.stop>
            <div class="su-section-head" style="padding:14px 16px;border-bottom:1px solid var(--su-line,rgba(0,0,0,.06));flex-shrink:0">
                <h3 class="su-card__title" style="margin:0">{{ __('instructor.create_new_question') }}</h3>
                <button type="button" @click="showCreateModal = false" class="su-btn" style="height:32px;width:32px;padding:0;justify-content:center">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <form action="{{ route('instructor.question-banks.questions.store', $questionBank) }}" method="POST" id="add-question-form" style="padding:16px;overflow-y:auto;flex:1">
                @csrf
                <div class="su-form-grid" style="grid-template-columns:1fr 1fr">
                    <div class="su-field" style="grid-column:1 / -1">
                        <label for="question_type">{{ __('instructor.question_type') }} <span style="color:#b91c1c">*</span></label>
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
                    <div class="su-field" id="options_field" style="display:none;grid-column:1 / -1">
                        <label>{{ __('instructor.options_one_per_line') }}</label>
                        <textarea name="options_text" rows="4" class="su-input" style="min-height:100px;resize:vertical" placeholder="{{ __('instructor.options_ph') }}"></textarea>
                    </div>
                    <div class="su-field" id="correct_answer_wrap" style="grid-column:1 / -1">
                        <label>{{ __('instructor.correct_answer') }} <span style="color:#b91c1c">*</span></label>
                        <div id="correct_answer_multiple_choice" style="display:none">
                            <select name="correct_answer" class="su-select">
                                <option value="">{{ __('instructor.choose_answer_after_options') }}</option>
                            </select>
                        </div>
                        <div id="correct_answer_true_false" style="display:none">
                            <select name="correct_answer" class="su-select">
                                <option value="">{{ __('instructor.choose_type') }}</option>
                                <option value="صح">{{ __('instructor.true_answer') }}</option>
                                <option value="خطأ">{{ __('instructor.false_answer') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="su-field" style="grid-column:1 / -1">
                        <label>{{ __('instructor.explanation') }}</label>
                        <textarea name="explanation" rows="2" class="su-input" style="min-height:64px;resize:vertical"></textarea>
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
                    <div class="su-field" style="grid-column:1 / -1">
                        <label>{{ __('instructor.category_label') }}</label>
                        <select name="category_id" class="su-select">
                            <option value="">{{ __('instructor.no_category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="su-field" style="grid-column:1 / -1">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span>{{ __('instructor.question_active') }}</span>
                        </label>
                    </div>
                </div>
                <div class="su-form-actions" style="margin-top:16px;padding-top:16px;border-top:1px solid var(--su-line,rgba(0,0,0,.06));justify-content:flex-end;gap:8px">
                    <button type="button" @click="showCreateModal = false" class="su-btn">{{ __('common.cancel') }}</button>
                    <button type="submit" class="su-btn su-btn--primary">
                        <i class="fas fa-save" aria-hidden="true"></i>
                        {{ __('instructor.add_question') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateQuestionForm() {
    var type = document.getElementById('question_type').value;
    var optionsField = document.getElementById('options_field');
    var ids = ['correct_answer_multiple_choice', 'correct_answer_true_false'];
    ids.forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.style.display = 'none';
            var inp = el.querySelector('[name="correct_answer"]');
            if (inp) inp.removeAttribute('required');
        }
    });
    if (optionsField) optionsField.style.display = 'none';
    if (type === 'multiple_choice') {
        if (optionsField) optionsField.style.display = 'block';
        var f = document.getElementById('correct_answer_multiple_choice');
        if (f) { f.style.display = 'block'; var s = f.querySelector('select[name="correct_answer"]'); if (s) s.setAttribute('required', 'required'); }
    } else if (type === 'true_false') {
        var f = document.getElementById('correct_answer_true_false');
        if (f) { f.style.display = 'block'; var s = f.querySelector('select[name="correct_answer"]'); if (s) s.setAttribute('required', 'required'); }
    }
}
function updateMultipleChoiceOptions() {
    var optionsText = document.querySelector('#add-question-form textarea[name="options_text"]');
    var select = document.querySelector('#correct_answer_multiple_choice select[name="correct_answer"]');
    if (!optionsText || !select) return;
    var options = optionsText.value.split('\n').filter(function(o) { return o.trim(); });
    var current = select.value;
    while (select.options.length > 1) select.remove(1);
    options.forEach(function(opt) {
        var o = document.createElement('option');
        o.value = o.textContent = opt.trim();
        select.appendChild(o);
    });
    if (current && Array.from(select.options).some(function(opt) { return opt.value === current; })) select.value = current;
}
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('add-question-form');
    if (form) {
        var optionsText = form.querySelector('textarea[name="options_text"]');
        if (optionsText) optionsText.addEventListener('input', updateMultipleChoiceOptions);
        form.addEventListener('submit', function() {
            var type = document.getElementById('question_type').value;
            if (type === 'multiple_choice') {
                var ids = ['correct_answer_true_false'];
                ids.forEach(function(id) {
                    var el = document.getElementById(id);
                    if (el) {
                        var inp = el.querySelector('[name="correct_answer"]');
                        if (inp) inp.disabled = true;
                    }
                });
            } else {
                var mc = document.getElementById('correct_answer_multiple_choice');
                if (mc) { var s = mc.querySelector('select[name="correct_answer"]'); if (s) s.disabled = true; }
            }
        });
    }
});
</script>
@endpush
@endsection
