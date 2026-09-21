@extends('layouts.instructor-timeline')

@section('title', __('instructor.profile') . ' - ' . config('app.name'))
@section('page_title', __('instructor.profile'))

@section('content')
@php
    $user = auth()->user();
    $memberSince = $user->created_at
        ? $user->created_at->copy()->locale(app()->getLocale())->translatedFormat('d F Y')
        : '—';
    $myCoursesCount = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->count();
    $totalStudents = \App\Models\StudentCourseEnrollment::whereHas('course', function ($q) use ($user) {
        $q->where('instructor_id', $user->id);
    })->where('status', 'active')->distinct('user_id')->count();
    $lastLogin = $user->last_login_at
        ? $user->last_login_at->copy()->locale(app()->getLocale())->diffForHumans()
        : '—';
    $isRtl = app()->getLocale() === 'ar';
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
    $initial = mb_substr($user->name ?? 'م', 0, 1);
    $tzOptions = \App\Support\AppTimezone::commonZones();
    $tzCurrent = old('timezone', $user->timezone ?: \App\Support\AppTimezone::academy());
    if ($tzCurrent && ! array_key_exists($tzCurrent, $tzOptions)) {
        $tzOptions = [$tzCurrent => $tzCurrent] + $tzOptions;
    }
@endphp

<section class="st-join-hero st-profile-hero" aria-label="{{ __('instructor.profile') }}">
    <div class="st-profile-hero__identity">
        <div class="st-profile-hero__avatar" aria-hidden="true">
            @if($user->profile_image)
                <img src="{{ $user->profile_image_url }}" alt=""
                     onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('is-hidden');">
                <span class="is-hidden">{{ $initial }}</span>
            @else
                <span>{{ $initial }}</span>
            @endif
        </div>
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">TADRIS LAB · {{ __('instructor.instructor_role') }}</p>
            <h2 class="st-join-hero__title">{{ $user->name }}</h2>
            <p class="st-join-hero__meta">
                @if($user->email) {{ $user->email }} @endif
                @if($user->email && $user->phone) · @endif
                @if($user->phone) {{ $user->phone }} @endif
            </p>
        </div>
    </div>
    <div class="st-join-hero__actions">
        @if(Route::has('instructor.courses.index'))
            <a href="{{ route('instructor.courses.index') }}" class="st-pill st-pill--outline">{{ __('instructor.my_courses') }}</a>
        @endif
        <a href="{{ route('dashboard') }}" class="st-pill st-pill--solid">{{ __('instructor.back_to_dashboard') }}</a>
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="{{ __('instructor.profile') }}">
    <article class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.my_courses') }}</p>
        <p class="st-stat-card__value">{{ number_format($myCoursesCount) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'مسندة إليك' : 'Assigned to you' }}</p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.students') }}</p>
        <p class="st-stat-card__value">{{ number_format($totalStudents) }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'تسجيلات نشطة' : 'Active enrollments' }}</p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask1 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.join_date') }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ $memberSince }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'مع المنصة' : 'On the platform' }}</p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="{{ $subjMask2 }}" alt="" width="132" height="132">
        <p class="st-stat-card__label">{{ __('instructor.last_login') }}</p>
        <p class="st-stat-card__value st-stat-card__value--text">{{ $lastLogin }}</p>
        <p class="st-stat-card__hint">{{ $isRtl ? 'من هذا الجهاز' : 'Recent activity' }}</p>
    </article>
</section>

@if(session('success'))
    <section class="st-panel st-lib-note st-lib-note--ok" role="status">
        <i class="fas fa-check-circle" aria-hidden="true"></i>
        <p>{{ session('success') }}</p>
    </section>
@endif

