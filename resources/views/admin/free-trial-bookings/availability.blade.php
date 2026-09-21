@extends('layouts.admin')

@section('title', 'أوقات الحصة المجانية - TADRIS LAB')
@section('page_title', 'أوقات الحصة المجانية')

@section('content')
@php
    $fmtTime = function ($t) {
        if ($t instanceof \Carbon\CarbonInterface) {
            return $t->format('H:i');
        }
        return substr((string) $t, 0, 5);
    };
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $activeCount = $windows->where('is_active', true)->count();
    $totalCount = $windows->count();
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">نوافذ التوفر · تظهر في تقويم الصفحة الرئيسية</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">أوقات الحصة المجانية</h2>
        </div>
        <div class="admin-hero-actions flex flex-wrap gap-2">
            <a href="{{ route('admin.free-trial-bookings.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-canvas">
                <i class="fas fa-list text-xs"></i>
                قائمة الحجوزات
            </a>
        </div>
    </section>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft" role="status">
            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-accent-soft text-accent"><i class="fas fa-check text-sm"></i></span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/10 px-4 py-3 text-sm text-danger shadow-soft">
            <ul class="list-disc space-y-1 pr-5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-3">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-accent-soft text-accent">
                <i class="fas fa-clock text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">إجمالي النوافذ</p>
            <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink">{{ number_format($totalCount) }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-metal/15 text-metal">
                <i class="fas fa-toggle-on text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">نشطة</p>
            <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink">{{ number_format($activeCount) }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-canvas-muted text-muted">
                <i class="fas fa-toggle-off text-sm"></i>
            </div>
            <p class="mt-3 text-xs text-muted">متوقفة</p>
            <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink">{{ number_format(max(0, $totalCount - $activeCount)) }}</p>
        </article>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">إضافة نافذة جديدة</h3>
            <p class="mt-0.5 text-xs text-muted">الأوقات بتوقيت الأكاديمية (مصر — القاهرة). مدة الشريحة غالباً 30 دقيقة — تظهر في تقويم الحجز على الموقع بتوقيت الزائر مع تلوين الجودة.</p>
        </div>
        <form method="post" action="{{ route('admin.free-trial-bookings.availability.store') }}" class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 sm:p-5 lg:grid-cols-6 lg:items-end">
            @csrf
            <div>
                <label class="{{ $labelClass }}" for="new_day">اليوم</label>
                <select id="new_day" name="day_of_week" class="{{ $fieldClass }}" required>
                    @foreach($dayNames as $num => $label)
                        <option value="{{ $num }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="new_start">من</label>
                <input id="new_start" type="time" name="start_time" value="10:00" class="{{ $fieldClass }}" required>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="new_end">إلى</label>
                <input id="new_end" type="time" name="end_time" value="18:00" class="{{ $fieldClass }}" required>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="new_duration">مدة الشريحة</label>
                <select id="new_duration" name="slot_duration_minutes" class="{{ $fieldClass }}">
                    <option value="30" selected>30 دقيقة</option>
                    <option value="15">15 دقيقة</option>
                    <option value="45">45 دقيقة</option>
                    <option value="60">60 دقيقة</option>
                </select>
            </div>
            <div class="flex h-11 items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" id="new_active" class="size-4 rounded border-line text-accent focus:ring-accent/20" checked>
                <label for="new_active" class="text-sm font-medium text-ink">نشط</label>
            </div>
            <button type="submit" class="btn-press inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                <i class="fas fa-plus text-xs"></i>
                إضافة
            </button>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div class="min-w-0">
                <h3 class="text-base font-semibold text-ink">النوافذ الحالية</h3>
                <p class="mt-0.5 text-xs text-muted">عدّل اليوم أو الوقت أو المدة ثم احفظ</p>
            </div>
        </div>

        <div class="divide-y divide-line">
            @forelse($windows as $w)
                <div class="px-4 py-4 sm:px-5 {{ ! $w->is_active ? 'bg-[#f7f8fa]/60' : '' }}">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-end">
                        <form method="post" action="{{ route('admin.free-trial-bookings.availability.update', $w) }}" class="grid flex-1 grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5 lg:items-end">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="{{ $labelClass }}">اليوم</label>
                                <select name="day_of_week" class="{{ $fieldClass }}">
                                    @foreach($dayNames as $num => $label)
                                        <option value="{{ $num }}" @selected((int) $w->day_of_week === $num)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">من</label>
                                <input type="time" name="start_time" value="{{ $fmtTime($w->start_time) }}" class="{{ $fieldClass }}">
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">إلى</label>
                                <input type="time" name="end_time" value="{{ $fmtTime($w->end_time) }}" class="{{ $fieldClass }}">
                            </div>
                            <div>
                                <label class="{{ $labelClass }}">المدة</label>
                                <select name="slot_duration_minutes" class="{{ $fieldClass }}">
                                    @foreach([15, 30, 45, 60] as $d)
                                        <option value="{{ $d }}" @selected((int) $w->slot_duration_minutes === $d)>{{ $d }} د</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <label class="inline-flex h-11 items-center gap-2 rounded-xl border border-line bg-[#f7f8fa] px-3 text-sm font-medium text-ink">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" class="size-4 rounded border-line text-accent focus:ring-accent/20" @checked($w->is_active)>
                                    نشط
                                </label>
                                <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">
                                    <i class="fas fa-save text-xs"></i>
                                    حفظ
                                </button>
                            </div>
                        </form>
                        <form method="post" action="{{ route('admin.free-trial-bookings.availability.destroy', $w) }}" onsubmit="return confirm('حذف هذه النافذة؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-danger/30 bg-danger/10 px-4 text-sm font-medium text-danger transition hover:bg-danger hover:text-white lg:w-auto">
                                <i class="fas fa-trash text-xs"></i>
                                حذف
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-4 py-16 text-center sm:px-5">
                    <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-accent-soft text-accent">
                        <i class="fas fa-clock"></i>
                    </div>
                    <p class="text-sm font-medium text-ink">لا توجد نوافذ بعد</p>
                    <p class="mt-1 text-xs text-muted">أضف أول نافذة من النموذج أعلاه لتظهر في تقويم الموقع.</p>
                </div>
            @endforelse
        </div>
    </article>
</div>
@endsection
