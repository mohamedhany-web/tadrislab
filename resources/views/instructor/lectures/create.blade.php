@extends('layouts.instructor-timeline')

@section('title', __('instructor.add_new_lecture') . ' - ' . config('app.name'))
@section('page_title', __('instructor.add_new_lecture'))

@push('styles')
<style>
    .su-platform-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:12px; margin-bottom:16px; }
    .su-platform-opt { padding:16px; border:1px solid var(--su-line); border-radius:14px; cursor:pointer; text-align:center; background:var(--su-card); transition:.15s; }
    .su-platform-opt:hover { border-color:var(--su-accent); }
    .su-platform-opt.active { border-color:var(--su-accent); background:rgba(11,61,145,.06); box-shadow:0 0 0 3px rgba(11,61,145,.12); }
    .su-platform-opt i { font-size:1.75rem; display:block; margin-bottom:6px; }
    .video-preview-container { min-height:300px; background:var(--su-bg); border:1px dashed var(--su-line); border-radius:14px; display:flex; align-items:center; justify-content:center; overflow:hidden; }
    .video-preview-container.has-video { border-style:solid; background:#000; }
    .video-preview-container iframe, .video-preview-container video { width:100%; height:100%; min-height:300px; }
    .loading-spinner { display:inline-block; width:18px; height:18px; border:2px solid var(--su-line); border-top-color:var(--su-accent); border-radius:50%; animation:su-spin .8s linear infinite; }
    @keyframes su-spin { to { transform:rotate(360deg); } }
</style>
@endpush

@section('content')
@php
    $placeholders = [
        'bunny' => __('instructor.paste_bunny'),
        'default' => __('instructor.paste_video'),
    ];
    $isRtl = app()->getLocale() === 'ar';
@endphp
<div class="su-page" style="max-width:56rem">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.lectures.index') }}">{{ __('instructor.lectures') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('instructor.add_new_lecture') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-chalkboard-teacher su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.add_new_lecture') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.create_lecture_subtitle') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.lectures.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <form action="{{ route('instructor.lectures.store') }}" method="POST" enctype="multipart/form-data"
          class="su-card"
          x-data="videoPreviewData()">
        @csrf
        <div style="display:flex;flex-direction:column;gap:28px">
            <div>
                <h2 style="font-size:15px;font-weight:700;margin:0 0 12px;padding-bottom:8px;border-bottom:1px solid var(--su-line)">{{ __('instructor.basic_info') }}</h2>
                <div class="su-form-grid" style="grid-template-columns:1fr 1fr">
                    <div class="su-field">
                        <label for="course_id">{{ __('instructor.course_label') }} <span style="color:#b91c1c">*</span></label>
                        <select name="course_id" id="course_id" required class="su-select">
                            <option value="">{{ __('instructor.choose_course') }}</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ (old('course_id', request('course_id')) == $course->id) ? 'selected' : '' }}>{{ $course->title }}</option>
                            @endforeach
                        </select>
                        @error('course_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="course_lesson_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('instructor.lesson_optional') }}</label>
                        <select name="course_lesson_id" id="course_lesson_id" class="su-select">
                            <option value="">{{ __('instructor.no_lesson') }}</option>
                            @foreach($lessons as $lesson)
                                <option value="{{ $lesson->id }}" {{ old('course_lesson_id') == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('instructor.lesson_link_hint') }}</p>
                        @error('course_lesson_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('instructor.lecture_title') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                               placeholder="{{ __('instructor.lecture_title_placeholder') }}"
                               class="su-input">
                        @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('instructor.description') }}</label>
                        <textarea name="description" id="description" rows="3" placeholder="{{ __('instructor.description_placeholder') }}"
                                  class="su-input" style="min-height:88px;resize:vertical">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Recording link -->
            <div style="padding-top:24px;border-top:1px solid var(--su-line)">
                <h2 style="font-size:15px;font-weight:700;margin:0 0 12px;padding-bottom:8px;border-bottom:1px solid var(--su-line)">
                    <i class="fas fa-video text-sky-600 ml-1"></i>
                    {{ __('instructor.recording_link_section') }}
                </h2>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">{{ __('instructor.video_source_question') }} <span class="text-red-500">*</span></label>
                    <div class="su-platform-grid">
                        <div class="su-platform-opt" :class="{ 'active': selectedPlatform === 'bunny' }" @click="selectPlatform('bunny')">
                            <i class="fas fa-cloud text-orange-600"></i>
                            <div class="font-bold text-slate-800 dark:text-slate-100 text-sm mt-1">Bunny.net</div>
                        </div>
                    </div>
                    <input type="hidden" name="video_platform" x-model="selectedPlatform" required>
                </div>

                <div x-show="selectedPlatform" x-transition class="space-y-4">
                    <div>
                        <label for="recording_url" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('instructor.video_url') }} <span class="text-red-500">*</span></label>
                        <div class="flex gap-3 flex-wrap">
                            <input type="url" id="recording_url" name="recording_url"
                                   x-model="videoUrl"
                                   @input.debounce.1000ms="updatePreview()"
                                   @paste.debounce.1000ms="updatePreview()"
                                   @blur="updatePreview()"
                                   value="{{ old('recording_url') }}"
                                   :placeholder="getPlaceholder()"
                                   class="su-input" style="flex:1;min-width:200px">
                            <button type="button" @click="updatePreview()"
                                    :disabled="!selectedPlatform || !videoUrl || isLoading"
                                    class="su-btn su-btn--primary">
                                <span x-show="!isLoading"><i class="fas fa-search ml-1"></i> {{ __('instructor.read_link') }}</span>
                                <span x-show="isLoading" class="flex items-center gap-2"><span class="loading-spinner"></span> {{ __('instructor.reading_link') }}</span>
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400" x-show="selectedPlatform"><i class="fas fa-info-circle ml-1"></i> {{ __('instructor.video_info_auto') }}</p>
                        @error('recording_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div x-show="videoInfo" class="su-card" style="background:var(--su-bg);margin:0" x-transition>
                        <h4 class="font-bold text-slate-800 dark:text-slate-100 mb-2 flex items-center gap-2"><i class="fas fa-info-circle text-sky-600"></i> {{ __('instructor.video_info') }}</h4>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><span class="font-semibold text-slate-600 dark:text-slate-400">{{ __('instructor.title_label') }}:</span> <span class="text-slate-800 dark:text-slate-100" x-text="videoInfo?.title || '{{ addslashes(__('instructor.not_available')) }}'"></span></div>
                            <div><span class="font-semibold text-slate-600 dark:text-slate-400">{{ __('instructor.duration_label') }}:</span> <span class="text-slate-800 dark:text-slate-100" x-text="videoInfo?.duration || '{{ addslashes(__('instructor.not_available')) }}'"></span></div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">{{ __('instructor.video_preview') }}</label>
                        <div class="video-preview-container" :class="{ 'has-video': hasPreview }">
                            <div x-show="!hasPreview && selectedPlatform" class="text-center text-slate-500 dark:text-slate-400 p-8">
                                <i class="fas fa-video text-4xl mb-3 text-slate-300"></i>
                                <p class="font-bold text-slate-600 dark:text-slate-400">{{ __('instructor.video_preview') }}</p>
                                <p class="text-sm">{{ __('instructor.video_preview_hint') }}</p>
                            </div>
                            <div x-ref="previewContainer" class="w-full h-full flex items-center justify-center p-4" style="min-height: 300px;"></div>
                        </div>
                    </div>
                </div>

                <div x-show="!selectedPlatform" class="su-empty">
                    <i class="fas fa-hand-point-up text-3xl text-sky-500 mb-2"></i>
                    <p class="font-bold text-slate-800 dark:text-slate-100">{{ __('instructor.choose_video_source_first') }}</p>
                    <p class="text-sm text-slate-600 dark:text-slate-400">{{ __('instructor.choose_platform_hint') }}</p>
                </div>
            </div>

            <!-- Date & duration -->
            <div style="padding-top:24px;border-top:1px solid var(--su-line)">
                <h2 style="font-size:15px;font-weight:700;margin:0 0 12px;padding-bottom:8px;border-bottom:1px solid var(--su-line)">
                    <i class="fas fa-calendar-alt text-sky-600 ml-1"></i>
                    {{ __('instructor.date_time') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @include('partials.timezone-select', ['value' => old('timezone', auth()->user()?->timezoneCode())])
                    <div>
                        <label for="scheduled_at" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('instructor.date_time') }} <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at') }}" required
                               class="su-input">
                        @error('scheduled_at')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="duration_minutes" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('instructor.duration_minutes_label') }} <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="15" max="480" required
                               class="su-input">
                        @error('duration_minutes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="min_watch_percent_to_unlock_next">{{ __('instructor.min_watch_percent') }}</label>
                        <input type="number" name="min_watch_percent_to_unlock_next" id="min_watch_percent_to_unlock_next"
                               value="{{ old('min_watch_percent_to_unlock_next', 0) }}" min="0" max="100"
                               class="su-input"
                               placeholder="{{ __('instructor.min_watch_percent_ph') }}">
                        <p style="margin:6px 0 0;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.min_watch_percent_hint') }}</p>
                    </div>
                </div>
            </div>

            <!-- مواد المحاضرة -->
            <div style="padding-top:24px;border-top:1px solid var(--su-line)">
                <h2 style="font-size:15px;font-weight:700;margin:0 0 12px;padding-bottom:8px;border-bottom:1px solid var(--su-line)">
                    <i class="fas fa-paperclip" aria-hidden="true"></i>
                    {{ __('instructor.lecture_materials') }}
                </h2>
                <p style="margin:0 0 12px;font-size:13px;color:var(--su-ink-40)">{{ __('instructor.lecture_materials_hint') }}</p>
                <div id="materials-container" style="display:flex;flex-direction:column;gap:12px">
                    <div class="material-row" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:12px;padding:14px;border:1px solid var(--su-line);border-radius:14px">
                        <div style="flex:1;min-width:180px">
                            <label style="display:block;font-size:12px;margin-bottom:4px">{{ __('instructor.file_label') }}</label>
                            <input type="file" name="material_files[]" class="su-input" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar,.png,.jpg,.jpeg">
                        </div>
                        <div style="width:12rem">
                            <label style="display:block;font-size:12px;margin-bottom:4px">{{ __('instructor.title_optional') }}</label>
                            <input type="text" name="material_titles[]" placeholder="{{ __('instructor.material_title_ph') }}" class="su-input">
                        </div>
                        <label class="su-chip" style="cursor:pointer">
                            <input type="hidden" name="material_visible[]" value="0">
                            <input type="checkbox" name="material_visible[]" value="1" checked style="margin-inline-end:6px">
                            <span>{{ __('instructor.visible_to_student') }}</span>
                        </label>
                        <button type="button" class="remove-material su-btn" style="display:none;height:32px"><i class="fas fa-times" aria-hidden="true"></i> {{ __('common.delete') }}</button>
                    </div>
                </div>
                <button type="button" id="add-material-btn" class="su-btn" style="margin-top:8px">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    {{ __('instructor.add_material') }}
                </button>
            </div>

            <!-- Notes -->
            <div style="padding-top:24px;border-top:1px solid var(--su-line)">
                <h2 style="font-size:15px;font-weight:700;margin:0 0 12px;padding-bottom:8px;border-bottom:1px solid var(--su-line)">
                    <i class="fas fa-sticky-note text-sky-600 ml-1"></i>
                    {{ __('instructor.notes_section') }}
                </h2>
                <div>
                    <label for="notes" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('instructor.additional_notes') }}</label>
                    <textarea name="notes" id="notes" rows="4" placeholder="{{ __('instructor.notes_placeholder') }}"
                              class="su-input" style="min-height:88px;resize:vertical">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Options -->
            <div style="padding-top:24px;border-top:1px solid var(--su-line)">
                <h2 style="font-size:15px;font-weight:700;margin:0 0 12px;padding-bottom:8px;border-bottom:1px solid var(--su-line)">
                    <i class="fas fa-cog text-sky-600 ml-1"></i>
                    {{ __('instructor.options_section') }}
                </h2>
                <div class="space-y-3">
                    <label class="su-chip" style="cursor:pointer;height:auto;padding:14px;width:100%;justify-content:flex-start;gap:12px">
                        <input type="checkbox" name="has_attendance_tracking" value="1" {{ old('has_attendance_tracking', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-sky-600 border-slate-300 rounded focus:ring-sky-500">
                        <div>
                            <div class="font-bold text-slate-800 dark:text-slate-100">{{ __('instructor.attendance_tracking') }}</div>
                            <div class="text-sm text-slate-600 dark:text-slate-400">{{ __('instructor.attendance_tracking_desc') }}</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="su-page-head__actions" style="justify-content:flex-end;border-top:1px solid var(--su-line);padding-top:16px;margin-top:8px">
            <a href="{{ route('instructor.lectures.index') }}" class="su-btn">{{ __('common.cancel') }}</a>
            <button type="submit" class="su-btn su-btn--primary">
                <i class="fas fa-save" aria-hidden="true"></i>
                {{ __('instructor.save_lecture') }}
            </button>
        </div>
    </form>
</div>

<script>
window.__lecturePlaceholders = @json($placeholders);

function videoPreviewData() {
    return {
        selectedPlatform: '{{ old('video_platform', 'bunny') }}',
        videoUrl: '{{ old('recording_url', '') }}',
        videoInfo: null,
        isLoading: false,
        hasPreview: false,
        selectPlatform(platform) {
            this.selectedPlatform = platform;
            this.videoUrl = '';
            this.hasPreview = false;
            this.videoInfo = null;
            this.clearPreview();
        },
        getPlaceholder() {
            const p = window.__lecturePlaceholders || {};
            if (this.selectedPlatform === 'bunny') return p.bunny || '';
            return p.default || '';
        },
        updatePreview() {
            if (!this.videoUrl || !this.selectedPlatform) { this.hasPreview = false; this.clearPreview(); return; }
            const url = String(this.videoUrl).trim();
            if (!url) { this.hasPreview = false; this.clearPreview(); return; }
            this.generatePreview(url);
            this.fetchVideoInfo();
        },
        generatePreview(url) {
            try {
                const container = this.$refs.previewContainer;
                if (!container) return;
                let html = '', isValid = false;
                const t = window.__lecturePlaceholders || {};
                const youtubeInvalid = '{{ addslashes(__('instructor.youtube_invalid')) }}';
                const vimeoInvalid = '{{ addslashes(__('instructor.vimeo_invalid')) }}';
                const driveNote = '{{ addslashes(__('instructor.drive_note')) }}';
                const directInvalid = '{{ addslashes(__('instructor.direct_invalid')) }}';
                const bunnyInvalid = '{{ addslashes(__('instructor.bunny_invalid')) }}';
                const previewError = '{{ addslashes(__('instructor.preview_error')) }}';

                if (this.selectedPlatform === 'bunny') {
                    const bunnyMatch = url.match(/mediadelivery\.net\/(embed|play)\/(\d+)\/([a-zA-Z0-9_-]+)/);
                    if (bunnyMatch && bunnyMatch[2] && bunnyMatch[3]) {
                        isValid = true;
                        const embedUrl = url.split('?')[0];
                        const src = embedUrl.startsWith('http') ? embedUrl : ('https://' + embedUrl.replace(/^\/+/, ''));
                        html = '<iframe src="' + src.replace(/"/g, '&quot;') + '" width="100%" height="400" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; picture-in-picture" allowfullscreen style="border-radius: 0.75rem;"></iframe>';
                    }
                    if (!isValid) html = '<div class="p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 rounded-lg text-red-700 text-sm"><i class="fas fa-exclamation-circle ml-1"></i> ' + bunnyInvalid + '</div>';
                }
                if (html) { container.innerHTML = html; this.hasPreview = true; } else { this.clearPreview(); }
            } catch (e) {
                console.error(e);
                const container = this.$refs.previewContainer;
                const previewError = '{{ addslashes(__('instructor.preview_error')) }}';
                if (container) { container.innerHTML = '<div class="p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 rounded-lg text-red-700 text-sm">' + previewError + '</div>'; this.hasPreview = true; }
            }
        },
        clearPreview() {
            const c = this.$refs.previewContainer;
            if (c) c.innerHTML = '';
            this.hasPreview = false;
        },
        async fetchVideoInfo() {
            if (!this.videoUrl || !this.selectedPlatform) return;
            this.isLoading = true;
            this.videoInfo = null;
            try {
                const r = await fetch('/api/video/info', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ url: this.videoUrl, platform: this.selectedPlatform })
                });
                const data = await r.json();
                if (data.success) this.videoInfo = data.data;
            } catch (e) { console.log('Video info fetch failed:', e); }
            finally { this.isLoading = false; }
        }
    };
}

document.addEventListener('DOMContentLoaded', function() {
    var materialsContainer = document.getElementById('materials-container');
    var addMaterialBtn = document.getElementById('add-material-btn');
    if (materialsContainer && addMaterialBtn) {
        addMaterialBtn.addEventListener('click', function() {
            var first = materialsContainer.querySelector('.material-row');
            if (!first) return;
            var clone = first.cloneNode(true);
            clone.querySelector('input[type="file"]').value = '';
            clone.querySelector('input[type="text"]').value = '';
            clone.querySelector('input[type="checkbox"]').checked = true;
            clone.querySelector('.remove-material').style.display = 'inline-flex';
            materialsContainer.appendChild(clone);
        });
        materialsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-material')) {
                var row = e.target.closest('.material-row');
                if (materialsContainer.querySelectorAll('.material-row').length > 1) row.remove();
            }
        });
    }

    const courseSelect = document.getElementById('course_id');
    if (!courseSelect) return;
    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        const lessonSelect = document.getElementById('course_lesson_id');
        while (lessonSelect.children.length > 1) lessonSelect.removeChild(lessonSelect.lastChild);
        if (!courseId) return;
        fetch('/api/courses/' + courseId + '/lessons')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.lessons) {
                    data.lessons.forEach(function(lesson) {
                        const opt = document.createElement('option');
                        opt.value = lesson.id;
                        opt.textContent = lesson.title;
                        lessonSelect.appendChild(opt);
                    });
                }
            })
            .catch(function() {
                fetch('{{ route('instructor.lectures.create') }}?course_id=' + courseId)
                    .then(function(r) { return r.text(); })
                    .then(function(html) {
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const sel = doc.getElementById('course_lesson_id');
                        if (sel) Array.from(sel.options).forEach(function(o) {
                            if (o.value) {
                                const no = document.createElement('option');
                                no.value = o.value;
                                no.textContent = o.textContent;
                                lessonSelect.appendChild(no);
                            }
                        });
                    })
                    .catch(function() {});
            });
    });
});
</script>
@endsection
