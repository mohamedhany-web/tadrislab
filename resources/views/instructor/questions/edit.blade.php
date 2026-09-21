@extends('layouts.instructor-timeline')

@section('title', __('instructor.edit_question') . ' - ' . config('app.name'))
@section('page_title', __('instructor.edit_question'))

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
                <a href="{{ route('instructor.question-banks.show', $question->questionBank) }}">{{ Str::limit($question->questionBank->title ?? '', 40) }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('common.edit') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-edit su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.edit_question') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.edit_question_sub') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.question-banks.show', $question->questionBank) }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <form action="{{ route('instructor.questions.update', $question) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="su-detail-grid">
            <div style="display:flex;flex-direction:column;gap:16px;min-width:0">
                <section class="su-card">
                    <h2 class="su-card__title"><i class="fas fa-question-circle" aria-hidden="true"></i> {{ __('instructor.question_info') }}</h2>
                    <div class="su-form-grid" style="grid-template-columns:1fr">
                        <div class="su-field">
                            <label for="question_type">{{ __('instructor.question_type') }} <span style="color:#b91c1c">*</span></label>
                            <select name="type" id="question_type" required onchange="updateQuestionForm()" class="su-select">
                                <option value="multiple_choice" {{ $question->type == 'multiple_choice' ? 'selected' : '' }}>{{ __('instructor.type_multiple_choice') }}</option>
                                <option value="true_false" {{ $question->type == 'true_false' ? 'selected' : '' }}>{{ __('instructor.type_true_false') }}</option>
                            </select>
                        </div>
                        <div class="su-field">
                            <label>{{ __('instructor.question_text') }} <span style="color:#b91c1c">*</span></label>
                            <textarea name="question" rows="4" required class="su-input" style="min-height:110px;resize:vertical"
                                      placeholder="{{ __('instructor.question_text_ph') }}">{{ old('question', $question->question) }}</textarea>
                            @error('question')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field" id="options_field" style="display: {{ $question->type == 'multiple_choice' ? 'block' : 'none' }};">
                            <label>{{ __('instructor.options_one_per_line') }}</label>
                            <textarea name="options_text" rows="4" class="su-input" style="min-height:100px;resize:vertical"
                                      placeholder="{{ __('instructor.options_ph') }}">@if($question->options && is_array($question->options)){{ implode("\n", $question->options) }}@endif</textarea>
                        </div>
                        <div class="su-field" id="correct_answer_field">
                            <label>{{ __('instructor.correct_answer') }} <span style="color:#b91c1c">*</span></label>
                            @php
                                $correctAnswer = is_array($question->correct_answer) ? $question->correct_answer : [$question->correct_answer];
                                $normalizedCorrectAnswers = $question->normalizeMultipleChoiceCorrectAnswers();
                            @endphp
                            <div id="correct_answer_multiple_choice" style="display: {{ $question->type == 'multiple_choice' ? 'block' : 'none' }};">
                                <select name="correct_answer" class="su-select">
                                    <option value="">{{ __('instructor.choose_correct_answer') }}</option>
                                    @if($question->options && is_array($question->options))
                                        @foreach($question->options as $optionIndex => $option)
                                            <option value="{{ $option }}" {{ in_array((int)$optionIndex, $normalizedCorrectAnswers, true) ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <p style="margin:6px 0 0;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.options_update_hint') }}</p>
                            </div>
                            <div id="correct_answer_true_false" style="display: {{ $question->type == 'true_false' ? 'block' : 'none' }};">
                                <select name="correct_answer" class="su-select">
                                    <option value="">{{ __('instructor.choose_type') }}</option>
                                    <option value="صح" {{ in_array('صح', $correctAnswer) ? 'selected' : '' }}>{{ __('instructor.true_answer') }}</option>
                                    <option value="خطأ" {{ in_array('خطأ', $correctAnswer) ? 'selected' : '' }}>{{ __('instructor.false_answer') }}</option>
                                </select>
                            </div>
                            @error('correct_answer')<p class="su-field-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="su-field">
                            <label>{{ __('instructor.explanation') }}</label>
                            <textarea name="explanation" rows="3" class="su-input" style="min-height:80px;resize:vertical"
                                      placeholder="{{ __('instructor.explanation') }}…">{{ old('explanation', $question->explanation) }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="su-card">
                    <h2 class="su-card__title"><i class="fas fa-sliders-h" aria-hidden="true"></i> {{ __('instructor.question_settings') }}</h2>
                    <div class="su-form-grid" style="grid-template-columns:1fr 1fr">
                        <div class="su-field">
                            <label>{{ __('instructor.points') }} <span style="color:#b91c1c">*</span></label>
                            <input type="number" name="points" value="{{ old('points', $question->points) }}" min="0.5" step="0.5" required class="su-input">
                        </div>
                        <div class="su-field">
                            <label>{{ __('instructor.difficulty') }} <span style="color:#b91c1c">*</span></label>
                            <select name="difficulty_level" required class="su-select">
                                <option value="easy" {{ $question->difficulty_level == 'easy' ? 'selected' : '' }}>{{ __('instructor.easy') }}</option>
                                <option value="medium" {{ $question->difficulty_level == 'medium' ? 'selected' : '' }}>{{ __('instructor.medium') }}</option>
                                <option value="hard" {{ $question->difficulty_level == 'hard' ? 'selected' : '' }}>{{ __('instructor.hard') }}</option>
                            </select>
                        </div>
                        <div class="su-field" style="grid-column:1 / -1">
                            <label>{{ __('instructor.category_label') }}</label>
                            <select name="category_id" class="su-select">
                                <option value="">{{ __('instructor.no_category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $question->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px">
                <section class="su-card">
                    <h2 class="su-card__title"><i class="fas fa-lightbulb" aria-hidden="true"></i> {{ __('instructor.tips') }}</h2>
                    <ul class="su-meta-list" style="font-size:13px;color:var(--su-ink-40)">
                        <li>• {{ __('instructor.tip_edit_question_1') }}</li>
                        <li>• {{ __('instructor.tip_edit_question_2') }}</li>
                        <li>• {{ __('instructor.tip_edit_question_3') }}</li>
                    </ul>
                </section>
                <section class="su-card">
                    <h2 class="su-card__title"><i class="fas fa-toggle-on" aria-hidden="true"></i> {{ __('instructor.status_label') }}</h2>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:500">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $question->is_active) ? 'checked' : '' }}>
                        <span>{{ __('instructor.question_active') }}</span>
                    </label>
                </section>
                <section class="su-card">
                    <div style="display:flex;flex-direction:column;gap:8px">
                        <button type="submit" class="su-btn su-btn--primary" style="justify-content:center">
                            <i class="fas fa-save" aria-hidden="true"></i>
                            {{ __('instructor.save_changes') }}
                        </button>
                        <a href="{{ route('instructor.question-banks.show', $question->questionBank) }}" class="su-btn" style="justify-content:center">
                            {{ __('common.cancel') }}
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function updateQuestionForm() {
    const type = document.getElementById('question_type').value;
    const optionsField = document.getElementById('options_field');
    const answerFields = ['correct_answer_multiple_choice', 'correct_answer_true_false'];
    answerFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) field.style.display = 'none';
        const select = field?.querySelector('select[name="correct_answer"]');
        const input = field?.querySelector('input[name="correct_answer"]');
        const textarea = field?.querySelector('textarea[name="correct_answer"]');
        if (select) select.removeAttribute('required');
        if (input) input.removeAttribute('required');
        if (textarea) textarea.removeAttribute('required');
    });
    if (type === 'multiple_choice') {
        if (optionsField) optionsField.style.display = 'block';
        const field = document.getElementById('correct_answer_multiple_choice');
        if (field) {
            field.style.display = 'block';
            const select = field.querySelector('select[name="correct_answer"]');
            if (select) select.setAttribute('required', 'required');
        }
        const optionsText = document.querySelector('textarea[name="options_text"]');
        if (optionsText) {
            optionsText.addEventListener('input', updateMultipleChoiceOptions);
        }
    } else if (type === 'true_false') {
        if (optionsField) optionsField.style.display = 'none';
        const field = document.getElementById('correct_answer_true_false');
        if (field) {
            field.style.display = 'block';
            const select = field.querySelector('select[name="correct_answer"]');
            if (select) select.setAttribute('required', 'required');
        }
    } else {
        if (optionsField) optionsField.style.display = 'none';
    }
}

function updateMultipleChoiceOptions() {
    const optionsText = document.querySelector('textarea[name="options_text"]');
    const select = document.querySelector('#correct_answer_multiple_choice select[name="correct_answer"]');
    if (!optionsText || !select) return;
    const options = optionsText.value.split('\n').filter(opt => opt.trim());
    const currentValue = select.value;
    while (select.options.length > 1) {
        select.remove(1);
    }
    options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option.trim();
        optionElement.textContent = option.trim();
        select.appendChild(optionElement);
    });
    if (currentValue && Array.from(select.options).some(opt => opt.value === currentValue)) {
        select.value = currentValue;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateQuestionForm();
    const optionsText = document.querySelector('textarea[name="options_text"]');
    if (optionsText) {
        optionsText.addEventListener('input', updateMultipleChoiceOptions);
    }
});

document.querySelector('form[action*="questions.update"]')?.addEventListener('submit', function(e) {
    const optionsText = this.querySelector('textarea[name="options_text"]');
    if (optionsText && optionsText.value && document.getElementById('question_type').value === 'multiple_choice') {
        const options = optionsText.value.split('\n').filter(opt => opt.trim());
        const optionsInput = document.createElement('input');
        optionsInput.type = 'hidden';
        optionsInput.name = 'options';
        optionsInput.value = JSON.stringify(options);
        this.appendChild(optionsInput);
    }
});
</script>
@endpush

@if(session('success'))
    <script>
        alert(@json(session('success')));
        window.location.href = @json(route('instructor.question-banks.show', $question->questionBank));
    </script>
@endif
@endsection
