@extends('layouts.admin')

@section('title', 'خدمات الاستشارات')
@section('page_title', 'خدمات الاستشارات')

@section('content')
@php
    $field = 'h-10 w-full rounded-xl border border-line bg-surface px-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-muted"><a href="{{ route('admin.consultations.index') }}" class="hover:text-accent">الاستشارات</a></p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">كتالوج الخدمات الاستشارية</h2>
            <p class="mt-1 text-sm text-muted">قسم مستقل عن المسارات — المدة والسعر والنوع تُدار من هنا.</p>
        </div>
        <a href="{{ route('admin.consultations.services.create') }}" class="btn-press inline-flex h-10 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">خدمة جديدة</a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft">{{ session('success') }}</div>
    @endif

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-canvas text-muted">
                    <tr>
                        <th class="px-4 py-3 text-start font-semibold">الخدمة</th>
                        <th class="px-4 py-3 text-start font-semibold">النوع</th>
                        <th class="px-4 py-3 text-start font-semibold">المدة</th>
                        <th class="px-4 py-3 text-start font-semibold">السعر</th>
                        <th class="px-4 py-3 text-start font-semibold">الحالة</th>
                        <th class="px-4 py-3 text-start font-semibold"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($services as $service)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-ink">{{ $service->title_ar }}</p>
                                <p class="text-xs text-muted">{{ $service->slug }}</p>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $typeLabels[$service->consultation_type] ?? $service->consultation_type }}</td>
                            <td class="px-4 py-3">{{ $service->duration_minutes }} د</td>
                            <td class="px-4 py-3">{{ number_format((float) $service->price, 2) }} {{ $service->currency }}</td>
                            <td class="px-4 py-3 text-xs">
                                @if(!$service->is_mvp)<span class="text-amber-700">مستقبلاً</span>
                                @elseif($service->is_published && $service->is_active)<span class="text-emerald-700 font-semibold">منشور</span>
                                @elseif($service->is_active)<span class="text-amber-700">مسودة</span>
                                @else<span class="text-rose-700">موقوف</span>@endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.consultations.services.edit', $service) }}" class="text-xs font-semibold text-accent">تعديل</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-muted">لا خدمات — أنشئ أول خدمة أو شغّل Seeder.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>
</div>
@endsection
