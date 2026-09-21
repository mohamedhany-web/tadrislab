@extends('layouts.admin')

@section('title', 'واجبات: ' . $course->title . ' - ' . config('app.name'))
@section('page_title', 'واجبات البرنامج')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-accent">لوحة التحكم</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.assignments.index') }}" class="hover:text-accent">الواجبات</a>
                <span class="mx-1">·</span>
                <span class="text-ink">{{ Str::limit($course->title, 40) }}</span>
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">{{ $course->title }}</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">إدارة واجبات هذا البرنامج — عرض، إضافة، تعديل، حذف، تسليمات.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.assignments.index') }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm text-ink-soft hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                كل البرامج
            </a>
            <a href="{{ route('admin.assignments.create', ['course_id' => $course->id]) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-plus text-xs"></i>
                إضافة واجب
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

    @if($assignments->count() > 0)
        <p class="text-xs text-muted">
            إجمالي <span class="font-semibold tabular-nums text-ink">{{ $assignments->total() }}</span> واجب
        </p>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-5 py-4">
                <h3 class="text-sm font-semibold text-ink">الواجبات ({{ $assignments->total() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-line">
                    <thead class="bg-[#f8faf9]">
                        <tr>
                            <th class="px-5 py-3 text-right text-xs font-medium text-muted">العنوان</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-muted">الدرس</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-muted">الاستحقاق</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-muted">التسليمات</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-muted">الحالة</th>
                            <th class="px-5 py-3 text-right text-xs font-medium text-muted">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-surface">
                        @foreach($assignments as $assignment)
                            @php
                                $statusClass = $assignment->status == 'published' ? 'bg-emerald-50 text-emerald-700' : ($assignment->status == 'draft' ? 'bg-amber-50 text-amber-800' : 'bg-[#f2f5f4] text-muted');
                                $statusText = $assignment->status == 'published' ? 'منشور' : ($assignment->status == 'draft' ? 'مسودة' : 'مؤرشف');
                            @endphp
                            <tr class="transition hover:bg-accent-soft/30">
                                <td class="px-5 py-4">
                                    <div class="text-sm font-semibold text-ink">{{ $assignment->title }}</div>
                                    @if($assignment->description)
                                        <div class="mt-0.5 line-clamp-1 text-xs text-muted">{{ Str::limit($assignment->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm text-ink-soft">{{ $assignment->lesson ? Str::limit($assignment->lesson->title, 25) : '—' }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm tabular-nums text-ink-soft">{{ $assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : '—' }}</td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex items-center rounded-lg bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">{{ $assignment->submissions_count }} تسليم</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <a href="{{ route('admin.assignments.show', $assignment) }}"
                                           class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent transition hover:bg-accent-soft hover:text-accent"
                                           title="عرض"><i class="fas fa-eye text-sm"></i></a>
                                        <a href="{{ route('admin.assignments.submissions', $assignment) }}"
                                           class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent transition hover:bg-accent-soft hover:text-accent"
                                           title="التسليمات"><i class="fas fa-inbox text-sm"></i></a>
                                        <a href="{{ route('admin.assignments.edit', $assignment) }}"
                                           class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent transition hover:bg-accent-soft hover:text-accent"
                                           title="تعديل"><i class="fas fa-edit text-sm"></i></a>
                                        <form action="{{ route('admin.assignments.destroy', $assignment) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الواجب؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-rose-600 transition hover:bg-rose-50"
                                                    title="حذف"><i class="fas fa-trash text-sm"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-line px-5 py-4">
                {{ $assignments->links() }}
            </div>
        </article>
    @else
        <article class="rounded-2xl border border-dashed border-line bg-surface px-6 py-14 text-center shadow-soft">
            <div class="mx-auto inline-flex size-14 items-center justify-center rounded-2xl bg-[#f2f5f4] text-accent">
                <i class="fas fa-tasks text-xl"></i>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-ink">لا توجد واجبات في هذا البرنامج</h3>
            <p class="mt-1 text-sm text-muted">يمكنك إضافة أول واجب لهذا البرنامج.</p>
            <a href="{{ route('admin.assignments.create', ['course_id' => $course->id]) }}"
               class="btn-press mt-5 inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-plus text-xs"></i>
                إضافة واجب
            </a>
        </article>
    @endif
</div>
@endsection
