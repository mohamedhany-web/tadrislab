@extends('layouts.instructor-timeline')

@section('title', $program->title())
@section('page_title', $program->title())

@section('content')
@php $isRtl = app()->getLocale() === 'ar'; @endphp

@if(session('success'))
    <div class="st-panel" style="margin-bottom:1rem;border-color:#86efac;background:#f0fdf4"><p style="margin:0;color:#166534">{{ session('success') }}</p></div>
@endif
@if(session('error'))
    <div class="st-panel" style="margin-bottom:1rem;border-color:#fecaca;background:#fef2f2"><p style="margin:0;color:#991b1b">{{ session('error') }}</p></div>
@endif

<section class="st-join-hero">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">{{ $modeLabel }}</p>
        <h2 class="st-join-hero__title">{{ $program->title() }}</h2>
        <p class="st-join-hero__meta">{{ $program->institution?->name() }} · {{ $program->statusLabel() }}</p>
    </div>
    <div class="st-join-hero__actions">
        <a href="{{ route('instructor.institution-delivery.index') }}" class="st-pill st-pill--outline">{{ $isRtl ? 'كل التعاقدات' : 'All deliveries' }}</a>
    </div>
</section>

<section class="st-panel">
    <div class="st-section-head">
        <h2>{{ $isRtl ? 'تحديث التنفيذ' : 'Update delivery' }}</h2>
        <p>{{ $isDirect ? ($isRtl ? 'سجّل تقدّم الخدمة ونتيجة الإغلاق للجهة.' : 'Log service progress and closing notes for the org.') : ($isRtl ? 'برنامج منصة مسند إليك — حدّث الحالة عند الحاجة.' : 'Platform program assigned to you — update status as needed.') }}</p>
    </div>
    <form method="POST" action="{{ route('instructor.institution-delivery.update', $program) }}" style="display:grid;gap:1rem;margin-top:1rem">
        @csrf
        @method('PUT')
        <label style="display:grid;gap:.35rem">
            <span style="font-size:.8rem;font-weight:600">{{ $isRtl ? 'الحالة' : 'Status' }}</span>
            <select name="status" style="height:2.75rem;border:1px solid #e2e8f0;border-radius:.75rem;padding:0 .9rem">
                @foreach(\App\Models\InstitutionProgram::statuses() as $key => $label)
                    <option value="{{ $key }}" @selected($program->status === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label style="display:grid;gap:.35rem">
            <span style="font-size:.8rem;font-weight:600">{{ $isRtl ? 'نسبة التقدّم' : 'Progress %' }}</span>
            <input type="number" name="progress_percent" min="0" max="100" value="{{ (int) $program->progress_percent }}" style="height:2.75rem;border:1px solid #e2e8f0;border-radius:.75rem;padding:0 .9rem">
        </label>
        <label style="display:grid;gap:.35rem">
            <span style="font-size:.8rem;font-weight:600">{{ $isRtl ? 'خطة / ملاحظات التنفيذ' : 'Delivery plan / notes' }}</span>
            <textarea name="improvement_plan" rows="3" style="border:1px solid #e2e8f0;border-radius:.75rem;padding:.75rem">{{ old('improvement_plan', $program->improvement_plan) }}</textarea>
        </label>
        <label style="display:grid;gap:.35rem">
            <span style="font-size:.8rem;font-weight:600">{{ $isRtl ? 'نتيجة الإغلاق' : 'Closing result' }}</span>
            <textarea name="result_notes" rows="3" style="border:1px solid #e2e8f0;border-radius:.75rem;padding:.75rem">{{ old('result_notes', $program->result_notes) }}</textarea>
        </label>
        <button type="submit" class="st-pill st-pill--solid">{{ $isRtl ? 'حفظ التحديث' : 'Save update' }}</button>
    </form>
</section>

@if($program->participants->isNotEmpty())
<section class="st-panel" style="margin-top:1rem">
    <h2>{{ $isRtl ? 'مشاركون مسجّلون (إن وُجدوا)' : 'Registered participants (if any)' }}</h2>
    <ul>
        @foreach($program->participants as $p)
            <li>{{ $p->name }} — {{ (int) $p->progress_percent }}%</li>
        @endforeach
    </ul>
</section>
@endif
@endsection