<div class="st-course-layout st-profile-layout">
    <div class="st-profile-side">
        <section class="st-panel">
            <div class="st-section-head">
                <h2>{{ __('instructor.account_info') }}</h2>
                <p>{{ __('instructor.manage_profile_data') }}</p>
            </div>
            <dl class="st-course-dl">
                <div>
                    <dt>{{ __('instructor.membership_number') }}</dt>
                    <dd>#{{ str_pad((string) $user->id, 5, '0', STR_PAD_LEFT) }}</dd>
                </div>
                <div>
                    <dt>{{ __('instructor.account_type') }}</dt>
                    <dd>{{ __('instructor.instructor_role') }}</dd>
                </div>
                <div>
                    <dt>{{ __('common.status') }}</dt>
                    <dd>
                        <span class="st-course-row__chip {{ $user->is_active ? 'is-ok' : 'is-off' }}">
                            {{ $user->is_active ? __('instructor.active_status') : __('instructor.not_active') }}
                        </span>
                    </dd>
                </div>
            </dl>
        </section>

        @if($user->isAcademyWorkingInstructor())
            <section class="st-panel">
                <div class="st-section-head">
                    <h2>{{ __('instructor.your_libraries') }}</h2>
                    <p>{{ __('instructor.your_libraries_desc') }}</p>
                </div>
                <div class="st-profile-links">
                    @if(Route::has('instructor.libraries.materials.index'))
                        <a href="{{ route('instructor.libraries.materials.index') }}" class="st-profile-link">
                            <span><i class="fas fa-file-upload" aria-hidden="true"></i> {{ __('instructor.upload_materials') }}</span>
                            <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                        </a>
                    @endif
                    @if(Route::has('instructor.libraries.curriculum.index'))
                        <a href="{{ route('instructor.libraries.curriculum.index') }}" class="st-profile-link">
                            <span><i class="fas fa-book-open" aria-hidden="true"></i> {{ __('instructor.view_academy_curriculum') }}</span>
                            <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                        </a>
                    @endif
                    @if(Route::has('instructor.courses.index'))
                        <a href="{{ route('instructor.courses.index') }}" class="st-profile-link">
                            <span><i class="fas fa-layer-group" aria-hidden="true"></i> {{ __('instructor.build_course_curriculum') }}</span>
                            <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                        </a>
                    @endif
                    @if(Route::has('instructor.libraries.videos.index'))
                        <a href="{{ route('instructor.libraries.videos.index') }}" class="st-profile-link">
                            <span><i class="fas fa-video" aria-hidden="true"></i> {{ __('instructor.video_library') }}</span>
                            <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                        </a>
                    @endif
                </div>
            </section>
        @endif

        <section class="st-panel">
            <div class="st-section-head">
                <h2>{{ __('instructor.tips_for_instructor') }}</h2>
            </div>
            <ul class="st-profile-tips">
                <li>
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                    <div>
                        <strong>{{ __('instructor.update_bio') }}</strong>
                        <span>{{ __('instructor.add_bio_for_students') }}</span>
                    </div>
                </li>
                <li>
                    <i class="fas fa-lock" aria-hidden="true"></i>
                    <div>
                        <strong>{{ __('instructor.strong_password') }}</strong>
                        <span>{{ __('instructor.change_password_regularly') }}</span>
                    </div>
                </li>
            </ul>
        </section>
    </div>

    <section class="st-panel st-profile-form-panel">
        <div class="st-section-head">
            <h2>{{ __('instructor.update_data') }}</h2>
            <p>{{ __('instructor.update_data_subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data" class="st-profile-form">
            @csrf
            @method('PUT')

            <div class="st-profile-form__grid">
                <div class="st-course-filters__field">
                    <label for="name">{{ __('instructor.full_name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<p class="st-profile-form__error">{{ $message }}</p>@enderror
                </div>
                <div class="st-course-filters__field">
                    <label for="phone">{{ __('instructor.phone') }}</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required>
                    @error('phone')<p class="st-profile-form__error">{{ $message }}</p>@enderror
                </div>
                <div class="st-course-filters__field st-profile-form__span">
                    <label for="email">{{ __('instructor.email_optional') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}">
                    @error('email')<p class="st-profile-form__error">{{ $message }}</p>@enderror
                </div>
                <div class="st-course-filters__field st-profile-form__span">
                    <label for="timezone">{{ __('instructor.timezone') }}</label>
                    <select name="timezone" id="timezone" data-timezone-select required>
                        @foreach ($tzOptions as $tzId => $tzLabel)
                            <option value="{{ $tzId }}" @selected($tzCurrent === $tzId)>{{ $tzLabel }}</option>
                        @endforeach
                    </select>
                    <span class="st-profile-form__hint">{{ __('instructor.timezone_hint') }}</span>
                    @error('timezone')<p class="st-profile-form__error">{{ $message }}</p>@enderror
                </div>
                <div class="st-course-filters__field st-profile-form__span">
                    <label for="bio">{{ __('instructor.bio_optional') }}</label>
                    <textarea name="bio" id="bio" rows="4" placeholder="{{ __('instructor.bio_placeholder_short') }}">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')<p class="st-profile-form__error">{{ $message }}</p>@enderror
                </div>
                <div class="st-course-filters__field st-profile-form__span">
                    <label>{{ __('instructor.profile_image') }}</label>
                    <div class="st-profile-upload">
                        <div class="st-profile-upload__preview" aria-hidden="true">
                            @if($user->profile_image)
                                <img src="{{ $user->profile_image_url }}" alt=""
                                     onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('is-hidden');">
                                <i class="fas fa-user is-hidden"></i>
                            @else
                                <i class="fas fa-user"></i>
                            @endif
                        </div>
                        <label class="st-pill st-pill--outline st-profile-upload__btn">
                            <i class="fas fa-upload" aria-hidden="true"></i>
                            {{ __('instructor.choose_image_label') }}
                            <input type="file" name="profile_image" accept="image/*" class="sr-only">
                        </label>
                    </div>
                    @error('profile_image')<p class="st-profile-form__error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="st-profile-password">
                <h3>{{ __('instructor.change_password') }}</h3>
                <p>{{ __('instructor.leave_empty_if_no_change') }}</p>
                <div class="st-profile-form__grid st-profile-form__grid--3">
                    <div class="st-course-filters__field">
                        <label for="current_password">{{ __('instructor.current_password') }}</label>
                        <input type="password" name="current_password" id="current_password" autocomplete="current-password">
                        @error('current_password')<p class="st-profile-form__error">{{ $message }}</p>@enderror
                    </div>
                    <div class="st-course-filters__field">
                        <label for="password">{{ __('instructor.new_password') }}</label>
                        <input type="password" name="password" id="password" autocomplete="new-password">
                        @error('password')<p class="st-profile-form__error">{{ $message }}</p>@enderror
                    </div>
                    <div class="st-course-filters__field">
                        <label for="password_confirmation">{{ __('instructor.confirm_password') }}</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password">
                    </div>
                </div>
            </div>

            <div class="st-profile-form__actions">
                <a href="{{ route('dashboard') }}" class="st-pill st-pill--outline">{{ __('instructor.back_to_dashboard') }}</a>
                <button type="submit" class="st-pill st-pill--solid">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    {{ __('instructor.save_changes') }}
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
