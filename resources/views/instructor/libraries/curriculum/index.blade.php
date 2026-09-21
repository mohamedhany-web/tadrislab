@extends('layouts.instructor-timeline')

@section('title', __('instructor.lib_curriculum_title') . ' - ' . config('app.name'))
@section('page_title', __('instructor.lib_curriculum_title'))

@section('content')
@php
    $isRtl = app()->getLocale() === 'ar';
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
    $tones = ['blue', 'pink', 'orange', 'purple'];
    $itemsTotal = method_exists($items, 'total') ? (int) $items->total() : $items->count();
    $categoriesCount = $categories->count();
    $coursesCount = ($teachingCourses ?? collect())->count();
    $sectionsTotal = (int) ($teachingCourses ?? collect())->sum('sections_count');
    $hasFilters = request()->anyFilled(['q', 'category_id', 'language']);
@endphp

<section class="st-join-hero" aria-label="{{ __('instructor.lib_curriculum_title') }}">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">TADRIS LAB</p>
        <h2 class="st-join-hero__title">{{ __('instructor.lib_curriculum_title') }}</h2>
        <p class="st-join-hero__meta">{{ __('instructor.lib_curriculum_subtitle') }}</p>
    </div>
    <div class="st-join-hero__actions">
        @if(Route::has('instructor.libraries.materials.index'))
            <a href="{{ route('instructor.libraries.materials.index') }}" class="st-pill st-pill--outline">
                {{ $isRtl ? 'ماتريال' : 'Materials' }}
            </a>
        @endif
        @if(Route::has('instructor.libraries.videos.index'))
            <a href="{{ route('instructor.libraries.videos.index') }}" class="st-pill st-pill--solid">
                {{ $isRtl ? 'فيديو' : 'Videos' }}
            </a>
        @endif
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="{{ __('instructor.lib_curriculum_title') }}">
    <article class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'المناهج' : 'Curricula' }}</p>
        <p class="st-stat-card__value">{{ number_format($itemsTotal) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'منشورة للعرض' : 'Published for browse' }}</p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'التصنيفات' : 'Categories' }}</p>
        <p class="st-stat-card__value">{{ number_format($categoriesCount) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'فلترة سريعة' : 'Quick filters' }}</p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ $isRtl ? 'كورساتك' : 'Your courses' }}</p>
        <p class="st-stat-card__value">{{ number_format($coursesCount) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'هيكل مسند' : 'Assigned structure' }}</p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.lib_curriculum_kpi_sections') }}</p>
        <p class="st-stat-card__value">{{ number_format($sectionsTotal) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'عبر كورساتك' : 'Across your courses' }}</p>
    </article>
</section>

<section class="st-panel st-lib-note" role="note">
    <i class="fas fa-info-circle" aria-hidden="true"></i>
    <p>{{ __('instructor.lib_curriculum_info') }}</p>
</section>

