@extends('layouts.admin')

@section('title', 'تفاصيل المحاضرة')
@section('page_title', 'تفاصيل المحاضرة')

@section('content')
@php
    $statusMap = [
        'scheduled' => ['label' => 'مجدولة', 'class' => 'bg-amber-50 text-amber-800'],
        'in_progress' => ['label' => 'قيد التنفيذ', 'class' => 'bg-accent-soft text-accent'],
        'completed' => ['label' => 'مكتملة', 'class' => 'bg-emerald-50 text-emerald-700'],
        'cancelled' => ['label' => 'ملغاة', 'class' => 'bg-rose-50 text-rose-700'],
    ];
    $status = $statusMap[$lecture->status ?? 'scheduled'] ?? $statusMap['scheduled'];
    $platformLabels = [
        'bunny' => 'Bunny.net',
    ];
    $platformLabel = $platformLabels[strtolower($lecture->video_platform ?? '')] ?? 'غير مدعوم';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحتوى · {{ Str::limit($lecture->course->title ?? '', 40) }}</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">{{ $lecture->title }}</h2>
            <p class="mt-1 text-sm text-muted">{{ $lecture->course->title ?? '' }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium {{ $status['class'] }}">{{ $status['label'] }}</span>
            <a href="{{ route('admin.lectures.edit', $lecture) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-edit text-xs"></i>
                تعديل
            </a>
            <a href="{{ route('admin.lectures.by-course', $lecture->course_id) }}"
               class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm text-ink-soft hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                رجوع لمحاضرات البرنامج
            </a>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-soft">
            <i class="fas fa-check-circle ml-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-5 xl:grid-cols-3">
        <div class="space-y-5 xl:col-span-2">
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <h3 class="text-sm font-semibold text-ink">المعلومات الأساسية</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-medium text-muted">البرنامج</p>
                        <p class="mt-1 font-semibold text-ink">{{ $lecture->course->title ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-muted">المحاضر</p>
                        <p class="mt-1 font-semibold text-ink">{{ $lecture->instructor->name ?? '—' }}</p>
                    </div>
                    @if($lecture->lesson)
                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium text-muted">الدرس المرتبط (اختياري)</p>
                            <p class="mt-1 text-ink">{{ $lecture->lesson->title }}</p>
                        </div>
                    @endif
                </div>
                @if($lecture->description)
                    <div class="mt-5 rounded-xl border border-line bg-[#f8faf9] px-4 py-3">
                        <p class="text-xs font-medium text-muted">الوصف</p>
                        <p class="mt-1 whitespace-pre-wrap text-sm text-ink">{{ $lecture->description }}</p>
                    </div>
                @endif
            </article>

            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <h3 class="text-sm font-semibold text-ink">رابط تسجيل المحاضرة</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-medium text-muted">منصة الفيديو</p>
                        <p class="mt-1 font-medium text-ink">{{ $platformLabel }}</p>
                    </div>
                    @if($lecture->recording_url)
                        <div class="sm:col-span-2">
                            <p class="text-xs font-medium text-muted">الرابط</p>
                            <a href="{{ $lecture->recording_url }}" target="_blank" rel="noopener"
                               class="mt-1 inline-flex items-center gap-1 break-all text-sm font-medium text-accent hover:text-[#184888]">
                                {{ Str::limit($lecture->recording_url, 60) }}
                                <i class="fas fa-external-link-alt text-[10px]"></i>
                            </a>
                        </div>
                    @else
                        <div class="sm:col-span-2">
                            <p class="text-sm text-muted">لم يُضف رابط تسجيل</p>
                        </div>
                    @endif
                </div>
            </article>

            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <h3 class="text-sm font-semibold text-ink">التاريخ والوقت</h3>
                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div>
                        <p class="text-xs font-medium text-muted">تاريخ ووقت المحاضرة</p>
                        <p class="mt-1 tabular-nums text-ink">{{ $lecture->scheduled_at ? $lecture->scheduled_at->format('Y-m-d H:i') : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-muted">المدة (دقيقة)</p>
                        <p class="mt-1 tabular-nums text-ink">{{ $lecture->duration_minutes ?? '—' }} دقيقة</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-muted">الحالة</p>
                        <span class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $status['class'] }}">
                            {{ $status['label'] }}
                        </span>
                    </div>
                </div>
            </article>

            @if($lecture->materials->isNotEmpty())
                <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                    <h3 class="text-sm font-semibold text-ink">مواد المحاضرة</h3>
                    <ul class="mt-4 space-y-3">
                        @foreach($lecture->materials as $material)
                            <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-line bg-[#f8faf9] p-4">
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-ink">{{ $material->title ?: $material->file_name }}</p>
                                    <p class="mt-0.5 text-xs text-muted">{{ $material->file_name }}</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    @if($material->is_visible_to_student)
                                        <span class="rounded-lg bg-emerald-50 px-2 py-1 text-[11px] font-medium text-emerald-700">ظاهر للطالب</span>
                                    @else
                                        <span class="rounded-lg bg-[#f2f5f4] px-2 py-1 text-[11px] font-medium text-muted">مخفي</span>
                                    @endif
                                    <a href="{{ storage_asset($material->file_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs font-medium text-accent hover:text-[#184888]">
                                        <i class="fas fa-download"></i>
                                        تحميل
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </article>
            @endif

            @if($lecture->notes)
                <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                    <h3 class="text-sm font-semibold text-ink">ملاحظات</h3>
                    <div class="mt-4 rounded-xl border border-line bg-[#f8faf9] px-4 py-3">
                        <p class="whitespace-pre-wrap text-sm text-ink">{{ $lecture->notes }}</p>
                    </div>
                </article>
            @endif
        </div>

        <div class="space-y-5">
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <h3 class="text-sm font-semibold text-ink">الخيارات</h3>
                <div class="mt-4 space-y-2">
                    <div class="flex items-center gap-3 rounded-xl border border-line p-3 {{ $lecture->has_attendance_tracking ? 'bg-emerald-50 border-emerald-200' : 'bg-[#f8faf9]' }}">
                        <i class="fas {{ $lecture->has_attendance_tracking ? 'fa-check-circle text-emerald-600' : 'fa-times-circle text-muted' }}"></i>
                        <span class="text-sm font-medium {{ $lecture->has_attendance_tracking ? 'text-emerald-800' : 'text-muted' }}">تتبع الحضور</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl border border-line p-3 {{ $lecture->has_assignment ? 'bg-accent-soft border-accent/20' : 'bg-[#f8faf9]' }}">
                        <i class="fas {{ $lecture->has_assignment ? 'fa-check-circle text-accent' : 'fa-times-circle text-muted' }}"></i>
                        <span class="text-sm font-medium {{ $lecture->has_assignment ? 'text-accent' : 'text-muted' }}">يوجد واجب</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl border border-line p-3 {{ $lecture->has_evaluation ? 'bg-amber-50 border-amber-200' : 'bg-[#f8faf9]' }}">
                        <i class="fas {{ $lecture->has_evaluation ? 'fa-check-circle text-amber-700' : 'fa-times-circle text-muted' }}"></i>
                        <span class="text-sm font-medium {{ $lecture->has_evaluation ? 'text-amber-800' : 'text-muted' }}">يوجد تقييم</span>
                    </div>
                </div>
            </article>

            @if($lecture->has_attendance_tracking)
                <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                    <h3 class="text-sm font-semibold text-ink">الحضور</h3>
                    <a href="{{ route('admin.attendance.lecture', $lecture) }}"
                       class="btn-press mt-4 inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-accent text-sm font-medium text-white hover:bg-[#184888]">
                        عرض تفاصيل الحضور
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                </article>
            @endif
        </div>
    </div>

    @if($lecture->has_assignment && $lecture->assignments->isNotEmpty())
        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <h3 class="text-sm font-semibold text-ink">واجبات المحاضرة</h3>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($lecture->assignments as $assignment)
                    <div class="rounded-xl border border-line bg-[#f8faf9] p-4">
                        <h4 class="font-semibold text-ink">{{ $assignment->title }}</h4>
                        <p class="mt-1 text-xs text-muted">
                            تاريخ التسليم: {{ $assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : '—' }}
                        </p>
                    </div>
                @endforeach
            </div>
        </article>
    @endif
</div>
@endsection
