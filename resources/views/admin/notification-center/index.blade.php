@extends('layouts.admin')

@section('title', 'مركز الإشعارات')
@section('page_title', 'الإشعارات')

@section('content')
<div class="space-y-5">
    <section>
        <p class="text-xs font-medium text-muted">Notification Layer · WhatsApp + Email</p>
        <h2 class="mt-1 text-2xl font-semibold text-ink">مركز الإشعارات</h2>
        <p class="mt-1 text-sm text-muted">إدارة القنوات والقوالب والأحداث التشغيلية دون تعديل الكود.</p>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">الحالة</p>
            <p class="mt-1 text-lg font-semibold {{ $enabled ? 'text-emerald-700' : 'text-rose-700' }}">{{ $enabled ? 'مفعّل' : 'معطّل' }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">القنوات النشطة</p>
            <p class="mt-1 text-lg font-semibold text-ink">{{ implode(' · ', $channels) ?: '—' }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">أحداث النظام</p>
            <p class="mt-1 text-lg font-semibold text-ink">{{ count($events) }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">قوالب مخصّصة</p>
            <p class="mt-1 text-lg font-semibold text-ink">{{ collect($templates)->where('overridden', true)->count() }}</p>
        </article>
    </section>

    <section class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
        <a href="{{ route('admin.notification-center.templates') }}" class="rounded-2xl border border-line bg-surface p-5 shadow-soft transition hover:border-accent">
            <p class="text-sm font-semibold text-ink">قوالب WhatsApp / Email</p>
            <p class="mt-1 text-xs text-muted">تعديل نصوص الأحداث الثمانية وحفظها من لوحة الإدارة.</p>
        </a>
        <a href="{{ route('admin.notifications.inbox') }}" class="rounded-2xl border border-line bg-surface p-5 shadow-soft transition hover:border-accent">
            <p class="text-sm font-semibold text-ink">وارد الإشعارات</p>
            <p class="mt-1 text-xs text-muted">صندوق الوارد داخل المنصة.</p>
        </a>
        <a href="{{ route('admin.notifications.index') }}" class="rounded-2xl border border-line bg-surface p-5 shadow-soft transition hover:border-accent">
            <p class="text-sm font-semibold text-ink">بث إشعارات</p>
            <p class="mt-1 text-xs text-muted">إرسال يدوي للمعلمين أو الموظفين.</p>
        </a>
        @if(Route::has('admin.messages.settings'))
        <a href="{{ route('admin.messages.settings') }}" class="rounded-2xl border border-line bg-surface p-5 shadow-soft transition hover:border-accent">
            <p class="text-sm font-semibold text-ink">إعدادات WhatsApp API</p>
            <p class="mt-1 text-xs text-muted">ربط القناة التشغيلية.</p>
        </a>
        @endif
        @if(Route::has('admin.email-broadcasts.index'))
        <a href="{{ route('admin.email-broadcasts.index', 'all_users') }}" class="rounded-2xl border border-line bg-surface p-5 shadow-soft transition hover:border-accent">
            <p class="text-sm font-semibold text-ink">بث البريد</p>
            <p class="mt-1 text-xs text-muted">حملات بريد جماعية.</p>
        </a>
        @endif
    </section>

    <section class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
        <h3 class="text-sm font-semibold text-ink">أحداث الطبقة المركزية</h3>
        <ul class="mt-3 grid gap-2 sm:grid-cols-2">
            @foreach($events as $event)
                <li class="rounded-xl border border-line px-3 py-2 text-sm text-ink">
                    <span class="font-medium">{{ $event }}</span>
                    @if(!empty($templates[$event]['overridden']))
                        <span class="mr-2 text-xs text-accent">معدّل</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>

    <section class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h3 class="text-sm font-semibold text-ink">سجل التسليمات</h3>
                <p class="mt-1 text-xs text-muted">آخر محاولات WhatsApp / Email / SMS / Push من طبقة الإشعارات.</p>
            </div>
            <p class="text-xs text-muted">{{ $recentDeliveries->count() }} سجل حديث</p>
        </div>
        @if($recentDeliveries->isEmpty())
            <p class="mt-4 text-sm text-muted">لا توجد تسليمات مسجّلة بعد.</p>
        @else
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-line text-right text-xs text-muted">
                            <th class="px-2 py-2 font-medium">الحدث</th>
                            <th class="px-2 py-2 font-medium">القناة</th>
                            <th class="px-2 py-2 font-medium">الحالة</th>
                            <th class="px-2 py-2 font-medium">المستلم</th>
                            <th class="px-2 py-2 font-medium">الوقت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentDeliveries as $delivery)
                            <tr class="border-b border-line/70">
                                <td class="px-2 py-2 text-ink">{{ $delivery->event_key }}</td>
                                <td class="px-2 py-2 text-ink">{{ $delivery->channel }}</td>
                                <td class="px-2 py-2">
                                    <span @class([
                                        'rounded-lg px-2 py-0.5 text-xs font-medium',
                                        'bg-emerald-50 text-emerald-800' => $delivery->status === 'delivered',
                                        'bg-amber-50 text-amber-800' => $delivery->status === 'skipped',
                                        'bg-rose-50 text-rose-800' => $delivery->status === 'failed',
                                    ])>{{ $delivery->status }}</span>
                                </td>
                                <td class="px-2 py-2 text-muted">{{ $delivery->recipient_name ?: ($delivery->recipient_email ?: $delivery->recipient_phone ?: '—') }}</td>
                                <td class="px-2 py-2 text-muted">{{ optional($delivery->sent_at ?: $delivery->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
@endsection
