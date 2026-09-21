@extends('layouts.admin')

@section('title', 'سجل النشاطات - ' . config('app.name'))
@section('page_title', 'سجل النشاطات')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition placeholder:text-muted focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $statCards = [
        [
            'label' => 'إجمالي النشاطات',
            'value' => number_format($stats['total'] ?? 0),
            'icon' => 'fas fa-history',
            'description' => 'جميع النشاطات المسجلة',
        ],
        [
            'label' => 'نشاطات اليوم',
            'value' => number_format($stats['today'] ?? 0),
            'icon' => 'fas fa-calendar-day',
            'description' => 'تم تسجيلها اليوم',
        ],
        [
            'label' => 'هذا الأسبوع',
            'value' => number_format($stats['this_week'] ?? 0),
            'icon' => 'fas fa-calendar-week',
            'description' => 'خلال الأسبوع الحالي',
        ],
        [
            'label' => 'هذا الشهر',
            'value' => number_format($stats['this_month'] ?? 0),
            'icon' => 'fas fa-calendar-alt',
            'description' => 'خلال الشهر الحالي',
        ],
    ];
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">النظام · سجل النشاطات</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">سجل النشاطات</h2>
            <p class="mt-1 text-sm text-muted">راقب كل العمليات التي تمت داخل المنصة</p>
        </div>
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button @click="open = !open"
                    class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-rose-600 px-4 text-sm font-medium text-white hover:bg-rose-700">
                <i class="fas fa-trash text-xs"></i>
                <span>مسح السجلات</span>
                <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 x-cloak
                 class="absolute left-0 z-50 mt-2 w-64 overflow-hidden rounded-2xl border border-line bg-surface shadow-soft"
                 style="display: none;">
                <div class="p-2">
                    <button onclick="clearActivityLog('filtered')"
                            class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-right text-sm text-ink transition-colors hover:bg-rose-50">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-filter text-muted"></i>
                            مسح المطابقة للفلتر
                        </span>
                        <i class="fas fa-chevron-left text-xs text-muted"></i>
                    </button>
                    <button onclick="clearActivityLog('old')"
                            class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-right text-sm text-ink transition-colors hover:bg-rose-50">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-calendar text-muted"></i>
                            مسح أقدم من 3 أشهر
                        </span>
                        <i class="fas fa-chevron-left text-xs text-muted"></i>
                    </button>
                    <button onclick="clearActivityLog('older')"
                            class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-right text-sm text-ink transition-colors hover:bg-rose-50">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-muted"></i>
                            مسح أقدم من 6 أشهر
                        </span>
                        <i class="fas fa-chevron-left text-xs text-muted"></i>
                    </button>
                    <hr class="my-2 border-line">
                    <button onclick="clearActivityLog('all')"
                            class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-right text-sm font-semibold text-rose-600 transition-colors hover:bg-rose-50">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-rose-500"></i>
                            مسح جميع السجلات
                        </span>
                        <i class="fas fa-chevron-left text-xs text-rose-400"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    @if(isset($stats))
    <section class="admin-kpi-grid grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($statCards as $card)
            <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="{{ $card['icon'] }} text-sm"></i>
                </div>
                <p class="mt-3 text-xs text-muted">{{ $card['label'] }}</p>
                <p class="mt-1 text-xl font-semibold tabular-nums tracking-tight text-ink">{{ $card['value'] }}</p>
                <p class="mt-1 text-[11px] text-muted">{{ $card['description'] }}</p>
            </article>
        @endforeach
    </section>
    @endif

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">فلترة وبحث النشاطات</h3>
            <p class="mt-0.5 text-xs text-muted">ابحث حسب النوع أو نطاق التاريخ</p>
        </div>
        <form method="GET" class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-2 xl:grid-cols-5 xl:items-end">
            <div>
                <label class="{{ $labelClass }}" for="search">البحث</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 start-3 flex items-center text-muted">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input id="search" type="text" name="search" value="{{ request('search') }}"
                           placeholder="البحث في النشاطات..."
                           class="{{ $fieldClass }} ps-9">
                </div>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="type">نوع النشاط</label>
                <select id="type" name="type" class="{{ $fieldClass }}">
                    <option value="">جميع الأنواع</option>
                    <option value="create" {{ request('type') == 'create' ? 'selected' : '' }}>إنشاء</option>
                    <option value="update" {{ request('type') == 'update' ? 'selected' : '' }}>تحديث</option>
                    <option value="delete" {{ request('type') == 'delete' ? 'selected' : '' }}>حذف</option>
                    <option value="login" {{ request('type') == 'login' ? 'selected' : '' }}>تسجيل دخول</option>
                    <option value="logout" {{ request('type') == 'logout' ? 'selected' : '' }}>تسجيل خروج</option>
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
                @if(request()->anyFilled(['search', 'type', 'date_from', 'date_to']))
                <a href="{{ route('admin.activity-log') }}"
                   class="btn-press inline-flex h-11 items-center justify-center rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent"
                   title="مسح الفلتر">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
            <div class="min-w-0">
                <h3 class="text-base font-semibold text-ink">العمليات المسجلة</h3>
                <p class="mt-0.5 text-xs text-muted">
                    <span class="font-semibold text-accent">{{ $activities->total() }}</span> عملية تم تسجيلها
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs text-muted">
                <i class="fas fa-clock"></i>
                <span>آخر تحديث: {{ now()->format('H:i') }}</span>
            </div>
        </div>

        @if ($activities->count() > 0)
            <div class="admin-table-wrap overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b border-line bg-canvas text-xs text-muted">
                        <tr>
                            <th class="px-4 py-3 text-start font-medium">المستخدم</th>
                            <th class="px-4 py-3 text-start font-medium">النوع</th>
                            <th class="px-4 py-3 text-start font-medium">الوصف</th>
                            <th class="px-4 py-3 text-start font-medium">الوقت</th>
                            <th class="px-4 py-3 text-center font-medium">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($activities as $activity)
                            <tr class="hover:bg-canvas/40">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-[#f2f5f4] text-sm font-bold text-accent">
                                            {{ mb_substr($activity->user->name ?? 'غ', 0, 1, 'UTF-8') }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-ink">{{ $activity->user->name ?? 'مستخدم غير معروف' }}</p>
                                            <p class="text-[11px] text-muted">{{ $activity->user->email ?? '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $actionType = $activity->action;
                                        $isCreate = str_contains($actionType, 'create') || str_contains($actionType, 'created');
                                        $isUpdate = str_contains($actionType, 'update') || str_contains($actionType, 'updated') || str_contains($actionType, 'changed');
                                        $isDelete = str_contains($actionType, 'delete') || str_contains($actionType, 'deleted');
                                        $isLogin = str_contains($actionType, 'login') && !str_contains($actionType, 'logout');
                                        $isLogout = str_contains($actionType, 'logout');

                                        if ($isCreate) {
                                            $badgeClasses = 'bg-emerald-50 text-emerald-700';
                                            $typeLabel = 'إنشاء';
                                        } elseif ($isUpdate) {
                                            $badgeClasses = 'bg-accent-soft text-accent';
                                            $typeLabel = 'تحديث';
                                        } elseif ($isDelete) {
                                            $badgeClasses = 'bg-rose-50 text-rose-700';
                                            $typeLabel = 'حذف';
                                        } elseif ($isLogin) {
                                            $badgeClasses = 'bg-[#f2f5f4] text-accent';
                                            $typeLabel = 'تسجيل دخول';
                                        } elseif ($isLogout) {
                                            $badgeClasses = 'bg-canvas-muted text-muted';
                                            $typeLabel = 'تسجيل خروج';
                                        } else {
                                            $badgeClasses = 'bg-amber-50 text-amber-700';
                                            $typeLabel = 'نشاط آخر';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses }}">
                                        <span class="size-1.5 rounded-full bg-current"></span>
                                        {{ $typeLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-ink">
                                        {{ $activity->description ?: ($activity->action_description ?? $activity->action) }}
                                    </p>
                                    @if ($activity->model_type && $activity->model_id)
                                        <p class="mt-1 text-[11px] text-muted">
                                            <i class="fas fa-link text-[10px]"></i>
                                            {{ class_basename($activity->model_type) }} #{{ $activity->model_id }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-muted">
                                    <p class="font-medium text-ink">{{ $activity->created_at->format('Y-m-d') }}</p>
                                    <p>{{ $activity->created_at->format('H:i:s') }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.activity-log.show', $activity) }}"
                                       class="btn-press inline-flex h-8 items-center gap-2 rounded-lg border border-line px-3 text-xs font-medium text-ink hover:bg-accent-soft hover:text-accent">
                                        <i class="fas fa-eye"></i>
                                        عرض التفاصيل
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($activities->hasPages())
                <div class="border-t border-line px-4 py-3 sm:px-5">
                    {{ $activities->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="px-4 py-12 text-center sm:px-5">
                <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-2xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-history text-2xl"></i>
                </div>
                <h3 class="text-base font-semibold text-ink">لا توجد نشاطات</h3>
                <p class="mx-auto mt-1 max-w-md text-sm text-muted">لا توجد نشاطات مطابقة للمعايير الحالية</p>
            </div>
        @endif
    </article>
</div>

@push('scripts')
<script>
function clearActivityLog(type) {
    let confirmMessage = '';

    switch(type) {
        case 'all':
            confirmMessage = 'هل أنت متأكد من مسح جميع السجلات؟\n\n⚠️ تحذير: هذا الإجراء لا يمكن التراجع عنه!';
            break;
        case 'old':
            confirmMessage = 'هل أنت متأكد من مسح السجلات الأقدم من 3 أشهر؟';
            break;
        case 'older':
            confirmMessage = 'هل أنت متأكد من مسح السجلات الأقدم من 6 أشهر؟';
            break;
        case 'filtered':
            confirmMessage = 'هل أنت متأكد من مسح السجلات المطابقة للفلتر الحالي؟';
            break;
    }

    if (confirm(confirmMessage)) {
        const loadingToast = document.createElement('div');
        loadingToast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-accent text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2';
        loadingToast.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري مسح السجلات...';
        document.body.appendChild(loadingToast);

        const formData = new FormData();
        formData.append('delete_type', type);
        formData.append('_token', '{{ csrf_token() }}');

        if (type === 'filtered') {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('type')) formData.append('type', urlParams.get('type'));
            if (urlParams.has('user_id')) formData.append('user_id', urlParams.get('user_id'));
            if (urlParams.has('date_from')) formData.append('date_from', urlParams.get('date_from'));
            if (urlParams.has('date_to')) formData.append('date_to', urlParams.get('date_to'));
            if (urlParams.has('search')) formData.append('search', urlParams.get('search'));
        }

        fetch('{{ route('admin.activity-log.destroy') }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.body.removeChild(loadingToast);

            if (data.success) {
                const successToast = document.createElement('div');
                successToast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-emerald-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2';
                successToast.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                document.body.appendChild(successToast);

                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                const errorToast = document.createElement('div');
                errorToast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-rose-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2';
                errorToast.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
                document.body.appendChild(errorToast);

                setTimeout(() => {
                    if (document.body.contains(errorToast)) {
                        document.body.removeChild(errorToast);
                    }
                }, 5000);
            }
        })
        .catch(error => {
            document.body.removeChild(loadingToast);

            const errorToast = document.createElement('div');
            errorToast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-rose-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2';
            errorToast.innerHTML = '<i class="fas fa-exclamation-circle"></i> حدث خطأ: ' + error.message;
            document.body.appendChild(errorToast);

            setTimeout(() => {
                if (document.body.contains(errorToast)) {
                    document.body.removeChild(errorToast);
                }
            }, 5000);
        });
    }
}
</script>
@endpush
@endsection
