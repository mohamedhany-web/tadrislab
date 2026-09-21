@extends('layouts.admin')

@section('title', 'الأدوات والموارد - ' . config('app.name'))
@section('page_title', 'الأدوات والموارد')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">ركيزة تدريس لاب · قابلة للإدارة</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">الأدوات والموارد</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">نماذج، قوائم تحقق، أدوات صفية، تخطيط، تقويم، وموارد قابلة للتحميل — ليست ملفات ثابتة في الكود.</p>
        </div>
        <a href="{{ route('admin.teacher-tools.create') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
            <i class="fas fa-plus text-xs"></i> أداة / مورد جديد
        </a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-soft">{{ session('success') }}</div>
    @endif

    <form method="GET" class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
        <div class="grid gap-3 md:grid-cols-4">
            <div class="md:col-span-2">
                <label class="{{ $labelClass }}" for="search">بحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="{{ $fieldClass }}" placeholder="عنوان أو slug…">
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
            <div>
                <label class="{{ $labelClass }}" for="status">الحالة</label>
                <select name="status" id="status" class="{{ $fieldClass }}">
                    <option value="">الكل</option>
                    <option value="published" @selected(request('status') === 'published')>منشور</option>
                    <option value="active" @selected(request('status') === 'active')>نشط</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>معطّل</option>
                </select>
            </div>
        </div>
        <div class="mt-3"><button type="submit" class="btn-press inline-flex h-10 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">تصفية</button></div>
    </form>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-sm">
                <thead>
                    <tr class="border-b border-line text-right text-xs font-medium text-muted">
                        <th class="px-4 py-3">العنوان</th>
                        <th class="px-4 py-3">النوع</th>
                        <th class="px-4 py-3">الوصول</th>
                        <th class="px-4 py-3">مسارات</th>
                        <th class="px-4 py-3">باقات</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($tools as $tool)
                        <tr class="hover:bg-[#f8faf9]">
                            <td class="px-4 py-3">
                                <div class="font-medium text-ink">{{ $tool->title_ar }}</div>
                                <div class="text-xs text-muted">{{ $tool->slug }}</div>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $typeLabels[$tool->tool_type] ?? $tool->tool_type }}</td>
                            <td class="px-4 py-3 text-muted">{{ $tool->access_mode }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ $tool->learning_paths_count }}</td>
                            <td class="px-4 py-3 tabular-nums">{{ $tool->packages_count }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium {{ $tool->is_published ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $tool->is_published ? 'منشور' : 'مسودة' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.teacher-tools.show', $tool) }}" class="text-xs font-semibold text-accent">عرض</a>
                                    <a href="{{ route('admin.teacher-tools.edit', $tool) }}" class="text-xs font-semibold text-ink-soft">تعديل</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-10 text-center text-muted">لا أدوات بعد — أنشئ أول مورد من الزر أعلاه.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tools->hasPages())
            <div class="border-t border-line px-4 py-3">{{ $tools->links() }}</div>
        @endif
    </article>
</div>
@endsection
