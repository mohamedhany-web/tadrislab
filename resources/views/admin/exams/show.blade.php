@extends('layouts.admin')

@section('title', 'تفاصيل الامتحان')
@section('page_title', 'تفاصيل الامتحان')

@php
    $stats = $exam->stats ?? [];
    $averageScore = $stats['average_score'] ?? 0;
    $passRate = $stats['pass_rate'] ?? 0;
@endphp

@section('content')
<div class="space-y-5" x-data="{ activeTab: 'questions' }">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-accent">لوحة التحكم</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.exams.index') }}" class="hover:text-accent">الامتحانات</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}" class="hover:text-accent">{{ Str::limit($exam->course->title ?? '', 30) }}</a>
                <span class="mx-1">·</span>
                <span class="text-ink">{{ Str::limit($exam->title, 40) }}</span>
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">{{ $exam->title }}</h2>
            <p class="mt-1 text-sm text-muted">{{ $exam->course->title ?? '' }} — {{ $exam->duration_minutes }} دقيقة</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.exams.edit', $exam) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-edit text-xs"></i>
                تعديل
            </a>
            <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                رجوع لامتحانات البرنامج
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

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <article class="rounded-2xl border border-line bg-surface shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line px-5 py-4">
                    <h3 class="text-sm font-semibold text-ink">معلومات الامتحان</h3>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $exam->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            {{ $exam->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                        @if($exam->is_published)
                            <span class="inline-flex items-center rounded-full bg-accent-soft px-3 py-1 text-xs font-medium text-accent">
                                <i class="fas fa-globe ml-1 text-[10px]"></i>
                                منشور
                            </span>
                        @endif
                    </div>
                </div>
                <div class="space-y-5 p-5">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <p class="mb-1 text-xs font-medium uppercase tracking-wider text-muted">البرنامج</p>
                            <p class="font-medium text-ink">{{ $exam->course->title ?? '—' }}</p>
                            @if($exam->course && $exam->course->academicSubject)
                                <p class="mt-0.5 text-sm text-muted">{{ $exam->course->academicSubject->name }}</p>
                            @endif
                        </div>
                        @if($exam->lesson)
                        <div>
                            <p class="mb-1 text-xs font-medium uppercase tracking-wider text-muted">الدرس</p>
                            <p class="font-medium text-ink">{{ $exam->lesson->title }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="mb-1 text-xs font-medium uppercase tracking-wider text-muted">مدة الامتحان</p>
                            <p class="font-medium tabular-nums text-ink">{{ $exam->duration_minutes }} دقيقة</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium uppercase tracking-wider text-muted">درجة النجاح</p>
                            <p class="font-medium tabular-nums text-ink">{{ $exam->passing_marks }}%</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium uppercase tracking-wider text-muted">المحاولات المسموحة</p>
                            <p class="font-medium text-ink">{{ $exam->attempts_allowed == 0 ? 'غير محدود' : $exam->attempts_allowed }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs font-medium uppercase tracking-wider text-muted">إجمالي الدرجات</p>
                            <p class="font-medium tabular-nums text-ink">{{ $exam->total_marks ?? '—' }}</p>
                        </div>
                    </div>
                    @if($exam->description)
                        <div>
                            <p class="mb-1 text-xs font-medium uppercase tracking-wider text-muted">الوصف</p>
                            <p class="text-sm text-ink-soft">{{ $exam->description }}</p>
                        </div>
                    @endif
                    @if($exam->instructions)
                        <div>
                            <p class="mb-1 text-xs font-medium uppercase tracking-wider text-muted">التعليمات</p>
                            <div class="whitespace-pre-wrap rounded-xl border border-line bg-[#f2f5f4] p-4 text-sm text-ink-soft">{{ $exam->instructions }}</div>
                        </div>
                    @endif
                </div>
            </article>
        </div>

        <div class="space-y-4">
            <article class="flex items-center gap-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <div class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-question-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold tabular-nums text-ink">{{ $exam->examQuestions->count() }}</p>
                    <p class="text-sm text-muted">أسئلة</p>
                </div>
            </article>
            <article class="flex items-center gap-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <div class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold tabular-nums text-ink">{{ $exam->attempts->count() }}</p>
                    <p class="text-sm text-muted">محاولات</p>
                </div>
            </article>
            <article class="flex items-center gap-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <div class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-star text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold tabular-nums text-ink">{{ number_format($averageScore, 1) }}</p>
                    <p class="text-sm text-muted">متوسط الدرجات</p>
                </div>
            </article>
            <article class="flex items-center gap-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <div class="inline-flex size-12 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-percentage text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold tabular-nums text-ink">{{ number_format($passRate, 1) }}%</p>
                    <p class="text-sm text-muted">معدل النجاح</p>
                </div>
            </article>
        </div>
    </div>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line bg-[#f2f5f4]/30 p-2">
            <nav class="flex flex-wrap gap-1" role="tablist">
                <button type="button" @click="activeTab = 'questions'" :class="activeTab === 'questions' ? 'bg-accent text-white' : 'bg-surface text-muted hover:bg-accent-soft hover:text-accent'" class="inline-flex items-center gap-2 rounded-xl border border-line px-4 py-2.5 text-sm font-semibold transition-colors">
                    <i class="fas fa-question-circle"></i>
                    الأسئلة ({{ $exam->examQuestions->count() }})
                </button>
                <button type="button" @click="activeTab = 'attempts'" :class="activeTab === 'attempts' ? 'bg-accent text-white' : 'bg-surface text-muted hover:bg-accent-soft hover:text-accent'" class="inline-flex items-center gap-2 rounded-xl border border-line px-4 py-2.5 text-sm font-semibold transition-colors">
                    <i class="fas fa-users"></i>
                    المحاولات ({{ $exam->attempts->count() }})
                </button>
                <button type="button" @click="activeTab = 'settings'" :class="activeTab === 'settings' ? 'bg-accent text-white' : 'bg-surface text-muted hover:bg-accent-soft hover:text-accent'" class="inline-flex items-center gap-2 rounded-xl border border-line px-4 py-2.5 text-sm font-semibold transition-colors">
                    <i class="fas fa-cogs"></i>
                    الإعدادات
                </button>
                <button type="button" @click="activeTab = 'actions'" :class="activeTab === 'actions' ? 'bg-accent text-white' : 'bg-surface text-muted hover:bg-accent-soft hover:text-accent'" class="inline-flex items-center gap-2 rounded-xl border border-line px-4 py-2.5 text-sm font-semibold transition-colors">
                    <i class="fas fa-tools"></i>
                    الإجراءات
                </button>
            </nav>
        </div>

        <div class="p-5">
            <div x-show="activeTab === 'questions'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
                    <h3 class="text-sm font-semibold text-ink">أسئلة الامتحان</h3>
                    <a href="{{ route('admin.exams.questions.manage', $exam) }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                        <i class="fas fa-cog text-xs"></i>
                        إدارة الأسئلة
                    </a>
                </div>

                @if($exam->examQuestions->count() > 0)
                    <div class="space-y-3">
                        @foreach($exam->examQuestions as $examQuestion)
                            @php
                                $diffClass = $examQuestion->question->difficulty_level == 'easy' ? 'bg-emerald-50 text-emerald-700' : ($examQuestion->question->difficulty_level == 'medium' ? 'bg-amber-50 text-amber-800' : 'bg-rose-50 text-rose-700');
                            @endphp
                            <div class="flex flex-wrap items-center justify-between gap-4 rounded-xl border border-line bg-[#f2f5f4]/50 p-4">
                                <div class="flex min-w-0 flex-1 items-center gap-4">
                                    <div class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-sm font-bold text-accent">{{ $examQuestion->order }}</div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-ink">{{ Str::limit($examQuestion->question->question ?? '', 100) }}</p>
                                        <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-muted">
                                            <span>{{ $examQuestion->question->getTypeLabel() ?? '—' }}</span>
                                            <span>{{ $examQuestion->marks }} نقطة</span>
                                            @if($examQuestion->question && $examQuestion->question->category)
                                                <span>{{ $examQuestion->question->category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-semibold {{ $diffClass }}">
                                    {{ $examQuestion->question->getDifficultyLabel() ?? '—' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-line bg-[#f2f5f4]/50 py-12 text-center">
                        <i class="fas fa-question-circle mb-4 text-5xl text-muted/40"></i>
                        <h4 class="mb-2 text-lg font-semibold text-ink">لا توجد أسئلة</h4>
                        <p class="mb-4 text-sm text-muted">ابدأ بإضافة الأسئلة لهذا الامتحان</p>
                        <a href="{{ route('admin.exams.questions.manage', $exam) }}" class="btn-press inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                            <i class="fas fa-plus text-xs"></i>
                            إضافة أسئلة
                        </a>
                    </div>
                @endif
            </div>

            <div x-show="activeTab === 'attempts'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak style="display: none;">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
                    <h3 class="text-sm font-semibold text-ink">محاولات الطلاب</h3>
                    <a href="{{ route('admin.exams.statistics', $exam) }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                        <i class="fas fa-chart-bar text-xs"></i>
                        إحصائيات مفصلة
                    </a>
                </div>

                @if($exam->attempts->count() > 0)
                    <div class="overflow-x-auto rounded-xl border border-line">
                        <table class="min-w-full divide-y divide-line">
                            <thead class="bg-[#f2f5f4]/50">
                                <tr>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted">المعلم</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted">النتيجة</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted">الوقت</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted">الحالة</th>
                                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted">التاريخ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line bg-surface">
                                @foreach($exam->attempts->take(20) as $attempt)
                                    <tr class="transition-colors hover:bg-accent-soft/20">
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="inline-flex size-10 items-center justify-center rounded-xl bg-[#f2f5f4] text-sm font-semibold text-accent">{{ substr($attempt->user->name ?? '?', 0, 1) }}</div>
                                                <div>
                                                    <div class="text-sm font-medium text-ink">{{ $attempt->user->name ?? '—' }}</div>
                                                    <div class="text-xs text-muted">{{ $attempt->user->email ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            @if($attempt->status === 'completed')
                                                <span class="text-sm font-medium tabular-nums text-ink">{{ number_format($attempt->score ?? 0, 1) }} / {{ $exam->total_marks }}</span>
                                                <span class="block text-xs tabular-nums text-muted">{{ number_format($attempt->percentage ?? 0, 1) }}%</span>
                                            @else
                                                <span class="text-sm text-muted">لم يكتمل</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-sm text-ink-soft">{{ $attempt->formatted_time ?? '—' }}</td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="inline-flex items-center rounded-lg px-2.5 py-0.5 text-xs font-semibold {{ $attempt->result_color == 'green' ? 'bg-emerald-50 text-emerald-700' : ($attempt->result_color == 'red' ? 'bg-rose-50 text-rose-700' : 'bg-[#f2f5f4] text-muted') }}">
                                                {{ $attempt->result_status ?? '—' }}
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-sm tabular-nums text-ink-soft">{{ $attempt->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-line bg-[#f2f5f4]/50 py-12 text-center">
                        <i class="fas fa-users mb-4 text-5xl text-muted/40"></i>
                        <h4 class="mb-2 text-lg font-semibold text-ink">لا توجد محاولات</h4>
                        <p class="text-sm text-muted">لم يقم أي طالب بأداء هذا الامتحان بعد</p>
                    </div>
                @endif
            </div>

            <div x-show="activeTab === 'settings'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak style="display: none;">
                <h3 class="mb-5 text-sm font-semibold text-ink">إعدادات الامتحان</h3>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div class="rounded-xl border border-line bg-[#f2f5f4]/50 p-5">
                        <h4 class="mb-4 flex items-center gap-2 font-semibold text-ink"><i class="fas fa-eye text-accent"></i> إعدادات العرض</h4>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-center justify-between"><span class="text-muted">خلط الأسئلة</span><span class="font-semibold {{ $exam->randomize_questions ? 'text-emerald-700' : 'text-muted' }}">{{ $exam->randomize_questions ? 'مفعل' : 'معطل' }}</span></li>
                            <li class="flex items-center justify-between"><span class="text-muted">خلط الخيارات</span><span class="font-semibold {{ $exam->randomize_options ? 'text-emerald-700' : 'text-muted' }}">{{ $exam->randomize_options ? 'مفعل' : 'معطل' }}</span></li>
                            <li class="flex items-center justify-between"><span class="text-muted">عرض النتائج فوراً</span><span class="font-semibold {{ $exam->show_results_immediately ? 'text-emerald-700' : 'text-muted' }}">{{ $exam->show_results_immediately ? 'مفعل' : 'معطل' }}</span></li>
                            <li class="flex items-center justify-between"><span class="text-muted">عرض الإجابات الصحيحة</span><span class="font-semibold {{ $exam->show_correct_answers ? 'text-emerald-700' : 'text-muted' }}">{{ $exam->show_correct_answers ? 'مفعل' : 'معطل' }}</span></li>
                        </ul>
                    </div>
                    <div class="rounded-xl border border-line bg-[#f2f5f4]/50 p-5">
                        <h4 class="mb-4 flex items-center gap-2 font-semibold text-ink"><i class="fas fa-shield-alt text-accent"></i> إعدادات الأمان</h4>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-center justify-between"><span class="text-muted">منع تبديل التبويبات</span><span class="font-semibold {{ $exam->prevent_tab_switch ? 'text-emerald-700' : 'text-muted' }}">{{ $exam->prevent_tab_switch ? 'مفعل' : 'معطل' }}</span></li>
                            <li class="flex items-center justify-between"><span class="text-muted">تسليم تلقائي</span><span class="font-semibold {{ $exam->auto_submit ? 'text-emerald-700' : 'text-muted' }}">{{ $exam->auto_submit ? 'مفعل' : 'معطل' }}</span></li>
                            <li class="flex items-center justify-between"><span class="text-muted">تتطلب كاميرا</span><span class="font-semibold {{ $exam->require_camera ? 'text-emerald-700' : 'text-muted' }}">{{ $exam->require_camera ? 'مطلوبة' : 'غير مطلوبة' }}</span></li>
                            <li class="flex items-center justify-between"><span class="text-muted">تتطلب مايكروفون</span><span class="font-semibold {{ $exam->require_microphone ? 'text-emerald-700' : 'text-muted' }}">{{ $exam->require_microphone ? 'مطلوب' : 'غير مطلوب' }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'actions'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak style="display: none;">
                <h3 class="mb-5 text-sm font-semibold text-ink">الإجراءات</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-2xl border border-line p-5 transition hover:border-accent/30">
                        <h4 class="mb-1 font-semibold text-ink">حالة الامتحان</h4>
                        <p class="mb-4 text-sm text-muted">تفعيل أو إيقاف</p>
                        <button type="button" onclick="toggleExamStatus({{ $exam->id }})" class="btn-press w-full rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition {{ $exam->is_active ? 'bg-rose-600 hover:bg-rose-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                            {{ $exam->is_active ? 'إيقاف الامتحان' : 'تفعيل الامتحان' }}
                        </button>
                    </div>
                    <div class="rounded-2xl border border-line p-5 transition hover:border-accent/30">
                        <h4 class="mb-1 font-semibold text-ink">حالة النشر</h4>
                        <p class="mb-4 text-sm text-muted">نشر للطلاب</p>
                        <button type="button" onclick="toggleExamPublish({{ $exam->id }})" class="btn-press w-full rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition {{ $exam->is_published ? 'bg-amber-600 hover:bg-amber-700' : 'bg-accent hover:bg-[#184888]' }}">
                            {{ $exam->is_published ? 'إلغاء النشر' : 'نشر الامتحان' }}
                        </button>
                    </div>
                    <div class="rounded-2xl border border-line p-5 transition hover:border-accent/30">
                        <h4 class="mb-1 font-semibold text-ink">معاينة</h4>
                        <p class="mb-4 text-sm text-muted">كمعلم</p>
                        <a href="{{ route('admin.exams.preview', $exam) }}" class="btn-press block w-full rounded-xl bg-accent px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-[#184888]">
                            معاينة الامتحان
                        </a>
                    </div>
                    <div class="rounded-2xl border border-line p-5 transition hover:border-accent/30">
                        <h4 class="mb-1 font-semibold text-ink">نسخ الامتحان</h4>
                        <p class="mb-4 text-sm text-muted">إنشاء نسخة</p>
                        <form action="{{ route('admin.exams.duplicate', $exam) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('هل تريد إنشاء نسخة من هذا الامتحان؟')" class="btn-press w-full rounded-xl border border-line px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-accent-soft hover:text-accent">
                                نسخ الامتحان
                            </button>
                        </form>
                    </div>
                    <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-5">
                        <h4 class="mb-1 font-semibold text-rose-900">حذف الامتحان</h4>
                        <p class="mb-4 text-sm text-rose-700">حذف نهائي</p>
                        <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('هل أنت متأكد من حذف هذا الامتحان؟ لا يمكن التراجع.');" class="btn-press w-full rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-700">
                                حذف الامتحان
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </article>
</div>

@push('scripts')
<script>
function toggleExamStatus(examId) {
    if (!confirm('هل تريد تغيير حالة هذا الامتحان؟')) return;
    fetch('/admin/exams/' + examId + '/toggle-status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { if (data.success) { location.reload(); } else { alert(data.message || 'حدث خطأ'); } })
    .catch(function() { alert('حدث خطأ'); });
}
function toggleExamPublish(examId) {
    if (!confirm('هل تريد تغيير حالة نشر هذا الامتحان؟')) return;
    fetch('/admin/exams/' + examId + '/toggle-publish', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { if (data.success) { location.reload(); } else { alert(data.message || 'حدث خطأ'); } })
    .catch(function() { alert('حدث خطأ'); });
}
</script>
@endpush
@endsection
