@extends('layouts.admin')

@section('title', 'تعديل خدمة استشارية')
@section('page_title', 'تعديل خدمة استشارية')

@section('content')
@php
    $field = 'h-10 w-full rounded-xl border border-line bg-surface px-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $area = 'w-full rounded-xl border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $s = $service;
@endphp
<div class="mx-auto max-w-3xl space-y-5">
    <p class="text-xs text-muted"><a href="{{ route('admin.consultations.services.index') }}" class="hover:text-accent">الخدمات</a></p>
    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.consultations.services.update', $service) }}" class="space-y-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
        @csrf @method('PUT')
        @include('admin.consultations.services._form', ['s' => $s, 'field' => $field, 'area' => $area, 'typeLabels' => $typeLabels, 'instructors' => $instructors])
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn-press inline-flex h-10 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white">حفظ</button>
        </div>
    </form>
    <form method="POST" action="{{ route('admin.consultations.services.destroy', $service) }}" onsubmit="return confirm('حذف أو إيقاف الخدمة؟');">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm font-semibold text-rose-600">حذف / إيقاف</button>
    </form>
</div>
@endsection
