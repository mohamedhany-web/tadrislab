@extends('layouts.instructor-timeline')

@section('title', __('instructor.lib_materials_title') . ' - ' . config('app.name'))
@section('page_title', __('instructor.lib_materials_title'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-folder-open su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.lib_materials_title') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.lib_materials_subtitle') }}</p>
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

    <section class="su-card" style="margin-bottom:20px">
        <h3 class="su-card__title" style="margin-bottom:14px">{{ __('instructor.lib_materials_create_folder') }}</h3>
        <form method="POST" action="{{ route('instructor.libraries.materials.folders.store') }}" class="su-form-grid">
            @csrf
            <div class="su-field">
                <label for="name_ar">{{ __('instructor.lib_materials_name_ar') }}</label>
                <input type="text" name="name_ar" id="name_ar" required class="su-input" placeholder="{{ __('instructor.lib_materials_name_ar') }}">
            </div>
            <div class="su-field">
                <label for="name_en">{{ __('instructor.lib_materials_name_en') }}</label>
                <input type="text" name="name_en" id="name_en" class="su-input" placeholder="{{ __('instructor.lib_materials_name_en') }}">
            </div>
            <div class="su-field">
                <label for="academic_year_id">{{ __('instructor.lib_materials_year_required') }}</label>
                <select name="academic_year_id" id="academic_year_id" required class="su-select">
                    <option value="">{{ __('instructor.lib_materials_year_required') }}</option>
                    @foreach($years as $y)
                        <option value="{{ $y->id }}">{{ $y->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="su-field">
                <label for="content_theme">{{ __('instructor.lib_videos_theme') }}</label>
                <select name="content_theme" id="content_theme" class="su-select">
                    @foreach(\App\Support\FamilyLibraryThemes::labels(app()->getLocale() === 'ar' ? 'ar' : 'en') as $key => $themeLabel)
                        <option value="{{ $key }}">{{ $themeLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="su-form-actions">
                <button type="submit" class="su-btn su-btn--primary" style="height:40px;justify-content:center;flex:1">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    {{ __('instructor.lib_materials_create') }}
                </button>
            </div>
        </form>
    </section>

    <div class="su-list">
        @forelse($folders as $folder)
            <a href="{{ route('instructor.libraries.materials.show', $folder) }}" class="su-list-item" style="text-decoration:none;color:inherit">
                <span class="su-list-item__ico su-soft-1"><i class="fas fa-folder" aria-hidden="true"></i></span>
                <div class="su-list-item__body">
                    <div class="su-list-item__title">{{ $folder->displayName() }}</div>
                    <div class="su-list-item__meta">
                        {{ $folder->academicYear->name ?? __('instructor.lib_materials_general_year') }}
                        · {{ __('instructor.lib_materials_files_count', ['count' => (int) $folder->materials_count]) }}
                        @if(! $folder->instructor_id)
                            · <span class="su-chip su-chip--warn" style="height:22px">{{ __('instructor.lib_materials_admin_folder') }}</span>
                        @endif
                    </div>
                </div>
                <span class="su-list-item__actions">
                    <span class="su-icon-link" style="pointer-events:none"><i class="fas fa-chevron-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i></span>
                </span>
            </a>
        @empty
            <div class="su-empty">
                <i class="fas fa-folder-open" aria-hidden="true"></i>
                <p>{{ __('instructor.lib_materials_empty') }}</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
