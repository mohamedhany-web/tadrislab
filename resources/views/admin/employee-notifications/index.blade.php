@extends('layouts.admin')

@section('title', 'إشعارات الموظفين - ' . config('app.name'))
@section('page_title', 'إشعارات الموظفين')

@section('content')
@php
    $priorities = \App\Models\Notification::getPriorities();
    $priorityBadges = [
        'urgent' => 'border-rose-100 bg-rose-50 text-rose-700',
        'high' => 'border-amber-100 bg-amber-50 text-amber-700',
        'normal' => 'border-line bg-[#f2f5f4] text-accent',
        'low' => 'border-line bg-canvas text-muted',
    ];
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">الموارد البشرية · التواصل الداخلي</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إشعارات الموظفين</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">إرسال إشعارات مخصصة للموظفين ومتابعة حالة القراءة.</p>
        </div>
        <a href="{{ route('admin.employee-notifications.create') }}"
           class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
            <i class="fas fa-paper-plane text-xs"></i>
            إرسال إشعار جديد
        </a>
    </section>

    <section class="grid gap-3 sm:grid-cols-3">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                <i class="fas fa-bell text-sm"></i>
            </div>
            <p class="mt-3 text-xs font-medium text-muted">إجمالي الإشعارات</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-ink">{{ number_format($stats['total']) }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                <i class="fas fa-envelope-open-text text-sm"></i>
            </div>
            <p class="mt-3 text-xs font-medium text-muted">غير المقروء</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-ink">{{ number_format($stats['unread']) }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                <i class="fas fa-calendar-day text-sm"></i>
            </div>
            <p class="mt-3 text-xs font-medium text-muted">أُرسلت اليوم</p>
            <p class="mt-1 text-2xl font-semibold tabular-nums text-ink">{{ number_format($stats['today']) }}</p>
        </article>
    </section>

    <section class="rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-sm font-semibold text-ink">البحث والفلترة</h3>
        </div>
        <div class="p-4 sm:p-5">
            <form method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-muted">الحالة</label>
                    <select name="status" class="h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:ring-accent/20">
                        <option value="">جميع الحالات</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>غير مقروءة</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>مقروءة</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                        <i class="fas fa-filter text-xs"></i>
                        فلترة
                    </button>
                </div>
            </form>
        </div>
    </section>

    @if($notifications->count() > 0)
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-sm font-semibold text-ink">قائمة الإشعارات</h3>
                <p class="mt-0.5 text-xs text-muted">
                    <span class="font-semibold tabular-nums text-accent">{{ number_format($notifications->total()) }}</span>
                    إشعار
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-line bg-canvas text-xs text-muted">
                        <tr>
                            <th class="px-4 py-3 text-start font-medium">الإشعار</th>
                            <th class="px-4 py-3 text-start font-medium">المستلم</th>
                            <th class="px-4 py-3 text-start font-medium">الأولوية</th>
                            <th class="px-4 py-3 text-start font-medium">التاريخ</th>
                            <th class="px-4 py-3 text-end font-medium">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach($notifications as $notification)
                            @php
                                $priorityKey = $notification->priority ?? 'normal';
                                $priorityBadge = $priorityBadges[$priorityKey] ?? $priorityBadges['normal'];
                            @endphp
                            <tr class="hover:bg-canvas/60">
                                <td class="px-4 py-3">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <div class="inline-flex size-9 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                                            <i class="fas fa-bell text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="truncate font-semibold text-ink">{{ $notification->title }}</p>
                                                @if(!$notification->is_read)
                                                    <span class="rounded-lg border border-line bg-accent-soft px-2 py-0.5 text-[10px] font-semibold text-accent">غير مقروء</span>
                                                @endif
                                            </div>
                                            <p class="mt-1 line-clamp-2 text-xs text-muted">{{ Str::limit($notification->message, 150) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 text-xs text-muted">
                                        <i class="fas fa-user text-accent"></i>
                                        {{ $notification->user->name ?? 'غير محدد' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-lg border px-2.5 py-1 text-[11px] font-semibold {{ $priorityBadge }}">
                                        {{ $priorities[$notification->priority] ?? $notification->priority }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs tabular-nums text-muted">{{ $notification->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('admin.employee-notifications.show', $notification) }}"
                                       class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent">
                                        <i class="fas fa-eye text-xs"></i>
                                        عرض
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-line px-4 py-4 sm:px-5">
                {{ $notifications->links() }}
            </div>
        </article>
    @else
        <section class="rounded-2xl border border-line bg-surface p-16 text-center shadow-soft">
            <div class="flex flex-col items-center gap-4">
                <div class="inline-flex size-12 items-center justify-center rounded-2xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-bell text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-ink">لا توجد إشعارات</p>
                    <p class="mt-1 text-xs text-muted">لم يتم إرسال أي إشعارات للموظفين بعد</p>
                </div>
                <a href="{{ route('admin.employee-notifications.create') }}"
                   class="btn-press mt-2 inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-paper-plane text-xs"></i>
                    إرسال إشعار جديد
                </a>
            </div>
        </section>
    @endif
</div>
@endsection
