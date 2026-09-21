@extends('layouts.admin')

@section('title', 'المدارس والمؤسسات')
@section('page_title', 'المدارس والمؤسسات')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-ink">المدارس والمؤسسات</h2>
            <p class="mt-1 text-sm text-muted">محور واحد للجهات التعليمية — حساب الجهة ← منسق ← مشاركون ← برامج.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.institution-programs.index') }}" class="btn-press inline-flex h-10 items-center rounded-xl border border-line px-4 text-sm text-ink-soft">البرامج والمشاريع</a>
            <a href="{{ route('admin.institutions.create') }}" class="btn-press inline-flex h-10 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">جهة جديدة</a>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft">{{ session('success') }}</div>
    @endif

    <form method="GET" class="flex flex-wrap gap-2 rounded-2xl border border-line bg-surface p-4">
        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="بحث بالاسم / البريد / المدينة" class="h-10 min-w-[14rem] flex-1 rounded-xl border border-line px-3 text-sm">
        <select name="org_type" class="h-10 rounded-xl border border-line px-3 text-sm">
            <option value="">كل الأنواع</option>
            @foreach($orgTypes as $key => $label)
                <option value="{{ $key }}" @selected(($filters['org_type'] ?? '') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn-press h-10 rounded-xl bg-ink px-4 text-sm font-medium text-white">تصفية</button>
    </form>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-canvas text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start">الجهة</th>
                        <th class="px-4 py-3 text-start">النوع</th>
                        <th class="px-4 py-3 text-start">التواصل</th>
                        <th class="px-4 py-3 text-start">أعضاء</th>
                        <th class="px-4 py-3 text-start">برامج</th>
                        <th class="px-4 py-3 text-start">الحالة</th>
                        <th class="px-4 py-3 text-start"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($institutions as $org)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.institutions.show', $org) }}" class="font-semibold text-ink hover:text-accent">{{ $org->name_ar }}</a>
                                <p class="text-xs text-muted">{{ $org->city }} {{ $org->country }}</p>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $orgTypes[$org->org_type] ?? $org->org_type }}</td>
                            <td class="px-4 py-3 text-xs text-muted">{{ $org->contact_name }}<br>{{ $org->contact_email }}</td>
                            <td class="px-4 py-3">{{ $org->members_count }}</td>
                            <td class="px-4 py-3">{{ $org->programs_count }}</td>
                            <td class="px-4 py-3">
                                @if($org->is_active)<span class="text-xs font-semibold text-emerald-700">نشط</span>
                                @else<span class="text-xs font-semibold text-rose-700">موقوف</span>@endif
                            </td>
                            <td class="px-4 py-3"><a href="{{ route('admin.institutions.show', $org) }}" class="text-xs font-semibold text-accent">إدارة</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-muted">لا جهات بعد — أنشئ حساب مدرسة أو مؤسسة.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line px-4 py-3">{{ $institutions->links() }}</div>
    </article>
</div>
@endsection
