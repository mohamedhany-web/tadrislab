@extends('layouts.admin')

@section('title', 'امتحانات: ' . $course->title)
@section('page_title', 'امتحانات البرنامج')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-accent">لوحة التحكم</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.exams.index') }}" class="hover:text-accent">الامتحانات</a>
                <span class="mx-1">·</span>
                <span class="text-ink">{{ Str::limit($course->title, 40) }}</span>
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">{{ $course->title }}</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">إدارة امتحانات هذا البرنامج — عرض، إضافة، تعديل، حذف، أسئلة، إحصائيات.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.exams.index') }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                كل البرامج
            </a>
            <a href="{{ route('admin.exams.create', ['course_id' => $course->id]) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-plus text-xs"></i>
                إضافة امتحان
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

    @if($exams->count() > 0)
        <p class="text-xs text-muted">
            عرض <span class="font-semibold tabular-nums text-ink">{{ $exams->firstItem() }}</span>–<span class="font-semibold tabular-nums text-ink">{{ $exams->lastItem() }}</span>
            من <span class="font-semibold tabular-nums text-ink">{{ $exams->total() }}</span> امتحان
        </p>

        <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-line px-5 py-4">
                <h3 class="text-sm font-semibold text-ink">الامتحانات ({{ $exams->total() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-line">
                    <thead class="bg-[#f2f5f4]/50">
                        <tr>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted">العنوان</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted">المدة</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted">الأسئلة</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted">المحاولات</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted">الحالة</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-muted">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line bg-surface">
                        @foreach($exams as $exam)
                            <tr class="transition-colors hover:bg-accent-soft/20">
                                <td class="px-5 py-4">
                                    <div class="text-sm font-semibold text-ink">{{ $exam->title }}</div>
                                    @if($exam->description)
                                        <div class="mt-0.5 line-clamp-1 text-xs text-muted">{{ Str::limit($exam->description, 50) }}</div>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm tabular-nums text-ink-soft">{{ $exam->duration_minutes }} د</td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm tabular-nums text-ink-soft">{{ $exam->questions_count ?? 0 }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-sm tabular-nums text-ink-soft">{{ $exam->attempts_count ?? 0 }}</td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $exam->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $exam->is_active ? 'نشط' : 'معطل' }}
                                    </span>
                                    @if($exam->is_published)
                                        <span class="mr-1 inline-flex items-center rounded-full bg-accent-soft px-2.5 py-1 text-xs font-medium text-accent">منشور</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <a href="{{ route('admin.exams.show', $exam) }}" class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent transition hover:bg-accent hover:text-white" title="عرض"><i class="fas fa-eye text-sm"></i></a>
                                        <a href="{{ route('admin.exams.questions.manage', $exam) }}" class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent transition hover:bg-accent hover:text-white" title="الأسئلة"><i class="fas fa-question-circle text-sm"></i></a>
                                        <a href="{{ route('admin.exams.statistics', $exam) }}" class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent transition hover:bg-accent hover:text-white" title="إحصائيات"><i class="fas fa-chart-bar text-sm"></i></a>
                                        <a href="{{ route('admin.exams.preview', $exam) }}" class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent transition hover:bg-accent hover:text-white" title="معاينة"><i class="fas fa-external-link-alt text-sm"></i></a>
                                        <a href="{{ route('admin.exams.edit', $exam) }}" class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent transition hover:bg-accent hover:text-white" title="تعديل"><i class="fas fa-edit text-sm"></i></a>
                                        <button type="button" onclick="toggleExamStatus({{ $exam->id }})" class="inline-flex size-9 items-center justify-center rounded-xl border border-line transition {{ $exam->is_active ? 'text-rose-700 hover:bg-rose-50' : 'text-emerald-700 hover:bg-emerald-50' }}" title="{{ $exam->is_active ? 'إيقاف' : 'تفعيل' }}"><i class="fas {{ $exam->is_active ? 'fa-pause' : 'fa-play' }} text-sm"></i></button>
                                        <button type="button" onclick="toggleExamPublish({{ $exam->id }})" class="inline-flex size-9 items-center justify-center rounded-xl border border-line text-amber-800 transition hover:bg-amber-50" title="{{ $exam->is_published ? 'إلغاء النشر' : 'نشر' }}"><i class="fas fa-globe text-sm"></i></button>
                                        <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الامتحان؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex size-9 items-center justify-center rounded-xl border border-line text-rose-700 transition hover:bg-rose-50" title="حذف"><i class="fas fa-trash text-sm"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-line px-5 py-4">
                {{ $exams->links() }}
            </div>
        </div>
    @else
        <article class="rounded-2xl border border-dashed border-line bg-surface px-6 py-14 text-center shadow-soft">
            <div class="mx-auto inline-flex size-14 items-center justify-center rounded-2xl bg-[#f2f5f4] text-accent">
                <i class="fas fa-clipboard-list text-xl"></i>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-ink">لا توجد امتحانات في هذا البرنامج</h3>
            <p class="mt-1 text-sm text-muted">يمكنك إضافة أول امتحان لهذا البرنامج.</p>
            <a href="{{ route('admin.exams.create', ['course_id' => $course->id]) }}"
               class="btn-press mt-5 inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-plus text-xs"></i>
                إضافة امتحان
            </a>
        </article>
    @endif
</div>

@push('scripts')
<script>
function toggleExamStatus(examId) {
    if (confirm('هل تريد تغيير حالة هذا الامتحان؟')) {
        fetch('/admin/exams/' + examId + '/toggle-status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) { if (data.success) location.reload(); else alert('حدث خطأ'); })
        .catch(function() { alert('حدث خطأ'); });
    }
}
function toggleExamPublish(examId) {
    if (confirm('هل تريد تغيير حالة نشر هذا الامتحان؟')) {
        fetch('/admin/exams/' + examId + '/toggle-publish', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) { if (data.success) location.reload(); else alert('حدث خطأ'); })
        .catch(function() { alert('حدث خطأ'); });
    }
}
</script>
@endpush
@endsection
