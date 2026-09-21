@extends('layouts.admin')

@section('title', 'الاستفسارات - ' . config('app.name'))
@section('page_title', 'الاستفسارات')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">إدارة داخلية · حتى لو بدأت المحادثة من واتساب</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">الاستفسارات</h2>
        </div>
        <a href="{{ route('admin.inquiries.create') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
            <i class="fas fa-plus text-xs"></i> تسجيل استفسار (واتساب / يدوي)
        </a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-soft">{{ session('success') }}</div>
    @endif

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['إجمالي', $stats['total'], 'text-ink'],
            ['جديد', $stats['new'], 'text-amber-700'],
            ['قيد المعالجة', $stats['in_progress'], 'text-accent'],
            ['محلول', $stats['resolved'], 'text-emerald-700'],
        ] as [$label, $value, $tone])
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <p class="text-xs text-muted">{{ $label }}</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums {{ $tone }}">{{ number_format($value) }}</p>
            </article>
        @endforeach
    </section>

    <form method="GET" class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
        <div class="grid gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="{{ $labelClass }}" for="search">بحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="{{ $fieldClass }}" placeholder="اسم، هاتف، مرجع، موضوع…">
            </div>
            <div>
                <label class="{{ $labelClass }}" for="status">الحالة</label>
                <select name="status" id="status" class="{{ $fieldClass }}">
                    <option value="">الكل</option>
                    @foreach($statusLabels as $key => $label)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="type">النوع</label>
                <select name="type" id="type" class="{{ $fieldClass }}">
                    <option value="">الكل</option>
                    @foreach($typeLabels as $key => $label)
                        <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-3 flex flex-wrap gap-2">
            <select name="source" class="{{ $fieldClass }} max-w-xs">
                <option value="">كل المصادر</option>
                @foreach($sourceLabels as $key => $label)
                    <option value="{{ $key }}" @selected(request('source') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-press inline-flex h-11 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">تصفية</button>
        </div>
    </form>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[860px] text-sm">
                <thead>
                    <tr class="border-b border-line text-right text-xs font-medium text-muted">
                        <th class="px-4 py-3">المرجع</th>
                        <th class="px-4 py-3">الاسم / التواصل</th>
                        <th class="px-4 py-3">النوع</th>
                        <th class="px-4 py-3">المصدر</th>
                        <th class="px-4 py-3">التاريخ</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($inquiries as $inquiry)
                        <tr class="hover:bg-[#f8faf9]">
                            <td class="px-4 py-3 font-mono text-xs">{{ $inquiry->reference }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-ink">{{ $inquiry->name }}</div>
                                <div class="text-xs text-muted">{{ $inquiry->phone ?: $inquiry->email ?: '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $typeLabels[$inquiry->inquiry_type] ?? $inquiry->inquiry_type }}</td>
                            <td class="px-4 py-3 text-muted">{{ $sourceLabels[$inquiry->source] ?? $inquiry->source }}</td>
                            <td class="px-4 py-3 tabular-nums text-muted">{{ optional($inquiry->inquired_at)->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $tone = match($inquiry->status) {
                                        'resolved' => 'bg-emerald-50 text-emerald-700',
                                        'in_progress' => 'bg-accent-soft text-accent',
                                        default => 'bg-amber-50 text-amber-800',
                                    };
                                @endphp
                                <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium {{ $tone }}">{{ $statusLabels[$inquiry->status] ?? $inquiry->status }}</span>
                            </td>
                            <td class="px-4 py-3"><a href="{{ route('admin.inquiries.show', $inquiry) }}" class="text-xs font-semibold text-accent">فتح</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-muted">لا استفسارات بعد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inquiries->hasPages())
            <div class="border-t border-line px-4 py-3">{{ $inquiries->links() }}</div>
        @endif
    </article>
</div>
@endsection
