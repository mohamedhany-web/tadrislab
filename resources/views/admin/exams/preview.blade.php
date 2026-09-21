@extends('layouts.admin')

@section('title', 'معاينة الامتحان')
@section('page_title', 'معاينة الامتحان')

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
                <span class="text-ink">المعاينة</span>
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">معاينة الامتحان</h2>
            <p class="mt-1 text-sm text-muted">{{ $exam->title }} — {{ $exam->examQuestions->count() }} سؤال</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.exams.show', $exam) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                العودة للامتحان
            </a>
            <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-list text-xs"></i>
                امتحانات البرنامج
            </a>
            <a href="{{ route('admin.exams.questions.manage', $exam) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-cog text-xs"></i>
                إدارة الأسئلة
            </a>
        </div>
    </section>

    <div class="mx-auto max-w-4xl space-y-5">
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-5 py-4">
                <h3 class="text-sm font-semibold text-ink">{{ $exam->title }}</h3>
            </div>
            <div class="space-y-4 p-5">
                @if($exam->description)
                    <div>
                        <h4 class="mb-2 text-xs font-medium uppercase tracking-wider text-muted">وصف الامتحان</h4>
                        <p class="text-sm leading-relaxed text-ink-soft">{!! nl2br(e($exam->description)) !!}</p>
                    </div>
                @endif

                @if($exam->instructions)
                    <div>
                        <h4 class="mb-2 text-xs font-medium uppercase tracking-wider text-muted">تعليمات الامتحان</h4>
                        <div class="rounded-xl border border-line bg-accent-soft/30 p-4">
                            <p class="text-sm leading-relaxed text-ink">{!! nl2br(e($exam->instructions)) !!}</p>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                    <div class="rounded-xl border border-line bg-[#f2f5f4]/50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wider text-muted">المدة</div>
                        <div class="mt-1 font-bold tabular-nums text-ink">{{ $exam->duration_minutes }} دقيقة</div>
                    </div>
                    <div class="rounded-xl border border-line bg-[#f2f5f4]/50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wider text-muted">عدد الأسئلة</div>
                        <div class="mt-1 font-bold tabular-nums text-ink">{{ $exam->examQuestions->count() }} سؤال</div>
                    </div>
                    <div class="rounded-xl border border-line bg-[#f2f5f4]/50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wider text-muted">إجمالي الدرجات</div>
                        <div class="mt-1 font-bold tabular-nums text-ink">{{ $exam->total_marks ?? $exam->calculateTotalMarks() }}</div>
                    </div>
                    <div class="rounded-xl border border-line bg-[#f2f5f4]/50 p-4">
                        <div class="text-xs font-medium uppercase tracking-wider text-muted">درجة النجاح</div>
                        <div class="mt-1 font-bold tabular-nums text-ink">{{ $exam->passing_marks }}%</div>
                    </div>
                </div>
            </div>
        </article>

        @if($exam->examQuestions->count() > 0)
            <div class="space-y-5">
                @foreach($exam->examQuestions as $index => $examQuestion)
                    @php $q = $examQuestion->question; @endphp
                    @if(!$q)
                        @continue
                    @endif
                    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                        <div class="p-5">
                            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-sm font-bold text-accent">
                                        {{ $index + 1 }}
                                    </span>
                                    @if($examQuestion->is_required)
                                        <span class="inline-flex items-center rounded-lg bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                            إجباري
                                        </span>
                                    @endif
                                    <span class="text-sm font-semibold tabular-nums text-muted">({{ $examQuestion->marks }} نقطة)</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-muted">
                                    <span>{{ $q->getTypeLabel() }}</span>
                                    @if($q->category)
                                        <span>|</span>
                                        <span>{{ $q->category->name }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="question-content mb-4 text-lg leading-relaxed text-ink">
                                {!! nl2br(e($q->question)) !!}
                            </div>

                            @if($q->image_url && $q->getImageUrl())
                                <div class="mb-4">
                                    <img src="{{ $q->getImageUrl() }}" alt="صورة السؤال" class="max-w-full rounded-xl border border-line shadow-soft">
                                </div>
                            @endif

                            @if($q->type === 'multiple_choice' && $q->options && count($q->options) > 0)
                                <div class="space-y-2">
                                    @foreach($q->options as $optIndex => $option)
                                        <div class="flex items-center rounded-xl border border-line bg-[#f2f5f4]/50 p-3">
                                            <span class="ml-3 inline-flex size-8 items-center justify-center rounded-lg border border-line bg-surface text-sm font-bold text-muted">{{ chr(65 + $optIndex) }}</span>
                                            <span class="text-ink">{{ $option }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($q->type === 'true_false')
                                <div class="space-y-2">
                                    <div class="flex items-center rounded-xl border border-line bg-[#f2f5f4]/50 p-3">
                                        <span class="ml-3 inline-flex size-8 items-center justify-center rounded-lg border border-line bg-surface text-sm font-bold text-muted">أ</span>
                                        <span class="text-ink">صحيح</span>
                                    </div>
                                    <div class="flex items-center rounded-xl border border-line bg-[#f2f5f4]/50 p-3">
                                        <span class="ml-3 inline-flex size-8 items-center justify-center rounded-lg border border-line bg-surface text-sm font-bold text-muted">ب</span>
                                        <span class="text-ink">خطأ</span>
                                    </div>
                                </div>
                            @elseif($q->type === 'fill_blank')
                                <div class="rounded-xl border border-dashed border-line bg-[#f2f5f4]/50 p-4">
                                    <span class="text-sm text-muted">منطقة الإجابة (املأ الفراغ)</span>
                                </div>
                            @elseif(in_array($q->type, ['short_answer', 'essay']))
                                <div class="min-h-[120px] rounded-xl border border-line bg-[#f2f5f4]/50 p-4">
                                    <span class="text-sm text-muted">{{ $q->type === 'essay' ? 'منطقة الإجابة المقالية' : 'منطقة الإجابة القصيرة' }}</span>
                                </div>
                            @elseif($q->type === 'matching')
                                <div class="rounded-xl border border-line bg-[#f2f5f4]/50 p-4">
                                    <span class="text-sm text-muted">سؤال مطابقة — قائمة العناصر</span>
                                </div>
                            @elseif($q->type === 'ordering')
                                <div class="rounded-xl border border-line bg-[#f2f5f4]/50 p-4">
                                    <span class="text-sm text-muted">سؤال ترتيب — قائمة العناصر</span>
                                </div>
                            @endif

                            @if($examQuestion->time_limit)
                                <div class="mt-3 flex items-center gap-1 text-xs text-muted">
                                    <i class="fas fa-clock"></i>
                                    وقت الإجابة المخصص: {{ $examQuestion->time_limit }} ثانية
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <article class="rounded-2xl border border-line bg-accent-soft/30 p-5 shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="mb-2 flex items-center gap-2 text-lg font-semibold text-ink">
                            <i class="fas fa-file-alt text-accent"></i>
                            ملخص الامتحان
                        </h3>
                        <ul class="space-y-1 text-sm text-muted">
                            <li>إجمالي الأسئلة: {{ $exam->examQuestions->count() }}</li>
                            <li>إجمالي الدرجات: {{ $exam->total_marks ?? $exam->calculateTotalMarks() }}</li>
                            <li>الأسئلة الإجبارية: {{ $exam->examQuestions->where('is_required', true)->count() }}</li>
                            <li>الأسئلة الاختيارية: {{ $exam->examQuestions->where('is_required', false)->count() }}</li>
                        </ul>
                    </div>
                    <div class="text-accent/40">
                        <i class="fas fa-clipboard-check text-4xl"></i>
                    </div>
                </div>
            </article>
        @else
            <article class="rounded-2xl border border-dashed border-line bg-surface px-6 py-14 text-center shadow-soft">
                <div class="mx-auto inline-flex size-14 items-center justify-center rounded-2xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-question-circle text-xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-ink">لا توجد أسئلة في الامتحان</h3>
                <p class="mt-1 text-sm text-muted">أضف أسئلة من صفحة إدارة الأسئلة ثم عاين الامتحان مرة أخرى.</p>
                <a href="{{ route('admin.exams.questions.manage', $exam) }}" class="btn-press mt-5 inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-plus text-xs"></i>
                    إدارة الأسئلة
                </a>
            </article>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.question-content {
    font-family: 'IBM Plex Sans Arabic', sans-serif;
    line-height: 1.8;
}
</style>
@endpush
