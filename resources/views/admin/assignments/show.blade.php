@extends('layouts.admin')

@section('title', 'تفاصيل الواجب: ' . $assignment->title . ' - ' . config('app.name'))
@section('page_title', 'تفاصيل الواجب')

@php
    $courseId = $assignment->advanced_course_id ?? $assignment->course_id;
    $statusClass = $assignment->status == 'published' ? 'bg-emerald-50 text-emerald-700' : ($assignment->status == 'draft' ? 'bg-amber-50 text-amber-800' : 'bg-[#f2f5f4] text-muted');
    $statusText = $assignment->status == 'published' ? 'منشور' : ($assignment->status == 'draft' ? 'مسودة' : 'مؤرشف');
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
                <span class="text-ink truncate">{{ Str::limit($assignment->title, 35) }}</span>
            </p>
            <h2 class="mt-1 truncate text-2xl font-semibold tracking-tight text-ink md:text-[28px]">{{ $assignment->title }}</h2>
            <p class="mt-1 text-sm text-muted">
                {{ $assignment->course->title ?? '—' }} · {{ $assignment->course->instructor->name ?? '—' }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.assignments.submissions', $assignment) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-inbox text-xs"></i>
                التسليمات
            </a>
            <a href="{{ route('admin.assignments.edit', $assignment) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm text-ink-soft hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-edit text-xs"></i>
                تعديل
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

    <div class="grid gap-5 xl:grid-cols-4">
        <div class="space-y-5 xl:col-span-3">
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold text-ink">معلومات الواجب</h3>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $statusClass }}">{{ $statusText }}</span>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <div class="rounded-xl border border-line px-3 py-3">
                        <p class="text-xs font-medium text-muted">البرنامج</p>
                        <p class="mt-1 truncate text-sm font-semibold text-ink" title="{{ $assignment->course->title ?? '' }}">{{ Str::limit($assignment->course->title ?? '—', 25) }}</p>
                    </div>
                    <div class="rounded-xl border border-line px-3 py-3">
                        <p class="text-xs font-medium text-muted">المدرب</p>
                        <p class="mt-1 text-sm font-semibold text-ink">{{ $assignment->course->instructor->name ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-line px-3 py-3">
                        <p class="text-xs font-medium text-muted">الاستحقاق</p>
                        <p class="mt-1 text-sm tabular-nums font-semibold text-ink">{{ $assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-line px-3 py-3">
                        <p class="text-xs font-medium text-muted">الدرجة الكلية</p>
                        <p class="mt-1 text-sm tabular-nums font-semibold text-ink">{{ $assignment->max_score }}</p>
                    </div>
                </div>

                @if($assignment->description)
                    <div class="mt-5">
                        <p class="text-xs font-medium text-muted">الوصف</p>
                        <div class="mt-2 rounded-xl border border-line bg-[#f8faf9] px-4 py-3 text-sm text-ink">{{ $assignment->description }}</div>
                    </div>
                @endif
                @if($assignment->instructions)
                    <div class="mt-5">
                        <p class="text-xs font-medium text-muted">التعليمات</p>
                        <div class="mt-2 whitespace-pre-wrap rounded-xl border border-line bg-[#f8faf9] px-4 py-3 text-sm text-ink">{{ $assignment->instructions }}</div>
                    </div>
                @endif
            </article>

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-5 py-4">
                    <h3 class="text-sm font-semibold text-ink">آخر التسليمات</h3>
                </div>
                @if($submissions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-line">
                            <thead class="bg-[#f8faf9]">
                                <tr>
                                    <th class="px-5 py-3 text-right text-xs font-medium text-muted">الطالب</th>
                                    <th class="px-5 py-3 text-right text-xs font-medium text-muted">التاريخ</th>
                                    <th class="px-5 py-3 text-right text-xs font-medium text-muted">الدرجة</th>
                                    <th class="px-5 py-3 text-right text-xs font-medium text-muted">الحالة</th>
                                    <th class="px-5 py-3 text-right text-xs font-medium text-muted">إجراء</th>
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
                                        <td class="whitespace-nowrap px-5 py-4 text-sm tabular-nums text-ink">{{ $sub->score !== null ? $sub->score . ' / ' . $assignment->max_score : '—' }}</td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $subStatusClass }}">{{ $subStatusText }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4">
                                            <a href="{{ route('admin.assignments.submissions', $assignment) }}?grade={{ $sub->id }}"
                                               class="text-sm font-medium text-accent hover:underline">تقييم</a>
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
                    <div class="px-6 py-10 text-center">
                        <div class="mx-auto inline-flex size-12 items-center justify-center rounded-xl bg-[#f2f5f4] text-muted">
                            <i class="fas fa-inbox text-lg"></i>
                        </div>
                        <p class="mt-3 text-sm text-muted">لا توجد تسليمات حتى الآن</p>
                        <a href="{{ route('admin.assignments.submissions', $assignment) }}"
                           class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-accent hover:underline">
                            عرض صفحة التسليمات
                        </a>
                    </div>
                @endif
            </article>
        </div>

        <div class="space-y-3">
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="flex items-center gap-3">
                    <div class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-inbox text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xl font-semibold tabular-nums text-ink">{{ $submissionStats['total'] }}</p>
                        <p class="text-xs text-muted">إجمالي التسليمات</p>
                    </div>
                </div>
            </article>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="flex items-center gap-3">
                    <div class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xl font-semibold tabular-nums text-ink">{{ $submissionStats['submitted'] }}</p>
                        <p class="text-xs text-muted">مُسلّم</p>
                    </div>
                </div>
            </article>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="flex items-center gap-3">
                    <div class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-check-double text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xl font-semibold tabular-nums text-ink">{{ $submissionStats['graded'] }}</p>
                        <p class="text-xs text-muted">مقيّم</p>
                    </div>
                </div>
            </article>
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="flex items-center gap-3">
                    <div class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-reply text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xl font-semibold tabular-nums text-ink">{{ $submissionStats['returned'] }}</p>
                        <p class="text-xs text-muted">مُرجع</p>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
@endsection
