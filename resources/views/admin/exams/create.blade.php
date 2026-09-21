@extends('layouts.admin')

@section('title', 'إنشاء امتحان جديد')
@section('page_title', 'إنشاء امتحان جديد')

@section('content')
@php
    $fieldClass = 'h-11 w-full rounded-xl border border-line bg-surface px-4 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $textareaClass = 'w-full rounded-xl border border-line bg-surface px-4 py-3 text-sm text-ink transition focus:border-accent focus:outline-none focus:ring-2 focus:ring-accent/20';
    $labelClass = 'mb-1.5 block text-xs font-medium text-muted';
    $checkboxClass = 'size-4 rounded border-line text-accent focus:ring-accent/20';
@endphp
<div class="space-y-5">
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-muted">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-accent">لوحة التحكم</a>
                <span class="mx-1">·</span>
                <a href="{{ route('admin.exams.index') }}" class="hover:text-accent">الامتحانات</a>
                @if($selectedCourse)
                    <span class="mx-1">·</span>
                    <a href="{{ route('admin.exams.by-course', $selectedCourse) }}" class="hover:text-accent">امتحانات البرنامج</a>
                @endif
                <span class="mx-1">·</span>
                <span class="text-ink">إنشاء امتحان جديد</span>
            </p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-ink md:text-[28px]">إنشاء امتحان جديد</h2>
            <p class="mt-1 max-w-2xl text-sm text-muted">أضف معلومات الامتحان، الإعدادات، والتوقيت ثم أضف الأسئلة لاحقاً.</p>
        </div>
        <a href="{{ $selectedCourse ? route('admin.exams.by-course', $selectedCourse) : route('admin.exams.index') }}"
           class="btn-press inline-flex h-9 items-center gap-2 rounded-xl border border-line px-4 text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
            <i class="fas fa-arrow-right text-xs"></i>
            {{ $selectedCourse ? 'العودة لامتحانات البرنامج' : 'العودة' }}
        </a>
    </section>

    <form action="{{ route('admin.exams.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="space-y-5 xl:col-span-2">
                <article class="rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-5 py-4">
                        <h3 class="text-sm font-semibold text-ink">معلومات الامتحان</h3>
                    </div>
                    <div class="space-y-5 p-5">
                        <div>
                            <label for="title" class="{{ $labelClass }}">
                                عنوان الامتحان <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                   class="{{ $fieldClass }}"
                                   placeholder="مثال: امتحان الوحدة الأولى - الرياضيات">
                            @error('title')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="advanced_course_id" class="{{ $labelClass }}">
                                    البرنامج <span class="text-rose-600">*</span>
                                </label>
                                <select name="advanced_course_id" id="advanced_course_id" required onchange="loadLessons()"
                                        class="{{ $fieldClass }}">
                                    <option value="">اختر البرنامج</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ (old('advanced_course_id', $selectedCourse) == $course->id) ? 'selected' : '' }}>
                                            {{ $course->title }} - {{ $course->academicSubject->name ?? 'غير محدد' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('advanced_course_id')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="course_lesson_id" class="{{ $labelClass }}">
                                    الدرس (اختياري)
                                </label>
                                <select name="course_lesson_id" id="course_lesson_id" class="{{ $fieldClass }}">
                                    <option value="">امتحان عام للبرنامج</option>
                                    @foreach($lessons as $lesson)
                                        <option value="{{ $lesson->id }}" {{ old('course_lesson_id') == $lesson->id ? 'selected' : '' }}>
                                            {{ $lesson->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="description" class="{{ $labelClass }}">وصف الامتحان</label>
                            <textarea name="description" id="description" rows="3" class="{{ $textareaClass }}"
                                      placeholder="وصف مختصر عن الامتحان ومحتواه...">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label for="instructions" class="{{ $labelClass }}">تعليمات الامتحان</label>
                            <textarea name="instructions" id="instructions" rows="4" class="{{ $textareaClass }}"
                                      placeholder="اكتب التعليمات التي ستظهر للطالب قبل بدء الامتحان...">{{ old('instructions') }}</textarea>
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-5 py-4">
                        <h3 class="text-sm font-semibold text-ink">إعدادات التوقيت والدرجات</h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                            <div>
                                <label for="duration_minutes" class="{{ $labelClass }}">
                                    مدة الامتحان (دقيقة) <span class="text-rose-600">*</span>
                                </label>
                                <input type="number" name="duration_minutes" id="duration_minutes"
                                       value="{{ old('duration_minutes', 60) }}" min="5" max="480" required
                                       class="{{ $fieldClass }}">
                            </div>

                            <div>
                                <label for="attempts_allowed" class="{{ $labelClass }}">
                                    عدد المحاولات المسموحة <span class="text-rose-600">*</span>
                                </label>
                                <input type="number" name="attempts_allowed" id="attempts_allowed"
                                       value="{{ old('attempts_allowed', 1) }}" min="0" max="10" required
                                       class="{{ $fieldClass }}">
                                <p class="mt-1 text-xs text-muted">0 = محاولات غير محدودة</p>
                            </div>

                            <div>
                                <label for="passing_marks" class="{{ $labelClass }}">
                                    درجة النجاح (%) <span class="text-rose-600">*</span>
                                </label>
                                <input type="number" name="passing_marks" id="passing_marks"
                                       value="{{ old('passing_marks', 60) }}" min="0" max="100" step="0.5" required
                                       class="{{ $fieldClass }}">
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="start_time" class="{{ $labelClass }}">تاريخ بداية الامتحان</label>
                                <input type="datetime-local" name="start_time" id="start_time"
                                       value="{{ old('start_time') }}" class="{{ $fieldClass }}">
                                <p class="mt-1 text-xs text-muted">اتركه فارغاً إذا كان متاحاً دائماً</p>
                            </div>

                            <div>
                                <label for="end_time" class="{{ $labelClass }}">تاريخ انتهاء الامتحان</label>
                                <input type="datetime-local" name="end_time" id="end_time"
                                       value="{{ old('end_time') }}" class="{{ $fieldClass }}">
                                <p class="mt-1 text-xs text-muted">اتركه فارغاً إذا كان متاحاً دائماً</p>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-5 py-4">
                        <h3 class="text-sm font-semibold text-ink">إعدادات العرض والأمان</h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div class="space-y-3">
                                <h4 class="text-xs font-semibold uppercase tracking-wider text-muted">إعدادات العرض</h4>

                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="checkbox" name="randomize_questions" value="1"
                                           {{ old('randomize_questions') ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm text-ink">خلط ترتيب الأسئلة</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="checkbox" name="randomize_options" value="1"
                                           {{ old('randomize_options') ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm text-ink">خلط ترتيب الخيارات</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="checkbox" name="show_results_immediately" value="1"
                                           {{ old('show_results_immediately', true) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm text-ink">عرض النتيجة فوراً</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="checkbox" name="show_correct_answers" value="1"
                                           {{ old('show_correct_answers') ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm text-ink">عرض الإجابات الصحيحة</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="checkbox" name="show_explanations" value="1"
                                           {{ old('show_explanations') ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm text-ink">عرض شرح الإجابات</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="checkbox" name="allow_review" value="1"
                                           {{ old('allow_review', true) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm text-ink">السماح بمراجعة الإجابات</span>
                                </label>
                            </div>

                            <div class="space-y-3">
                                <h4 class="text-xs font-semibold uppercase tracking-wider text-muted">إعدادات الأمان</h4>

                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="checkbox" name="prevent_tab_switch" value="1"
                                           {{ old('prevent_tab_switch', true) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm text-ink">منع تبديل التبويبات</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="checkbox" name="auto_submit" value="1"
                                           {{ old('auto_submit', true) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm text-ink">تسليم تلقائي عند انتهاء الوقت</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="checkbox" name="require_camera" value="1"
                                           {{ old('require_camera') ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm text-ink">تتطلب تفعيل الكاميرا</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="checkbox" name="require_microphone" value="1"
                                           {{ old('require_microphone') ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm text-ink">تتطلب تفعيل المايكروفون</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-line p-3 transition hover:bg-accent-soft/30">
                                    <input type="checkbox" name="is_active" value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }} class="{{ $checkboxClass }}">
                                    <span class="text-sm text-ink">امتحان نشط</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <div class="space-y-5">
                <article class="rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="border-b border-line px-5 py-4">
                        <h3 class="text-sm font-semibold text-ink">معلومات سريعة</h3>
                    </div>
                    <div class="p-5">
                        <div class="rounded-xl border border-line bg-accent-soft/30 p-4">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-info-circle text-accent"></i>
                                <span class="text-sm font-medium text-ink">نصائح</span>
                            </div>
                            <ul class="mt-2 space-y-1 text-sm text-muted">
                                <li>• بعد الإنشاء ستتمكن من إضافة الأسئلة</li>
                                <li>• يمكن اختيار أسئلة من البنك أو إنشاء جديدة</li>
                                <li>• تأكد من اختبار الامتحان قبل النشر</li>
                            </ul>
                        </div>
                    </div>
                </article>

                <article class="rounded-2xl border border-line bg-surface shadow-soft">
                    <div class="space-y-3 p-5">
                        <button type="submit"
                                class="btn-press inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent px-4 text-sm font-medium text-white hover:bg-[#184888]">
                            <i class="fas fa-save text-xs"></i>
                            إنشاء الامتحان
                        </button>

                        <a href="{{ route('admin.exams.index') }}"
                           class="btn-press inline-flex h-11 w-full items-center justify-center rounded-xl border border-line text-sm font-medium text-ink transition hover:bg-accent-soft hover:text-accent">
                            إلغاء
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function loadLessons() {
    const courseId = document.getElementById('advanced_course_id').value;
    const lessonSelect = document.getElementById('course_lesson_id');

    lessonSelect.innerHTML = '<option value="">امتحان عام للبرنامج</option>';

    if (courseId) {
        fetch(`/admin/courses/${courseId}/lessons-list`)
            .then(response => response.json())
            .then(lessons => {
                lessons.forEach(lesson => {
                    const option = document.createElement('option');
                    option.value = lesson.id;
                    option.textContent = lesson.title;
                    lessonSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading lessons:', error);
            });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const courseId = document.getElementById('advanced_course_id').value;
    if (courseId) {
        loadLessons();
    }
});
</script>
@endpush
@endsection
