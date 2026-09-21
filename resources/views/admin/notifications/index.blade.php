@extends('layouts.admin')

@section('title', 'إدارة الإشعارات - ' . config('app.name'))
@section('page_title', 'إدارة الإشعارات')

@section('content')
@php
    $summaryCards = [
        [
            'label' => 'إجمالي الإشعارات',
            'value' => number_format($stats['total']),
            'icon' => 'fas fa-bell',
        ],
        [
            'label' => 'غير المقروء',
            'value' => number_format($stats['unread']),
            'icon' => 'fas fa-envelope-open-text',
        ],
        [
            'label' => 'مقروء',
            'value' => number_format($stats['total'] - $stats['unread']),
            'icon' => 'fas fa-check-circle',
        ],
        [
            'label' => 'أُرسلت اليوم',
            'value' => number_format($stats['today']),
            'icon' => 'fas fa-calendar-day',
        ],
    ];
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">التنبيهات والرسائل · إرسال ومتابعة</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">مركز الإشعارات</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">
                إرسال التنبيهات للطلاب ومتابعة حالة القراءة للمستلمين. تنبيهات تذاكر الدعم وغيرها من الرسائل الموجهة لك تظهر في
                <a href="{{ route('admin.notifications.inbox') }}" class="font-semibold text-accent hover:underline">وارد الإشعارات</a>.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.notifications.statistics') }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-chart-line text-xs"></i>
                الإحصائيات
            </a>
            <a href="{{ route('admin.notifications.create') }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-paper-plane text-xs"></i>
                إرسال إشعار جديد
            </a>
        </div>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($summaryCards as $card)
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="{{ $card['icon'] }} text-sm"></i>
                </div>
                <p class="mt-3 text-xs font-medium text-muted">{{ $card['label'] }}</p>
                <p class="mt-1 text-2xl font-semibold tabular-nums text-ink">{{ $card['value'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-sm font-semibold text-ink">البحث والفلترة</h3>
            <p class="mt-0.5 text-xs text-muted">تصفية الإشعارات حسب النوع والحالة أو البحث داخل العناوين.</p>
        </div>
        <div class="p-4 sm:p-5">
            <form method="GET" id="filterForm" class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-muted">البحث</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 start-3 flex items-center text-muted"><i class="fas fa-search text-xs"></i></span>
                        <input type="text" name="search" value="{{ old('search', request('search')) }}" maxlength="255" placeholder="عنوان الإشعار أو محتواه"
                               class="h-11 w-full rounded-xl border border-line bg-surface ps-9 pe-4 text-sm text-ink focus:border-accent focus:ring-accent/20" />
                    </div>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-muted">نوع الإشعار</label>
                    <select name="type" class="h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:ring-accent/20">
                        <option value="">جميع الأنواع</option>
                        @foreach ($notificationTypes as $key => $type)
                            <option value="{{ htmlspecialchars($key, ENT_QUOTES, 'UTF-8') }}" {{ request('type') == $key ? 'selected' : '' }}>{{ htmlspecialchars($type, ENT_QUOTES, 'UTF-8') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-muted">الحالة</label>
                    <select name="status" class="h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:ring-accent/20">
                        <option value="">جميع الحالات</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>مقروءة</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>غير مقروءة</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-press inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                        <i class="fas fa-filter text-xs"></i>
                        تطبيق الفلاتر
                    </button>
                    @if(request()->anyFilled(['search', 'type', 'status']))
                        <a href="{{ route('admin.notifications.index') }}" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-line text-muted hover:bg-canvas" title="مسح الفلتر">
                            <i class="fas fa-times text-xs"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-col gap-3 border-b border-line px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div>
                <h3 class="text-sm font-semibold text-ink">الإشعارات المرسلة</h3>
                <p class="mt-0.5 text-xs text-muted">
                    <span class="font-semibold tabular-nums text-accent">{{ number_format($notifications->total()) }}</span>
                    إشعار تم إرساله حتى الآن.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="markAllAsRead()"
                        class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent">
                    <i class="fas fa-check-double text-xs"></i>
                    تحديد الكل كمقروء
                </button>
                <button type="button" onclick="cleanupOld()"
                        class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-rose-200 px-4 text-sm font-medium text-rose-700 hover:bg-rose-50">
                    <i class="fas fa-trash-alt text-xs"></i>
                    حذف القديمة
                </button>
            </div>
        </div>

        @if ($notifications->count() > 0)
            <div class="divide-y divide-line">
                @foreach ($notifications as $notification)
                    @php
                        $typeColor = match($notification->type_color ?? '') {
                            'blue' => 'bg-[#f2f5f4] text-accent',
                            'green' => 'bg-emerald-50 text-emerald-700',
                            'yellow' => 'bg-amber-50 text-amber-700',
                            'red' => 'bg-rose-50 text-rose-700',
                            'purple' => 'bg-canvas text-muted',
                            'orange' => 'bg-orange-50 text-orange-700',
                            default => 'bg-canvas text-muted'
                        };
                        $priorityClasses = match($notification->priority ?? '') {
                            'urgent' => 'bg-rose-50 text-rose-700 border border-rose-100',
                            'high' => 'bg-amber-50 text-amber-700 border border-amber-100',
                            'low' => 'bg-canvas text-muted border border-line',
                            default => 'bg-[#f2f5f4] text-accent border border-line'
                        };
                    @endphp
                    <div class="p-4 transition hover:bg-canvas/60 sm:p-5 {{ $notification->is_read ? 'opacity-75' : '' }}">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="flex flex-1 items-start gap-4">
                                <div class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl {{ $typeColor }}">
                                    <i class="{{ $notification->type_icon ?? 'fas fa-bell' }} text-sm"></i>
                                </div>
                                <div class="min-w-0 flex-1 space-y-2">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                                        <h4 class="text-sm font-semibold text-ink">{{ htmlspecialchars($notification->title, ENT_QUOTES, 'UTF-8') }}</h4>
                                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-[11px] font-semibold {{ $priorityClasses }}">
                                            <span class="size-1.5 rounded-full bg-current"></span>
                                            {{ htmlspecialchars(\App\Models\Notification::getPriorities()[$notification->priority] ?? $notification->priority, ENT_QUOTES, 'UTF-8') }}
                                        </span>
                                        @unless ($notification->is_read)
                                            <span class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-accent-soft px-2.5 py-1 text-[11px] font-semibold text-accent">
                                                <span class="size-1.5 rounded-full bg-current"></span>
                                                جديد
                                            </span>
                                        @endunless
                                    </div>
                                    <p class="text-sm leading-relaxed text-muted">{{ htmlspecialchars(Str::limit($notification->message, 200), ENT_QUOTES, 'UTF-8') }}</p>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-muted">
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-user text-accent"></i>
                                            {{ htmlspecialchars($notification->user->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-tag text-accent"></i>
                                            {{ htmlspecialchars(\App\Models\Notification::getTypes()[$notification->type] ?? $notification->type, ENT_QUOTES, 'UTF-8') }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-clock text-accent"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                        @if ($notification->target_type !== 'individual')
                                            <span class="inline-flex items-center gap-1.5">
                                                <i class="fas fa-users text-accent"></i>
                                                {{ htmlspecialchars(\App\Models\Notification::getTargetTypes()[$notification->target_type] ?? $notification->target_type, ENT_QUOTES, 'UTF-8') }}
                                            </span>
                                        @endif
                                        @if ($notification->action_url)
                                            <span class="inline-flex items-center gap-1.5 text-accent">
                                                <i class="fas fa-link"></i>
                                                {{ htmlspecialchars($notification->action_text ?: 'رابط مرتبط', ENT_QUOTES, 'UTF-8') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex shrink-0 items-center gap-2 self-end lg:self-start">
                                <a href="{{ route('admin.notifications.show', $notification) }}"
                                   class="inline-flex size-9 items-center justify-center rounded-xl border border-line text-accent hover:bg-accent-soft hover:text-accent"
                                   title="عرض التفاصيل">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="delete-form" onsubmit="return confirmDelete(event);">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex size-9 items-center justify-center rounded-xl border border-rose-200 text-rose-700 hover:bg-rose-50"
                                            title="حذف">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($notifications->hasPages())
                <div class="border-t border-line px-4 py-4 sm:px-5">
                    {{ $notifications->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="px-4 py-16 text-center sm:px-5">
                <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-2xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-bell-slash text-lg"></i>
                </div>
                <p class="text-sm font-semibold text-ink">لا توجد إشعارات</p>
                <p class="mt-1 text-xs text-muted">لا توجد إشعارات مطابقة للبحث الحالي.</p>
            </div>
        @endif
    </article>

    <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft xl:col-span-2">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-sm font-semibold text-ink">إرسال سريع</h3>
                <p class="mt-0.5 text-xs text-muted">أرسل تنبيهاً عاماً لجميع الطلاب بخطوات بسيطة.</p>
            </div>
            <div class="p-4 sm:p-5">
                <form id="quick-send-form" class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-muted">العنوان</label>
                        <input type="text" id="quick_title" name="title" required maxlength="255"
                               class="h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:ring-accent/20"
                               placeholder="عنوان الإشعار" />
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-muted">المستهدفون</label>
                        <select id="quick_target" name="target_type" required onchange="updateQuickTargetCount()"
                                class="h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:ring-accent/20">
                            <option value="">اختر المستهدفين</option>
                            <option value="all_students">جميع الطلاب</option>
                        </select>
                        <p class="mt-2 text-xs text-muted">سيتم الإرسال إلى <span id="quick-target-count" class="font-semibold text-accent">0</span> معلم.</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-xs font-medium text-muted">نص الإشعار</label>
                        <textarea id="quick_message" name="message" rows="3" required maxlength="1000"
                                  class="w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm leading-relaxed text-ink focus:border-accent focus:ring-accent/20 resize-none"
                                  placeholder="اكتب رسالة مختصرة ومباشرة..."></textarea>
                    </div>
                    <div class="flex items-center justify-end md:col-span-2">
                        <button type="button" onclick="sendQuickNotification()" id="quick-send-btn"
                                class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white hover:bg-[#184888] disabled:cursor-not-allowed disabled:opacity-50">
                            <i class="fas fa-paper-plane text-xs"></i>
                            إرسال سريع
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-4 py-4 sm:px-5">
                <h3 class="text-sm font-semibold text-ink">توزيع حسب النوع</h3>
                <p class="mt-0.5 text-xs text-muted">مؤشر سريع لأكثر أنواع الإشعارات استخداماً.</p>
            </div>
            <div class="p-4 sm:p-5">
                @if ($stats['by_type']->count() > 0)
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($stats['by_type'] as $type => $count)
                            <div class="rounded-xl border border-line bg-canvas p-4 text-center">
                                <p class="text-xl font-semibold tabular-nums text-ink">{{ $count }}</p>
                                <p class="mt-1 text-xs text-muted">{{ htmlspecialchars($notificationTypes[$type] ?? $type, ENT_QUOTES, 'UTF-8') }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="py-4 text-center text-sm text-muted">لم يتم تجميع بيانات كافية بعد.</p>
                @endif
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    // حماية من Double Submit
    let quickSendSubmitting = false;
    let actionSubmitting = false;

    // حماية من XSS - تنقية البيانات
    function sanitizeInput(input) {
        if (!input) return '';
        const div = document.createElement('div');
        div.textContent = input;
        return div.innerHTML.replace(/[<>]/g, '');
    }

    function updateQuickTargetCount() {
        const targetType = document.getElementById('quick_target')?.value;
        const targetId = null;
        if (targetType && targetType.trim()) {
            // حماية من SQL Injection في URL
            const safeTargetType = encodeURIComponent(targetType.trim());
            fetch(`{{ route('admin.notifications.target-count') }}?target_type=${safeTargetType}&target_id=${targetId || ''}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    const countEl = document.getElementById('quick-target-count');
                    if (countEl) {
                        countEl.textContent = parseInt(data.count) || 0;
                    }
                })
                .catch(() => {
                    const countEl = document.getElementById('quick-target-count');
                    if (countEl) {
                        countEl.textContent = 0;
                    }
                });
        } else {
            const countEl = document.getElementById('quick-target-count');
            if (countEl) {
                countEl.textContent = 0;
            }
        }
    }

    function sendQuickNotification() {
        if (quickSendSubmitting) {
            return false;
        }

        const form = document.getElementById('quick-send-form');
        if (!form) return;

        const titleEl = document.getElementById('quick_title');
        const messageEl = document.getElementById('quick_message');
        const targetEl = document.getElementById('quick_target');
        const sendBtn = document.getElementById('quick-send-btn');

        if (!titleEl || !messageEl || !targetEl) return;

        // Sanitization - تنقية البيانات
        const title = sanitizeInput(titleEl.value.trim());
        const message = sanitizeInput(messageEl.value.trim());
        const targetType = targetEl.value.trim();

        if (!title || !message || !targetType) {
            alert('يرجى ملء جميع الحقول المطلوبة');
            return false;
        }

        // التحقق من الأطوال
        if (title.length > 255) {
            alert('العنوان يجب ألا يتجاوز 255 حرف');
            return false;
        }

        if (message.length > 1000) {
            alert('النص يجب ألا يتجاوز 1000 حرف');
            return false;
        }

        const payload = {
            title: title,
            message: message,
            target_type: targetType,
            target_id: null,
        };

        quickSendSubmitting = true;
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch('{{ route("admin.notifications.quick-send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message || 'تم إرسال الإشعار بنجاح');
                form.reset();
                const countEl = document.getElementById('quick-target-count');
                if (countEl) countEl.textContent = 0;
                window.location.reload();
            } else {
                alert(data.message || 'حدث خطأ في إرسال الإشعار');
                quickSendSubmitting = false;
                if (sendBtn) {
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال سريع';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في إرسال الإشعار. يرجى المحاولة مرة أخرى.');
            quickSendSubmitting = false;
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال سريع';
            }
        });
    }

    function markAllAsRead() {
        if (actionSubmitting) return false;
        if (!confirm('هل تريد تحديد جميع الإشعارات كمقروءة؟')) return false;

        actionSubmitting = true;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch('{{ route("admin.notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message || 'تم التحديث بنجاح');
                window.location.reload();
            } else {
                actionSubmitting = false;
            }
        })
        .catch(() => {
            alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
            actionSubmitting = false;
        });
    }

    function cleanupOld() {
        if (actionSubmitting) return false;
        if (!confirm('هل تريد حذف الإشعارات المقروءة الأقدم من 30 يوم؟')) return false;

        actionSubmitting = true;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch('{{ route("admin.notifications.cleanup") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ days: 30 }),
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message || 'تم الحذف بنجاح');
                window.location.reload();
            } else {
                actionSubmitting = false;
            }
        })
        .catch(() => {
            alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
            actionSubmitting = false;
        });
    }

    function confirmDelete(event) {
        if (actionSubmitting) {
            event.preventDefault();
            return false;
        }
        const confirmed = confirm('هل أنت متأكد من حذف هذا الإشعار؟');
        if (confirmed) {
            actionSubmitting = true;
            const btn = event.target.closest('form').querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
        }
        return confirmed;
    }

    // حماية من XSS في بيانات البحث
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.value = searchInput.value.replace(/<[^>]*>/g, '').trim();
            }
        });
    }

    // منع الإرسال المتكرر
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (actionSubmitting) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endpush
@endsection
