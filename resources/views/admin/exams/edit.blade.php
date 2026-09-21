@extends('layouts.admin')

@section('title', 'تحرير الامتحان')
@section('page_title', 'تحرير الامتحان')

@php
    $startTime = old('start_time');
    if ($startTime === null && $exam->start_time) $startTime = $exam->start_time->format('Y-m-d\TH:i');
    $endTime = old('end_time');
    if ($endTime === null && $exam->end_time) $endTime = $exam->end_time->format('Y-m-d\TH:i');
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $textareaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20 resize-none';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $checkboxClass = 'size-4 rounded border-line text-accent focus:ring-accent/20';
@endphp

@section('content')
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-accent">لوحة التحكم</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.exams.index') }}" class="hover:text-accent">الامتحانات</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}" class="hover:text-accent">{{ Str::limit($exam->course->title ?? '', 30) }}</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.exams.show', $exam) }}" class="hover:text-accent">{{ Str::limit($exam->title, 25) }}</a>
                <span class="mx-1">·</span>
                <span class="text-ink">تحرير</span>
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">تحرير الامتحان</h2>
            <p class="mt-1 text-sm text-muted">{{ Str::limit($exam->title, 50) }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.exams.show', $exam) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-eye text-xs"></i>
                عرض
            </a>
            <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}"
               class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                <i class="fas fa-arrow-right text-xs"></i>
                رجوع لامتحانات البرنامج
            </a>
        </div>
    </section>

    <form action="{{ route('admin.exams.update', $exam) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="space-y-5 xl:col-span-2">
                <article class="rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-5 py-4">
                        <h3 class="text-sm font-semibold text-ink">معلومات الامتحان</h3>
                    </div>
                    <div class="space-y-5 p-5">
                        <div>
                            <label for="title" class="{{ $labelClass }}">عنوان الامتحان <span class="text-rose-600">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $exam->title) }}" required
                                   class="{{ $fieldClass }}" placeholder="مثال: امتحان الوحدة الأولى">
                            @error('title')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="advanced_course_id" class="{{ $labelClass }}">البرنامج <span class="text-rose-600">*</span></label>
                            <select name="advanced_course_id" id="advanced_course_id" required class="{{ $fieldClass }}">
                                <option value="">اختر البرنامج</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('advanced_course_id', $exam->advanced_course_id) == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}{{ $course->academicSubject ? ' — ' . $course->academicSubject->name : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('advanced_course_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="course_lesson_id" class="{{ $labelClass }}">الدرس (اختياري)</label>
                            <select name="course_lesson_id" id="course_lesson_id" class="{{ $fieldClass }}">
                                <option value="">لا يوجد</option>
                                @foreach($lessons as $lesson)
                                    <option value="{{ $lesson->id }}" {{ old('course_lesson_id', $exam->course_lesson_id) == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                                @endforeach
                            </select>
                            @error('course_lesson_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="description" class="{{ $labelClass }}">الوصف</label>
                            <textarea name="description" id="description" rows="3" placeholder="وصف مختصر" class="{{ $textareaClass }}">{{ old('description', $exam->description) }}</textarea>
                            @error('description')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="instructions" class="{{ $labelClass }}">تعليمات الامتحان</label>
                            <textarea name="instructions" id="instructions" rows="4" placeholder="تعليمات للطلاب قبل البدء" class="{{ $textareaClass }}">{{ old('instructions', $exam->instructions) }}</textarea>
                            @error('instructions')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-5 py-4">
                        <h3 class="text-sm font-semibold text-ink">إعدادات الامتحان</h3>
                    </div>
                    <div class="space-y-5 p-5">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="duration_minutes" class="{{ $labelClass }}">مدة الامتحان (دقيقة) <span class="text-rose-600">*</span></label>
                                <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" required min="5" max="480"
                                       class="{{ $fieldClass }}" placeholder="60">
                                @error('duration_minutes')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="attempts_allowed" class="{{ $labelClass }}">المحاولات المسموحة <span class="text-rose-600">*</span></label>
                                <input type="number" name="attempts_allowed" id="attempts_allowed" value="{{ old('attempts_allowed', $exam->attempts_allowed) }}" required min="0" max="10"
                                       class="{{ $fieldClass }}" placeholder="1">
                                <p class="mt-1 text-xs text-muted">0 = غير محدود</p>
                                @error('attempts_allowed')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="passing_marks" class="{{ $labelClass }}">درجة النجاح (%) <span class="text-rose-600">*</span></label>
                                <input type="number" name="passing_marks" id="passing_marks" value="{{ old('passing_marks', $exam->passing_marks) }}" required min="0" max="100" step="0.1"
                                       class="{{ $fieldClass }}" placeholder="50">
                                @error('passing_marks')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="total_marks" class="{{ $labelClass }}">إجمالي الدرجات</label>
                                <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" min="0" step="0.1"
                                       class="{{ $fieldClass }}" placeholder="يُحسب من الأسئلة">
                                @error('total_marks')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-5 py-4">
                        <h3 class="text-sm font-semibold text-ink">توقيتات الامتحان</h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="start_time" class="{{ $labelClass }}">وقت البداية</label>
                                <input type="datetime-local" name="start_time" id="start_time" value="{{ $startTime }}" class="{{ $fieldClass }}">
                                <p class="mt-1 text-xs text-muted">فارغ = متاح فوراً</p>
                                @error('start_time')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="end_time" class="{{ $labelClass }}">وقت النهاية</label>
                                <input type="datetime-local" name="end_time" id="end_time" value="{{ $endTime }}" class="{{ $fieldClass }}">
                                <p class="mt-1 text-xs text-muted">فارغ = متاح باستمرار</p>
                                @error('end_time')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-5 py-4">
                        <h3 class="text-sm font-semibold text-ink">إعدادات العرض والمراجعة</h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div class="space-y-3">
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="hidden" name="randomize_questions" value="0">
                                    <input type="checkbox" name="randomize_questions" value="1" {{ old('randomize_questions', $exam->randomize_questions) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm font-medium text-ink">خلط ترتيب الأسئلة</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="hidden" name="randomize_options" value="0">
                                    <input type="checkbox" name="randomize_options" value="1" {{ old('randomize_options', $exam->randomize_options) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm font-medium text-ink">خلط خيارات الإجابة</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="hidden" name="show_results_immediately" value="0">
                                    <input type="checkbox" name="show_results_immediately" value="1" {{ old('show_results_immediately', $exam->show_results_immediately) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm font-medium text-ink">عرض النتائج فور الانتهاء</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="hidden" name="allow_review" value="0">
                                    <input type="checkbox" name="allow_review" value="1" {{ old('allow_review', $exam->allow_review) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm font-medium text-ink">السماح بمراجعة الأسئلة والإجابات</span>
                                </label>
                            </div>
                            <div class="space-y-3">
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="hidden" name="show_correct_answers" value="0">
                                    <input type="checkbox" name="show_correct_answers" value="1" {{ old('show_correct_answers', $exam->show_correct_answers) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm font-medium text-ink">عرض الإجابات الصحيحة</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="hidden" name="show_explanations" value="0">
                                    <input type="checkbox" name="show_explanations" value="1" {{ old('show_explanations', $exam->show_explanations) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm font-medium text-ink">عرض تفسيرات الإجابات</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="hidden" name="prevent_tab_switch" value="0">
                                    <input type="checkbox" name="prevent_tab_switch" value="1" {{ old('prevent_tab_switch', $exam->prevent_tab_switch) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm font-medium text-ink">منع تبديل التبويبات أثناء الامتحان</span>
                                </label>
                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="hidden" name="auto_submit" value="0">
                                    <input type="checkbox" name="auto_submit" value="1" {{ old('auto_submit', $exam->auto_submit) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm font-medium text-ink">تسليم تلقائي عند انتهاء الوقت</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-5 py-4">
                        <h3 class="text-sm font-semibold text-ink">إعدادات الأمان</h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                <input type="hidden" name="require_camera" value="0">
                                <input type="checkbox" name="require_camera" value="1" {{ old('require_camera', $exam->require_camera) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                <span class="text-sm font-medium text-ink">تتطلب تفعيل الكاميرا</span>
                            </label>
                            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                <input type="hidden" name="require_microphone" value="0">
                                <input type="checkbox" name="require_microphone" value="1" {{ old('require_microphone', $exam->require_microphone) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                <span class="text-sm font-medium text-ink">تتطلب تفعيل الميكروفون</span>
                            </label>
                        </div>
                    </div>
                </article>
            </div>

            <div class="space-y-5">
                <article class="rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-5 py-4">
                        <h3 class="text-sm font-semibold text-ink">حالة الامتحان</h3>
                    </div>
                    <div class="p-5">
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-line p-4 transition hover:bg-accent-soft/30">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $exam->is_active) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                            <span class="font-semibold text-ink">امتحان نشط</span>
                        </label>
                        <p class="mt-2 text-xs text-muted">غير النشط لا يظهر للطلاب</p>
                    </div>
                </article>

                <article class="rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-5 py-4">
                        <h3 class="text-sm font-semibold text-ink">معلومات</h3>
                    </div>
                    <div class="space-y-3 p-5 text-sm">
                        <div class="flex justify-between"><span class="text-muted">تاريخ الإنشاء</span><span class="font-medium tabular-nums text-ink">{{ $exam->created_at->format('Y-m-d H:i') }}</span></div>
                        <div class="flex justify-between"><span class="text-muted">آخر تحديث</span><span class="font-medium tabular-nums text-ink">{{ $exam->updated_at->format('Y-m-d H:i') }}</span></div>
                        <div class="flex justify-between"><span class="text-muted">الأسئلة</span><span class="font-medium tabular-nums text-ink">{{ $exam->examQuestions->count() }}</span></div>
                        <div class="flex justify-between"><span class="text-muted">المحاولات</span><span class="font-medium tabular-nums text-ink">{{ $exam->attempts->count() }}</span></div>
                    </div>
                </article>

                <div class="flex flex-col gap-3">
                    <button type="submit" class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                        <i class="fas fa-save text-xs"></i>
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.exams.by-course', $exam->advanced_course_id) }}" class="btn-press inline-flex h-11 w-full items-center justify-center rounded-xl border border-line text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                        إلغاء
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var courseSelect = document.getElementById('advanced_course_id');
    var lessonSelect = document.getElementById('course_lesson_id');
    if (!courseSelect || !lessonSelect) return;

    courseSelect.addEventListener('change', function() {
        var courseId = this.value;
        lessonSelect.innerHTML = '<option value="">جاري التحميل...</option>';

        if (!courseId) {
            lessonSelect.innerHTML = '<option value="">لا يوجد</option>';
            return;
        }

        var url = '/admin/courses/' + courseId + '/lessons-list';
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                lessonSelect.innerHTML = '<option value="">لا يوجد</option>';
                var list = Array.isArray(data) ? data : (data.lessons || data.data || []);
                list.forEach(function(lesson) {
                    var opt = document.createElement('option');
                    opt.value = lesson.id;
                    opt.textContent = lesson.title;
                    lessonSelect.appendChild(opt);
                });
            })
            .catch(function() {
                lessonSelect.innerHTML = '<option value="">خطأ في التحميل</option>';
            });
    });
});
</script>
@endpush
@endsection
