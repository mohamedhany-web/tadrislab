@extends('layouts.admin')

@section('title', 'محاضرات: ' . $course->title)
@section('page_title', 'محاضرات البرنامج')

@section('content')
@php
    $statusClasses = [
        'scheduled' => 'bg-amber-50 text-amber-800',
        'in_progress' => 'bg-accent-soft text-accent',
        'completed' => 'bg-emerald-50 text-emerald-700',
        'cancelled' => 'bg-rose-50 text-rose-700',
    ];
    $statusLabels = [
        'scheduled' => 'مجدولة',
        'in_progress' => 'قيد التنفيذ',
        'completed' => 'مكتملة',
        'cancelled' => 'ملغاة',
    ];
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحتوى · المحاضرات المباشرة</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">{{ $course->title }}</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">إدارة محاضرات هذا البرنامج — عرض، إضافة، تعديل، حذف</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.lectures.index') }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm text-ink-soft hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                كل البرامج
            </a>
            <a href="{{ route('admin.lectures.create', ['course_id' => $course->id]) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-plus text-xs"></i>
                إضافة محاضرة
            </a>
        </div>
    </section>

    @if($lectures->count() > 0)
        <p class="text-xs text-muted">
            عرض <span class="font-semibold tabular-nums text-ink">{{ $lectures->firstItem() }}</span>–<span class="font-semibold tabular-nums text-ink">{{ $lectures->lastItem() }}</span>
            من <span class="font-semibold tabular-nums text-ink">{{ $lectures->total() }}</span> محاضرة
        </p>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-line bg-[#f8faf9]">
                            <th class="px-4 py-3 text-right text-xs font-medium text-muted">العنوان</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-muted">المحاضر</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-muted">التاريخ</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-muted">الحالة</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-muted">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach($lectures as $lecture)
                            @php
                                $statusClass = $statusClasses[$lecture->status] ?? $statusClasses['scheduled'];
                                $statusText = $statusLabels[$lecture->status] ?? 'مجدولة';
                            @endphp
                            <tr class="transition hover:bg-accent-soft/20">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-ink">{{ $lecture->title }}</p>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-ink">{{ $lecture->instructor->name ?? '—' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap tabular-nums text-muted">
                                    {{ $lecture->scheduled_at ? $lecture->scheduled_at->format('Y-m-d H:i') : '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.lectures.show', $lecture) }}"
                                           class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent transition hover:bg-accent hover:text-white"
                                           title="عرض">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.lectures.edit', $lecture) }}"
                                           class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent transition hover:bg-accent hover:text-white"
                                           title="تعديل">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.lectures.destroy', $lecture) }}" method="POST" class="inline" onsubmit="return confirm('هل تريد حذف هذه المحاضرة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex size-9 items-center justify-center rounded-xl border border-rose-200 text-rose-700 transition hover:bg-rose-50"
                                                    title="حذف">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-line px-4 py-3">
                {{ $lectures->links() }}
            </div>
        </article>
    @else
        <article class="rounded-2xl border border-dashed border-line bg-surface px-6 py-14 text-center shadow-soft">
            <div class="mx-auto inline-flex size-14 items-center justify-center rounded-2xl bg-[#f2f5f4] text-accent">
                <i class="fas fa-calendar-times text-xl"></i>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-ink">لا توجد محاضرات في هذا البرنامج</h3>
            <p class="mt-1 text-sm text-muted">يمكنك إضافة أول محاضرة لهذا البرنامج</p>
            <a href="{{ route('admin.lectures.create', ['course_id' => $course->id]) }}"
               class="btn-press mt-5 inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-plus text-xs"></i>
                إضافة محاضرة
            </a>
        </article>
    @endif
</div>
@endsection
