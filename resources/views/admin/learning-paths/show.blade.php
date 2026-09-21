@extends('layouts.admin')

@section('title', $path->title_ar)
@section('page_title', $path->title_ar)

@section('content')
@php
    $field = 'h-10 w-full rounded-xl border border-line bg-surface px-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $area = 'w-full rounded-xl border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $practiceLabels = [
        'tool' => 'أداة',
        'application' => 'تطبيق عملي',
        'template' => 'قالب',
        'checklist' => 'قائمة تحقق',
        'activity' => 'نشاط',
        'assessment' => 'تقييم',
    ];
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs text-muted"><a href="{{ route('admin.learning-paths.index') }}" class="hover:text-accent">المسارات التعليمية</a></p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">{{ $path->title_ar }}</h2>
            <p class="mt-1 text-sm text-muted">
                هيكل: مسار → وحدات → دروس/محتوى → أدوات / نشاط / تقييم · Progress &amp; Completion
                @if($path->skill_focus_ar) · مهارة: <span class="font-medium text-ink">{{ $path->skill_focus_ar }}</span>@endif
            </p>
            <p class="mt-1 text-xs text-muted">
                @if($path->is_sellable_standalone)
                    قابل للبيع منفردًا
                    @if($path->price !== null) · {{ number_format((float) $path->price, 2) }} {{ $path->currency ?: 'QAR' }}@endif
                    @if($path->access_days) · {{ $path->access_days }} يوم وصول@endif
                @else
                    غير قابل للبيع منفردًا (يُضمَّن عبر الباقات)
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if($path->is_published)
                <a href="{{ route('public.learning-paths.show', $path->slug) }}" target="_blank" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm text-ink-soft">عرض عام</a>
            @endif
            <a href="{{ route('admin.learning-paths.edit', $path) }}" class="btn-press inline-flex h-9 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">تعديل البيانات</a>
        </div>
    </section>

    @if(session('success'))
        <div class="rounded-2xl border border-line bg-surface px-4 py-3 text-sm font-medium text-ink shadow-soft">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-2xl border border-danger/20 bg-danger/5 p-4 text-sm text-danger">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-2">
        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <h3 class="text-base font-semibold text-ink">الباقات المرتبطة</h3>
            <p class="mt-1 text-xs text-muted mb-3">يمكن بيع المسار منفردًا أو تضمينه داخل باقة.</p>
            @if($path->packages->isEmpty())
                <p class="text-sm text-muted">لا باقات مرتبطة — عدّل المسار أو اربط من صفحة الباقة.</p>
            @else
                <ul class="space-y-1">
                    @foreach($path->packages as $package)
                        <li>
                            <a href="{{ route('admin.packages.show', $package) }}" class="text-sm font-medium text-accent hover:underline">{{ $package->name }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <h3 class="text-base font-semibold text-ink">تفعيل للمعلم (منفرد)</h3>
            <p class="mt-1 text-xs text-muted mb-3">تفعيل يدوي لمسار منفرد — دون انتظار الدفع الآلي.</p>
            <form method="POST" action="{{ route('admin.learning-paths.activate-learner', $path) }}" class="flex flex-wrap items-end gap-3">
                @csrf
                <div class="min-w-[12rem] flex-1">
                    <label class="mb-1 block text-xs text-muted">المعلم</label>
                    <select name="user_id" required class="{{ $field }}">
                        <option value="">— اختر —</option>
                        @foreach($learners as $learner)
                            <option value="{{ $learner->id }}">{{ $learner->name }} ({{ $learner->email }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-press inline-flex h-10 items-center rounded-xl bg-accent px-4 text-sm font-medium text-white">تفعيل الوصول</button>
            </form>
        </article>
    </div>

    <article class="rounded-2xl border border-accent/20 bg-accent-soft/20 p-5 shadow-soft">
        <h3 class="text-base font-semibold text-ink">إضافة وحدة قصيرة</h3>
        <p class="mt-1 text-xs text-muted mb-3">كل وحدة تغطي مهارة فرعية — أضف الدروس والأدوات/الأنشطة داخلها.</p>
        <form method="POST" action="{{ route('admin.learning-paths.units.store', $path) }}" class="grid gap-3 sm:grid-cols-12 items-end">
            @csrf
            <div class="sm:col-span-4">
                <label class="mb-1 block text-xs text-muted">عنوان الوحدة *</label>
                <input type="text" name="title_ar" required class="{{ $field }}" placeholder="مثال: قواعد الصف الواضحة">
            </div>
            <div class="sm:col-span-3">
                <label class="mb-1 block text-xs text-muted">ملخص</label>
                <input type="text" name="summary_ar" class="{{ $field }}">
            </div>
            <div class="sm:col-span-2">
                <label class="mb-1 block text-xs text-muted">دقائق تقديرية</label>
                <input type="number" name="estimated_minutes" min="1" class="{{ $field }}">
            </div>
            <div class="sm:col-span-1">
                <label class="mb-1 block text-xs text-muted">ترتيب</label>
                <input type="number" name="sort_order" min="0" class="{{ $field }}">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="btn-press inline-flex h-10 w-full items-center justify-center rounded-xl bg-accent text-sm font-medium text-white">إضافة وحدة</button>
            </div>
        </form>
    </article>

    @forelse($path->units as $unit)
        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-line px-4 py-4 sm:px-5">
                <div>
                    <p class="text-xs font-semibold text-accent">وحدة {{ $loop->iteration }} · ترتيب {{ $unit->sort_order }}</p>
                    <h3 class="text-lg font-semibold text-ink">{{ $unit->title_ar }}</h3>
                    @if($unit->summary_ar)<p class="mt-0.5 text-sm text-muted">{{ $unit->summary_ar }}</p>@endif
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <form method="POST" action="{{ route('admin.learning-paths.units.reorder', $unit) }}">
                        @csrf
                        <input type="hidden" name="direction" value="up">
                        <button type="submit" class="rounded-lg border border-line px-2 py-1 text-xs text-ink-soft" title="أعلى">↑</button>
                    </form>
                    <form method="POST" action="{{ route('admin.learning-paths.units.reorder', $unit) }}">
                        @csrf
                        <input type="hidden" name="direction" value="down">
                        <button type="submit" class="rounded-lg border border-line px-2 py-1 text-xs text-ink-soft" title="أسفل">↓</button>
                    </form>
                    <form method="POST" action="{{ route('admin.learning-paths.units.destroy', $unit) }}" onsubmit="return confirm('حذف الوحدة وكل دروسها وتطبيقاتها؟');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-semibold text-rose-600">حذف الوحدة</button>
                    </form>
                </div>
            </div>

            <div class="grid gap-5 p-4 sm:p-5 lg:grid-cols-2">
                <div>
                    <h4 class="mb-2 text-sm font-semibold text-ink">دروس / محتوى ({{ $unit->lessons->count() }})</h4>
                    <ul class="mb-3 space-y-2">
                        @forelse($unit->lessons as $lesson)
                            <li class="rounded-xl border border-line px-3 py-2" x-data="{ edit: false }">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-medium text-ink">{{ $lesson->title_ar }}</p>
                                        <p class="text-xs text-muted">{{ $lesson->content_type }}@if($lesson->is_preview) · معاينة@endif @if($lesson->body_ar)· محتوى محفوظ@endif</p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="edit = !edit" class="text-[11px] font-semibold text-accent">تعديل</button>
                                        <form method="POST" action="{{ route('admin.learning-paths.lessons.reorder', $lesson) }}">
                                            @csrf
                                            <input type="hidden" name="direction" value="up">
                                            <button type="submit" class="text-[11px] text-ink-soft">↑</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.learning-paths.lessons.reorder', $lesson) }}">
                                            @csrf
                                            <input type="hidden" name="direction" value="down">
                                            <button type="submit" class="text-[11px] text-ink-soft">↓</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.learning-paths.lessons.destroy', $lesson) }}" onsubmit="return confirm('حذف الدرس؟');">
                                            @csrf @method('DELETE')
                                            <button class="text-[11px] text-rose-600">حذف</button>
                                        </form>
                                    </div>
                                </div>
                                <form x-show="edit" x-cloak method="POST" action="{{ route('admin.learning-paths.lessons.update', $lesson) }}" class="mt-3 space-y-2 border-t border-line pt-3">
                                    @csrf @method('PUT')
                                    <input type="text" name="title_ar" required value="{{ $lesson->title_ar }}" class="{{ $field }}">
                                    <select name="content_type" class="{{ $field }}">
                                        @foreach($lessonTypes as $t)
                                            <option value="{{ $t }}" @selected($lesson->content_type === $t)>{{ $t }}</option>
                                        @endforeach
                                    </select>
                                    <textarea name="body_ar" rows="5" class="{{ $area }}" placeholder="محتوى الدرس للمعلم">{{ $lesson->body_ar }}</textarea>
                                    <input type="number" name="duration_minutes" min="1" value="{{ $lesson->duration_minutes }}" placeholder="دقائق" class="{{ $field }}">
                                    <label class="inline-flex items-center gap-2 text-xs text-ink">
                                        <input type="checkbox" name="is_preview" value="1" class="rounded border-line text-accent" @checked($lesson->is_preview)> معاينة مجانية
                                    </label>
                                    <label class="inline-flex items-center gap-2 text-xs text-ink">
                                        <input type="checkbox" name="is_active" value="1" class="rounded border-line text-accent" @checked($lesson->is_active)> نشط
                                    </label>
                                    <button type="submit" class="btn-press inline-flex h-8 items-center rounded-lg bg-accent px-3 text-xs font-medium text-white">حفظ الدرس</button>
                                </form>
                            </li>
                        @empty
                            <li class="text-sm text-muted">لا دروس بعد — أضف درسًا أدناه.</li>
                        @endforelse
                    </ul>
                    <form method="POST" action="{{ route('admin.learning-paths.units.lessons.store', $unit) }}" class="space-y-2 rounded-xl bg-canvas p-3">
                        @csrf
                        <p class="text-xs font-semibold text-muted">إضافة درس/محتوى</p>
                        <input type="text" name="title_ar" required placeholder="عنوان الدرس للمعلم" class="{{ $field }}">
                        <select name="content_type" class="{{ $field }}">
                            @foreach($lessonTypes as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                        <textarea name="body_ar" rows="4" placeholder="محتوى الدرس (يظهر للمعلم بعد التفعيل)" class="{{ $area }}"></textarea>
                        <input type="number" name="duration_minutes" min="1" placeholder="دقائق تقديرية" class="{{ $field }}">
                        <label class="inline-flex items-center gap-2 text-xs text-ink">
                            <input type="checkbox" name="is_preview" value="1" class="rounded border-line text-accent"> معاينة مجانية
                        </label>
                        <button type="submit" class="btn-press inline-flex h-9 items-center rounded-lg bg-accent px-3 text-xs font-medium text-white">إضافة درس</button>
                    </form>
                </div>

                <div>
                    <h4 class="mb-2 text-sm font-semibold text-ink">ممارسات / أدوات / تقييم ({{ $unit->practices->count() }})</h4>
                    <ul class="mb-3 space-y-2">
                        @forelse($unit->practices as $practice)
                            <li class="rounded-xl border border-line px-3 py-2" x-data="{ edit: false }">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-medium text-ink">{{ $practice->title_ar }}</p>
                                        <p class="text-xs text-muted">{{ $practice->typeLabel('ar') }} @if($practice->body_ar)· محتوى محفوظ@endif</p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="edit = !edit" class="text-[11px] font-semibold text-accent">تعديل</button>
                                        <form method="POST" action="{{ route('admin.learning-paths.practices.reorder', $practice) }}">
                                            @csrf
                                            <input type="hidden" name="direction" value="up">
                                            <button type="submit" class="text-[11px] text-ink-soft">↑</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.learning-paths.practices.reorder', $practice) }}">
                                            @csrf
                                            <input type="hidden" name="direction" value="down">
                                            <button type="submit" class="text-[11px] text-ink-soft">↓</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.learning-paths.practices.destroy', $practice) }}" onsubmit="return confirm('حذف العنصر؟');">
                                            @csrf @method('DELETE')
                                            <button class="text-[11px] text-rose-600">حذف</button>
                                        </form>
                                    </div>
                                </div>
                                <form x-show="edit" x-cloak method="POST" action="{{ route('admin.learning-paths.practices.update', $practice) }}" class="mt-3 space-y-2 border-t border-line pt-3">
                                    @csrf @method('PUT')
                                    <input type="text" name="title_ar" required value="{{ $practice->title_ar }}" class="{{ $field }}">
                                    <select name="practice_type" class="{{ $field }}">
                                        @foreach($practiceTypes as $t)
                                            <option value="{{ $t }}" @selected($practice->practice_type === $t)>{{ $practiceLabels[$t] ?? $t }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="summary_ar" value="{{ $practice->summary_ar }}" placeholder="ملخص قصير" class="{{ $field }}">
                                    <textarea name="body_ar" rows="5" class="{{ $area }}" placeholder="تفاصيل الممارسة للمعلم">{{ $practice->body_ar }}</textarea>
                                    <input type="url" name="resource_url" value="{{ $practice->resource_url }}" placeholder="رابط مورد" class="{{ $field }}">
                                    <label class="inline-flex items-center gap-2 text-xs text-ink">
                                        <input type="checkbox" name="is_active" value="1" class="rounded border-line text-accent" @checked($practice->is_active)> نشط
                                    </label>
                                    <button type="submit" class="btn-press inline-flex h-8 items-center rounded-lg bg-accent px-3 text-xs font-medium text-white">حفظ الممارسة</button>
                                </form>
                            </li>
                        @empty
                            <li class="text-sm text-muted">لا عناصر بعد — أضف ممارسة أدناه.</li>
                        @endforelse
                    </ul>
                    <form method="POST" action="{{ route('admin.learning-paths.units.practices.store', $unit) }}" class="space-y-2 rounded-xl bg-canvas p-3">
                        @csrf
                        <p class="text-xs font-semibold text-muted">إضافة ممارسة / أداة / تقييم</p>
                        <input type="text" name="title_ar" required placeholder="العنوان" class="{{ $field }}">
                        <select name="practice_type" class="{{ $field }}">
                            @foreach($practiceTypes as $t)
                                <option value="{{ $t }}">{{ $practiceLabels[$t] ?? $t }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="summary_ar" placeholder="ملخص قصير" class="{{ $field }}">
                        <textarea name="body_ar" rows="4" placeholder="تفاصيل الممارسة (تظهر للمعلم)" class="{{ $area }}"></textarea>
                        <input type="url" name="resource_url" placeholder="رابط مورد (اختياري)" class="{{ $field }}">
                        <button type="submit" class="btn-press inline-flex h-9 items-center rounded-lg bg-accent px-3 text-xs font-medium text-white">إضافة</button>
                    </form>
                </div>
            </div>
        </article>
    @empty
        <div class="rounded-2xl border border-dashed border-line px-4 py-12 text-center text-sm text-muted">
            لا وحدات بعد. أضف أول وحدة قصيرة من النموذج أعلاه — يمكن ترك الدروس والتطبيقات فارغة حتى تجهيز المحتوى.
        </div>
    @endforelse
</div>
@endsection
