@extends('layouts.student-timeline')

@section('title', 'أدواتي ومواردي')

@section('content')
<div class="space-y-5">
    <section>
        <h2 class="text-xl font-semibold text-ink">الأدوات والموارد المتاحة لك</h2>
        <p class="mt-1 text-sm text-muted">من الباقات والمسارات والموارد المجانية المنشورة.</p>
    </section>

    @if($tools->isEmpty())
        <div class="rounded-2xl border border-line bg-surface p-8 text-center text-sm text-muted">
            لا أدوات متاحة بعد.
            <div class="mt-4"><a href="{{ route('public.tools.index') }}" class="text-accent font-semibold">استكشف الكتالوج</a></div>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($tools as $tool)
                <a href="{{ route('public.tools.show', $tool->slug) }}" class="rounded-2xl border border-line bg-surface p-4 shadow-soft hover:border-accent/40">
                    <p class="text-xs text-muted">{{ $typeLabels[$tool->tool_type] ?? $tool->tool_type }}</p>
                    <h3 class="mt-1 font-semibold text-ink">{{ $tool->title_ar }}</h3>
                    @if($tool->summary_ar)
                        <p class="mt-2 text-sm text-muted line-clamp-2">{{ $tool->summary_ar }}</p>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
