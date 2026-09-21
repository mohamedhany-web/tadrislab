@extends('layouts.admin')

@section('title', 'تعديل جهة')
@section('page_title', 'تعديل جهة')

@section('content')
@php
    $field = 'h-10 w-full rounded-xl border border-line bg-surface px-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $area = 'w-full rounded-xl border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="mx-auto max-w-3xl space-y-5">
    <p class="text-xs text-muted"><a href="{{ route('admin.institutions.show', $institution) }}" class="hover:text-accent">{{ $institution->name_ar }}</a></p>
    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.institutions.update', $institution) }}" class="space-y-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
        @csrf @method('PUT')
        @include('admin.institutions._form', ['institution' => $institution, 'field' => $field, 'area' => $area, 'orgTypes' => $orgTypes])
        <button type="submit" class="btn-press inline-flex h-10 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white">حفظ</button>
    </form>
</div>
@endsection
