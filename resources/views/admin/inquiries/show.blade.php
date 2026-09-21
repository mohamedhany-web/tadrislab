@extends('layouts.admin')

@section('title', $inquiry->reference)
@section('page_title', 'تفاصيل الاستفسار')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs text-muted"><a href="{{ route('admin.inquiries.index') }}" class="hover:text-accent">الاستفسارات</a></p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $inquiry->reference }}</h2>
            <p class="mt-1 text-sm text-muted">{{ $inquiry->typeLabel() }} · {{ $inquiry->sourceLabel() }} · {{ optional($inquiry->inquired_at)->format('Y-m-d H:i') }}</p>
        </div>
        <form method="POST" action="{{ route('admin.inquiries.destroy', $inquiry) }}" onsubmit="return confirm('حذف الاستفسار؟')">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex h-9 items-center rounded-xl border border-rose-200 px-4 text-sm text-rose-700">حذف</button>
        </form>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm shadow-soft">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-disc space-y-1 pr-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.inquiries.update', $inquiry) }}" class="space-y-5">
        @csrf
        @method('PUT')
        <div class="grid gap-5 lg:grid-cols-3">
            <article class="space-y-4 rounded-2xl border border-line bg-surface p-5 shadow-soft lg:col-span-2">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="{{ $labelClass }}" for="name">الاسم *</label>
                        <input id="name" name="name" required value="{{ old('name', $inquiry->name) }}" class="{{ $fieldClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}" for="phone">الهاتف</label>
                        <input id="phone" name="phone" value="{{ old('phone', $inquiry->phone) }}" class="{{ $fieldClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}" for="email">البريد</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $inquiry->email) }}" class="{{ $fieldClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}" for="inquiry_type">النوع *</label>
                        <select id="inquiry_type" name="inquiry_type" required class="{{ $fieldClass }}">
                            @foreach($typeLabels as $key => $label)
                                <option value="{{ $key }}" @selected(old('inquiry_type', $inquiry->inquiry_type) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labelClass }}" for="status">الحالة *</label>
                        <select id="status" name="status" required class="{{ $fieldClass }}">
                            @foreach($statusLabels as $key => $label)
                                <option value="{{ $key }}" @selected(old('status', $inquiry->status) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labelClass }}" for="source">المصدر</label>
                        <select id="source" name="source" class="{{ $fieldClass }}">
                            @foreach($sourceLabels as $key => $label)
                                <option value="{{ $key }}" @selected(old('source', $inquiry->source) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="{{ $labelClass }}" for="subject">الموضوع</label>
                        <input id="subject" name="subject" value="{{ old('subject', $inquiry->subject) }}" class="{{ $fieldClass }}">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="{{ $labelClass }}" for="message">نص الاستفسار</label>
                        <textarea id="message" name="message" rows="4" class="{{ $areaClass }}">{{ old('message', $inquiry->message) }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="{{ $labelClass }}" for="admin_notes">ملاحظات الإدارة</label>
                        <textarea id="admin_notes" name="admin_notes" rows="4" class="{{ $areaClass }}">{{ old('admin_notes', $inquiry->admin_notes) }}</textarea>
                    </div>
                </div>
            </article>

            <article class="space-y-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
                <h3 class="text-sm font-semibold text-ink">الارتباطات</h3>
                <div>
                    <label class="{{ $labelClass }}" for="user_id">User ID</label>
                    <input id="user_id" name="user_id" type="number" value="{{ old('user_id', $inquiry->user_id) }}" class="{{ $fieldClass }}">
                    @if($inquiry->user)
                        <p class="mt-1 text-xs text-muted">{{ $inquiry->user->name }} — {{ $inquiry->user->email }}</p>
                    @endif
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="order_id">Order ID</label>
                    <input id="order_id" name="order_id" type="number" value="{{ old('order_id', $inquiry->order_id) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="consultation_request_id">Booking ID</label>
                    <input id="consultation_request_id" name="consultation_request_id" type="number" value="{{ old('consultation_request_id', $inquiry->consultation_request_id) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="institution_id">Institution</label>
                    <select id="institution_id" name="institution_id" class="{{ $fieldClass }}">
                        <option value="">—</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" @selected(old('institution_id', $inquiry->institution_id) == $inst->id)>{{ $inst->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="institution_program_id">برنامج الجهة</label>
                    <select id="institution_program_id" name="institution_program_id" class="{{ $fieldClass }}">
                        <option value="">—</option>
                        @foreach($institutionPrograms as $program)
                            <option value="{{ $program->id }}" @selected(old('institution_program_id', $inquiry->institution_program_id) == $program->id)>
                                #{{ $program->id }} — {{ $program->title_ar }}
                            </option>
                        @endforeach
                    </select>
                    @if($inquiry->institutionProgram)
                        <p class="mt-1 text-xs text-muted">{{ $inquiry->institutionProgram->title_ar }} · {{ $inquiry->institutionProgram->status }}</p>
                    @endif
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="assigned_to">مسؤول المتابعة</label>
                    <select id="assigned_to" name="assigned_to" class="{{ $fieldClass }}">
                        <option value="">—</option>
                        @foreach($staff as $member)
                            <option value="{{ $member->id }}" @selected(old('assigned_to', $inquiry->assigned_to) == $member->id)>{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="inquired_at">تاريخ الاستفسار</label>
                    <input id="inquired_at" name="inquired_at" type="datetime-local" value="{{ old('inquired_at', optional($inquiry->inquired_at)->format('Y-m-d\\TH:i')) }}" class="{{ $fieldClass }}">
                </div>
                @if($inquiry->resolved_at)
                    <p class="text-xs text-muted">تاريخ الحل: {{ $inquiry->resolved_at->format('Y-m-d H:i') }}</p>
                @endif
            </article>
        </div>
        <button type="submit" class="btn-press inline-flex h-11 items-center rounded-xl bg-accent px-6 text-sm font-medium text-white">حفظ التغييرات</button>
    </form>
</div>
@endsection
