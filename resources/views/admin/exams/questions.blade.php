@extends('layouts.admin')

@section('title', 'إدارة أسئلة الامتحان')
@section('page_title', 'إدارة أسئلة الامتحان')

@php
    $currentQuestionIds = $exam->examQuestions->pluck('question_id')->toArray();
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $checkboxClass = 'size-4 rounded border-line text-accent focus:ring-accent/20';
@endphp

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-accent">لوحة التحكم</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.exams.index') }}" class="hover:text-accent">الامتحانات</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}" class="hover:text-accent">{{ Str::limit($exam->course?->title ?? '', 25) }}</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.exams.show', $exam) }}" class="hover:text-accent">{{ Str::limit($exam->title, 25) }}</a>
                <span class="mx-1">·</span>
                <span class="text-ink">الأسئلة</span>
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إدارة أسئلة الامتحان</h2>
            <p class="mt-1 text-sm text-muted">{{ $exam->title }} — إجمالي الدرجات: {{ $exam->total_marks ?? 0 }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.exams.show', $exam) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-eye text-xs"></i>
                عرض الامتحان
            </a>
            <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                رجوع لامتحانات البرنامج
            </a>
            <a href="{{ route('admin.question-bank.index') }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-database text-xs"></i>
                بنك الأسئلة
            </a>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-soft">
            <i class="fas fa-check-circle ml-1"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 shadow-soft">
            <i class="fas fa-exclamation-circle ml-1"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-soft">
            <p class="mb-1 font-semibold">يرجى تصحيح الأخطاء:</p>
            <ul class="list-inside list-disc">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line px-5 py-4">
                    <h3 class="text-sm font-semibold text-ink">أسئلة الامتحان ({{ $exam->examQuestions->count() }})</h3>
                    <span class="text-sm font-medium tabular-nums text-muted">إجمالي الدرجات: {{ $exam->total_marks ?? 0 }}</span>
                </div>
                <div class="p-5">
                    @if($exam->examQuestions->count() > 0)
                        <ul class="space-y-3">
                            @foreach($exam->examQuestions as $eq)
                                @php
                                    $q = $eq->question;
                                    $diffClass = $q && $q->difficulty_level == 'easy' ? 'bg-emerald-50 text-emerald-700' : ($q && $q->difficulty_level == 'medium' ? 'bg-amber-50 text-amber-800' : 'bg-rose-50 text-rose-700');
                                @endphp
                                <li class="flex flex-wrap items-start justify-between gap-3 rounded-xl border border-line bg-[#f2f5f4]/50 p-4 transition hover:border-accent/30">
                                    <div class="flex min-w-0 flex-1 items-start gap-3">
                                        <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg bg-[#f2f5f4] text-sm font-bold text-accent">{{ $eq->order }}</span>
                                        <div class="min-w-0">
                                            <p class="font-medium text-ink">{{ Str::limit($q->question ?? '—', 120) }}</p>
                                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                                @if($q)
                                                    <span class="text-muted">{{ $q->getTypeLabel() }}</span>
                                                    <span class="font-semibold text-accent">{{ $eq->marks }} نقطة</span>
                                                    <span class="inline-flex items-center rounded px-2 py-0.5 {{ $diffClass }}">{{ $q->getDifficultyLabel() }}</span>
                                                    @if($eq->time_limit)
                                                        <span class="text-muted">{{ $eq->time_limit }} ثانية</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.exams.questions.remove', [$exam, $eq]) }}" method="POST" class="inline" onsubmit="return confirm('هل تريد إزالة هذا السؤال من الامتحان؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex size-9 items-center justify-center rounded-xl border border-line text-muted transition hover:bg-rose-50 hover:text-rose-700" title="إزالة من الامتحان">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="rounded-xl border border-dashed border-line bg-[#f2f5f4]/50 py-12 text-center">
                            <i class="fas fa-question-circle mb-4 text-5xl text-muted/40"></i>
                            <h3 class="mb-2 text-lg font-semibold text-ink">لا توجد أسئلة في الامتحان</h3>
                            <p class="mb-4 text-sm text-muted">أضف أسئلة من بنك الأسئلة باستخدام النموذج على اليمين</p>
                            <a href="{{ route('admin.question-bank.index') }}" class="btn-press inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                                <i class="fas fa-database text-xs"></i>
                                بنك الأسئلة
                            </a>
                        </div>
                    @endif
                </div>
            </article>
        </div>

        <div>
            <article class="sticky top-4 overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-5 py-4">
                    <h3 class="text-sm font-semibold text-ink">إضافة سؤال من البنك</h3>
                </div>
                <div class="p-5">
                    <form action="{{ route('admin.exams.questions.add', $exam) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="question_id" class="{{ $labelClass }}">السؤال <span class="text-rose-600">*</span></label>
                            <select name="question_id" id="question_id" required class="{{ $fieldClass }}">
                                <option value="">اختر السؤال</option>
                                @foreach($categories as $category)
                                    @if($category->questions && $category->questions->count() > 0)
                                        <optgroup label="{{ $category->name }}">
                                            @foreach($category->questions as $q)
                                                @if(!in_array($q->id, $currentQuestionIds))
                                                    <option value="{{ $q->id }}" data-type="{{ $q->getTypeLabel() }}" data-diff="{{ $q->getDifficultyLabel() }}">
                                                        {{ Str::limit($q->question, 60) }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                            @error('question_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="marks" class="{{ $labelClass }}">الدرجة (نقطة) <span class="text-rose-600">*</span></label>
                            <input type="number" name="marks" id="marks" value="{{ old('marks', 1) }}" required min="0.5" max="100" step="0.5"
                                   class="{{ $fieldClass }}" placeholder="1">
                            @error('marks')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="time_limit" class="{{ $labelClass }}">حد زمني (ثانية) — اختياري</label>
                            <input type="number" name="time_limit" id="time_limit" value="{{ old('time_limit') }}" min="10" max="600"
                                   class="{{ $fieldClass }}" placeholder="—">
                        </div>
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-3">
                            <input type="hidden" name="is_required" value="0">
                            <input type="checkbox" name="is_required" value="1" {{ old('is_required', true) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                            <span class="text-sm font-medium text-ink">سؤال مطلوب</span>
                        </label>
                        <button type="submit" class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                            <i class="fas fa-plus text-xs"></i>
                            إضافة السؤال للامتحان
                        </button>
                    </form>
                    @if(empty($categories) || $categories->sum(fn($c) => $c->questions ? $c->questions->count() : 0) == 0)
                        <p class="mt-4 text-sm text-muted">لا توجد أسئلة في البنك. <a href="{{ route('admin.question-bank.index') }}" class="font-medium text-accent hover:underline">إضافة أسئلة</a></p>
                    @endif
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
