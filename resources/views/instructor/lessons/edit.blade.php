@extends('layouts.instructor-timeline')

@section('title', __('instructor.lessons_edit_title') . ': ' . $lesson->title)
@section('page_title', __('instructor.lessons_edit_title'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.courses.index') }}">{{ __('instructor.courses') }}</a>
                <span>/</span>
                <a href="{{ route('instructor.courses.show', $course->id) }}">{{ $course->title }}</a>
                <span>/</span>
                <a href="{{ route('instructor.courses.lessons.index', $course->id) }}">{{ __('instructor.lessons_breadcrumb') }}</a>
                <span>/</span>
                <strong style="color:var(--su-ink)">{{ __('instructor.lessons_edit_title') }}</strong>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-edit su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.lessons_edit_title') }}
            </h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08);color:#b91c1c;font-size:13px">
            <strong>{{ __('instructor.form_fix_errors') }}</strong>
            <ul style="margin:8px 0 0;padding-inline-start:18px">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="su-card">
        <form action="{{ route('instructor.courses.lessons.update', [$course->id, $lesson->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="su-form-grid">
                <div class="su-field" style="grid-column:span 2">
                    <label for="title">{{ __('instructor.lessons_title_label') }} *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $lesson->title) }}" required class="su-input" placeholder="{{ __('instructor.lessons_title_ph') }}">
                </div>
                <div class="su-field">
                    <label for="lessonType">{{ __('instructor.lessons_type_label') }} *</label>
                    <select name="type" id="lessonType" required class="su-select">
                        <option value="video" {{ old('type', $lesson->type) == 'video' ? 'selected' : '' }}>{{ __('instructor.lessons_type_video') }}</option>
                        <option value="text" {{ old('type', $lesson->type) == 'text' ? 'selected' : '' }}>{{ __('instructor.lessons_type_text') }}</option>
                        <option value="document" {{ old('type', $lesson->type) == 'document' ? 'selected' : '' }}>{{ __('instructor.lessons_type_document') }}</option>
                        <option value="quiz" {{ old('type', $lesson->type) == 'quiz' ? 'selected' : '' }}>{{ __('instructor.lessons_type_quiz') }}</option>
                    </select>
                </div>
            </div>

            <div class="su-field" style="margin-top:16px">
                <label for="description">{{ __('instructor.description') }}</label>
                <textarea name="description" id="description" rows="3" class="su-input" style="min-height:88px;padding-top:10px" placeholder="{{ __('instructor.lessons_desc_ph') }}">{{ old('description', $lesson->description) }}</textarea>
            </div>

            <div class="video-section {{ old('type', $lesson->type) != 'video' ? 'hidden' : '' }}" style="margin-top:16px">
                <div class="su-card su-soft-1" style="padding:16px">
                    <h3 class="su-card__title" style="margin-bottom:12px">
                        <i class="fas fa-video" aria-hidden="true"></i>
                        {{ __('instructor.lessons_video_settings') }}
                    </h3>
                    <div class="su-form-grid">
                        <div class="su-field">
                            <label for="video_url">{{ __('instructor.lessons_video_url') }}</label>
                            <input type="url" name="video_url" id="video_url" value="{{ old('video_url', $lesson->video_url) }}" class="su-input" placeholder="https://...">
                        </div>
                        <div class="su-field">
                            <label for="video_file">{{ __('instructor.lessons_video_file_label') }}</label>
                            <input type="file" name="video_file" id="video_file" accept="video/*" class="su-input">
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-section {{ old('type', $lesson->type) != 'text' ? 'hidden' : '' }}" style="margin-top:16px">
                <div class="su-field">
                    <label for="content">{{ __('instructor.lessons_content_label') }}</label>
                    <textarea name="content" id="content" rows="8" class="su-input" style="min-height:180px;padding-top:10px" placeholder="{{ __('instructor.lessons_content_ph') }}">{{ old('content', $lesson->content) }}</textarea>
                </div>
            </div>

            <div class="su-form-grid" style="margin-top:16px">
                <div class="su-field">
                    <label for="duration_minutes">{{ __('instructor.lessons_duration_label') }}</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" min="0" class="su-input">
                </div>
                <div class="su-field">
                    <label for="order">{{ __('instructor.lessons_order_label') }} *</label>
                    <input type="number" name="order" id="order" value="{{ old('order', $lesson->order) }}" min="0" required class="su-input">
                </div>
            </div>

            <div class="su-field" style="margin-top:16px">
                <label for="attachments">{{ __('instructor.lessons_attachments') }}</label>
                <input type="file" name="attachments[]" id="attachments" multiple class="su-input">
                <p style="margin:6px 0 0;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.lessons_attachments_edit_hint') }}</p>
            </div>

            <div class="su-card su-soft-2" style="margin-top:16px;padding:16px">
                <h3 class="su-card__title" style="margin-bottom:12px">{{ __('instructor.lessons_options') }}</h3>
                <div style="display:flex;flex-direction:column;gap:10px">
                    <label style="display:inline-flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $lesson->is_active) ? 'checked' : '' }}>
                        {{ __('instructor.lessons_active_check') }}
                    </label>
                    <label style="display:inline-flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
                        <input type="checkbox" name="is_free" value="1" {{ old('is_free', $lesson->is_free) ? 'checked' : '' }}>
                        {{ __('instructor.lessons_free_check') }}
                    </label>
                </div>
            </div>

            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;margin-top:24px;padding-top:16px;border-top:0.5px solid var(--su-line)">
                <a href="{{ route('instructor.courses.lessons.index', $course->id) }}" class="su-btn">
                    <i class="fas fa-times" aria-hidden="true"></i>
                    {{ __('instructor.cancel') }}
                </a>
                <button type="submit" class="su-btn su-btn--primary">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    {{ __('instructor.lessons_save_edits') }}
                </button>
            </div>
        </form>
    </section>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const lessonType = document.getElementById('lessonType');
    const videoSection = document.querySelector('.video-section');
    const textSection = document.querySelector('.text-section');

    function updateSections() {
        const type = lessonType.value;
        videoSection.classList.add('hidden');
        textSection.classList.add('hidden');
        if (type === 'video') videoSection.classList.remove('hidden');
        else if (type === 'text') textSection.classList.remove('hidden');
    }

    lessonType.addEventListener('change', updateSections);
    updateSections();
});
</script>
@endpush
@endsection
