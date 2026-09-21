@php
    $p = $program;
    $d = $defaults ?? [];
@endphp
<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">الجهة</label>
        <select name="institution_id" class="{{ $field }}">
            <option value="">— بدون ربط بعد / Inquiry عام —</option>
            @foreach($institutions as $org)
                <option value="{{ $org->id }}" @selected(old('institution_id', $p->institution_id ?? ($d['institution_id'] ?? null)) == $org->id)>{{ $org->name_ar }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">الخدمة *</label>
        <select name="service_key" required class="{{ $field }}">
            @foreach($serviceLabels as $key => $label)
                <option value="{{ $key }}" @selected(old('service_key', $p->service_key ?? 'school_institutional_training') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">النوع</label>
        <select name="program_kind" class="{{ $field }}">
            <option value="training" @selected(old('program_kind', $p->program_kind ?? ($d['program_kind'] ?? 'training')) === 'training')>تدريب</option>
            <option value="development" @selected(old('program_kind', $p->program_kind ?? ($d['program_kind'] ?? '')) === 'development')>تطوير مؤسسي</option>
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">العنوان *</label>
        <input type="text" name="title_ar" required value="{{ old('title_ar', $p->title_ar ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">الحالة *</label>
        <select name="status" required class="{{ $field }}">
            @foreach($statuses as $key => $label)
                <option value="{{ $key }}" @selected(old('status', $p->status ?? 'inquiry') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">طريقة التنفيذ</label>
        <select name="delivery_mode" class="{{ $field }}">
            <option value="">—</option>
            @foreach($deliveryModes as $key => $label)
                <option value="{{ $key }}" @selected(old('delivery_mode', $p->delivery_mode ?? '') === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">عدد المشاركين المخطط</label>
        <input type="number" name="planned_participants" min="1" value="{{ old('planned_participants', $p->planned_participants ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">المدة (ساعات)</label>
        <input type="number" name="duration_hours" min="1" value="{{ old('duration_hours', $p->duration_hours ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">السعر</label>
        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $p->price ?? '') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">العملة</label>
        <input type="text" name="currency" value="{{ old('currency', $p->currency ?? 'QAR') }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">تاريخ البداية</label>
        <input type="date" name="starts_on" value="{{ old('starts_on', optional($p->starts_on ?? null)?->format('Y-m-d')) }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">تاريخ النهاية</label>
        <input type="date" name="ends_on" value="{{ old('ends_on', optional($p->ends_on ?? null)?->format('Y-m-d')) }}" class="{{ $field }}">
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">المدرب المعيَّن</label>
        <select name="assigned_instructor_id" class="{{ $field }}">
            <option value="">—</option>
            @foreach($instructors as $ins)
                <option value="{{ $ins->id }}" @selected(old('assigned_instructor_id', $p->assigned_instructor_id ?? null) == $ins->id)>{{ $ins->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1.5 block text-xs font-medium text-muted">منسق من المنصة</label>
        <select name="coordinator_user_id" class="{{ $field }}">
            <option value="">—</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}" @selected(old('coordinator_user_id', $p->coordinator_user_id ?? null) == $u->id)>{{ $u->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">ملاحظات الاستفسار (Inquiry)</label>
        <textarea name="inquiry_notes" rows="2" class="{{ $area }}">{{ old('inquiry_notes', $p->inquiry_notes ?? '') }}</textarea>
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">العرض (Proposal)</label>
        <textarea name="proposal_notes" rows="2" class="{{ $area }}">{{ old('proposal_notes', $p->proposal_notes ?? '') }}</textarea>
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">تشخيص الاحتياج (للتطوير المؤسسي)</label>
        <textarea name="diagnosis_notes" rows="2" class="{{ $area }}">{{ old('diagnosis_notes', $p->diagnosis_notes ?? '') }}</textarea>
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">خطة التحسين</label>
        <textarea name="improvement_plan" rows="2" class="{{ $area }}">{{ old('improvement_plan', $p->improvement_plan ?? '') }}</textarea>
    </div>
    <div class="sm:col-span-2">
        <label class="mb-1.5 block text-xs font-medium text-muted">نتيجة الإغلاق</label>
        <textarea name="result_notes" rows="2" class="{{ $area }}">{{ old('result_notes', $p->result_notes ?? '') }}</textarea>
    </div>
</div>
