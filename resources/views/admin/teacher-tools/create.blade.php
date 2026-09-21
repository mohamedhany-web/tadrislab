@extends('layouts.admin')

@section('title', 'أداة جديدة - ' . config('app.name'))
@section('page_title', 'أداة / مورد جديد')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-muted"><a href="{{ route('admin.teacher-tools.index') }}" class="hover:text-accent">الأدوات والموارد</a></p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">إضافة أداة أو مورد</h2>
        </div>
    </section>

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-disc space-y-1 pr-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.teacher-tools.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @include('admin.teacher-tools._form', ['tool' => null])
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn-press inline-flex h-11 items-center rounded-xl bg-accent px-6 text-sm font-medium text-white">حفظ</button>
            <a href="{{ route('admin.teacher-tools.index') }}" class="inline-flex h-11 items-center rounded-xl border border-line px-5 text-sm">إلغاء</a>
        </div>
    </form>
</div>
@endsection
