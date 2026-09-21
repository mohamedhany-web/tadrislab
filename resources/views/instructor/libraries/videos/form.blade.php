@extends('layouts.instructor-timeline')

@section('title', $mode === 'create' ? __('instructor.lib_videos_form_create') : __('instructor.lib_videos_form_edit'))
@section('page_title', $mode === 'create' ? __('instructor.lib_videos_form_create') : __('instructor.lib_videos_form_edit'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ $indexRoute }}">{{ __('instructor.lib_videos_title') }}</a>
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-video su-page-head__ico" aria-hidden="true"></i>
                {{ $mode === 'create' ? __('instructor.lib_videos_form_create_heading') : __('instructor.lib_videos_form_edit') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.lib_videos_form_sub') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ $indexRoute }}" class="su-btn">
                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08);color:#b91c1c;font-size:13px">
            {{ $errors->first() }}
        </div>
    @endif
    @if(session('error'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08);color:#b91c1c;font-size:13px">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" id="library-video-form"
          action="{{ $mode === 'create' ? $storeRoute : $storeRoute }}">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif

        <input type="hidden" name="file_path" id="file_path" value="{{ old('file_path', $video->file_path) }}">
        <input type="hidden" name="storage_disk" id="storage_disk" value="{{ old('storage_disk', $video->storage_disk ?: ($uploadDisk ?? 'r2')) }}">
        <input type="hidden" name="file_size" id="file_size" value="{{ old('file_size', $video->file_size ?? 0) }}">
        <input type="hidden" name="mime_type" id="mime_type" value="{{ old('mime_type', $video->mime_type) }}">

        <section class="su-card" style="margin-bottom:20px">
            <div class="su-form-grid" style="grid-template-columns:1fr">
                <div class="su-field">
                    <label for="title">{{ __('instructor.lib_videos_title_label') }}</label>
                    <input type="text" name="title" id="title" required value="{{ old('title', $video->title) }}" class="su-input" placeholder="{{ __('instructor.lib_videos_title_ph') }}">
                </div>
                <div class="su-field">
                    <label for="description">{{ __('instructor.description') }}</label>
                    <textarea name="description" id="description" rows="3" class="su-input" style="min-height:88px;padding-top:10px" placeholder="{{ __('instructor.lessons_desc_ph') }}">{{ old('description', $video->description) }}</textarea>
                </div>
            </div>

            <div class="su-form-grid" style="margin-top:16px">
                <div class="su-field">
                    <label for="content_theme">{{ __('instructor.lib_videos_theme') }}</label>
                    <select name="content_theme" id="content_theme" class="su-select">
                        @foreach(\App\Support\FamilyLibraryThemes::labels(app()->getLocale() === 'ar' ? 'ar' : 'en') as $key => $themeLabel)
                            <option value="{{ $key }}" @selected(old('content_theme', $video->content_theme ?: 'kids') === $key)>{{ $themeLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="su-field">
                    <label for="series_title">{{ __('instructor.lib_videos_series') }}</label>
                    <input type="text" name="series_title" id="series_title" value="{{ old('series_title', $video->series_title) }}" class="su-input">
                </div>
                <div class="su-field">
                    <label for="age_label">{{ __('instructor.lib_videos_age') }}</label>
                    <input type="text" name="age_label" id="age_label" value="{{ old('age_label', $video->age_label) }}" class="su-input" placeholder="{{ __('instructor.lib_videos_age_ph') }}">
                </div>
                <div class="su-field">
                    <label for="library_folder_id">{{ __('instructor.lib_videos_your_folder') }}</label>
                    <select name="library_folder_id" id="library_folder_id" class="su-select">
                        <option value="">{{ __('instructor.lib_videos_no_folder') }}</option>
                        @foreach(($folders ?? []) as $folder)
                            <option value="{{ $folder->id }}" @selected((string) old('library_folder_id', $video->library_folder_id) === (string) $folder->id)>
                                {{ $folder->name_ar }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="su-field">
                    <label for="sort_order">{{ __('instructor.lib_videos_sort') }}</label>
                    <input type="number" name="sort_order" id="sort_order" min="0" value="{{ old('sort_order', $video->sort_order ?? 0) }}" class="su-input">
                </div>
            </div>

            <div class="su-card su-soft-1" style="margin-top:16px;padding:16px">
                <h3 class="su-card__title" style="margin-bottom:10px">{{ __('instructor.lib_videos_external') }}</h3>
                <div class="su-field">
                    <input type="url" name="external_url" id="external_url" value="{{ old('external_url', $video->external_url) }}" class="su-input" placeholder="https://youtube.com/…">
                </div>
            </div>

            <div class="su-card su-soft-2" style="margin-top:16px;padding:16px">
                <h3 class="su-card__title" style="margin-bottom:6px">{{ __('instructor.lib_videos_upload_cf') }}</h3>
                <p style="margin:0 0 12px;font-size:12px;color:var(--su-ink-40)">
                    {{ __('instructor.lib_videos_upload_hint', ['disk' => $uploadDisk ?? 'r2']) }}
                </p>
                <input type="file" id="video_file" accept="video/*,.mp4,.webm,.mov,.mkv,.m4v,.avi" class="su-input">
                <div id="upload-progress-wrap" class="hidden" style="margin-top:12px">
                    <div style="display:flex;align-items:center;justify-content:space-between;font-size:12px;color:var(--su-ink-40);margin-bottom:6px">
                        <span id="upload-status">…</span>
                        <span id="upload-percent">0%</span>
                    </div>
                    <div style="height:8px;border-radius:999px;background:var(--su-line);overflow:hidden">
                        <div id="upload-bar" style="height:100%;width:0;background:var(--su-ink);transition:width .15s"></div>
                    </div>
                </div>
                <div id="upload-result" class="hidden" style="margin-top:8px;font-size:12px;color:#15803d"></div>
                @if($mode === 'edit' && $video->file_path)
                    <label style="display:inline-flex;align-items:center;gap:8px;margin-top:12px;font-size:13px;cursor:pointer">
                        <input type="checkbox" name="clear_file" value="1">
                        {{ __('instructor.lib_videos_clear_file') }}
                    </label>
                @endif
            </div>

            <div class="su-form-grid" style="margin-top:16px">
                <div class="su-field">
                    <label for="duration_seconds">{{ __('instructor.lib_videos_duration') }}</label>
                    <input type="number" name="duration_seconds" id="duration_seconds" min="0" value="{{ old('duration_seconds', $video->duration_seconds ?? 0) }}" class="su-input">
                </div>
                <div class="su-field" style="display:flex;align-items:flex-end;padding-bottom:4px">
                    <label style="display:inline-flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
                        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $video->is_published ?? true))>
                        {{ __('instructor.lib_videos_publish_check') }}
                    </label>
                </div>
            </div>
        </section>

        <button type="submit" id="save-btn" class="su-btn su-btn--primary">
            <i class="fas fa-save" aria-hidden="true"></i>
            {{ __('common.save') }}
        </button>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var fileInput = document.getElementById('video_file');
    var progressWrap = document.getElementById('upload-progress-wrap');
    var progressBar = document.getElementById('upload-bar');
    var progressPct = document.getElementById('upload-percent');
    var progressStatus = document.getElementById('upload-status');
    var uploadResult = document.getElementById('upload-result');
    var saveBtn = document.getElementById('save-btn');
    var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || document.querySelector('input[name="_token"]')?.value;

    function setProgress(pct, status) {
        progressWrap.classList.remove('hidden');
        progressBar.style.width = pct + '%';
        progressPct.textContent = Math.round(pct) + '%';
        if (status) progressStatus.textContent = status;
    }

    function putFile(url, file, contentType, extraHeaders, onPercent) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('PUT', url, true);
            xhr.setRequestHeader('Content-Type', contentType);
            if (extraHeaders) {
                Object.keys(extraHeaders).forEach(function (k) {
                    if (String(k).toLowerCase() === 'content-type') return;
                    var val = extraHeaders[k];
                    if (Array.isArray(val)) val = val[0];
                    try { xhr.setRequestHeader(k, val); } catch (e) {}
                });
            }
            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable && onPercent) onPercent((e.loaded / e.total) * 100);
            };
            xhr.onload = function () {
                if (xhr.status >= 200 && xhr.status < 300) resolve();
                else reject(new Error('فشل رفع الملف (HTTP ' + xhr.status + ')'));
            };
            xhr.onerror = function () { reject(new Error('خطأ شبكة أثناء الرفع')); };
            xhr.send(file);
        });
    }

    function putFileViaServer(token, file, onPercent) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', @json($proxyRoute ?? ''), true);
            xhr.timeout = 10 * 60 * 1000;
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.upload.onprogress = function (e) {
                if (e.lengthComputable && onPercent) onPercent((e.loaded / e.total) * 100);
            };
            xhr.onload = function () {
                var data = {};
                try { data = JSON.parse(xhr.responseText || '{}'); } catch (e) { data = {}; }
                if (xhr.status >= 200 && xhr.status < 300 && data.ok) resolve(data);
                else reject(new Error(data.message || 'فشل الرفع عبر الخادم (HTTP ' + xhr.status + ')'));
            };
            xhr.onerror = function () { reject(new Error('تعذّر الاتصال بالخادم أثناء الرفع الاحتياطي.')); };
            xhr.ontimeout = function () { reject(new Error('انتهت مهلة الرفع عبر الخادم.')); };
            var fd = new FormData();
            fd.append('upload_token', token);
            fd.append('file', file);
            xhr.send(fd);
        });
    }

    if (!fileInput) return;

    fileInput.addEventListener('change', async function () {
        var file = fileInput.files && fileInput.files[0];
        if (!file) return;

        uploadResult.classList.add('hidden');
        saveBtn.disabled = true;
        setProgress(0, 'تجهيز رابط Cloudflare…');

        try {
            var presignRes = await fetch(@json($presignRoute), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    content_type: file.type || 'video/mp4',
                    filename: file.name
                })
            });
            var presignData = await presignRes.json();
            if (!presignRes.ok || !presignData.direct_upload || !presignData.upload_url) {
                throw new Error(presignData.message || 'تعذر تجهيز الرفع المباشر');
            }

            setProgress(1, 'جاري الرفع إلى Cloudflare…');
            try {
                await putFile(
                    presignData.upload_url,
                    file,
                    presignData.content_type || file.type || 'video/mp4',
                    presignData.headers || {},
                    function (pct) { setProgress(pct, 'جاري الرفع إلى Cloudflare…'); }
                );
            } catch (directErr) {
                if (file.size > 200 * 1024 * 1024) {
                    throw new Error((directErr && directErr.message) ? directErr.message : 'فشل الرفع المباشر. الملف أكبر من حد الرفع عبر الخادم.');
                }
                setProgress(1, 'الرفع المباشر حُجب — التحويل عبر الخادم…');
                await putFileViaServer(
                    presignData.upload_token,
                    file,
                    function (pct) { setProgress(pct, 'جاري الرفع عبر الخادم…'); }
                );
            }

            setProgress(99, 'تأكيد الملف…');
            var completeRes = await fetch(@json($completeRoute), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ upload_token: presignData.upload_token })
            });
            var completeData = await completeRes.json();
            if (!completeRes.ok || !completeData.ok) {
                throw new Error(completeData.message || 'فشل تأكيد الرفع');
            }

            document.getElementById('file_path').value = completeData.file_path;
            document.getElementById('storage_disk').value = completeData.storage_disk;
            document.getElementById('file_size').value = completeData.file_size;
            document.getElementById('mime_type').value = completeData.mime_type || '';
            setProgress(100, 'اكتمل الرفع');
            uploadResult.textContent = 'تم الرفع بنجاح (' + (completeData.file_size_human || '') + '). احفظ النموذج.';
            uploadResult.classList.remove('hidden');
            uploadResult.style.color = '#15803d';
        } catch (err) {
            setProgress(0, 'فشل الرفع');
            uploadResult.textContent = err.message || String(err);
            uploadResult.classList.remove('hidden');
            uploadResult.style.color = '#b91c1c';
            document.getElementById('file_path').value = @json(old('file_path', $video->file_path));
        } finally {
            saveBtn.disabled = false;
        }
    });
})();
</script>
@endpush
