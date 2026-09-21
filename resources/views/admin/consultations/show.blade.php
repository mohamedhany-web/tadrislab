@extends('layouts.admin')

@section('title', 'حجز استشارة #'.$consultation->id)
@section('header', 'حجز استشارة #'.$consultation->id)

@section('content')
@php
    $CR = \App\Models\ConsultationRequest::class;
@endphp
<div class="space-y-6 max-w-5xl">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
    @endif

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 space-y-3">
        <div class="flex flex-wrap justify-between gap-2">
            <h2 class="text-xl font-bold text-slate-900">تفاصيل الحجز</h2>
            <span class="px-3 py-1 rounded-full bg-slate-100 text-sm font-semibold">{{ $consultation->briefStatusLabel() }}</span>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-slate-500">الخدمة</dt><dd class="font-semibold">{{ $consultation->service?->title_ar ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">النوع</dt><dd class="font-semibold">{{ $consultation->typeLabel() }}</dd></div>
            <div><dt class="text-slate-500">المعلم</dt><dd class="font-semibold">{{ $consultation->student->name ?? '—' }} — {{ $consultation->student->email ?? '' }}</dd></div>
            <div><dt class="text-slate-500">المدرب</dt><dd class="font-semibold">{{ $consultation->instructor->name ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">المبلغ</dt><dd class="font-semibold">{{ number_format($consultation->price_amount, 2) }} {{ $consultation->currency ?: 'QAR' }}</dd></div>
            <div><dt class="text-slate-500">المدة</dt><dd class="font-semibold">{{ (int) $consultation->duration_minutes }} دقيقة</dd></div>
            @if($consultation->preferred_slot_at)
            <div><dt class="text-slate-500">موعد مقترح من الحاجز</dt><dd class="font-semibold"><x-app-datetime :at="$consultation->preferred_slot_at" /></dd></div>
            @endif
            @if($consultation->scheduled_at)
            <div><dt class="text-slate-500">الموعد المؤكد</dt><dd class="font-semibold"><x-app-datetime :at="$consultation->scheduled_at" /></dd></div>
            @endif
            @if($consultation->contact_name || $consultation->contact_phone)
            <div class="sm:col-span-2"><dt class="text-slate-500">بيانات التواصل</dt>
                <dd class="font-semibold">{{ $consultation->contact_name }} · {{ $consultation->contact_phone }} · {{ $consultation->contact_email }}</dd>
            </div>
            @endif
            @if($consultation->organization_name)
            <div class="sm:col-span-2"><dt class="text-slate-500">الجهة</dt><dd class="font-semibold">{{ $consultation->organization_name }}</dd></div>
            @endif
            @if($consultation->payment_method)
            <div><dt class="text-slate-500">طريقة الدفع</dt><dd class="font-semibold">
                @if(in_array($consultation->payment_method, ['online', 'paypal', 'kashier'], true)) دفع إلكتروني
                @elseif($consultation->payment_method === 'bank_transfer') تحويل بنكي / محفظة
                @elseif($consultation->payment_method === 'cash') نقدي
                @else أخرى @endif
            </dd></div>
            @endif
            @if($consultation->order_id)
            <div><dt class="text-slate-500">طلب الدفع</dt>
                <dd class="font-semibold">
                    <a href="{{ route('admin.orders.show', $consultation->order_id) }}" class="text-sky-700 underline">#{{ $consultation->order_id }}</a>
                    @if($consultation->paid_confirmed_at)
                        <span class="ms-2 text-xs text-emerald-700 font-bold">مدفوع</span>
                    @endif
                </dd>
            </div>
            @endif
            @if($consultation->payment_proof)
            <div class="sm:col-span-2">
                <dt class="text-slate-500 mb-2">إيصال الدفع</dt>
                <dd>
                    <a href="{{ storage_asset($consultation->payment_proof) }}" target="_blank" rel="noopener" class="inline-block">
                        <img src="{{ storage_asset($consultation->payment_proof) }}" alt="إيصال" class="max-h-64 rounded-lg border border-slate-200 shadow-sm">
                    </a>
                </dd>
            </div>
            @endif
            @if($consultation->student_message)
            <div class="sm:col-span-2"><dt class="text-slate-500">رسالة الحاجز</dt><dd class="text-slate-800 whitespace-pre-line">{{ $consultation->student_message }}</dd></div>
            @endif
        </dl>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <h3 class="font-bold text-slate-900 mb-3">ملاحظات إدارية</h3>
        <form method="POST" action="{{ route('admin.consultations.notes', $consultation) }}">
            @csrf
            <textarea name="admin_notes" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm mb-2">{{ old('admin_notes', $consultation->admin_notes) }}</textarea>
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold">حفظ الملاحظات</button>
        </form>
    </div>

    @if($consultation->isAwaitingAdmin())
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-6 space-y-4">
        <p class="text-sm text-emerald-900 font-semibold">الحجز New — راجع الدفع ثم أكّد الموعد (Confirmed) مع إنشاء غرفة الجلسة وإرسال WhatsApp / Email.</p>
        @if(in_array($consultation->status, [$CR::STATUS_NEW, $CR::STATUS_PENDING, $CR::STATUS_PAYMENT_REPORTED, $CR::STATUS_AWAITING_VERIFICATION], true) && !$consultation->paid_confirmed_at)
        @if($consultation->order_id && $consultation->order && $consultation->order->status === \App\Models\Order::STATUS_PENDING)
        <p class="text-sm text-slate-700 mb-2">يوجد طلب دفع مرتبط — يُفضّل <a href="{{ route('admin.orders.show', $consultation->order_id) }}" class="text-sky-700 font-bold underline">الموافقة على الطلب #{{ $consultation->order_id }}</a> لتسجيل الفاتورة وتأكيد الدفع تلقائيًا.</p>
        @endif
        <form method="POST" action="{{ route('admin.consultations.confirm-payment', $consultation) }}" class="inline">
            @csrf
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-bold">تأكيد استلام الدفع فقط</button>
        </form>
        @endif
        <form method="POST" action="{{ route('admin.consultations.schedule', $consultation) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-emerald-200">
            @csrf
            @include('partials.timezone-select', [
                'value' => old('timezone', $consultation->instructor?->timezoneCode() ?? auth()->user()?->timezoneCode()),
                'class' => 'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm',
                'labelClass' => 'block text-xs font-semibold text-slate-600 mb-1',
            ])
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">موعد الجلسة *</label>
                <input type="datetime-local" name="scheduled_at"
                       value="{{ old('scheduled_at', $consultation->preferred_slot_at?->format('Y-m-d\TH:i')) }}"
                       class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">المدة (دقيقة)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $consultation->duration_minutes) }}" min="15" max="480" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-sky-600 text-white text-sm font-bold">تأكيد الحجز (Confirmed) + إشعار</button>
            </div>
        </form>
    </div>
    @endif

    @if($consultation->isActiveBooking())
    <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-6 space-y-4">
        <h3 class="font-bold text-emerald-900">جلسة مؤكدة</h3>
        @if($consultation->scheduled_at)
            <p class="text-sm text-emerald-800">الموعد: <x-app-datetime :at="$consultation->scheduled_at" /></p>
        @endif
        @if($consultation->classroomMeeting)
            <p class="text-xs font-mono break-all">{{ url('classroom/join/'.$consultation->classroomMeeting->code) }}</p>
        @endif

        <form method="POST" action="{{ route('admin.consultations.reschedule', $consultation) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-emerald-200">
            @csrf
            <p class="sm:col-span-2 text-sm font-semibold text-slate-800">إعادة جدولة (Rescheduled)</p>
            @include('partials.timezone-select', [
                'value' => old('timezone', $consultation->instructor?->timezoneCode() ?? auth()->user()?->timezoneCode()),
                'class' => 'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm',
                'labelClass' => 'block text-xs font-semibold text-slate-600 mb-1',
            ])
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">الموعد الجديد *</label>
                <input type="datetime-local" name="scheduled_at" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">المدة</label>
                <input type="number" name="duration_minutes" value="{{ $consultation->duration_minutes }}" min="15" max="480" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-semibold">حفظ إعادة الجدولة</button>
            </div>
        </form>
    </div>
    @endif

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <h3 class="font-bold text-slate-900 mb-3">نتيجة الجلسة / التوصيات</h3>
        <form method="POST" action="{{ route('admin.consultations.outcome', $consultation) }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">تسجيل النتيجة</label>
                <textarea name="outcome_notes" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('outcome_notes', $consultation->outcome_notes) }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">التوصيات</label>
                <textarea name="recommendations" rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">{{ old('recommendations', $consultation->recommendations) }}</textarea>
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-semibold">حفظ النتيجة</button>
        </form>
    </div>

    <div class="flex flex-wrap gap-3">
        @if(!in_array($consultation->status, [$CR::STATUS_CANCELLED, $CR::STATUS_COMPLETED], true))
        <form method="POST" action="{{ route('admin.consultations.cancel', $consultation) }}" onsubmit="return confirm('إلغاء الحجز؟');">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-lg border border-rose-300 text-rose-700 text-sm font-semibold">إلغاء (Cancelled)</button>
        </form>
        @endif
        @if($consultation->isActiveBooking())
        <form method="POST" action="{{ route('admin.consultations.complete', $consultation) }}">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-700 text-white text-sm font-semibold">إكمال (Completed)</button>
        </form>
        @endif
        <a href="{{ route('admin.consultations.index') }}" class="px-4 py-2 rounded-lg text-sky-600 text-sm font-semibold hover:underline">رجوع</a>
    </div>
</div>
@endsection
