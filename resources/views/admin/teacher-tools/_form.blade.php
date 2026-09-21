@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $tool = $tool ?? null;
    $selectedPaths = old('learning_paths', $tool?->learningPaths?->pluck('id')->all() ?? []);
    $selectedPackages = old('packages', $tool?->packages?->pluck('id')->all() ?? []);
@endphp

<div class="grid gap-5 lg:grid-cols-2">
    <article class="space-y-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
        <div>
            <label class="{{ $labelClass }}" for="title_ar">العنوان (عربي) *</label>
            <input id="title_ar" name="title_ar" required value="{{ old('title_ar', $tool->title_ar ?? '') }}" class="{{ $fieldClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}" for="title_en">العنوان (إنجليزي)</label>
            <input id="title_en" name="title_en" value="{{ old('title_en', $tool->title_en ?? '') }}" class="{{ $fieldClass }}">
        </div>
        <div>
            <label class="{{ $labelClass }}" for="slug">Slug</label>
            <input id="slug" name="slug" value="{{ old('slug', $tool->slug ?? '') }}" class="{{ $fieldClass }}" placeholder="يُولَّد تلقائيًا إن تُرك فارغًا">
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="{{ $labelClass }}" for="tool_type">النوع *</label>
                <select id="tool_type" name="tool_type" required class="{{ $fieldClass }}">
                    @foreach($typeLabels as $key => $label)
                        <option value="{{ $key }}" @selected(old('tool_type', $tool->tool_type ?? 'templates') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="{{ $labelClass }}" for="access_mode">وضع الوصول *</label>
                <select id="access_mode" name="access_mode" required class="{{ $fieldClass }}">
                    @foreach($accessLabels as $key => $label)
                        <option value="{{ $key }}" @selected(old('access_mode', $tool->access_mode ?? 'free') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="{{ $labelClass }}" for="summary_ar">ملخص عربي</label>
            <textarea id="summary_ar" name="summary_ar" rows="2" class="{{ $areaClass }}">{{ old('summary_ar', $tool->summary_ar ?? '') }}</textarea>
        </div>
        <div>
            <label class="{{ $labelClass }}" for="description_ar">وصف عربي</label>
            <textarea id="description_ar" name="description_ar" rows="5" class="{{ $areaClass }}">{{ old('description_ar', $tool->description_ar ?? '') }}</textarea>
        </div>
    </article>

    <article class="space-y-4 rounded-2xl border border-line bg-surface p-5 shadow-soft">
        <div>
            <label class="{{ $labelClass }}" for="external_url">رابط خارجي (اختياري)</label>
            <input id="external_url" name="external_url" type="url" value="{{ old('external_url', $tool->external_url ?? '') }}" class="{{ $fieldClass }}" placeholder="https://…">
        </div>
        <div>
            <label class="{{ $labelClass }}" for="file">ملف للتحميل</label>
            <input id="file" name="file" type="file" class="{{ $fieldClass }}">
            @if(!empty($tool?->file_name))
                <p class="mt-1 text-xs text-muted">الحالي: {{ $tool->file_name }}</p>
            @endif
        </div>
        <div>
            <label class="{{ $labelClass }}" for="thumbnail">صورة مصغّرة</label>
            <input id="thumbnail" name="thumbnail" type="file" accept="image/*" class="{{ $fieldClass }}">
        </div>
        <div class="grid gap-4 sm:grid-cols-3">
            <div>
                <label class="{{ $labelClass }}" for="sort_order">الترتيب</label>
                <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $tool->sort_order ?? 0) }}" class="{{ $fieldClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}" for="price">سعر مستقل</label>
                <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $tool->price ?? '') }}" class="{{ $fieldClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}" for="currency">العملة</label>
                <input id="currency" name="currency" value="{{ old('currency', $tool->currency ?? platform_currency()) }}" class="{{ $fieldClass }}">
            </div>
        </div>
        <div class="flex flex-wrap gap-5 pt-1">
            <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $tool->is_active ?? true)) class="rounded border-line text-accent"> نشط</label>
            <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $tool->is_published ?? false)) class="rounded border-line text-accent"> منشور</label>
            <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_standalone_product" value="1" @checked(old('is_standalone_product', $tool->is_standalone_product ?? false)) class="rounded border-line text-accent"> منتج مستقل لاحقًا</label>
        </div>

        <div>
            <label class="{{ $labelClass }}">ربط بمسارات</label>
            <div class="mt-1 max-h-40 space-y-1 overflow-y-auto rounded-xl border border-line p-3">
                @forelse($learningPaths as $path)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="learning_paths[]" value="{{ $path->id }}" @checked(in_array($path->id, $selectedPaths)) class="rounded border-line text-accent">
                        <span>{{ $path->title_ar }}</span>
                    </label>
                @empty
                    <p class="text-xs text-muted">لا مسارات نشطة.</p>
                @endforelse
            </div>
        </div>
        <div>
            <label class="{{ $labelClass }}">ربط بباقات</label>
            <div class="mt-1 max-h-40 space-y-1 overflow-y-auto rounded-xl border border-line p-3">
                @forelse($packages as $package)
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="packages[]" value="{{ $package->id }}" @checked(in_array($package->id, $selectedPackages)) class="rounded border-line text-accent">
                        <span>{{ $package->name }}</span>
                    </label>
                @empty
                    <p class="text-xs text-muted">لا باقات نشطة.</p>
                @endforelse
            </div>
        </div>
    </article>
</div>
