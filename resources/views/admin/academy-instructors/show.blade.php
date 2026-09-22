@extends('layouts.admin')

@section('title', $instructor->name.' - مدرب تدريس لاب')
@section('page_title', $instructor->name)

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $areaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $locale = app()->getLocale() === 'ar' ? 'ar' : 'en';
    $grantedCourseIds = $grantedCourseIds ?? [];
    $grantedPathIds = $grantedPathIds ?? [];
    $grantedServiceKeys = $grantedServiceKeys ?? [];
    $grantableServices = $grantableServices ?? [];
    $allCourses = $allCourses ?? collect();
    $allLearningPaths = $allLearningPaths ?? collect();
@endphp

<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">
                <a href="{{ route('admin.academy-instructors.index') }}" class="hover:text-accent">مدربو الأكاديمية</a>
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">{{ $instructor->name }}</h2>
            <p class="mt-1 text-sm text-muted">{{ $instructor->email }} @if($instructor->phone)· {{ $instructor->phone }}@endif</p>
            <p class="mt-2 text-xs text-muted">
                صلاحية التقديم:
                @if($instructor->instructorDeliveryEnabled())
                    <span class="font-semibold text-emerald-700">مفعّل</span>
                @else
                    <span class="font-semibold text-amber-700">غير مفعّل</span>
                @endif
                · الحساب:
                @if($instructor->is_active)
                    <span class="font-semibold text-emerald-700">نشط</span>
                @else
                    <span class="font-semibold text-rose-700">موقوف</span>
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(Route::has('admin.teachers.show'))
                <a href="{{ route('admin.teachers.show', $instructor) }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white">مركز التحكم</a>
            @endif
            <a href="{{ route('admin.academy-instructors.index') }}" class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink-soft">رجوع</a>
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

    <article class="rounded-2xl border border-accent/20 bg-accent-soft/30 p-5 shadow-soft">
        <div class="mb-4">
            <h3 class="text-base font-semibold text-ink">تفعيل المدرب · مسارات مسندة · كورسات مسجّلة · خدمات</h3>
            <p class="mt-1 text-sm text-muted">إسناد مسار يفعّل خدمة «المسارات التعليمية» تلقائيًا؛ إسناد كورس مسجّل يفعّل خدمة «الكورسات». المدرب يرى التوصيف/المنهج حسب الإسناد.</p>
        </div>
        <form method="POST" action="{{ route('admin.academy-instructors.grants.update', $instructor) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div class="flex flex-wrap gap-6">
                <label class="inline-flex items-center gap-2 text-sm text-ink">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $instructor->is_active)) class="rounded border-line text-accent">
                    حساب نشط
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-ink">
                    <input type="checkbox" name="instructor_grants_enabled" value="1" @checked(old('instructor_grants_enabled', $instructor->instructor_grants_enabled)) class="rounded border-line text-accent">
                    تفعيل صلاحيات التقديم
                </label>
            </div>

            <div class="grid gap-5 lg:grid-cols-3">
                <div>
                    <p class="mb-2 text-xs font-semibold text-muted">الخدمات المسموحة</p>
                    <div class="max-h-64 space-y-2 overflow-y-auto rounded-xl border border-line bg-surface p-3">
                        @forelse($grantableServices as $key => $svc)
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg px-2 py-2 hover:bg-canvas">
                                <input type="checkbox" name="service_keys[]" value="{{ $key }}"
                                       @checked(in_array($key, old('service_keys', $grantedServiceKeys), true))
                                       class="mt-1 rounded border-line text-accent">
                                <span class="min-w-0">
                                    <span class="block text-sm font-medium text-ink">{{ $svc['label_'.$locale] ?? $svc['label_ar'] ?? $key }}</span>
                                    <span class="block text-xs text-muted">{{ $svc['hint_ar'] ?? '' }}</span>
                                </span>
                            </label>
                        @empty
                            <p class="py-4 text-center text-sm text-muted">لا خدمات معرّفة في الإعدادات.</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <p class="mb-2 text-xs font-semibold text-muted">مسارات تعليمية مسندة</p>
                    <div class="max-h-64 space-y-1 overflow-y-auto rounded-xl border border-line bg-surface p-3">
                        @forelse($allLearningPaths as $path)
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-2 hover:bg-canvas">
                                <input type="checkbox" name="path_ids[]" value="{{ $path->id }}"
                                       @checked(in_array((int) $path->id, array_map('intval', old('path_ids', $grantedPathIds)), true) || (int) $path->instructor_id === (int) $instructor->id)
                                       class="rounded border-line text-accent">
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm text-ink">{{ $path->title_ar }}</span>
                                    <span class="block text-xs text-muted">{{ $path->skill_focus_ar ?: $path->slug }}</span>
                                </span>
                            </label>
                        @empty
                            <p class="py-4 text-center text-sm text-muted">لا مسارات بعد — أنشئها من المسارات التعليمية.</p>
                        @endforelse
                    </div>
                </div>
                <div>
                    <p class="mb-2 text-xs font-semibold text-muted">كورسات مسجّلة مسندة</p>
                    <div class="max-h-64 space-y-1 overflow-y-auto rounded-xl border border-line bg-surface p-3">
                        @forelse($allCourses as $course)
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg px-2 py-2 hover:bg-canvas">
                                <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                                       @checked(in_array((int) $course->id, array_map('intval', old('course_ids', $grantedCourseIds)), true) || (int) $course->instructor_id === (int) $instructor->id)
                                       class="rounded border-line text-accent">
                                <span class="flex-1 text-sm text-ink">{{ $course->title }}</span>
                                @if((int) $course->instructor_id === (int) $instructor->id)
                                    <span class="text-[10px] font-semibold text-accent">مالك</span>
                                @elseif($course->instructor_id)
                                    <span class="text-[10px] text-muted">مسند لآخر</span>
                                @endif
                            </label>
                        @empty
                            <p class="py-4 text-center text-sm text-muted">لا كورسات نشطة بعد.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-press inline-flex h-11 items-center gap-2 rounded-xl bg-accent px-5 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-save text-xs"></i> حفظ التفعيل والصلاحيات
            </button>
        </form>
    </article>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">مجموعات جماعية</p>
            <p class="mt-1 text-xl font-semibold text-ink">{{ $collectiveGroups->count() }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">مجموعات فردية</p>
            <p class="mt-1 text-xl font-semibold text-ink">{{ $individualGroups->count() }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">كورسات مسموحة</p>
            <p class="mt-1 text-xl font-semibold text-ink">{{ $courses->count() }}</p>
        </article>
        <article class="rounded-2xl border border-line bg-surface p-4 shadow-soft">
            <p class="text-xs text-muted">خدمات مسموحة</p>
            <p class="mt-1 text-xl font-semibold text-ink">{{ count($grantedServiceKeys) }}</p>
        </article>
    </section>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="xl:col-span-2 space-y-5">
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">مجموعات جماعية</h3>
                </div>
                <div class="divide-y divide-line">
                    @forelse($collectiveGroups as $group)
                        <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-5">
                            <div>
                                <p class="font-medium text-ink">{{ $group->title }}</p>
                                <p class="text-xs text-muted">{{ $group->cohorts_count }} دفعة · سعة {{ $group->capacity }}</p>
                            </div>
                            @if(Route::has('admin.tutoring-groups.edit'))
                                <a href="{{ route('admin.tutoring-groups.edit', ['type' => 'collective', 'tutoringGroup' => $group]) }}" class="text-xs font-semibold text-accent">إدارة</a>
                            @endif
                        </div>
                    @empty
                        <p class="px-4 py-8 text-center text-sm text-muted">لا مجموعات جماعية لهذا المدرّب.</p>
                    @endforelse
                </div>
            </article>

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">مجموعات فردية</h3>
                </div>
                <div class="divide-y divide-line">
                    @forelse($individualGroups as $group)
                        <div class="flex items-center justify-between gap-3 px-4 py-3 sm:px-5">
                            <div>
                                <p class="font-medium text-ink">{{ $group->title }}</p>
                                <p class="text-xs text-muted">{{ $group->packages_count }} باقة</p>
                            </div>
                            @if(Route::has('admin.tutoring-groups.edit'))
                                <a href="{{ route('admin.tutoring-groups.edit', ['type' => 'individual', 'tutoringGroup' => $group]) }}" class="text-xs font-semibold text-accent">إدارة</a>
                            @endif
                        </div>
                    @empty
                        <p class="px-4 py-8 text-center text-sm text-muted">لا مجموعات فردية لهذا المدرّب.</p>
                    @endforelse
                </div>
            </article>

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4 sm:px-5">
                    <h3 class="text-base font-semibold text-ink">الكورسات</h3>
                </div>
                <div class="divide-y divide-line">
                    @forelse($courses as $course)
                        <div class="px-4 py-3 sm:px-5">
                            <p class="font-medium text-ink">{{ $course->title }}</p>
                            <p class="text-xs text-muted">
                                {{ $course->academicYear?->name ?? '—' }}
                                @if($course->academicSubject) · {{ $course->academicSubject->name }}@endif
                            </p>
                        </div>
                    @empty
                        <p class="px-4 py-8 text-center text-sm text-muted">لا كورسات مسندة.</p>
                    @endforelse
                </div>
            </article>

            @if($upcomingBookings->isNotEmpty())
                <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-4 py-4 sm:px-5">
                        <h3 class="text-base font-semibold text-ink">حصص قادمة</h3>
                    </div>
                    <div class="divide-y divide-line">
                        @foreach($upcomingBookings as $booking)
                            <div class="px-4 py-3 sm:px-5 text-sm">
                                <p class="font-medium text-ink">{{ $booking->tutoringGroup?->title }}</p>
                                <p class="text-xs text-muted">{{ $booking->user?->name ?? 'ضيف' }} · {{ $booking->starts_at?->format('Y-m-d H:i') }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endif
        </div>

        <aside class="space-y-5">
            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4">
                    <h3 class="text-base font-semibold text-ink">ربط معلم بهذا المدرب</h3>
                    <p class="mt-0.5 text-xs text-muted">اختياري — لمتابعة معلم معيّن (المتعلّم المهني) مع هذا المدرب</p>
                </div>
                <form method="POST" action="{{ route('admin.academy-instructors.assignments.store') }}" class="space-y-3 p-4">
                    @csrf
                    <input type="hidden" name="instructor_id" value="{{ $instructor->id }}">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-muted">المعلم</label>
                        <select name="student_id" required class="{{ $fieldClass }}">
                            <option value="">اختر معلمًا…</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>{{ $student->name }} — {{ $student->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-muted">النطاق</label>
                        <select name="scope" class="{{ $fieldClass }}">
                            <option value="general">عام</option>
                            <option value="collective">مجموعات جماعية</option>
                            <option value="individual">مجموعات فردية</option>
                            <option value="courses">كورسات</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-muted">السنة الأكاديمية (اختياري)</label>
                        <select name="academic_year_id" class="{{ $fieldClass }}">
                            <option value="">—</option>
                            @foreach($years as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-muted">ملاحظات</label>
                        <textarea name="notes" rows="3" class="{{ $areaClass }}" placeholder="سبب التوصيف أو جدول المتابعة…">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent text-sm font-medium text-white">
                        <i class="fas fa-user-plus text-xs"></i> حفظ التوصيف
                    </button>
                </form>
            </article>

            <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
                <div class="border-b border-line px-4 py-4">
                    <h3 class="text-base font-semibold text-ink">التوصيفات الحالية</h3>
                </div>
                <div class="divide-y divide-line max-h-[420px] overflow-y-auto">
                    @forelse($assignments as $assignment)
                        <div class="px-4 py-3 space-y-2">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="font-medium text-ink truncate">{{ $assignment->student?->name }}</p>
                                    <p class="text-[11px] text-muted">{{ $assignment->scopeLabel() }} · {{ $assignment->statusLabel() }}</p>
                                    @if($assignment->academicYear)
                                        <p class="text-[11px] text-muted">{{ $assignment->academicYear->name }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach(['active' => 'نشط', 'paused' => 'إيقاف', 'ended' => 'إنهاء'] as $st => $label)
                                    <form method="POST" action="{{ route('admin.academy-instructors.assignments.status', $assignment) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $st }}">
                                        <button type="submit" class="rounded-lg border border-line px-2 py-1 text-[10px] font-semibold text-ink-soft hover:bg-canvas {{ $assignment->status === $st ? 'bg-accent-soft text-accent border-accent/30' : '' }}">{{ $label }}</button>
                                    </form>
                                @endforeach
                                <form method="POST" action="{{ route('admin.academy-instructors.assignments.destroy', $assignment) }}" onsubmit="return confirm('حذف التوصيف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-danger/20 px-2 py-1 text-[10px] font-semibold text-danger">حذف</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="px-4 py-8 text-center text-sm text-muted">لا توصيفات يدوية بعد.</p>
                    @endforelse
                </div>
            </article>
        </aside>
    </div>
</div>
@endsection
