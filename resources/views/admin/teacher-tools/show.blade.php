@extends('layouts.admin')

@section('title', $tool->title_ar)
@section('page_title', 'تفاصيل الأداة')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-muted"><a href="{{ route('admin.teacher-tools.index') }}" class="hover:text-accent">الأدوات والموارد</a></p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $tool->title_ar }}</h2>
            <p class="mt-1 text-sm text-muted">{{ $typeLabels[$tool->tool_type] ?? $tool->tool_type }} · {{ $accessLabels[$tool->access_mode] ?? $tool->access_mode }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.teacher-tools.edit', $tool) }}" class="btn-press inline-flex h-9 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">تعديل</a>
            <form method="POST" action="{{ route('admin.teacher-tools.destroy', $tool) }}" onsubmit="return confirm('حذف هذا المورد؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex h-9 items-center rounded-xl border border-rose-200 px-4 text-sm text-rose-700">حذف</button>
            </form>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm shadow-soft">{{ session('success') }}</div>
    @endif

    <div class="grid gap-5 lg:grid-cols-3">
        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft lg:col-span-2">
            <h3 class="text-sm font-semibold text-ink">الوصف</h3>
            <p class="mt-2 whitespace-pre-line text-sm text-muted">{{ $tool->description_ar ?: ($tool->summary_ar ?: '—') }}</p>
            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <div class="rounded-xl border border-line px-3 py-2"><p class="text-xs text-muted">Slug</p><p class="mt-1 text-sm font-medium">{{ $tool->slug }}</p></div>
                <div class="rounded-xl border border-line px-3 py-2"><p class="text-xs text-muted">ملف</p><p class="mt-1 text-sm font-medium">{{ $tool->file_name ?: '—' }}</p></div>
                <div class="rounded-xl border border-line px-3 py-2"><p class="text-xs text-muted">رابط</p><p class="mt-1 truncate text-sm font-medium">{{ $tool->external_url ?: '—' }}</p></div>
            </div>
        </article>
        <div class="space-y-5">
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <h3 class="text-sm font-semibold">المسارات ({{ $tool->learningPaths->count() }})</h3>
                <ul class="mt-3 space-y-2 text-sm">
                    @forelse($tool->learningPaths as $path)
                        <li><a class="text-accent" href="{{ route('admin.learning-paths.show', $path) }}">{{ $path->title_ar }}</a></li>
                    @empty
                        <li class="text-muted">غير مربوط بمسار</li>
                    @endforelse
                </ul>
            </article>
            <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <h3 class="text-sm font-semibold">الباقات ({{ $tool->packages->count() }})</h3>
                <ul class="mt-3 space-y-2 text-sm">
                    @forelse($tool->packages as $package)
                        <li><a class="text-accent" href="{{ route('admin.packages.show', $package) }}">{{ $package->name }}</a></li>
                    @empty
                        <li class="text-muted">غير مربوط بباقة</li>
                    @endforelse
                </ul>
            </article>
        </div>
    </div>
</div>
@endsection
