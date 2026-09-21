@extends('layouts.admin')

@section('title', 'تفاصيل حضور المحاضرة - ' . ($lecture->title ?? ''))
@section('page_title', 'تفاصيل حضور المحاضرة')

@section('content')
@php
    $fieldClass = 'h-11 rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">التسجيلات · الحضور</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">{{ $lecture->title }}</h2>
            <p class="mt-1 text-sm text-muted">{{ $lecture->course->title ?? 'بدون برنامج' }}</p>
        </div>
        <a href="{{ route('admin.attendance.index') }}"
           class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            الرجوع لقائمة الحضور
        </a>
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

    <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft sm:p-5">
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-ink"><i class="fas fa-upload ml-1 text-accent"></i> رفع ملف Teams</h3>
            <p class="mt-1 text-xs text-muted">ارفع ملف CSV أو Excel لتسجيل حضور المحاضرة.</p>
        </div>
        <form action="{{ route('admin.attendance.upload-teams', $lecture) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            @csrf
            <input type="file" name="file" accept=".csv,.xlsx,.xls"
                   class="block w-full text-sm text-ink file:ml-2 file:rounded-lg file:border-0 file:bg-[#f2f5f4] file:px-3 file:py-2 file:text-xs file:font-medium file:text-accent sm:w-auto {{ $fieldClass }}">
            <button type="submit" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-upload text-xs"></i>
                رفع ملف Teams
            </button>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-3">
            <h3 class="text-sm font-semibold text-ink">سجلات الحضور للمحاضرة</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm">
                <thead>
                    <tr class="border-b border-line text-right text-xs font-medium text-muted">
                        <th class="px-4 py-3">الطالب</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">الدقائق</th>
                        <th class="px-4 py-3">النسبة</th>
                        <th class="px-4 py-3">وقت التسجيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($attendanceRecords as $record)
                        <tr class="hover:bg-[#f8faf9]">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent"><i class="fas fa-user text-xs"></i></div>
                                    <span class="font-medium text-ink">{{ $record->student->name ?? 'غير محدد' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusClasses = [
                                        'present' => 'bg-emerald-50 text-emerald-700',
                                        'late' => 'bg-amber-50 text-amber-800',
                                        'partial' => 'bg-accent-soft text-accent',
                                        'absent' => 'bg-rose-50 text-rose-700',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $statusClasses[$record->status] ?? 'bg-[#f2f5f4] text-muted' }}">
                                    {{ $record->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 tabular-nums text-muted">{{ (int) ($record->attendance_minutes ?? 0) }}</td>
                            <td class="px-4 py-3 tabular-nums text-muted">{{ number_format((float) ($record->attendance_percentage ?? 0), 1) }}%</td>
                            <td class="px-4 py-3 text-xs tabular-nums text-muted">{{ optional($record->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm text-muted">لا توجد سجلات حضور لهذه المحاضرة حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</div>
@endsection
