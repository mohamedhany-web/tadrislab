@extends('layouts.admin')

@section('title', 'تحليلات الأكاديمية')
@section('page_title', 'تحليلات وتوجهات الأكاديمية')

@section('content')
@php
    $kpis = $snapshot['kpis'] ?? [];
    $actions = $snapshot['actions'] ?? [];
    $insights = $snapshot['insights'] ?? [];
    $attention = $snapshot['attention'] ?? [];
    $trends = $snapshot['trends'] ?? [];
    $pulse = $snapshot['pulse'] ?? [];
    $recent = $snapshot['recent'] ?? [];
    $ordersTrend = $trends['orders_7d'] ?? [];
    $maxOrders = max(1, collect($ordersTrend)->max('count') ?: 1);
@endphp

<div class="space-y-5" id="academy-insights-root"
     data-poll-url="{{ $pollUrl }}"
     data-interval="12000">

    <section class="rounded-2xl border border-line bg-surface p-5 shadow-soft overflow-hidden relative">
        <div class="absolute inset-y-0 left-0 w-40 pointer-events-none opacity-80"
             style="background: radial-gradient(ellipse at center, rgba(15,118,110,0.12), transparent 70%);"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div class="min-w-0">
                <p class="text-xs font-medium text-muted inline-flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-accent/10 text-accent px-2.5 py-1 text-[11px] font-bold">
                        <span class="size-1.5 rounded-full bg-accent animate-pulse"></span>
                        Real-time
                    </span>
                    أقوى طبقة قرار للإدارة
                </p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تحليلات البيانات وتوجهات الأكاديمية</h2>
                <p class="mt-1.5 text-sm text-muted max-w-2xl">
                    لقطة حية من كل بيانات المنصة: طلاب، مدربون، طلبات، مجموعات، دعم، وإيراد — مع اقتراحات يجب اتخاذها الآن.
                </p>
            </div>
            <div class="flex flex-col items-start lg:items-end gap-1">
                <p class="text-[11px] font-bold text-muted uppercase tracking-wider">آخر تحديث</p>
                <p class="text-sm font-semibold text-ink tabular-nums" data-pulse-time>{{ $pulse['generated_at_human'] ?? '—' }}</p>
                <p class="text-[11px] text-muted" data-poll-status>تحديث تلقائي كل 12 ثانية</p>
            </div>
        </div>
    </section>

    {{-- KPIs --}}
    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4" data-kpi-grid>
        @foreach($kpis as $kpi)
            @php
                $tone = $kpi['tone'] ?? 'ink';
                $toneClass = match ($tone) {
                    'warn' => 'text-amber-700',
                    'ok' => 'text-emerald-700',
                    'gold' => 'text-[#8A6A00]',
                    'live' => 'text-rose-600',
                    'accent' => 'text-accent',
                    default => 'text-ink',
                };
            @endphp
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft" data-kpi="{{ $kpi['key'] }}">
                <p class="text-[11px] font-bold text-muted">{{ $kpi['label'] }}</p>
                <p class="mt-1 text-2xl font-semibold tracking-tight tabular-nums {{ $toneClass }}" data-kpi-value>
                    {{ $kpi['formatted'] ?? number_format((float) $kpi['value']) }}
                </p>
                @if(!empty($kpi['delta']))
                    <p class="mt-1 text-[11px] font-medium text-muted" data-kpi-delta>{{ $kpi['delta'] }}</p>
                @endif
                @if(!empty($kpi['hint']))
                    <p class="mt-0.5 text-[11px] text-muted/80" data-kpi-hint>{{ $kpi['hint'] }}</p>
                @endif
            </article>
        @endforeach
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
        {{-- Actions --}}
        <section class="xl:col-span-2 space-y-3">
            <div class="flex items-center justify-between gap-2">
                <h3 class="text-base font-semibold text-ink">اقتراحات يجب اتخاذها</h3>
                <span class="text-[11px] font-bold text-muted" data-actions-count>{{ count($actions) }} إجراء</span>
            </div>
            <div class="space-y-3" data-actions-list>
                @foreach($actions as $action)
                    @php
                        $prio = $action['priority'] ?? 'low';
                        $badge = match ($prio) {
                            'high' => 'bg-rose-50 text-rose-700 border-rose-100',
                            'medium' => 'bg-amber-50 text-amber-800 border-amber-100',
                            default => 'bg-[#f2f5f4] text-muted border-line',
                        };
                        $label = match ($prio) {
                            'high' => 'عاجل',
                            'medium' => 'مهم',
                            default => 'متابعة',
                        };
                    @endphp
                    <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-bold {{ $badge }}">{{ $label }}</span>
                                <h4 class="text-sm font-semibold text-ink">{{ $action['title'] }}</h4>
                            </div>
                            <p class="text-sm text-muted">{{ $action['body'] }}</p>
                        </div>
                        @if(!empty($action['url']) && !empty($action['cta']))
                            <a href="{{ $action['url'] }}" class="btn-press inline-flex h-9 shrink-0 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                                {{ $action['cta'] }}
                            </a>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Insights + spark --}}
        <div class="space-y-5">
            <section class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <h3 class="text-sm font-semibold text-ink mb-3">توجهات سريعة</h3>
                <div class="space-y-3" data-insights-list>
                    @foreach($insights as $insight)
                        @php
                            $icon = match ($insight['tone'] ?? 'info') {
                                'up' => 'fa-arrow-trend-up text-emerald-600',
                                'down' => 'fa-arrow-trend-down text-rose-600',
                                'warn' => 'fa-triangle-exclamation text-amber-600',
                                'live' => 'fa-broadcast-tower text-rose-600',
                                default => 'fa-lightbulb text-accent',
                            };
                        @endphp
                        <div class="rounded-xl border border-line px-3 py-2.5">
                            <p class="text-xs font-bold text-ink flex items-center gap-2">
                                <i class="fas {{ $icon }} text-[11px]"></i>
                                {{ $insight['title'] }}
                            </p>
                            <p class="mt-1 text-[12px] text-muted leading-relaxed">{{ $insight['body'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-ink">الطلبات · 7 أيام</h3>
                    <span class="text-[11px] text-muted" data-orders-week-total>{{ collect($ordersTrend)->sum('count') }}</span>
                </div>
                <div class="flex items-end gap-1.5 h-24" data-orders-bars>
                    @foreach($ordersTrend as $day)
                        @php $h = max(8, (int) round(($day['count'] / $maxOrders) * 100)); @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 min-w-0">
                            <div class="w-full rounded-t-md bg-accent/80" style="height: {{ $h }}%" title="{{ $day['count'] }}"></div>
                            <span class="text-[9px] font-bold text-muted truncate w-full text-center">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <h3 class="text-sm font-semibold text-ink mb-3">مؤشرات الانتباه</h3>
                <dl class="space-y-2 text-sm" data-attention-list>
                    @foreach([
                        'orders_pending' => 'طلبات معلّقة',
                        'tutoring_pending' => 'حجوزات معلّقة',
                        'withdrawals_pending' => 'سحوبات معلّقة',
                        'tickets_open' => 'تذاكر مفتوحة',
                        'cohorts_at_risk' => 'دفعات مهددة',
                        'ungraded_submissions' => 'واجبات بلا تقييم',
                    ] as $key => $label)
                        <div class="flex items-center justify-between gap-2 border-b border-line last:border-0 pb-2 last:pb-0">
                            <dt class="text-muted">{{ $label }}</dt>
                            <dd class="font-semibold tabular-nums text-ink" data-attention="{{ $key }}">{{ number_format((int) ($attention[$key] ?? 0)) }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        @foreach([
            'orders' => 'أحدث الطلبات',
            'students' => 'أحدث الطلاب',
            'activity' => 'نبض النشاط',
        ] as $key => $title)
            <section class="rounded-2xl border border-line bg-surface shadow-soft overflow-hidden">
                <div class="px-4 pt-4 pb-2 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-ink">{{ $title }}</h3>
                </div>
                <div class="px-2 pb-3 space-y-1" data-recent="{{ $key }}">
                    @forelse(($recent[$key] ?? []) as $row)
                        @if(!empty($row['url']))
                            <a href="{{ $row['url'] }}" class="flex items-center justify-between gap-2 rounded-xl px-2.5 py-2 hover:bg-canvas transition-colors">
                        @else
                            <div class="flex items-center justify-between gap-2 rounded-xl px-2.5 py-2">
                        @endif
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-ink truncate">{{ $row['title'] }}</p>
                                <p class="text-[11px] text-muted truncate">{{ $row['meta'] ?? '' }}</p>
                            </div>
                            <span class="text-[10px] text-muted whitespace-nowrap">{{ $row['when'] ?? '' }}</span>
                        @if(!empty($row['url']))
                            </a>
                        @else
                            </div>
                        @endif
                    @empty
                        <p class="text-center text-xs text-muted py-8">لا بيانات</p>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
(function () {
    const root = document.getElementById('academy-insights-root');
    if (!root) return;
    const url = root.dataset.pollUrl;
    const interval = Math.max(8000, parseInt(root.dataset.interval || '12000', 10));
    const statusEl = root.querySelector('[data-poll-status]');
    let timer = null;
    let inFlight = false;

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    function priorityBadge(prio) {
        if (prio === 'high') return { cls: 'bg-rose-50 text-rose-700 border-rose-100', label: 'عاجل' };
        if (prio === 'medium') return { cls: 'bg-amber-50 text-amber-800 border-amber-100', label: 'مهم' };
        return { cls: 'bg-[#f2f5f4] text-muted border-line', label: 'متابعة' };
    }

    function insightIcon(tone) {
        if (tone === 'up') return 'fa-arrow-trend-up text-emerald-600';
        if (tone === 'down') return 'fa-arrow-trend-down text-rose-600';
        if (tone === 'warn') return 'fa-triangle-exclamation text-amber-600';
        if (tone === 'live') return 'fa-broadcast-tower text-rose-600';
        return 'fa-lightbulb text-accent';
    }

    function render(data) {
        if (data.pulse && data.pulse.generated_at_human) {
            const t = root.querySelector('[data-pulse-time]');
            if (t) t.textContent = data.pulse.generated_at_human;
        }

        (data.kpis || []).forEach((kpi) => {
            const card = root.querySelector('[data-kpi="' + kpi.key + '"]');
            if (!card) return;
            const v = card.querySelector('[data-kpi-value]');
            const d = card.querySelector('[data-kpi-delta]');
            const h = card.querySelector('[data-kpi-hint]');
            if (v) v.textContent = kpi.formatted || Number(kpi.value || 0).toLocaleString('en-US');
            if (d && kpi.delta) d.textContent = kpi.delta;
            if (h && kpi.hint) h.textContent = kpi.hint;
        });

        Object.keys(data.attention || {}).forEach((key) => {
            const el = root.querySelector('[data-attention="' + key + '"]');
            if (el) el.textContent = Number(data.attention[key] || 0).toLocaleString('en-US');
        });

        const actionsList = root.querySelector('[data-actions-list]');
        const actionsCount = root.querySelector('[data-actions-count]');
        if (actionsList && Array.isArray(data.actions)) {
            if (actionsCount) actionsCount.textContent = data.actions.length + ' إجراء';
            actionsList.innerHTML = data.actions.map((a) => {
                const b = priorityBadge(a.priority);
                const btn = (a.url && a.cta)
                    ? '<a href="' + esc(a.url) + '" class="btn-press inline-flex h-9 shrink-0 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">' + esc(a.cta) + '</a>'
                    : '';
                return '<article class="rounded-2xl border border-line bg-surface p-4 shadow-soft flex flex-col sm:flex-row sm:items-center gap-3">'
                    + '<div class="flex-1 min-w-0"><div class="flex flex-wrap items-center gap-2 mb-1.5">'
                    + '<span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-bold ' + b.cls + '">' + b.label + '</span>'
                    + '<h4 class="text-sm font-semibold text-ink">' + esc(a.title) + '</h4></div>'
                    + '<p class="text-sm text-muted">' + esc(a.body) + '</p></div>' + btn + '</article>';
            }).join('');
        }

        const insightsList = root.querySelector('[data-insights-list]');
        if (insightsList && Array.isArray(data.insights)) {
            insightsList.innerHTML = data.insights.map((i) => {
                return '<div class="rounded-xl border border-line px-3 py-2.5">'
                    + '<p class="text-xs font-bold text-ink flex items-center gap-2"><i class="fas ' + insightIcon(i.tone) + ' text-[11px]"></i>'
                    + esc(i.title) + '</p>'
                    + '<p class="mt-1 text-[12px] text-muted leading-relaxed">' + esc(i.body) + '</p></div>';
            }).join('');
        }

        const bars = root.querySelector('[data-orders-bars]');
        const weekTotal = root.querySelector('[data-orders-week-total]');
        const series = (data.trends && data.trends.orders_7d) || [];
        if (bars && series.length) {
            const max = Math.max(1, ...series.map((d) => Number(d.count || 0)));
            if (weekTotal) weekTotal.textContent = series.reduce((s, d) => s + Number(d.count || 0), 0);
            bars.innerHTML = series.map((d) => {
                const h = Math.max(8, Math.round((Number(d.count || 0) / max) * 100));
                return '<div class="flex-1 flex flex-col items-center gap-1 min-w-0">'
                    + '<div class="w-full rounded-t-md bg-accent/80" style="height:' + h + '%" title="' + esc(d.count) + '"></div>'
                    + '<span class="text-[9px] font-bold text-muted truncate w-full text-center">' + esc(d.label) + '</span></div>';
            }).join('');
        }

        ['orders', 'students', 'activity'].forEach((key) => {
            const box = root.querySelector('[data-recent="' + key + '"]');
            const rows = (data.recent && data.recent[key]) || [];
            if (!box) return;
            if (!rows.length) {
                box.innerHTML = '<p class="text-center text-xs text-muted py-8">لا بيانات</p>';
                return;
            }
            box.innerHTML = rows.map((r) => {
                const inner = '<div class="min-w-0"><p class="text-xs font-bold text-ink truncate">' + esc(r.title) + '</p>'
                    + '<p class="text-[11px] text-muted truncate">' + esc(r.meta || '') + '</p></div>'
                    + '<span class="text-[10px] text-muted whitespace-nowrap">' + esc(r.when || '') + '</span>';
                if (r.url) {
                    return '<a href="' + esc(r.url) + '" class="flex items-center justify-between gap-2 rounded-xl px-2.5 py-2 hover:bg-canvas transition-colors">' + inner + '</a>';
                }
                return '<div class="flex items-center justify-between gap-2 rounded-xl px-2.5 py-2">' + inner + '</div>';
            }).join('');
        });
    }

    async function poll() {
        if (inFlight || document.hidden) return;
        inFlight = true;
        try {
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('poll failed');
            const data = await res.json();
            render(data);
            if (statusEl) statusEl.textContent = 'متصل · تحديث تلقائي كل 12 ثانية';
        } catch (e) {
            if (statusEl) statusEl.textContent = 'تعذّر التحديث — إعادة المحاولة…';
        } finally {
            inFlight = false;
        }
    }

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) poll();
    });

    timer = setInterval(poll, interval);
})();
</script>
@endpush
@endsection
