<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">العنوان (عربي) *</label>
        <input type="text" name="title_ar" required value="{{ old('title_ar', $s->title_ar ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">العنوان (إنجليزي)</label>
        <input type="text" name="title_en" value="{{ old('title_en', $s->title_en ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $s->slug ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">نوع الاستشارة *</label>
        <select name="consultation_type" required class="{{ $field }}">
            @foreach($typeLabels as $key => $label)
                <option value="{{ $key }}" @selected(old('consultation_type', $s->consultation_type ?? 'teacher_individual') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">ملخص عربي</label>
        <input type="text" name="summary_ar" value="{{ old('summary_ar', $s->summary_ar ?? '') }}" class="{{ $field }}">
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">وصف عربي</label>
        <textarea name="description_ar" rows="3" class="{{ $area }}">{{ old('description_ar', $s->description_ar ?? '') }}</textarea>
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">المدة (دقيقة) *</label>
        <input type="number" name="duration_minutes" min="15" max="480" required value="{{ old('duration_minutes', $s->duration_minutes ?? 30) }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">السعر *</label>
        <input type="number" step="0.01" min="0" name="price" required value="{{ old('price', $s->price ?? 0) }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">العملة</label>
        <input type="text" name="currency" value="{{ old('currency', $s->currency ?? 'QAR') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">ترتيب</label>
        <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $s->sort_order ?? '') }}" class="{{ $field }}">
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">مدرب افتراضي</label>
        <select name="default_instructor_id" class="{{ $field }}">
            <option value="">—</option>
            @foreach($instructors as $ins)
                <option value="{{ $ins->id }}" @selected(old('default_instructor_id', $s->default_instructor_id ?? null) == $ins->id)>{{ $ins->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="flex flex-wrap gap-6 pt-2">
    <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="requires_instructor" value="1" @checked(old('requires_instructor', $s->requires_instructor ?? true)) class="rounded border-line text-accent"> يتطلب مدربًا</label>
    <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $s->is_active ?? true)) class="rounded border-line text-accent"> نشط</label>
    <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $s->is_published ?? false)) class="rounded border-line text-accent"> منشور للحجز</label>
    <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_mvp" value="1" @checked(old('is_mvp', $s->is_mvp ?? true)) class="rounded border-line text-accent"> ضمن MVP (إخفاء Coaching إن أُلغي)</label>
</div>
