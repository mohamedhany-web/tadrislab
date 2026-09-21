@extends('layouts.instructor-timeline')

@section('title', __('instructor.lib_videos_title') . ' - ' . config('app.name'))
@section('page_title', __('instructor.lib_videos_title'))

@section('content')
<div class="su-page">
    <div class="su-page-head">
        <div class="min-w-0">
            <h1 class="su-page-head__title">
                <i class="fas fa-video su-page-head__ico" aria-hidden="true"></i>
                {{ __('instructor.lib_videos_title') }}
            </h1>
            <p class="su-page-head__sub">{{ __('instructor.lib_videos_subtitle') }}</p>
        </div>
        <div class="su-page-head__actions">
            <a href="{{ route('instructor.libraries.videos.create') }}" class="su-btn su-btn--primary">
                <i class="fas fa-plus" aria-hidden="true"></i>
                {{ __('instructor.lib_videos_add') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.08);color:#15803d;font-size:13px">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="su-card" style="margin-bottom:16px;padding:12px 16px;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08);color:#b91c1c;font-size:13px">
            {{ session('error') }}
        </div>
    @endif

    <section class="su-card" style="margin-bottom:20px">
        <h3 class="su-card__title" style="margin-bottom:14px">{{ __('instructor.lib_videos_new_folder') }}</h3>
        <form method="POST" action="{{ route('instructor.libraries.videos.folders.store') }}" class="su-form-grid">
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
                <label for="academic_year_id">{{ __('instructor.year') }}</label>
                <select name="academic_year_id" id="academic_year_id" class="su-select">
                    <option value="">{{ __('instructor.lib_videos_no_year') }}</option>
                    @foreach($years as $y)
                        <option value="{{ $y->id }}">{{ $y->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="su-field">
                <label for="content_theme">{{ __('instructor.lib_videos_theme') }}</label>
                <select name="content_theme" id="content_theme" class="su-select">
                    @foreach(\App\Support\FamilyLibraryThemes::labels(app()->getLocale() === 'ar' ? 'ar' : 'en') as $key => $themeLabel)
                        <option value="{{ $key }}" @selected($key === 'kids')>{{ $themeLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="su-form-actions">
                <button type="submit" class="su-btn" style="height:40px;justify-content:center;flex:1">
                    <i class="fas fa-folder-plus" aria-hidden="true"></i>
                    {{ __('instructor.lib_videos_create_folder') }}
                </button>
            </div>
        </form>
        @if($folders->isNotEmpty())
            <div style="margin-top:14px;display:flex;flex-wrap:wrap;gap:8px">
                @foreach($folders as $folder)
                    <span class="su-chip">
                        {{ $folder->displayName() }}
                        <em style="font-style:normal;opacity:.6;margin-inline-start:4px">{{ (int) $folder->library_videos_count }}</em>
                    </span>
                @endforeach
            </div>
        @endif
    </section>

    @if(($academyVideos ?? collect())->isNotEmpty())
        <section class="su-card su-card--flush" style="margin-bottom:20px">
            <div style="padding:14px 16px;border-bottom:0.5px solid var(--su-line)">
                <h3 class="su-card__title" style="margin:0">{{ __('instructor.lib_videos_academy') }}</h3>
                <p style="margin:4px 0 0;font-size:12px;color:var(--su-ink-40)">{{ __('instructor.lib_videos_academy_sub') }}</p>
            </div>
            <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
                <table class="su-table">
                    <thead>
                        <tr>
                            <th>{{ __('instructor.lib_videos_col_title') }}</th>
                            <th>{{ __('instructor.lib_videos_col_folder') }}</th>
                            <th>{{ __('instructor.lib_videos_col_source') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($academyVideos as $video)
                            <tr>
                                <td><strong style="font-weight:600">{{ $video->title }}</strong></td>
                                <td style="color:var(--su-ink-40)">{{ $video->folder?->displayName() ?: '—' }}</td>
                                <td>{{ $video->sourceLabel() }}</td>
                                <td style="text-align:end">
                                    <a href="{{ route('instructor.libraries.videos.watch', $video) }}" class="su-btn" style="height:32px">{{ __('instructor.lib_videos_watch') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <section class="su-card su-card--flush">
        <div style="padding:14px 16px;border-bottom:0.5px solid var(--su-line)">
            <h3 class="su-card__title" style="margin:0">{{ __('instructor.lib_videos_yours') }}</h3>
        </div>
        <div class="su-table-wrap" style="border:0;border-radius:0;background:transparent">
            <table class="su-table">
                <thead>
                    <tr>
                        <th>{{ __('instructor.lib_videos_col_title') }}</th>
                        <th>{{ __('instructor.lib_videos_col_folder') }}</th>
                        <th>{{ __('instructor.lib_videos_col_source') }}</th>
                        <th>{{ __('instructor.lib_videos_col_publish') }}</th>
                        <th>{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($videos as $video)
                        <tr>
                            <td><strong style="font-weight:600">{{ $video->title }}</strong></td>
                            <td style="color:var(--su-ink-40)">{{ $video->folder?->displayName() ?: '—' }}</td>
                            <td>{{ $video->sourceLabel() }}</td>
                            <td>
                                <form method="POST" action="{{ route('instructor.libraries.videos.toggle', $video) }}">
                                    @csrf
                                    <button type="submit" class="su-chip {{ $video->is_published ? 'su-chip--ok' : 'su-chip--off' }}" style="cursor:pointer;border:0">
                                        {{ $video->is_published ? __('instructor.lib_videos_published') : __('instructor.lib_videos_draft') }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div style="display:flex;flex-wrap:wrap;gap:8px">
                                    <a href="{{ route('instructor.libraries.videos.watch', $video) }}" class="su-btn" style="height:32px">{{ __('instructor.lib_videos_watch') }}</a>
                                    <a href="{{ route('instructor.libraries.videos.edit', $video) }}" class="su-btn" style="height:32px">{{ __('common.edit') }}</a>
                                    <form method="POST" action="{{ route('instructor.libraries.videos.destroy', $video) }}" onsubmit="return confirm(@json(__('instructor.lib_videos_confirm_delete')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="su-btn su-btn--danger" style="height:32px">{{ __('common.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="su-empty">
                                    <i class="fas fa-video" aria-hidden="true"></i>
                                    <p>{{ __('instructor.lib_videos_empty') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($videos, 'hasPages') && $videos->hasPages())
            <div class="su-pager" style="padding:12px">{{ $videos->links() }}</div>
        @endif
    </section>
</div>
@endsection
