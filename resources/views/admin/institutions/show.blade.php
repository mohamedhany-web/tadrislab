@extends('layouts.admin')

@section('title', $institution->name_ar)
@section('page_title', $institution->name_ar)

@section('content')
@php
    $field = 'h-10 w-full rounded-xl border border-line bg-surface px-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-muted"><a href="{{ route('admin.institutions.index') }}" class="hover:text-accent">الجهات</a></p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $institution->name_ar }}</h2>
            <p class="mt-1 text-sm text-muted">{{ $orgTypes[$institution->org_type] ?? '' }} · {{ $institution->engagementModeLabel() }} · {{ $institution->city }} {{ $institution->country }}</p>
            @if($institution->seat_limit)
                <p class="mt-1 text-xs text-muted">حد مقاعد المنصة: {{ $institution->seat_limit }}</p>
            @endif
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.institution-programs.create', ['institution_id' => $institution->id]) }}" class="btn-press inline-flex h-9 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">برنامج جديد</a>
            <a href="{{ route('admin.institutions.edit', $institution) }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm">تعديل</a>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-2">
        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <h3 class="font-semibold text-ink">بيانات التواصل</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div><dt class="text-muted">المسؤول</dt><dd class="font-medium">{{ $institution->contact_name ?: '—' }}</dd></div>
                <div><dt class="text-muted">البريد</dt><dd>{{ $institution->contact_email ?: '—' }}</dd></div>
                <div><dt class="text-muted">الهاتف</dt><dd>{{ $institution->contact_phone ?: '—' }}</dd></div>
            </dl>
        </article>

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <h3 class="mb-3 font-semibold text-ink">إضافة منسق / مشارك</h3>
            <form method="POST" action="{{ route('admin.institutions.members.store', $institution) }}" class="grid gap-2 sm:grid-cols-2">
                @csrf
                <select name="member_role" class="{{ $field }}">
                    <option value="coordinator">منسق / مسؤول</option>
                    <option value="participant">معلم / مشارك</option>
                </select>
                <select name="user_id" class="{{ $field }}">
                    <option value="">— ربط مستخدم (اختياري) —</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
                <input type="text" name="name" placeholder="الاسم" class="{{ $field }}">
                <input type="email" name="email" placeholder="البريد" class="{{ $field }}">
                <input type="text" name="phone" placeholder="الهاتف" class="{{ $field }}">
                <input type="text" name="title" placeholder="المسمى الوظيفي" class="{{ $field }}">
                <div class="sm:col-span-2">
                    <button type="submit" class="btn-press inline-flex h-9 items-center rounded-lg bg-accent px-4 text-xs font-medium text-white">إضافة</button>
                </div>
            </form>
        </article>
    </div>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-3"><h3 class="font-semibold text-ink">الأعضاء ({{ $institution->members->count() }})</h3></div>
        <ul class="divide-y divide-line">
            @forelse($institution->members as $member)
                <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 text-sm">
                    <div>
                        <p class="font-medium text-ink">{{ $member->displayName() }} <span class="text-xs text-muted">· {{ $member->roleLabel() }}</span></p>
                        <p class="text-xs text-muted">{{ $member->email }} {{ $member->phone }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.institutions.members.destroy', [$institution, $member]) }}" onsubmit="return confirm('حذف العضو؟');">
                        @csrf @method('DELETE')
                        <button class="text-xs font-semibold text-rose-600">حذف</button>
                    </form>
                </li>
            @empty
                <li class="px-4 py-8 text-center text-sm text-muted">لا أعضاء بعد.</li>
            @endforelse
        </ul>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex items-center justify-between border-b border-line px-4 py-3">
            <h3 class="font-semibold text-ink">البرامج والمشاريع</h3>
            <a href="{{ route('admin.institution-programs.create', ['institution_id' => $institution->id]) }}" class="text-xs font-semibold text-accent">إضافة</a>
        </div>
        <ul class="divide-y divide-line">
            @forelse($institution->programs as $program)
                <li class="flex flex-wrap items-center justify-between gap-2 px-4 py-3 text-sm">
                    <div>
                        <a href="{{ route('admin.institution-programs.show', $program) }}" class="font-medium text-ink hover:text-accent">{{ $program->title_ar }}</a>
                        <p class="text-xs text-muted">{{ $program->serviceLabel() }} · {{ $program->statusLabel() }} · {{ $program->kindLabel() }}</p>
                    </div>
                    <span class="text-xs text-muted">{{ $program->progress_percent }}%</span>
                </li>
            @empty
                <li class="px-4 py-8 text-center text-sm text-muted">لا برامج بعد.</li>
            @endforelse
        </ul>
    </article>
</div>
@endsection
