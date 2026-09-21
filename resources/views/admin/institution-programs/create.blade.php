@extends('layouts.admin')

@section('title', 'برنامج جديد')
@section('page_title', 'برنامج / مشروع جديد')

@section('content')
@php
    $field = 'h-10 w-full rounded-xl border border-line bg-surface px-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $area = 'w-full rounded-xl border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="mx-auto max-w-3xl space-y-5">
    <p class="text-xs text-muted"><a href="{{ route('admin.institution-programs.index') }}" class="hover:text-accent">البرامج</a></p>
    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.institution-programs.store') }}" class="space-y-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
        @csrf
        @include('admin.institution-programs._form')
        <button type="submit" class="btn-press inline-flex h-10 items-center rounded-xl bg-accent px-5 text-sm font-medium text-white">إنشاء</button>
    </form>
</div>
@endsection
