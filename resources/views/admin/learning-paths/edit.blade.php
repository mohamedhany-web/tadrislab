@extends('layouts.admin')

@section('title', 'تعديل مسار')
@section('page_title', 'تعديل مسار')

@section('content')
@php
    $field = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $area = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="mx-auto max-w-3xl space-y-5">
    <div>
        <p class="text-xs text-muted"><a href="{{ route('admin.learning-paths.show', $path) }}" class="hover:text-accent">{{ $path->title_ar }}</a></p>
        <h2 class="mt-1 text-2xl font-semibold text-ink">تعديل بيانات المسار</h2>
    </div>

    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.learning-paths.update', $path) }}" class="space-y-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
        @csrf
        @method('PUT')
        @include('admin.learning-paths._form', ['path' => $path, 'instructors' => $instructors, 'packages' => $packages ?? collect(), 'field' => $field, 'area' => $area])
        <button type="submit" class="btn-press inline-flex h-11 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white">حفظ التعديلات</button>
    </form>
</div>
@endsection
