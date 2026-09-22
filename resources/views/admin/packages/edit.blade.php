@extends('layouts.admin')

@section('title', 'تعديل الباقة - ' . config('app.name'))
@section('page_title', 'تعديل باقة')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $selectedIds = old('courses', $package->courses->pluck('id')->toArray());
    $selectedPathIds = old('learning_paths', $package->learningPaths->pluck('id')->toArray());
    $coursesJson = $courses->map(fn ($c) => ['id' => $c->id, 'price' => (float) $c->price])->values()->toJson();
    $featureRows = old('features', $package->features ?: ['']);
    if ($featureRows === []) { $featureRows = ['']; }
    $toolRows = old('tools_resources', $package->tools_resources ?: ['']);
    if ($toolRows === []) { $toolRows = ['']; }
@endphp
<div class="space-y-5" x-data="packageForm({{ $coursesJson }}, {{ (float) old('price', $package->price) }})">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-medium text-muted">الباقات · قابلة للتعديل بالكامل</p>
            <h2 class="mt-1 text-2xl font-semibold text-ink">تعديل: {{ $package->name }}</h2>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.packages.show', $package) }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm text-ink-soft">عرض</a>
            <a href="{{ route('admin.packages.index') }}" class="btn-press inline-flex h-9 items-center rounded-xl border border-line px-4 text-sm text-ink-soft">رجوع</a>
        </div>
    </section>

    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-soft">
            <ul class="list-disc pr-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-accent/20 bg-accent-soft/40 px-4 py-3 text-sm text-ink">
        <strong>حاسبة التوفير:</strong>
        مجموع أسعار البرامج =
        <span class="font-bold tabular-nums text-accent" x-text="coursesTotal.toFixed(2)"></span>
        · سعر الباقة =
        <span class="font-bold tabular-nums" x-text="packagePrice.toFixed(2)"></span>
        · التوفير =
        <span class="font-bold tabular-nums text-emerald-700" x-text="Math.max(0, coursesTotal - packagePrice).toFixed(2)"></span>
        <span class="text-muted">({{ $package->currencyCode() }})</span>
    </div>

    <form action="{{ route('admin.packages.update', $package) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <h3 class="text-sm font-semibold text-ink">المعلومات الأساسية</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}" for="name">اسم الباقة *</label>
                    <input id="name" name="name" value="{{ old('name', $package->name) }}" required class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="slug">الرابط (Slug)</label>
                    <input id="slug" name="slug" value="{{ old('slug', $package->slug) }}" class="{{ $fieldClass }}" dir="ltr">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="package_type">نوع الباقة (تدريس لاب)</label>
                    <select id="package_type" name="package_type" class="{{ $fieldClass }}">
                        @foreach(\App\Models\Package::trackLabels() as $key => $label)
                            <option value="{{ $key }}" @selected(old('package_type', $package->package_type ?: 'individual') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="cta_mode">زر الدعوة</label>
                    <select id="cta_mode" name="cta_mode" class="{{ $fieldClass }}">
                        <option value="register" @selected(old('cta_mode', $package->cta_mode ?: 'register') === 'register')>تسجيل / اشتراك</option>
                        <option value="contact" @selected(old('cta_mode', $package->cta_mode) === 'contact')>تواصل لعرض مؤسسي</option>
                        <option value="quote" @selected(old('cta_mode', $package->cta_mode) === 'quote')>طلب عرض سعر</option>
                    </select>
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="price">السعر النهائي *</label>
                    <input type="number" step="0.01" min="0" id="price" name="price" x-model.number="packagePrice" required class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="original_price">السعر الأصلي (قبل الخصم)</label>
                    <input type="number" step="0.01" min="0" id="original_price" name="original_price" value="{{ old('original_price', $package->original_price) }}" class="{{ $fieldClass }}">
                </div>
                <div class="md:col-span-2">
                    <label class="{{ $labelClass }}" for="discount_note">ملاحظة الخصم / المزايا السعرية</label>
                    <input id="discount_note" name="discount_note" value="{{ old('discount_note', $package->discount_note) }}" class="{{ $fieldClass }}" placeholder="مثال: خصم إطلاق 25%">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="currency">العملة</label>
                    <input id="currency" name="currency" value="{{ old('currency', $package->currency ?: 'QAR') }}" class="{{ $fieldClass }}" dir="ltr">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="consultation_sessions">جلسات الاستشارة</label>
                    <input type="number" min="0" id="consultation_sessions" name="consultation_sessions" value="{{ old('consultation_sessions', $package->consultation_sessions) }}" class="{{ $fieldClass }}" placeholder="اختياري">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="participant_seats">عدد المقاعد / المشاركين</label>
                    <input type="number" min="0" id="participant_seats" name="participant_seats" value="{{ old('participant_seats', $package->participant_seats) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="order">ترتيب العرض</label>
                    <input type="number" min="0" id="order" name="order" value="{{ old('order', $package->order) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="duration_days">مدة الصلاحية (أيام)</label>
                    <input type="number" min="0" id="duration_days" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="starts_at">تاريخ البداية</label>
                    <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at', $package->starts_at?->format('Y-m-d\TH:i')) }}" class="{{ $fieldClass }}">
                </div>
                <div>
                    <label class="{{ $labelClass }}" for="ends_at">تاريخ الانتهاء</label>
                    <input type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at', $package->ends_at?->format('Y-m-d\TH:i')) }}" class="{{ $fieldClass }}">
                </div>
            </div>
        </article>

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft space-y-4">
            <div>
                <label class="{{ $labelClass }}" for="description">الوصف (صفحة التفاصيل)</label>
                <textarea id="description" name="description" rows="4" class="w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">{{ old('description', $package->description) }}</textarea>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="card_summary">نص البطاقة (صفحة الأسعار)</label>
                <textarea id="card_summary" name="card_summary" rows="3" class="w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20">{{ old('card_summary', $package->card_summary) }}</textarea>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="thumbnail">صورة الباقة</label>
                @if($package->thumbnail)
                    <div class="mb-2">
                        <img src="{{ storage_asset($package->thumbnail) }}" alt="" class="h-24 w-24 rounded-xl object-cover border border-line">
                    </div>
                @endif
                <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="{{ $fieldClass }}">
                <p class="mt-1 text-xs text-muted">اتركه فارغاً للاحتفاظ بالصورة الحالية.</p>
            </div>
        </article>

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <label class="{{ $labelClass }}">المزايا (Benefits)</label>
            <div id="features-container" class="space-y-2">
                @foreach($featureRows as $feature)
                    <div class="flex gap-2">
                        <input type="text" name="features[]" value="{{ $feature }}" class="h-11 flex-1 rounded-xl border border-line px-4 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20" placeholder="ميزة">
                        <button type="button" onclick="removeFeature(this)" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-line text-rose-600 {{ count($featureRows) === 1 ? 'hidden' : '' }}"><i class="fas fa-times"></i></button>
                    </div>
                @endforeach
            </div>
            <button type="button" onclick="addFeature()" class="btn-press mt-3 inline-flex h-9 items-center gap-2 rounded-xl border border-line px-3 text-sm text-ink-soft hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-plus text-xs"></i> إضافة ميزة
            </button>
        </article>

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <label class="{{ $labelClass }}">الأدوات والموارد المشمولة</label>
            <p class="mb-2 text-xs text-muted">قائمة قابلة للإدارة — نماذج، قوائم تحقق، أدوات صفية…</p>
            <div id="tools-container" class="space-y-2">
                @foreach($toolRows as $tool)
                    <div class="flex gap-2">
                        <input type="text" name="tools_resources[]" value="{{ $tool }}" class="h-11 flex-1 rounded-xl border border-line px-4 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20" placeholder="أداة أو مورد">
                        <button type="button" onclick="removeTool(this)" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-line text-rose-600 {{ count($toolRows) === 1 ? 'hidden' : '' }}"><i class="fas fa-times"></i></button>
                    </div>
                @endforeach
            </div>
            <button type="button" onclick="addTool()" class="btn-press mt-3 inline-flex h-9 items-center gap-2 rounded-xl border border-line px-3 text-sm text-ink-soft hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-plus text-xs"></i> إضافة أداة / مورد
            </button>
        </article>

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <label class="{{ $labelClass }}">الكورسات المسجّلة في الباقة <span class="text-muted font-normal">(اختياري)</span></label>
            <p class="mb-2 text-xs text-muted">تفعيل الباقة يفتح هذه الكورسات المسجّلة تلقائيًا للمتعلم.</p>
            <div class="mt-2 max-h-72 space-y-1 overflow-y-auto rounded-xl border border-line p-3">
                @forelse($courses as $course)
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-2 hover:bg-[#f8faf9]">
                        <input type="checkbox" name="courses[]" value="{{ $course->id }}"
                               data-price="{{ (float) $course->price }}"
                               @checked(in_array($course->id, $selectedIds))
                               @change="recalc()"
                               class="rounded border-line text-accent focus:ring-accent/20">
                        <span class="flex-1 text-sm text-ink">{{ $course->title }}</span>
                        <span class="text-xs tabular-nums text-muted">
                            @if((float) $course->price > 0)
                                {{ number_format((float) $course->price, 2) }} {{ $package->currencyCode() }}
                            @else
                                مجاني
                            @endif
                        </span>
                    </label>
                @empty
                    <p class="py-6 text-center text-sm text-muted">لا توجد كورسات مسجّلة نشطة</p>
                @endforelse
            </div>
        </article>

        <article class="rounded-2xl border border-accent/20 bg-accent-soft/20 p-5 shadow-soft">
            <label class="{{ $labelClass }}">المسارات التعليمية في الباقة <span class="text-muted font-normal">(اختياري)</span></label>
            <p class="mb-2 text-xs text-muted">تفعيل الباقة للمعلم يفتح هذه المسارات تلقائيًا (منفصل عن الكورسات المسجّلة).</p>
            <div class="mt-2 max-h-72 space-y-1 overflow-y-auto rounded-xl border border-line bg-surface p-3">
                @forelse(($learningPaths ?? collect()) as $path)
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-2 hover:bg-[#f8faf9]">
                        <input type="checkbox" name="learning_paths[]" value="{{ $path->id }}"
                               @checked(in_array($path->id, $selectedPathIds))
                               class="rounded border-line text-accent focus:ring-accent/20">
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-medium text-ink">{{ $path->title_ar }}</span>
                            <span class="block text-xs text-muted">{{ $path->skill_focus_ar ?: $path->slug }}</span>
                        </span>
                    </label>
                @empty
                    <p class="py-6 text-center text-sm text-muted">لا مسارات نشطة بعد.</p>
                @endforelse
            </div>
        </article>

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <label class="{{ $labelClass }}">أدوات وموارد مربوطة (كيانات)</label>
            <p class="mb-2 text-xs text-muted">عند تفعيل الباقة يُفتح الوصول لهذه الأدوات عبر entitlement.</p>
            @php
                $selectedToolIds = old('teacher_tools', $package->teacherTools->pluck('id')->toArray());
            @endphp
            <div class="mt-2 max-h-72 space-y-1 overflow-y-auto rounded-xl border border-line bg-surface p-3">
                @forelse(($teacherTools ?? collect()) as $tool)
                    <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-2 hover:bg-[#f8faf9]">
                        <input type="checkbox" name="teacher_tools[]" value="{{ $tool->id }}"
                               @checked(in_array($tool->id, $selectedToolIds))
                               class="rounded border-line text-accent focus:ring-accent/20">
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-medium text-ink">{{ $tool->title_ar }}</span>
                            <span class="block text-xs text-muted">{{ $tool->slug }}</span>
                        </span>
                    </label>
                @empty
                    <p class="py-6 text-center text-sm text-muted">لا أدوات في المكتبة بعد — أضفها من «الأدوات والموارد».</p>
                @endforelse
            </div>
        </article>

        <article class="rounded-2xl border border-line bg-surface p-5 shadow-soft">
            <div class="flex flex-wrap gap-6">
                <label class="inline-flex items-center gap-2 text-sm text-ink"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $package->is_active)) class="rounded border-line text-accent"> نشط</label>
                <label class="inline-flex items-center gap-2 text-sm text-ink"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $package->is_featured)) class="rounded border-line text-accent"> مميز</label>
                <label class="inline-flex items-center gap-2 text-sm text-ink"><input type="checkbox" name="is_popular" value="1" @checked(old('is_popular', $package->is_popular)) class="rounded border-line text-accent"> الأكثر شعبية</label>
                <label class="inline-flex items-center gap-2 text-sm text-ink"><input type="checkbox" name="includes_tools" value="1" @checked(old('includes_tools', $package->includes_tools ?? true)) class="rounded border-line text-accent"> تشمل أدوات وموارد</label>
            </div>
        </article>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-6 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-save text-xs"></i> حفظ التغييرات
            </button>
            <a href="{{ route('admin.packages.index') }}" class="inline-flex h-11 items-center rounded-xl border border-line px-5 text-sm text-ink-soft">إلغاء</a>
        </div>
    </form>
