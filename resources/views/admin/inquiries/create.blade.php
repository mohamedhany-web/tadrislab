@extends('layouts.admin')

@section('title', 'تسجيل استفسار')
@section('page_title', 'تسجيل استفسار')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section>
        <p class="text-xs text-muted"><a href="{{ route('admin.inquiries.index') }}" class="hover:text-accent">الاستفسارات</a></p>
        <h2 class="mt-1 text-2xl font-semibold text-ink">تسجيل استفسار داخلي</h2>
        <p class="mt-1 text-sm text-muted">استخدم المصدر «واتساب» عندما تبدأ المحادثة خارج المنصة.</p>
    </section>

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-disc space-y-1 pr-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.inquiries.store') }}" class="space-y-5">
        @csrf
        <article class="grid gap-4 rounded-2xl border border-line bg-surface p-5 shadow-soft lg:grid-cols-2">
            <div>
                <label class="{{ $labelClass }}" for="name">الاسم *</label>
                <input id="name" name="name" required value="{{ old('name') }}" class="{{ $fieldClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}" for="phone">الهاتف</label>
                <input id="phone" name="phone" value="{{ old('phone') }}" class="{{ $fieldClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}" for="email">البريد</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="{{ $fieldClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}" for="inquiry_type">نوع الاستفسار *</label>
                <select id="inquiry_type" name="inquiry_type" required class="{{ $fieldClass }}">
                    @foreach($typeLabels as $key => $label)
                        <option value="{{ $key }}" @selected(old('inquiry_type', 'general') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="source">المصدر *</label>
                <select id="source" name="source" required class="{{ $fieldClass }}">
                    @foreach($sourceLabels as $key => $label)
                        <option value="{{ $key }}" @selected(old('source', 'whatsapp') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="inquired_at">تاريخ الاستفسار</label>
                <input id="inquired_at" name="inquired_at" type="datetime-local" value="{{ old('inquired_at', now()->format('Y-m-d\\TH:i')) }}" class="{{ $fieldClass }}">
            </div>
            <div class="lg:col-span-2">
                <label class="{{ $labelClass }}" for="subject">الموضوع</label>
                <input id="subject" name="subject" value="{{ old('subject') }}" class="{{ $fieldClass }}">
            </div>
            <div class="lg:col-span-2">
                <label class="{{ $labelClass }}" for="message">نص الاستفسار</label>
                <textarea id="message" name="message" rows="4" class="{{ $areaClass }}">{{ old('message') }}</textarea>
            </div>
            <div class="lg:col-span-2">
                <label class="{{ $labelClass }}" for="admin_notes">ملاحظات الإدارة</label>
                <textarea id="admin_notes" name="admin_notes" rows="3" class="{{ $areaClass }}">{{ old('admin_notes') }}</textarea>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="order_id">Order ID</label>
                <input id="order_id" name="order_id" type="number" value="{{ old('order_id') }}" class="{{ $fieldClass }}" list="orders-list">
                <datalist id="orders-list">@foreach($recentOrders as $o)<option value="{{ $o->id }}">#{{ $o->id }} — {{ $o->status }}</option>@endforeach</datalist>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="consultation_request_id">Booking / Consultation ID</label>
                <input id="consultation_request_id" name="consultation_request_id" type="number" value="{{ old('consultation_request_id') }}" class="{{ $fieldClass }}" list="bookings-list">
                <datalist id="bookings-list">@foreach($recentBookings as $b)<option value="{{ $b->id }}">#{{ $b->id }} — {{ $b->status }}</option>@endforeach</datalist>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="institution_id">Institution ID</label>
                <select id="institution_id" name="institution_id" class="{{ $fieldClass }}">
                    <option value="">—</option>
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}" @selected(old('institution_id') == $inst->id)>{{ $inst->name_ar }} (#{{ $inst->id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="institution_program_id">برنامج الجهة</label>
                <select id="institution_program_id" name="institution_program_id" class="{{ $fieldClass }}">
                    <option value="">—</option>
                    @foreach($institutionPrograms as $program)
                        <option value="{{ $program->id }}" @selected(old('institution_program_id') == $program->id)>
                            #{{ $program->id }} — {{ $program->title_ar }} (جهة {{ $program->institution_id }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="user_id">User ID (إن وُجد)</label>
                <input id="user_id" name="user_id" type="number" value="{{ old('user_id') }}" class="{{ $fieldClass }}">
            </div>
        </article>
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn-press inline-flex h-11 items-center rounded-xl bg-accent px-6 text-sm font-medium text-white">حفظ الاستفسار</button>
            <a href="{{ route('admin.inquiries.index') }}" class="inline-flex h-11 items-center rounded-xl border border-line px-5 text-sm">إلغاء</a>
        </div>
    </form>
</div>
@endsection
