@php $p = $path; @endphp
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">العنوان (عربي) *</label>
        <input type="text" name="title_ar" value="{{ old('title_ar', $p->title_ar ?? '') }}" required class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">العنوان (إنجليزي)</label>
        <input type="text" name="title_en" value="{{ old('title_en', $p->title_en ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">Slug (رابط)</label>
        <input type="text" name="slug" value="{{ old('slug', $p->slug ?? '') }}" class="{{ $field }}" placeholder="classroom-management">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">المدرب (اختياري)</label>
        <select name="instructor_id" class="{{ $field }}">
            <option value="">—</option>
            @foreach($instructors as $ins)
                <option value="{{ $ins->id }}" @selected(old('instructor_id', $p->instructor_id ?? null) == $ins->id)>{{ $ins->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">المهارة المستهدفة (عربي)</label>
        <input type="text" name="skill_focus_ar" value="{{ old('skill_focus_ar', $p->skill_focus_ar ?? '') }}" class="{{ $field }}" placeholder="مثال: إدارة الصف">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">المهارة المستهدفة (إنجليزي)</label>
        <input type="text" name="skill_focus_en" value="{{ old('skill_focus_en', $p->skill_focus_en ?? '') }}" class="{{ $field }}">
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">ملخص قصير (عربي)</label>
        <input type="text" name="summary_ar" value="{{ old('summary_ar', $p->summary_ar ?? '') }}" maxlength="500" class="{{ $field }}">
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">ملخص قصير (إنجليزي)</label>
        <input type="text" name="summary_en" value="{{ old('summary_en', $p->summary_en ?? '') }}" maxlength="500" class="{{ $field }}">
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">وصف (عربي)</label>
        <textarea name="description_ar" rows="3" class="{{ $area }}">{{ old('description_ar', $p->description_ar ?? '') }}</textarea>
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">وصف (إنجليزي)</label>
        <textarea name="description_en" rows="3" class="{{ $area }}">{{ old('description_en', $p->description_en ?? '') }}</textarea>
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">مدة تقديرية (دقائق)</label>
        <input type="number" name="estimated_minutes" min="1" value="{{ old('estimated_minutes', $p->estimated_minutes ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">ترتيب العرض</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $p->sort_order ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">سعر البيع المنفرد</label>
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $p->price ?? '') }}" class="{{ $field }}" placeholder="اختياري">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">العملة</label>
        <input type="text" name="currency" value="{{ old('currency', $p->currency ?? 'QAR') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">أيام الوصول بعد التفعيل</label>
        <input type="number" name="access_days" min="1" value="{{ old('access_days', $p->access_days ?? '') }}" class="{{ $field }}" placeholder="فارغ = دائم">
    </div>
</div>
@if(isset($packages) && $packages->isNotEmpty())
<div class="mt-4">
    <p class="mb-2 text-xs font-semibold text-muted">تضمين المسار داخل باقات</p>
    <div class="max-h-48 space-y-1 overflow-y-auto rounded-xl border border-line p-3">
        @php $selectedPackages = old('packages', isset($p) && $p ? $p->packages->pluck('id')->all() : []); @endphp
        @foreach($packages as $package)
            <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-1.5 hover:bg-canvas">
                <input type="checkbox" name="packages[]" value="{{ $package->id }}"
                       @checked(in_array($package->id, array_map('intval', (array) $selectedPackages), true))
                       class="rounded border-line text-accent">
                <span class="text-sm text-ink">{{ $package->name }}</span>
            </label>
        @endforeach
    </div>
</div>
@endif
<div class="flex flex-wrap gap-6 pt-3">
    <label class="inline-flex items-center gap-2 text-sm text-ink">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $p->is_active ?? true)) class="rounded border-line text-accent">
        نشط
    </label>
    <label class="inline-flex items-center gap-2 text-sm text-ink">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $p->is_published ?? false)) class="rounded border-line text-accent">
        منشور للجمهور
    </label>
    <label class="inline-flex items-center gap-2 text-sm text-ink">
        <input type="checkbox" name="is_sellable_standalone" value="1" @checked(old('is_sellable_standalone', $p->is_sellable_standalone ?? false)) class="rounded border-line text-accent">
        قابل للبيع منفردًا
    </label>
</div>
