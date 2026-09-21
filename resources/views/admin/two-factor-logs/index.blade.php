@extends('layouts.admin')

@section('title', 'سجلات التحقق الثنائي (2FA) - ' . config('app.name'))
@section('page_title', 'تسجيلات الدخول (2FA)')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $statCards = [
        [
            'label' => 'إجمالي السجلات',
            'value' => number_format($stats['total'] ?? 0),
            'icon' => 'fas fa-shield-alt',
        ],
        [
            'label' => 'اليوم',
            'value' => number_format($stats['today'] ?? 0),
            'icon' => 'fas fa-calendar-day',
        ],
        [
            'label' => 'إرسال رمز',
            'value' => number_format($stats['challenge_sent'] ?? 0),
            'icon' => 'fas fa-paper-plane',
        ],
        [
            'label' => 'تحقق ناجح',
            'value' => number_format($stats['verified'] ?? 0),
            'icon' => 'fas fa-check-circle',
        ],
        [
            'label' => 'فشل التحقق',
            'value' => number_format($stats['failed'] ?? 0),
            'icon' => 'fas fa-times-circle',
        ],
    ];
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">الأمان · التحقق الثنائي</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">سجلات التحقق الثنائي (2FA)</h2>
            <p class="mt-1 text-sm text-muted">جميع أحداث تسجيل الدخول بالتحقق الثنائي (إرسال رمز، تحقق ناجح، فشل)</p>
        </div>
    </section>

    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ($statCards as $card)
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="{{ $card['icon'] }} text-sm"></i>
                </div>
                <p class="mt-3 text-xs text-muted">{{ $card['label'] }}</p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink">{{ $card['value'] }}</p>
            </article>
        @endforeach
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">فلترة وبحث</h3>
            <p class="mt-0.5 text-xs text-muted">ابحث بالبريد أو المستخدم أو نوع الحدث</p>
        </div>
        <form method="GET" class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-2 xl:grid-cols-5 xl:items-end">
            <div>
                <label class="{{ $labelClass }}" for="search">البحث (بريد / مستخدم)</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-muted">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="البحث..."
                           class="{{ $fieldClass }} ps-9">
                </div>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="event">نوع الحدث</label>
                <select id="event" name="event" class="{{ $fieldClass }}">
                    <option value="">جميع الأحداث</option>
                    <option value="challenge_sent" {{ request('event') === 'challenge_sent' ? 'selected' : '' }}>إرسال رمز</option>
                    <option value="verified" {{ request('event') === 'verified' ? 'selected' : '' }}>تحقق ناجح</option>
                    <option value="failed" {{ request('event') === 'failed' ? 'selected' : '' }}>فشل التحقق</option>
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="date_from">من تاريخ</label>
                <input id="date_from" type="date" name="date_from" value="{{ request('date_from') }}" class="{{ $fieldClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}" for="date_to">إلى تاريخ</label>
                <input id="date_to" type="date" name="date_to" value="{{ request('date_to') }}" class="{{ $fieldClass }}">
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="btn-press inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-search text-xs"></i>
                    <span>بحث</span>
                </button>
                @if(request()->anyFilled(['search', 'event', 'date_from', 'date_to']))
                    <a href="{{ route('admin.two-factor-logs.index') }}"
                       class="btn-press inline-flex h-11 items-center justify-center rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent"
                       title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">السجلات</h3>
            <p class="mt-0.5 text-xs text-muted">
                <span class="font-semibold text-accent">{{ $logs->total() }}</span> سجل
            </p>
        </div>

        @if ($logs->count() > 0)
            <div class="admin-table-wrap overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-line bg-canvas text-xs text-muted">
                        <tr>
                            <th class="px-4 py-3 text-start font-medium">المستخدم</th>
                            <th class="px-4 py-3 text-start font-medium">البريد</th>
                            <th class="px-4 py-3 text-start font-medium">الحدث</th>
                            <th class="px-4 py-3 text-start font-medium">IP</th>
                            <th class="px-4 py-3 text-start font-medium">التاريخ والوقت</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-canvas/40">
                                <td class="px-4 py-3">
                                    @if($log->user)
                                        <div class="flex items-center gap-3">
                                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-sm font-bold text-accent">
                                                {{ mb_substr($log->user->name ?? 'غ', 0, 1, 'UTF-8') }}
                                            </div>
                                            <p class="font-semibold text-ink">{{ $log->user->name }}</p>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-ink-soft">{{ $log->email ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $eventClasses = [
                                            'challenge_sent' => 'bg-amber-50 text-amber-700',
                                            'verified' => 'bg-emerald-50 text-emerald-700',
                                            'failed' => 'bg-rose-50 text-rose-700',
                                        ];
                                        $c = $eventClasses[$log->event] ?? 'bg-canvas-muted text-muted';
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $c }}">
                                        {{ $log->event_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-ink-soft">{{ $log->ip_address ?? '—' }}</td>
                                <td class="px-4 py-3 text-xs text-muted">
                                    <p class="font-medium text-ink">{{ $log->created_at->format('Y-m-d') }}</p>
                                    <p>{{ $log->created_at->format('H:i:s') }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($logs->hasPages())
                <div class="border-t border-line px-4 py-3 sm:px-5">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="px-4 py-12 text-center sm:px-5">
                <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-shield-alt text-2xl"></i>
                </div>
                <h3 class="text-base font-semibold text-ink">لا توجد سجلات 2FA</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-muted">لا توجد أحداث تحقق ثنائي مطابقة للفلتر</p>
            </div>
        @endif
    </article>
</div>
@endsection