<section class="st-panel st-course-filters">
    <form method="GET" class="st-course-filters__form st-course-filters__form--3">
        <div class="st-course-filters__field">
            <label for="q">{{ __('common.search') }}</label>
            <input type="search" name="q" id="q" value="{{ request('q') }}"
                   placeholder="{{ __('instructor.lib_curriculum_search_ph') }}">
        </div>
        <div class="st-course-filters__field">
            <label for="category_id">{{ __('instructor.lib_curriculum_all_categories') }}</label>
            <select name="category_id" id="category_id">
                <option value="">{{ __('instructor.lib_curriculum_all_categories') }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((string) request('category_id') === (string) $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="st-course-filters__field">
            <label for="language">{{ __('instructor.lib_curriculum_all_languages') }}</label>
            <select name="language" id="language">
                <option value="">{{ __('instructor.lib_curriculum_all_languages') }}</option>
                <option value="ar" @selected(request('language') === 'ar')>العربية</option>
                <option value="en" @selected(request('language') === 'en')>English</option>
                <option value="fr" @selected(request('language') === 'fr')>Français</option>
            </select>
        </div>
        <div class="st-course-filters__actions">
            <button type="submit" class="st-pill st-pill--solid">{{ __('instructor.lib_curriculum_filter') }}</button>
            @if($hasFilters)
                <a href="{{ route('instructor.libraries.curriculum.index') }}" class="st-pill st-pill--outline">
                    {{ $isRtl ? 'مسح' : 'Clear' }}
                </a>
            @endif
        </div>
    </form>
</section>

<section class="st-msg-intro">
    <div>
        <h2>{{ $isRtl ? 'المناهج التفاعلية' : 'Interactive curricula' }}</h2>
        <p>{{ $isRtl ? 'تصفّح مناهج الأكاديمية وافتح المحتوى — بدون رفع من هنا.' : 'Browse academy curricula and open content — uploads stay admin-only.' }}</p>
    </div>
</section>

@if($items->isEmpty())
    <section class="st-panel st-lp-empty {{ ($teachingCourses ?? collect())->isNotEmpty() ? 'st-lp-empty--compact' : '' }}">
        <i class="fas fa-sitemap" aria-hidden="true"></i>
        <h3>{{ __('instructor.lib_curriculum_empty') }}</h3>
        <p>{{ $hasFilters
            ? ($isRtl ? 'لا نتائج لهذا الفلتر. جرّب مسح التصفية.' : 'No matches for this filter. Try clearing filters.')
            : ($isRtl ? 'عند نشر الإدارة لمناهج تفاعلية ستظهر هنا للتصفح.' : 'When admin publishes interactive curricula, they will appear here.') }}</p>
    </section>
@else
    <section class="st-lp-grid" aria-label="{{ __('instructor.lib_curriculum_title') }}">
        @foreach($items as $i => $item)
            @php
                $tone = $tones[$i % count($tones)];
                $idx = method_exists($items, 'firstItem')
                    ? (int) $items->firstItem() + $i
                    : $i + 1;
            @endphp
            <a href="{{ route('instructor.libraries.curriculum.show', $item) }}" class="st-lp-card st-lp-card--{{ $tone }}">
                <img class="st-lp-card__blob" src="{{ $i % 2 ? $subjMask2 : $subjMask1 }}" alt="" width="120" height="120">
                <div class="st-lp-card__top">
                    <span class="st-lp-card__index">{{ str_pad((string) $idx, 2, '0', STR_PAD_LEFT) }}</span>
                    @if($item->category)
                        <span class="st-course-row__chip is-ok">{{ $item->category->name }}</span>
                    @endif
                </div>
                <h3 class="st-lp-card__title">{{ $item->title }}</h3>
                @if($item->description || $item->subject)
                    <p class="st-lp-card__focus">
                        {{ $item->subject ? $item->subject.($item->description ? ' · ' : '') : '' }}
                        {{ $item->description ? \Illuminate\Support\Str::limit($item->description, 100) : '' }}
                    </p>
                @endif
                <ul class="st-lp-card__meta">
                    @if($item->language)
                        <li><i class="fas fa-language" aria-hidden="true"></i> {{ strtoupper($item->language) }}</li>
                    @endif
                    @if($item->grade_level)
                        <li><i class="fas fa-signal" aria-hidden="true"></i> {{ $item->grade_level }}</li>
                    @endif
                </ul>
                <span class="st-lp-card__cta">
                    {{ __('instructor.lib_curriculum_open') }}
                    <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                </span>
            </a>
        @endforeach
    </section>

    @if(method_exists($items, 'hasPages') && $items->hasPages())
        <div class="st-pager">{{ $items->links() }}</div>
    @endif
@endif

@if(($teachingCourses ?? collect())->isNotEmpty())
    <section class="st-msg-intro">
        <div>
            <h2>{{ __('instructor.lib_curriculum_courses_structure') }}</h2>
            <p>{{ __('instructor.lib_curriculum_courses_structure_sub') }}</p>
        </div>
    </section>

    <section class="st-panel st-cons-table" aria-label="{{ __('instructor.lib_curriculum_courses_structure') }}">
        @foreach($teachingCourses as $course)
            <a href="{{ route('instructor.libraries.curriculum.course', $course) }}" class="st-cons-row">
                <span class="st-cons-row__avatar" aria-hidden="true">
                    <i class="fas fa-graduation-cap"></i>
                </span>
                <span class="st-cons-row__main">
                    <strong>{{ $course->title }}</strong>
                    @php
                        $yearName = $course->academicSubject?->academicYear?->name;
                        $subjName = $course->academicSubject?->name;
                        $courseMeta = collect([$yearName, $subjName])->filter()->implode(' · ');
                    @endphp
                    @if($courseMeta !== '')
                        <em>{{ $courseMeta }}</em>
                    @endif
                </span>
                <span class="st-cons-row__meta">
                    <span class="st-cons-row__chip">
                        {{ __('instructor.lib_curriculum_sections_count', ['count' => $course->sections_count]) }}
                    </span>
                </span>
                <span class="st-cons-row__cta">
                    {{ __('instructor.lib_curriculum_view_structure') }}
                    <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                </span>
            </a>
        @endforeach
    </section>
@endif
@endsection
