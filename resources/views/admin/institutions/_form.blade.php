@php $i = $institution; @endphp
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">الاسم (عربي) *</label>
        <input type="text" name="name_ar" required value="{{ old('name_ar', $i->name_ar ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">الاسم (إنجليزي)</label>
        <input type="text" name="name_en" value="{{ old('name_en', $i->name_en ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $i->slug ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">نوع الجهة *</label>
        <select name="org_type" required class="{{ $field }}">
            @foreach($orgTypes as $key => $label)
                <option value="{{ $key }}" @selected(old('org_type', $i->org_type ?? 'school') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-[11px] text-muted">تصنيف عرض فقط — المحور التجاري واحد للجميع.</p>
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">الدولة</label>
        <input type="text" name="country" value="{{ old('country', $i->country ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">المدينة</label>
        <input type="text" name="city" value="{{ old('city', $i->city ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">اسم جهة الاتصال</label>
        <input type="text" name="contact_name" value="{{ old('contact_name', $i->contact_name ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">البريد</label>
        <input type="email" name="contact_email" value="{{ old('contact_email', $i->contact_email ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">الهاتف / واتساب</label>
        <input type="text" name="contact_phone" value="{{ old('contact_phone', $i->contact_phone ?? '') }}" class="{{ $field }}">
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">ملاحظات</label>
        <textarea name="notes" rows="3" class="{{ $area }}">{{ old('notes', $i->notes ?? '') }}</textarea>
    </div>
</div>
@if(!$i)
<div class="mt-4 rounded-xl border border-dashed border-line p-4">
    <p class="mb-3 text-xs font-semibold text-muted">منسق أولي (اختياري)</p>
    <div class="grid gap-3 sm:grid-cols-2">
        <input type="text" name="coordinator_name" value="{{ old('coordinator_name') }}" placeholder="اسم المنسق" class="{{ $field }}">
        <input type="email" name="coordinator_email" value="{{ old('coordinator_email') }}" placeholder="بريد المنسق" class="{{ $field }}">
        <input type="text" name="coordinator_phone" value="{{ old('coordinator_phone') }}" placeholder="هاتف" class="{{ $field }}">
    </div>
</div>
@endif
<label class="mt-4 inline-flex items-center gap-2 text-sm">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $i->is_active ?? true)) class="rounded border-line text-accent">
    حساب نشط
</label>
