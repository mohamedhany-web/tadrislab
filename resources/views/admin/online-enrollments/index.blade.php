@extends('layouts.admin')

@section('title', 'تسجيلات البرامج - ' . config('app.name'))
@section('page_title', 'تسجيلات البرامج')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">التسجيلات · البرامج المسجّلة</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تسجيلات الطلاب في البرامج</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">تفعيل يدوي، بحث بالطالب، ومتابعة التقدم والحالة.</p>
        </div>
        <a href="{{ route('admin.online-enrollments.create') }}"
           class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
            <i class="fas fa-plus text-xs"></i>
            تسجيل طالب جديد
        </a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-soft">
            <i class="fas fa-check-circle ml-1"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 shadow-soft">
            @foreach($errors->all() as $error)
                <p><i class="fas fa-exclamation-circle ml-1"></i> {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent"><i class="fas fa-users text-sm"></i></div>
            <p class="mt-3 text-xs font-medium text-muted">إجمالي التسجيلات</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-ink">{{ number_format($stats['total'] ?? 0) }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent"><i class="fas fa-clock text-sm"></i></div>
            <p class="mt-3 text-xs font-medium text-muted">في الانتظار</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-amber-700">{{ number_format($stats['pending'] ?? 0) }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent"><i class="fas fa-check-circle text-sm"></i></div>
            <p class="mt-3 text-xs font-medium text-muted">نشط</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-emerald-700">{{ number_format($stats['active'] ?? 0) }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent"><i class="fas fa-graduation-cap text-sm"></i></div>
            <p class="mt-3 text-xs font-medium text-muted">مكتمل</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-ink">{{ number_format($stats['completed'] ?? 0) }}</p>
        </article>
    </section>

    <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft sm:p-5">
        <div class="mb-4 flex flex-wrap items-start justify-between gap-2">
            <div>
                <h3 class="text-sm font-semibold text-ink"><i class="fas fa-bolt ml-1 text-accent"></i> تفعيل سريع بالبريد</h3>
                <p class="mt-1 text-xs text-muted">أدخل بريد الطالب واختر البرنامج — يُنشأ/يُفعّل التسجيل ويُرسل بريد التفعيل.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.online-enrollments.quick-activate') }}" class="grid gap-3 md:grid-cols-3">
            @csrf
            <div>
                <label class="{{ $labelClass }}" for="quick_email">بريد الطالب</label>
                <input type="email" name="email" id="quick_email" value="{{ old('email') }}" placeholder="student@example.com" class="{{ $fieldClass }}" dir="ltr">
            </div>
            <div>
                <label class="{{ $labelClass }}" for="quick_course">البرنامج</label>
                <select name="advanced_course_id" id="quick_course" class="{{ $fieldClass }}">
                    <option value="">اختر البرنامج</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(old('advanced_course_id') == $course->id)>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-check-circle text-xs"></i> تفعيل الآن
                </button>
            </div>
        </form>
    </article>

    <form method="GET" action="{{ route('admin.online-enrollments.index') }}" class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
        <div class="grid gap-3 md:grid-cols-4">
            <div class="md:col-span-1">
                <label class="{{ $labelClass }}" for="search">بحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="اسم، بريد، أو هاتف..." class="{{ $fieldClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}" for="status">الحالة</label>
                <select name="status" id="status" class="{{ $fieldClass }}">
                    <option value="">جميع الحالات</option>
                    <option value="pending" @selected(request('status') === 'pending')>في الانتظار</option>
                    <option value="active" @selected(request('status') === 'active')>نشط</option>
                    <option value="completed" @selected(request('status') === 'completed')>مكتمل</option>
                    <option value="suspended" @selected(request('status') === 'suspended')>معلّق</option>
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="course_id">البرنامج</label>
                <select name="course_id" id="course_id" class="{{ $fieldClass }}">
                    <option value="">جميع البرامج</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="btn-press inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-filter text-xs"></i> تطبيق
                </button>
                @if(request()->hasAny(['search', 'status', 'course_id']))
                    <a href="{{ route('admin.online-enrollments.index') }}" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-line text-muted hover:bg-accent-soft hover:text-accent" title="إعادة تعيين">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>

    <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
        <h3 class="text-sm font-semibold text-ink">بحث سريع بالهاتف</h3>
        <div class="mt-3 flex flex-col gap-2 sm:flex-row">
            <input type="text" id="quickSearchPhone" placeholder="رقم هاتف الطالب..." class="{{ $fieldClass }} sm:flex-1">
            <button type="button" onclick="quickSearchByPhone()" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-search text-xs"></i> بحث
            </button>
        </div>
        <div id="quickSearchResult" class="mt-3 hidden"></div>
    </article>

    @if($enrollments->count() > 0)
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] text-sm">
                    <thead>
                        <tr class="border-b border-line text-right text-xs font-medium text-muted">
                            <th class="px-4 py-3">الطالب</th>
                            <th class="px-4 py-3">البرنامج</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">التقدم</th>
                            <th class="px-4 py-3">التسجيل</th>
                            <th class="px-4 py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach($enrollments as $enrollment)
                            <tr class="hover:bg-[#f8faf9]">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent"><i class="fas fa-user text-xs"></i></div>
                                        <div>
                                            <div class="font-medium text-ink">{{ $enrollment->student->name ?? '—' }}</div>
                                            <div class="text-xs text-muted" dir="ltr">{{ $enrollment->student->phone ?? ($enrollment->student->email ?? '') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-ink">{{ $enrollment->course->title ?? '—' }}</div>
                                    <div class="text-xs text-muted">
                                        {{ $enrollment->course?->academicYear?->name }}
                                        @if($enrollment->course?->academicSubject?->name)
                                            · {{ $enrollment->course->academicSubject->name }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $badge = match($enrollment->status) {
                                            'active' => 'bg-emerald-50 text-emerald-700',
                                            'pending' => 'bg-amber-50 text-amber-800',
                                            'completed' => 'bg-accent-soft text-accent',
                                            'suspended' => 'bg-rose-50 text-rose-700',
                                            default => 'bg-[#f2f5f4] text-muted',
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-[11px] font-medium {{ $badge }}">{{ $enrollment->status_text }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 min-w-[100px]">
                                        <div class="h-1.5 flex-1 rounded-full bg-[#e8eeec]">
                                            <div class="h-1.5 rounded-full bg-accent" style="width: {{ min(100, (float) $enrollment->progress) }}%"></div>
                                        </div>
                                        <span class="tabular-nums text-xs text-muted">{{ number_format((float) $enrollment->progress, 0) }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs tabular-nums text-muted">
                                    {{ $enrollment->enrolled_at?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('admin.online-enrollments.show', $enrollment) }}" class="inline-flex size-8 items-center justify-center rounded-lg border border-line text-muted hover:bg-accent-soft hover:text-accent" title="عرض"><i class="fas fa-eye text-xs"></i></a>
                                        @if($enrollment->status === 'pending' || $enrollment->status === 'suspended')
                                            <form method="POST" action="{{ route('admin.online-enrollments.activate', $enrollment) }}" onsubmit="return confirm('تفعيل هذا التسجيل؟');">
                                                @csrf
                                                <button type="submit" class="inline-flex size-8 items-center justify-center rounded-lg border border-line text-emerald-700 hover:bg-emerald-50" title="تفعيل"><i class="fas fa-play text-xs"></i></button>
                                            </form>
                                        @elseif($enrollment->status === 'active')
                                            <form method="POST" action="{{ route('admin.online-enrollments.deactivate', $enrollment) }}" onsubmit="return confirm('إيقاف هذا التسجيل؟');">
                                                @csrf
                                                <button type="submit" class="inline-flex size-8 items-center justify-center rounded-lg border border-line text-amber-700 hover:bg-amber-50" title="إيقاف"><i class="fas fa-pause text-xs"></i></button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.online-enrollments.destroy', $enrollment) }}" onsubmit="return confirm('حذف هذا التسجيل؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex size-8 items-center justify-center rounded-lg border border-line text-rose-600 hover:bg-rose-50" title="حذف"><i class="fas fa-trash text-xs"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-line px-4 py-3">
                {{ $enrollments->appends(request()->query())->links() }}
            </div>
        </article>
    @else
        <article class="rounded-2xl border border-dashed border-line bg-surface px-6 py-14 text-center shadow-soft">
            <div class="mx-auto inline-flex size-14 items-center justify-center rounded-2xl bg-[#f2f5f4] text-accent"><i class="fas fa-user-graduate text-xl"></i></div>
            <h3 class="mt-4 text-lg font-semibold text-ink">لا توجد تسجيلات</h3>
            <p class="mt-1 text-sm text-muted">لم يُعثر على تسجيلات تطابق معايير البحث.</p>
            <a href="{{ route('admin.online-enrollments.create') }}" class="btn-press mt-5 inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">تسجيل طالب</a>
        </article>
    @endif
</div>

<script>
function quickSearchByPhone() {
    const phone = document.getElementById('quickSearchPhone').value.trim();
    const resultDiv = document.getElementById('quickSearchResult');
    if (!phone) { alert('يرجى إدخال رقم الهاتف'); return; }
    resultDiv.innerHTML = '<p class="text-sm text-muted">جاري البحث...</p>';
    resultDiv.classList.remove('hidden');
    fetch(`{{ route('admin.online-enrollments.search-by-phone') }}?phone=${encodeURIComponent(phone)}`, {
        headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
    })
    .then(r => r.json().then(d => ({ ok: r.ok, d })))
    .then(({ ok, d }) => {
        if (ok && d.success) {
            const s = d.student;
            resultDiv.innerHTML = `<div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-900">
                <p class="font-semibold">${s.name}</p>
                <p class="mt-1 text-xs" dir="ltr">${s.phone || ''}</p>
                <a class="btn-press mt-2 inline-flex h-8 items-center rounded-lg bg-accent px-3 text-xs font-medium text-white" href="{{ route('admin.online-enrollments.create') }}?student_id=${s.id}">تسجيل في برنامج</a>
            </div>`;
        } else {
            resultDiv.innerHTML = `<div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">${d.error || 'لم يُعثر على طالب'}</div>`;
        }
    })
    .catch(() => {
        resultDiv.innerHTML = '<div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">تعذّر البحث</div>';
    });
}
document.getElementById('quickSearchPhone')?.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') quickSearchByPhone();
});
</script>
@endsection
