@extends('layouts.admin')

@section('title', 'تعديل المحاضرة')
@section('page_title', 'تعديل المحاضرة')

@section('content')
@php
    $scheduledAtValue = old('scheduled_at');
    if ($scheduledAtValue === null && $lecture->scheduled_at) {
        $scheduledAtValue = \App\Support\AppTimezone::datetimeLocalValue(
            $lecture->scheduled_at,
            old('timezone', auth()->user()?->timezoneCode())
        );
    }
    $platforms = [
        'bunny' => 'Bunny.net',
    ];
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $textareaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">المحتوى · {{ Str::limit($lecture->course->title ?? '', 40) }}</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تعديل المحاضرة</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">{{ Str::limit($lecture->title, 80) }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.lectures.show', $lecture) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm text-ink-soft hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-eye text-xs"></i>
                عرض
            </a>
            <a href="{{ route('admin.lectures.by-course', $lecture->course_id) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm text-ink-soft hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                رجوع لمحاضرات البرنامج
            </a>
        </div>
    </section>

    <form action="{{ route('admin.lectures.update', $lecture) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-5 py-4">
                <h3 class="text-sm font-semibold text-ink">المعلومات الأساسية</h3>
            </div>
            <div class="space-y-5 p-5 sm:p-6">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label for="course_id" class="{{ $labelClass }}">البرنامج <span class="text-rose-600">*</span></label>
                        <select name="course_id" id="course_id" required class="{{ $fieldClass }}">
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $lecture->course_id) == $course->id ? 'selected' : '' }}>{{ Str::limit($course->title, 55) }}</option>
                            @endforeach
                        </select>
                        @error('course_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="instructor_id" class="{{ $labelClass }}">المحاضر <span class="text-rose-600">*</span></label>
                        <select name="instructor_id" id="instructor_id" required class="{{ $fieldClass }}">
                            @foreach($instructors as $instructor)
                                <option value="{{ $instructor->id }}" {{ old('instructor_id', $lecture->instructor_id) == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                            @endforeach
                        </select>
                        @error('instructor_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="course_lesson_id" class="{{ $labelClass }}">الدرس المرتبط (اختياري)</label>
                    <select name="course_lesson_id" id="course_lesson_id" class="{{ $fieldClass }}">
                        <option value="">لا يوجد</option>
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}" {{ old('course_lesson_id', $lecture->course_lesson_id) == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                        @endforeach
                    </select>
                    @error('course_lesson_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="title" class="{{ $labelClass }}">عنوان المحاضرة <span class="text-rose-600">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $lecture->title) }}" required
                           class="{{ $fieldClass }}"
                           placeholder="مثال: المحاضرة الأولى - المقدمة">
                    @error('title')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="description" class="{{ $labelClass }}">الوصف</label>
                    <textarea name="description" id="description" rows="3"
                              class="{{ $textareaClass }} resize-none"
                              placeholder="وصف مختصر عن المحاضرة">{{ old('description', $lecture->description) }}</textarea>
                    @error('description')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-5 py-4">
                <h3 class="text-sm font-semibold text-ink">رابط تسجيل المحاضرة</h3>
            </div>
            <div class="space-y-5 p-5 sm:p-6">
                <div>
                    <label for="video_platform" class="{{ $labelClass }}">منصة الفيديو</label>
                    <select name="video_platform" id="video_platform" class="{{ $fieldClass }}">
                        <option value="">—</option>
                        @foreach($platforms as $key => $label)
                            <option value="{{ $key }}" {{ old('video_platform', $lecture->video_platform) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="recording_url" class="{{ $labelClass }}">رابط التسجيل أو الفيديو</label>
                    <input type="url" name="recording_url" id="recording_url" value="{{ old('recording_url', $lecture->recording_url) }}"
                           class="{{ $fieldClass }}"
                           placeholder="https://...">
                    @error('recording_url')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-5 py-4">
                <h3 class="text-sm font-semibold text-ink">التاريخ والوقت والحالة</h3>
            </div>
            <div class="p-5 sm:p-6">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @include('partials.timezone-select', [
                        'value' => old('timezone', auth()->user()?->timezoneCode()),
                        'class' => $fieldClass,
                        'labelClass' => $labelClass,
                    ])
                    <div>
                        <label for="scheduled_at" class="{{ $labelClass }}">تاريخ ووقت المحاضرة <span class="text-rose-600">*</span></label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ $scheduledAtValue }}" required
                               class="{{ $fieldClass }}">
                        @error('scheduled_at')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="duration_minutes" class="{{ $labelClass }}">المدة (دقيقة) <span class="text-rose-600">*</span></label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $lecture->duration_minutes) }}" min="1" max="480" required
                               class="{{ $fieldClass }}">
                        @error('duration_minutes')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="status" class="{{ $labelClass }}">الحالة <span class="text-rose-600">*</span></label>
                        <select name="status" id="status" required class="{{ $fieldClass }}">
                            <option value="scheduled" {{ old('status', $lecture->status) == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                            <option value="in_progress" {{ old('status', $lecture->status) == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                            <option value="completed" {{ old('status', $lecture->status) == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            <option value="cancelled" {{ old('status', $lecture->status) == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                        </select>
                        @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-5 py-4">
                <h3 class="text-sm font-semibold text-ink">ملاحظات</h3>
            </div>
            <div class="p-5 sm:p-6">
                <textarea name="notes" id="notes" rows="4"
                          class="{{ $textareaClass }} resize-none"
                          placeholder="ملاحظات إضافية">{{ old('notes', $lecture->notes) }}</textarea>
                @error('notes')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </article>

        <article class="overflow-hidden rounded-2xl border border-line bg-surface shadow-soft">
            <div class="border-b border-line px-5 py-4">
                <h3 class="text-sm font-semibold text-ink">خيارات المحاضرة</h3>
            </div>
            <div class="p-5 sm:p-6">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-4 transition hover:border-accent/30 hover:bg-accent-soft/20">
                        <input type="hidden" name="has_attendance_tracking" value="0">
                        <input type="checkbox" name="has_attendance_tracking" value="1" {{ old('has_attendance_tracking', $lecture->has_attendance_tracking) ? 'checked' : '' }}
                               class="size-4 rounded border-line text-accent focus:ring-accent/20">
                        <span class="text-sm font-medium text-ink">تتبع الحضور</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-4 transition hover:border-accent/30 hover:bg-accent-soft/20">
                        <input type="hidden" name="has_assignment" value="0">
                        <input type="checkbox" name="has_assignment" value="1" {{ old('has_assignment', $lecture->has_assignment) ? 'checked' : '' }}
                               class="size-4 rounded border-line text-accent focus:ring-accent/20">
                        <span class="text-sm font-medium text-ink">يوجد واجب</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-4 transition hover:border-accent/30 hover:bg-accent-soft/20">
                        <input type="hidden" name="has_evaluation" value="0">
                        <input type="checkbox" name="has_evaluation" value="1" {{ old('has_evaluation', $lecture->has_evaluation) ? 'checked' : '' }}
                               class="size-4 rounded border-line text-accent focus:ring-accent/20">
                        <span class="text-sm font-medium text-ink">يوجد تقييم للمحاضر</span>
                    </label>
                </div>
            </div>
        </article>

        <div class="flex flex-wrap items-center justify-end gap-2">
            <a href="{{ route('admin.lectures.by-course', $lecture->course_id) }}"
               class="btn-press inline-flex h-10 items-center rounded-xl border border-line px-4 text-sm font-medium text-ink hover:bg-accent-soft hover:text-accent">
                إلغاء
            </a>
            <button type="submit"
                    class="btn-press inline-flex h-10 items-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                <i class="fas fa-save text-xs"></i>
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection
