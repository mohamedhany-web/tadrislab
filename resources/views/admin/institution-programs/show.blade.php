@extends('layouts.admin')

@section('title', $program->title_ar)
@section('page_title', $program->title_ar)

@section('content')
@php
    $field = 'h-10 w-full rounded-xl border border-line bg-surface px-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-muted"><a href="{{ route('admin.institution-programs.index') }}" class="hover:text-accent">البرامج</a>
                @if($program->institution)
                    · <a href="{{ route('admin.institutions.show', $program->institution) }}" class="hover:text-accent">{{ $program->institution->name_ar }}</a>
                @endif
            </p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $program->title_ar }}</h2>
            <p class="mt-1 text-sm text-muted">{{ $program->serviceLabel() }} · {{ $program->kindLabel() }} · {{ $program->statusLabel() }}</p>
        </div>
        <a href="{{ route('admin.institution-programs.edit', $program) }}" class="btn-press inline-flex h-9 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">تعديل التفاصيل</a>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-3">
        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft lg:col-span-2">
            <h3 class="font-semibold text-ink">ملخص التنفيذ</h3>
            <dl class="mt-3 grid gap-3 sm:grid-cols-2 text-sm">
                <div><dt class="text-muted">المشاركون المخططون</dt><dd class="font-medium">{{ $program->planned_participants ?: '—' }}</dd></div>
                <div><dt class="text-muted">المدة</dt><dd class="font-medium">{{ $program->duration_hours ? $program->duration_hours.' ساعة' : '—' }}</dd></div>
                <div><dt class="text-muted">التنفيذ</dt><dd class="font-medium">{{ $deliveryModes[$program->delivery_mode] ?? '—' }}</dd></div>
                <div><dt class="text-muted">التسعير</dt><dd class="font-medium">@if($program->price !== null){{ number_format((float)$program->price, 2) }} {{ $program->currency }}@else حسب الاتفاق @endif</dd></div>
                <div><dt class="text-muted">المدرب</dt><dd class="font-medium">{{ $program->instructor?->name ?? '—' }}</dd></div>
                <div><dt class="text-muted">التقدّم</dt><dd class="font-medium">{{ $program->progress_percent }}%</dd></div>
            </dl>
            @if($program->inquiry_notes)<p class="mt-4 text-sm"><span class="text-muted">Inquiry:</span> {{ $program->inquiry_notes }}</p>@endif
            @if($program->proposal_notes)<p class="mt-2 text-sm"><span class="text-muted">Proposal:</span> {{ $program->proposal_notes }}</p>@endif
            @if($program->diagnosis_notes)<p class="mt-2 text-sm"><span class="text-muted">تشخيص:</span> {{ $program->diagnosis_notes }}</p>@endif
            @if($program->improvement_plan)<p class="mt-2 text-sm"><span class="text-muted">خطة تحسين:</span> {{ $program->improvement_plan }}</p>@endif
            @if($program->result_notes)<p class="mt-2 text-sm"><span class="text-muted">نتيجة:</span> {{ $program->result_notes }}</p>@endif
        </article>

        <article class="rounded-2xl border border-accent/20 bg-accent-soft/20 p-5 shadow-soft">
            <h3 class="font-semibold text-ink">تحديث الحالة</h3>
            <form method="POST" action="{{ route('admin.institution-programs.status', $program) }}" class="mt-3 space-y-3">
                @csrf
                <select name="status" class="{{ $field }}">
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" @selected($program->status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-press inline-flex h-9 w-full items-center justify-center rounded-xl bg-accent text-sm font-medium text-white">حفظ الحالة</button>
            </form>
            <p class="mt-3 text-[11px] text-muted">التطوير المؤسسي يبدأ غالبًا بـ Inquiry ثم Proposal دون Checkout مباشر.</p>
        </article>
    </div>

    <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
        <h3 class="mb-3 font-semibold text-ink">إضافة مشارك</h3>
        <form method="POST" action="{{ route('admin.institution-programs.participants.store', $program) }}" class="grid gap-2 sm:grid-cols-12 items-end">
            @csrf
            @if($members->isNotEmpty())
            <div class="sm:col-span-4">
                <label class="mb-1 block text-xs text-muted">من أعضاء الجهة</label>
                <select name="institution_member_id" class="{{ $field }}">
                    <option value="">—</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}">{{ $m->displayName() }} ({{ $m->roleLabel() }})</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="sm:col-span-3">
                <label class="mb-1 block text-xs text-muted">أو اسم</label>
                <input type="text" name="name" class="{{ $field }}" placeholder="اسم المشارك">
            </div>
            <div class="sm:col-span-3">
                <label class="mb-1 block text-xs text-muted">بريد</label>
                <input type="email" name="email" class="{{ $field }}">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="btn-press inline-flex h-10 w-full items-center justify-center rounded-xl bg-accent text-sm font-medium text-white">إضافة</button>
            </div>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-3"><h3 class="font-semibold text-ink">المشاركون ({{ $program->participants->count() }})</h3></div>
        <ul class="divide-y divide-line">
            @forelse($program->participants as $participant)
                <li class="px-4 py-3">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-medium text-ink">{{ $participant->name }}</p>
                            <p class="text-xs text-muted">{{ $participant->email }} · {{ $participant->statusLabel() }} · {{ $participant->progress_percent }}%</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('admin.institution-programs.participants.update', $participant) }}" class="flex flex-wrap items-center gap-2">
                                @csrf @method('PUT')
                                <select name="status" class="h-8 rounded-lg border border-line px-2 text-xs">
                                    @foreach($participantStatuses as $k => $lab)
                                        <option value="{{ $k }}" @selected($participant->status === $k)>{{ $lab }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="progress_percent" min="0" max="100" value="{{ $participant->progress_percent }}" class="h-8 w-16 rounded-lg border border-line px-2 text-xs">
                                <button class="text-xs font-semibold text-accent">حفظ</button>
                            </form>
                            <form method="POST" action="{{ route('admin.institution-programs.participants.destroy', $participant) }}" onsubmit="return confirm('حذف؟');">
                                @csrf @method('DELETE')
                                <button class="text-xs font-semibold text-rose-600">حذف</button>
                            </form>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-8 text-center text-sm text-muted">لا مشاركين بعد — أضفهم بعد الموافقة والجدولة.</li>
            @endforelse
        </ul>
    </article>
</div>
@endsection
