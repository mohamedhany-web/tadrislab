@extends('layouts.admin')

@section('title', 'إدارة محتوى المسارات')
@section('page_title', 'إدارة محتوى المسارات')

@section('content')
@php
    $field = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight text-ink">إدارة محتوى المسارات</h2>
            <p class="mt-1 text-sm text-muted">نظام محتوى تدريس لاب: مسار → وحدات → دروس → تطبيقات وأدوات صفية. ابنِ المحتوى من هنا دون كورسات لغة منفصلة.</p>
        </div>
        <a href="{{ route('admin.learning-paths.create') }}" class="btn-press inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
            <i class="fas fa-plus text-xs"></i> مسار جديد
        </a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft">{{ session('success') }}</div>
    @endif

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-canvas text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start font-semibold">المسار</th>
                        <th class="px-4 py-3 text-start font-semibold">المهارة</th>
                        <th class="px-4 py-3 text-start font-semibold">وحدات</th>
                        <th class="px-4 py-3 text-start font-semibold">دروس</th>
                        <th class="px-4 py-3 text-start font-semibold">تطبيقات</th>
                        <th class="px-4 py-3 text-start font-semibold">باقات</th>
                        <th class="px-4 py-3 text-start font-semibold">بيع</th>
                        <th class="px-4 py-3 text-start font-semibold">الحالة</th>
                        <th class="px-4 py-3 text-start font-semibold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($paths as $path)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.learning-paths.show', $path) }}" class="font-semibold text-ink hover:text-accent">{{ $path->title_ar }}</a>
                                <p class="text-xs text-muted">{{ $path->slug }}</p>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $path->skill_focus_ar ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $path->units_count }}</td>
                            <td class="px-4 py-3">{{ $path->lessons_count }}</td>
                            <td class="px-4 py-3">{{ $path->practices_count }}</td>
                            <td class="px-4 py-3">{{ $path->packages_count }}</td>
                            <td class="px-4 py-3 text-xs text-muted">
                                @if($path->is_sellable_standalone)
                                    منفرد
                                    @if($path->price !== null)
                                        · {{ number_format((float) $path->price, 0) }} {{ $path->currency ?: 'QAR' }}
                                    @endif
                                @else
                                    باقة فقط
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($path->is_published && $path->is_active)
                                    <span class="text-xs font-semibold text-emerald-700">منشور</span>
                                @elseif($path->is_active)
                                    <span class="text-xs font-semibold text-amber-700">مسودة نشطة</span>
                                @else
                                    <span class="text-xs font-semibold text-rose-700">موقوف</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.learning-paths.show', $path) }}" class="text-xs font-semibold text-accent">بناء المحتوى</a>
                                    <a href="{{ route('admin.learning-paths.edit', $path) }}" class="text-xs font-semibold text-ink-soft">تعديل</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-muted">لا مسارات بعد — أنشئ أول مسار تطوير مهني.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</div>
@endsection
