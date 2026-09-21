@extends('layouts.instructor-timeline')

@section('title', $folder->displayName())
@section('page_title', $folder->displayName())

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <nav class="su-crumb-inline" aria-label="breadcrumb">
                <a href="{{ route('instructor.libraries.materials.index') }}">{{ __('instructor.lib_materials_title') }}</a>
                @if($folder->academicYear?->name)
                    <span>/</span>
                    <span>{{ $folder->academicYear->name }}</span>
                @endif
            </nav>
            <h1 class="su-page-head__title">
                <i class="fas fa-folder-open su-page-head__ico" aria-hidden="true"></i>
                {{ $folder->displayName() }}
            </h1>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.libraries.materials.index') }}" class="su-btn">
                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08);color:#b91c1c;font-size:13px">
            {{ $errors->first() }}
        </div>
    @endif

    @if($canManage ?? true)
        <section class="su-card" style="margin-bottom:20px">
            <h3 class="su-card__title" style="margin-bottom:14px">{{ __('instructor.lib_materials_upload_title') }}</h3>
            <form method="POST" action="{{ route('instructor.libraries.materials.upload', $folder) }}" enctype="multipart/form-data" class="su-form-grid">
                @csrf
                <div class="su-field">
                    <label for="title">{{ __('instructor.lib_materials_title_ph') }}</label>
                    <input type="text" name="title" id="title" class="su-input" placeholder="{{ __('instructor.lib_materials_title_ph') }}">
                </div>
                <div class="su-field">
                    <label for="description">{{ __('instructor.lib_materials_desc_ph') }}</label>
                    <input type="text" name="description" id="description" class="su-input" placeholder="{{ __('instructor.lib_materials_desc_ph') }}">
                </div>
                <div class="su-field">
                    <label for="content_theme">{{ __('instructor.lib_videos_theme') }}</label>
                    <select name="content_theme" id="content_theme" class="su-select">
                        @foreach(\App\Support\FamilyLibraryThemes::labels(app()->getLocale() === 'ar' ? 'ar' : 'en') as $key => $themeLabel)
                            <option value="{{ $key }}" @selected(($folder->content_theme ?: 'general') === $key)>{{ $themeLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="su-field">
                    <label for="experience_mode">{{ __('instructor.lib_materials_col_theme') }}</label>
                    <select name="experience_mode" id="experience_mode" class="su-select">
                        <option value="download">{{ __('instructor.lib_materials_mode_download') }}</option>
                        <option value="view">{{ __('instructor.lib_materials_mode_view') }}</option>
                        <option value="play">{{ __('instructor.lib_materials_mode_play') }}</option>
                    </select>
                </div>
                <div class="su-field">
                    <label for="file">{{ __('instructor.lib_materials_col_file') }}</label>
                    <input type="file" name="file" id="file" required accept="{{ \App\Support\FamilyLibraryThemes::materialAcceptAttr() }}" class="su-input">
                </div>
                <div class="su-field" style="display:flex;align-items:flex-end;padding-bottom:4px">
                    <label class="su-check" style="display:inline-flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
                        <input type="checkbox" name="is_visible_to_student" value="1" checked>
                        {{ __('instructor.lib_materials_visible_student') }}
                    </label>
                </div>
                <div class="su-form-actions">
                    <button type="submit" class="su-btn su-btn--primary" style="height:40px;justify-content:center;flex:1">
                        <i class="fas fa-upload" aria-hidden="true"></i>
                        {{ __('instructor.lib_materials_upload') }}
                    </button>
                </div>
            </form>
        </section>
    @else
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.08);color:#92400e;font-size:13px">
            {{ __('instructor.lib_materials_admin_readonly') }}
        </div>
    @endif

    <section class="su-card su-card--flush">
        <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
            <table class="su-table">
                <thead>
                    <tr>
                        <th>{{ __('instructor.lib_materials_col_file') }}</th>
                        <th>{{ __('instructor.lib_materials_col_theme') }}</th>
                        <th>{{ __('instructor.lib_materials_col_visibility') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($folder->materials as $m)
                        <tr>
                            <td>
                                <strong style="font-weight:600">{{ $m->title ?: $m->file_name }}</strong>
                                <div style="font-size:12px;color:var(--su-ink-40)">{{ $m->file_name }}</div>
                            </td>
                            <td style="color:var(--su-ink-40);font-size:12px">
                                {{ $m->themeLabel(app()->getLocale() === 'ar' ? 'ar' : 'en') }} · {{ $m->experience_mode ?: 'download' }}
                            </td>
                            <td>
                                <span class="su-chip {{ $m->is_visible_to_student ? 'su-chip--ok' : 'su-chip--off' }}">
                                    {{ $m->is_visible_to_student ? __('instructor.lib_materials_visible') : __('instructor.lib_materials_hidden') }}
                                </span>
                            </td>
                            <td style="text-align:end">
                                <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:flex-end;gap:8px">
                                    @if($m->file_path)
                                        @php
                                            $mode = $m->experience_mode ?: \App\Support\FamilyLibraryThemes::detectExperienceMode($m->file_name, $m->content_theme);
                                            $canPlay = \App\Support\FamilyLibraryThemes::isPlayableInPlatform($m->file_name, $mode);
                                        @endphp
                                        @if($canPlay)
                                            <a href="{{ route('instructor.libraries.materials.experience', [$folder, $m]) }}" class="su-btn" style="height:32px">{{ __('common.view') }}</a>
                                        @endif
                                        <a href="{{ route('instructor.libraries.materials.download', [$folder, $m]) }}" class="su-btn" style="height:32px">{{ __('instructor.download') }}</a>
                                    @endif
                                    @if($canManage ?? true)
                                        <form method="POST" action="{{ route('instructor.libraries.materials.destroy', [$folder, $m]) }}" onsubmit="return confirm(@json(__('instructor.lib_materials_confirm_delete')))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="su-btn su-btn--danger" style="height:32px">{{ __('common.delete') }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="su-empty">
                                    <i class="fas fa-file" aria-hidden="true"></i>
                                    <p>{{ __('instructor.lib_materials_empty_files') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
