@extends('layouts.admin')

@php
    $kindTitle = match ($filters['kind'] ?? null) {
        'training' => 'برامج التدريب',
        'development' => 'التطوير المؤسسي',
        default => 'برامج المؤسسات',
    };
@endphp
@section('title', $kindTitle)
@section('page_title', $kindTitle)

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold text-ink">{{ $kindTitle }}</h2>
            <p class="mt-1 text-sm text-muted">Inquiry → Proposal → Approved → Scheduled → In Progress → Completed</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.institutions.index') }}" class="btn-press inline-flex h-10 items-center rounded-xl border border-line px-4 text-sm">الجهات</a>
            <a href="{{ route('admin.institution-programs.create', array_filter(['program_kind' => $filters['kind'] ?? null])) }}" class="btn-press inline-flex h-10 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">إضافة</a>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-6">
        @foreach($stats as $key => $count)
            <a href="{{ route('admin.institution-programs.index', ['status' => $key]) }}" class="rounded-xl border border-line bg-surface p-3">
                <p class="text-[11px] text-muted">{{ $statuses[$key] ?? $key }}</p>
                <p class="text-xl font-bold text-ink">{{ $count }}</p>
            </a>
        @endforeach
    </div>

    <form method="GET" class="flex flex-wrap gap-2 rounded-2xl border border-line bg-surface p-4">
        <select name="status" class="h-10 rounded-xl border border-line px-3 text-sm">
            <option value="">كل الحالات</option>
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="service_key" class="h-10 rounded-xl border border-line px-3 text-sm">
            <option value="">كل الخدمات</option>
            @foreach($serviceLabels as $key => $label)
                <option value="{{ $key }}" @selected(($filters['service_key'] ?? '') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="kind" class="h-10 rounded-xl border border-line px-3 text-sm">
            <option value="">تدريب + تطوير</option>
            <option value="training" @selected(($filters['kind'] ?? '') === 'training')>تدريب</option>
            <option value="development" @selected(($filters['kind'] ?? '') === 'development')>تطوير مؤسسي</option>
        </select>
        <button class="h-10 rounded-xl bg-ink px-4 text-sm font-medium text-white">تصفية</button>
    </form>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-canvas text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start">البرنامج</th>
                        <th class="px-4 py-3 text-start">الجهة</th>
                        <th class="px-4 py-3 text-start">الخدمة</th>
                        <th class="px-4 py-3 text-start">الحالة</th>
                        <th class="px-4 py-3 text-start">مشاركون</th>
                        <th class="px-4 py-3 text-start">التقدّم</th>
                        <th class="px-4 py-3 text-start"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($programs as $program)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.institution-programs.show', $program) }}" class="font-semibold text-ink hover:text-accent">{{ $program->title_ar }}</a>
                                <p class="text-xs text-muted">{{ $program->kindLabel() }}</p>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $program->institution?->name_ar ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-muted">{{ $program->serviceLabel() }}</td>
                            <td class="px-4 py-3"><span class="rounded-md bg-canvas px-2 py-1 text-xs">{{ $program->statusLabel() }}</span></td>
                            <td class="px-4 py-3">{{ $program->planned_participants ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $program->progress_percent }}%</td>
                            <td class="px-4 py-3"><a href="{{ route('admin.institution-programs.show', $program) }}" class="text-xs font-semibold text-accent">إدارة</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-muted">لا برامج بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-line px-4 py-3">{{ $programs->links() }}</div>
    </article>
</div>
@endsection