</div>

<script>
function packageForm(courses, initialPrice) {
    return {
        packagePrice: initialPrice || 0,
        coursesTotal: 0,
        recalc() {
            let total = 0;
            document.querySelectorAll('input[name="courses[]"]:checked').forEach((el) => {
                total += parseFloat(el.dataset.price || '0') || 0;
            });
            this.coursesTotal = total;
        },
        init() {
            this.$nextTick(() => this.recalc());
        }
    };
}
function addFeature() {
    const container = document.getElementById('features-container');
    const row = document.createElement('div');
    row.className = 'flex gap-2';
    row.innerHTML = `<input type="text" name="features[]" class="h-11 flex-1 rounded-xl border border-line px-4 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20" placeholder="ميزة إضافية">
        <button type="button" onclick="removeFeature(this)" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-line text-rose-600"><i class="fas fa-times"></i></button>`;
    container.appendChild(row);
    container.querySelectorAll('button').forEach(btn => btn.classList.remove('hidden'));
}
function removeFeature(button) {
    const container = document.getElementById('features-container');
    button.parentElement.remove();
    if (container.querySelectorAll('input').length === 1) {
        container.querySelectorAll('button').forEach(btn => btn.classList.add('hidden'));
    }
}
function addTool() {
    const container = document.getElementById('tools-container');
    const row = document.createElement('div');
    row.className = 'flex gap-2';
    row.innerHTML = `<input type="text" name="tools_resources[]" class="h-11 flex-1 rounded-xl border border-line px-4 text-sm focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20" placeholder="أداة أو مورد">
        <button type="button" onclick="removeTool(this)" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-line text-rose-600"><i class="fas fa-times"></i></button>`;
    container.appendChild(row);
    container.querySelectorAll('button').forEach(btn => btn.classList.remove('hidden'));
}
function removeTool(button) {
    const container = document.getElementById('tools-container');
    button.parentElement.remove();
    if (container.querySelectorAll('input').length === 1) {
        container.querySelectorAll('button').forEach(btn => btn.classList.add('hidden'));
    }
}
</script>
@endsection
