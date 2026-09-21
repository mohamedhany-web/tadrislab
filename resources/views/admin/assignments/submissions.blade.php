@extends('layouts.admin')

@section('title', 'تسليمات الواجب: ' . $assignment->title . ' - ' . config('app.name'))
@section('page_title', 'تسليمات الواجب')

@php
    $courseId = $assignment->advanced_course_id ?? $assignment->course_id;
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $textareaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
@endphp

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-accent">لوحة التحكم</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.assignments.index') }}" class="hover:text-accent">الواجبات</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.assignments.by-course', $courseId) }}" class="hover:text-accent">{{ Str::limit($assignment->course?->title ?? '', 25) }}</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.assignments.show', $assignment) }}" class="hover:text-accent truncate">{{ Str::limit($assignment->title, 25) }}</a>
                <span class="mx-1">·</span>
                <span class="text-ink">التسليمات</span>
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تسليمات الواجب</h2>
            <p class="mt-1 truncate text-sm text-muted">{{ $assignment->title }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.assignments.show', $assignment) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-eye text-xs"></i>
                تفاصيل الواجب
            </a>
            <a href="{{ route('admin.assignments.by-course', $courseId) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm text-ink-soft hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                رجوع لواجبات البرنامج
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

    <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-4">
                <div class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-tasks text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="truncate text-sm font-semibold text-ink">{{ $assignment->title }}</h3>
                    <p class="text-xs text-muted">{{ $assignment->course->title ?? '—' }} · الدرجة الكلية: {{ $assignment->max_score }}</p>
                </div>
            </div>
            <div class="rounded-xl border border-line bg-accent-soft px-4 py-2 text-center">
                <div class="text-xl font-semibold tabular-nums text-accent">{{ $submissions->total() }}</div>
                <div class="text-xs font-medium text-muted">إجمالي التسليمات</div>
            </div>
        </div>
    </article>

    @if(isset($gradeSubmission) && $gradeSubmission)
        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft" id="grade-form-box">
            <h3 class="text-sm font-semibold text-ink">تقييم تسليم: {{ $gradeSubmission->student->name ?? 'طالب' }}</h3>
            <p class="mt-0.5 text-xs text-muted">أدخل الدرجة والتعليق وحدّد حالة التسليم.</p>

            <form action="{{ route('admin.assignments.grade', [$assignment, $gradeSubmission]) }}" method="POST" class="mt-5 max-w-xl space-y-4">
                @csrf
                <div>
                    <label class="{{ $labelClass }}" for="score">الدرجة (0 - {{ $assignment->max_score }}) <span class="text-rose-500">*</span></label>
                    <input type="number" name="score" id="score" value="{{ old('score', $gradeSubmission->score) }}" min="0" max="{{ $assignment->max_score }}" required
                           class="{{ $fieldClass }}">
                    @error('score')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="feedback">التعليق / التغذية الراجعة</label>
                    <textarea name="feedback" id="feedback" rows="3" class="{{ $textareaClass }}"
                              placeholder="تعليق للطالب">{{ old('feedback', $gradeSubmission->feedback) }}</textarea>
                    @error('feedback')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="status">الحالة <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" class="{{ $fieldClass }}">
                        <option value="submitted" {{ old('status', $gradeSubmission->status) == 'submitted' ? 'selected' : '' }}>مُسلّم</option>
                        <option value="graded" {{ old('status', $gradeSubmission->status) == 'graded' ? 'selected' : '' }}>مقيّم</option>
                        <option value="returned" {{ old('status', $gradeSubmission->status) == 'returned' ? 'selected' : '' }}>مُرجع للطالب</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="btn-press inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white hover:bg-[#184888]">
                        <i class="fas fa-check text-xs"></i>
                        حفظ التقييم
                    </button>
                    <a href="{{ route('admin.assignments.submissions', $assignment) }}"
                       class="btn-press inline-flex h-10 items-center gap-2 rounded-xl border border-line px-5 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent">
                        إلغاء
                    </a>
                </div>
            </form>
        </article>
    @endif

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-5 py-4">
            <h3 class="text-sm font-semibold text-ink">قائمة التسليمات</h3>
        </div>
        @if($submissions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-line">
                    <thead class="bg-[#f8faf9]">
                        <tr>
                            <th class="px-5 py-3 text-right text-xs font-medium text-muted">الطالب</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-muted">تاريخ التسليم</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-muted">المحتوى</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-muted">الدرجة</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-muted">الحالة</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-muted">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-surface">
                        @foreach($submissions as $sub)
                            @php
                                $subStatusClass = $sub->status == 'graded' || $sub->status == 'returned' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-800';
                                $subStatusText = $sub->status == 'returned' ? 'مُرجع' : ($sub->status == 'graded' ? 'مقيّم' : 'مُسلّم');
                            @endphp
                            <tr class="transition hover:bg-accent-soft/30">
                                <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-ink">{{ $sub->student->name ?? '—' }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm tabular-nums text-ink-soft">{{ $sub->submitted_at ? $sub->submitted_at->format('Y-m-d H:i') : '—' }}</td>
                                <td class="max-w-[200px] truncate px-5 py-4 text-sm text-ink-soft" title="{{ strip_tags($sub->content ?? '') }}">{{ Str::limit(strip_tags($sub->content ?? ''), 50) ?: '—' }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm tabular-nums text-ink">{{ $sub->score !== null ? $sub->score . ' / ' . $assignment->max_score : '—' }}</td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $subStatusClass }}">{{ $subStatusText }}</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <a href="{{ route('admin.assignments.submissions', ['assignment' => $assignment, 'grade' => $sub->id]) }}#grade-form-box"
                                       class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent transition hover:bg-accent-soft hover:text-accent"
                                       title="تقييم">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-line px-5 py-4">
                {{ $submissions->withQueryString()->links() }}
            </div>
        @else
            <div class="px-6 py-14 text-center">
                <div class="mx-auto inline-flex size-14 items-center justify-center rounded-2xl bg-[#f2f5f4] text-muted">
                    <i class="fas fa-inbox text-xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-ink">لا توجد تسليمات</h3>
                <p class="mt-1 text-sm text-muted">لم يسلّم أي طالب هذا الواجب بعد.</p>
                <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('admin.assignments.show', $assignment) }}"
                       class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent">
                        <i class="fas fa-eye text-xs"></i>
                        تفاصيل الواجب
                    </a>
                    <a href="{{ route('admin.assignments.by-course', $courseId) }}"
                       class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm text-ink-soft hover:bg-accent-soft hover:text-accent">
                        <i class="fas fa-arrow-right text-xs"></i>
                        واجبات البرنامج
                    </a>
                </div>
            </div>
        @endif
    </article>
</div>
@endsection
