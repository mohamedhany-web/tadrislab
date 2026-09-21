@extends('layouts.admin')

@section('title', 'أداء الموقع - ' . config('app.name'))
@section('page_title', 'أداء الموقع')

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                <i class="fas fa-tachometer-alt text-sm"></i>
            </div>
            <div>
                <p class="text-xs font-medium text-muted">النظام · الأداء</p>
                <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">لوحة أداء الموقع</h2>
                <p class="mt-1 text-sm text-muted">متابعة وتحسين أداء الموقع</p>
            </div>
        </div>
    </section>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">معلومات النظام</h3>
        </div>
        <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 sm:p-5 lg:grid-cols-3">
            <div class="flex items-center justify-between rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div>
                    <p class="text-xs font-medium text-muted">إصدار PHP</p>
                    <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-ink">{{ htmlspecialchars($systemInfo['php_version'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</p>
                </div>
                <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fab fa-php text-sm"></i>
                </div>
            </div>
            <div class="flex items-center justify-between rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div>
                    <p class="text-xs font-medium text-muted">إصدار Laravel</p>
                    <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-ink">{{ htmlspecialchars($systemInfo['laravel_version'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</p>
                </div>
                <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fab fa-laravel text-sm"></i>
                </div>
            </div>
            <div class="flex items-center justify-between rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <div>
                    <p class="text-xs font-medium text-muted">حد الذاكرة</p>
                    <p class="mt-1 text-2xl font-semibold tabular-nums tracking-tight text-ink">{{ htmlspecialchars($systemInfo['memory_limit'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</p>
                </div>
                <div class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                    <i class="fas fa-memory text-sm"></i>
                </div>
            </div>
        </div>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">معلومات الأداء</h3>
        </div>
        <div class="grid grid-cols-1 gap-4 p-4 sm:p-5 lg:grid-cols-2">
            <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <h4 class="mb-4 flex items-center gap-2 text-base font-semibold text-ink">
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-microchip text-sm"></i>
                    </span>
                    استخدام الذاكرة
                </h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between rounded-xl border border-line bg-canvas p-4">
                        <span class="text-sm font-medium text-ink-soft">الاستخدام الحالي:</span>
                        <span class="text-lg font-semibold tabular-nums text-ink">{{ htmlspecialchars($performanceInfo['memory_usage'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-line bg-canvas p-4">
                        <span class="text-sm font-medium text-ink-soft">الحد الأقصى:</span>
                        <span class="text-lg font-semibold tabular-nums text-ink">{{ htmlspecialchars($performanceInfo['memory_peak'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <h4 class="mb-4 flex items-center gap-2 text-base font-semibold text-ink">
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-hdd text-sm"></i>
                    </span>
                    مساحة القرص
                </h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between rounded-xl border border-line bg-canvas p-4">
                        <span class="text-sm font-medium text-ink-soft">المساحة المتاحة:</span>
                        <span class="text-lg font-semibold tabular-nums text-ink">{{ htmlspecialchars($performanceInfo['disk_free_space'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-line bg-canvas p-4">
                        <span class="text-sm font-medium text-ink-soft">إجمالي المساحة:</span>
                        <span class="text-lg font-semibold tabular-nums text-ink">{{ htmlspecialchars($performanceInfo['disk_total_space'] ?? 'N/A', ENT_QUOTES, 'UTF-8') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">إدارة الكاش</h3>
            <p class="mt-1 text-sm text-muted">إدارة وتنظيف الكاش لتحسين الأداء</p>
        </div>

        <div class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-4 sm:p-5">
            <div class="rounded-xl border border-line bg-canvas p-4">
                <div class="text-xs font-medium text-muted">كاش الإعدادات</div>
                <div class="mt-1 text-xl font-semibold tabular-nums text-ink">{{ htmlspecialchars($cacheSizes['config'] ?? '0 KB', ENT_QUOTES, 'UTF-8') }}</div>
            </div>
            <div class="rounded-xl border border-line bg-canvas p-4">
                <div class="text-xs font-medium text-muted">كاش المسارات</div>
                <div class="mt-1 text-xl font-semibold tabular-nums text-ink">{{ htmlspecialchars($cacheSizes['route'] ?? '0 KB', ENT_QUOTES, 'UTF-8') }}</div>
            </div>
            <div class="rounded-xl border border-line bg-canvas p-4">
                <div class="text-xs font-medium text-muted">كاش العروض</div>
                <div class="mt-1 text-xl font-semibold tabular-nums text-ink">{{ htmlspecialchars($cacheSizes['view'] ?? '0 KB', ENT_QUOTES, 'UTF-8') }}</div>
            </div>
            <div class="rounded-xl border border-line bg-canvas p-4">
                <div class="text-xs font-medium text-muted">كاش التطبيق</div>
                <div class="mt-1 text-xl font-semibold tabular-nums text-ink">{{ htmlspecialchars($cacheSizes['application'] ?? '0 KB', ENT_QUOTES, 'UTF-8') }}</div>
            </div>
        </div>

        <div class="border-t border-line px-4 py-4 sm:px-5 sm:py-5">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <button onclick="clearCache('config')" class="btn-press inline-flex items-center justify-center gap-2 rounded-xl bg-accent px-4 py-3 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-trash"></i>
                    مسح كاش الإعدادات
                </button>
                <button onclick="clearCache('route')" class="btn-press inline-flex items-center justify-center gap-2 rounded-xl bg-accent px-4 py-3 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-trash"></i>
                    مسح كاش المسارات
                </button>
                <button onclick="clearCache('view')" class="btn-press inline-flex items-center justify-center gap-2 rounded-xl bg-accent px-4 py-3 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-trash"></i>
                    مسح كاش العروض
                </button>
                <button onclick="clearCache('application')" class="btn-press inline-flex items-center justify-center gap-2 rounded-xl bg-accent px-4 py-3 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-trash"></i>
                    مسح كاش التطبيق
                </button>
                <button onclick="clearCache('all')" class="btn-press inline-flex items-center justify-center gap-2 rounded-xl border border-line px-4 py-3 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent">
                    <i class="fas fa-broom"></i>
                    مسح جميع الكاش
                </button>
                <button onclick="optimizeCache()" class="btn-press inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-medium text-white hover:bg-emerald-700">
                    <i class="fas fa-rocket"></i>
                    تحسين الأداء
                </button>
            </div>
        </div>
    </article>

    <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
        <div class="border-b border-line px-4 py-4 sm:px-5">
            <h3 class="text-base font-semibold text-ink">أدوات التحسين</h3>
        </div>
        <div class="grid grid-cols-1 gap-4 p-4 sm:p-5 lg:grid-cols-2">
            <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <h4 class="mb-4 flex items-center gap-2 text-base font-semibold text-ink">
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-file-archive text-sm"></i>
                    </span>
                    تنظيف الملفات المؤقتة
                </h4>
                <p class="mb-4 text-sm text-muted">
                    حذف الملفات المؤقتة القديمة لتحرير مساحة القرص
                </p>
                <button onclick="clearTempFiles()" class="btn-press inline-flex w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 py-3 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-broom"></i>
                    تنظيف الملفات المؤقتة
                </button>
            </div>

            <div class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
                <h4 class="mb-4 flex items-center gap-2 text-base font-semibold text-ink">
                    <span class="inline-flex size-9 items-center justify-center rounded-xl bg-[#f2f5f4] text-accent">
                        <i class="fas fa-database text-sm"></i>
                    </span>
                    تحسين قاعدة البيانات
                </h4>
                <p class="mb-4 text-sm text-muted">
                    تحسين الجداول وتحسين الأداء
                </p>
                <button onclick="optimizeDatabase()" class="btn-press inline-flex w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 py-3 text-sm font-medium text-white hover:bg-[#184888]">
                    <i class="fas fa-tools"></i>
                    تحسين قاعدة البيانات
                </button>
            </div>
        </div>
    </article>
</div>

@push('scripts')
<script>
let isSubmitting = false;

function clearCache(type) {
    if (isSubmitting) return;

    const sanitizedType = type.replace(/[^a-z_]/gi, '');
    if (!['config', 'route', 'view', 'application', 'compiled', 'all'].includes(sanitizedType)) {
        alert('نوع غير صالح');
        return;
    }

    if (confirm('هل أنت متأكد من مسح الكاش؟')) {
        isSubmitting = true;
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';

        fetch(`{{ route('admin.performance.clear-cache') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({ type: sanitizedType })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
                button.disabled = false;
                button.innerHTML = originalText;
                isSubmitting = false;
            }
        })
        .catch(error => {
            alert('حدث خطأ أثناء الاتصال');
            button.disabled = false;
            button.innerHTML = originalText;
            isSubmitting = false;
        });
    }
}

function optimizeCache() {
    if (isSubmitting) return;

    if (confirm('هل تريد تحسين الأداء بإنشاء الكاش؟')) {
        isSubmitting = true;
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحسين...';

        fetch(`{{ route('admin.performance.optimize-cache') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
                button.disabled = false;
                button.innerHTML = originalText;
                isSubmitting = false;
            }
        })
        .catch(error => {
            alert('حدث خطأ أثناء الاتصال');
            button.disabled = false;
            button.innerHTML = originalText;
            isSubmitting = false;
        });
    }
}

function clearTempFiles() {
    if (isSubmitting) return;

    if (confirm('هل تريد حذف الملفات المؤقتة؟')) {
        isSubmitting = true;
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التنظيف...';

        fetch(`{{ route('admin.performance.clear-temp-files') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            } else {
                alert('حدث خطأ: ' + data.message);
            }
            button.disabled = false;
            button.innerHTML = originalText;
            isSubmitting = false;
        })
        .catch(error => {
            alert('حدث خطأ أثناء الاتصال');
            button.disabled = false;
            button.innerHTML = originalText;
            isSubmitting = false;
        });
    }
}

function optimizeDatabase() {
    if (isSubmitting) return;

    if (confirm('هل تريد تحسين قاعدة البيانات؟ قد يستغرق هذا الأمر بعض الوقت.')) {
        isSubmitting = true;
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحسين...';

        fetch(`{{ route('admin.performance.optimize-database') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            } else {
                alert('حدث خطأ: ' + data.message);
            }
            button.disabled = false;
            button.innerHTML = originalText;
            isSubmitting = false;
        })
        .catch(error => {
            alert('حدث خطأ أثناء الاتصال');
            button.disabled = false;
            button.innerHTML = originalText;
            isSubmitting = false;
        });
    }
}
</script>
@endpush
@endsection
