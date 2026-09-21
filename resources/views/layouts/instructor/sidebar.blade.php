@php
    $user = auth()->user();
    $isInstructor = $user && ($user->isInstructor() || $user->isTeacher() || in_array(strtolower((string) $user->role), ['teacher', 'instructor'], true));
    $closeSidebar = 'if (isNarrow) setTimeout(() => { sidebarOpen = false }, 50)';

    $teachingCourseIds = $user->teachingAdvancedCourseIds();
    $myCoursesCount = $teachingCourseIds->count();
    $showCourses = instructor_ui('show_courses', false);
    $hasTeachingCourses = $showCourses && $myCoursesCount > 0;
    $showLearningPaths = instructor_ui('show_learning_paths', true);
    $myPathCount = $showLearningPaths ? $user->teachingLearningPathIds()->count() : 0;
    $showConsultations = instructor_ui('show_consultations', true);
    $showTutoring = instructor_ui('show_tutoring', false);
    $showLive = instructor_ui('show_live_broadcast', false);
    $canAccessCurriculumLibrary = instructor_ui('show_libraries', true) && $user->isAcademyWorkingInstructor();
    $totalStudents = (! $showCourses || $teachingCourseIds->isEmpty())
        ? 0
        : \App\Models\StudentCourseEnrollment::whereIn('advanced_course_id', $teachingCourseIds)->where('status', 'active')->distinct('user_id')->count('user_id');

    $tbUpcoming = 0;
    if (\Illuminate\Support\Facades\Schema::hasTable('tutoring_group_bookings')) {
        $tbUpcoming = \App\Models\TutoringGroupBooking::where('instructor_id', $user->id)
            ->where('status', 'confirmed')
            ->where('starts_at', '>=', now())
            ->count();
    }
    $liveCount = 0;
    try {
        $liveCount = \App\Models\LiveSession::where('instructor_id', $user->id)->where('status', 'live')->count();
    } catch (\Throwable $e) {
    }
@endphp

{{-- SnowUI Sidebar: brand + favorites fixed; dashboards/pages scroll --}}
<div class="su-brand">
    @if($user->profile_image)
        <img src="{{ $user->profile_image_url }}" alt="" width="24" height="24" style="width:24px;height:24px;max-width:24px;max-height:24px;border-radius:999px;object-fit:cover;flex-shrink:0;display:block">
    @else
        <img src="{{ versioned_asset('images/instructor-panel/avatar-byewind.png') }}" alt="" width="24" height="24" class="su-brand__avatar" style="width:24px;height:24px;max-width:24px;max-height:24px;border-radius:999px;object-fit:cover;flex-shrink:0;display:block">
    @endif
    <span class="su-brand__name">{{ config('app.name') }}</span>
    <template x-if="isNarrow">
        <button @click="sidebarOpen = false" type="button"
                class="su-icon-btn su-brand__close" aria-label="{{ __('instructor.close_menu') }}">
            <i class="fas fa-times text-xs"></i>
        </button>
    </template>
</div>

<div class="su-fav-block" x-data="{ favTab: 'favorites' }">
    <div class="su-fav-tabs" role="tablist">
        <button type="button" class="su-fav-tab" :class="{ 'is-on': favTab === 'favorites' }" @click="favTab = 'favorites'">
            {{ __('instructor.nav_favorites') }}
        </button>
        <button type="button" class="su-fav-tab" :class="{ 'is-on': favTab === 'recently' }" @click="favTab = 'recently'">
            {{ __('instructor.nav_recently') }}
        </button>
    </div>

    <div class="su-fav-list" x-show="favTab === 'favorites'">
        <a href="{{ route('dashboard') }}" @click="{{ $closeSidebar }}"
           class="su-link su-link--fav {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
            <span class="su-link__dot"></span>
            <span class="su-link__txt">{{ __('instructor.overview') }}</span>
        </a>
        @if(Route::has('instructor.calendar'))
        <a href="{{ route('instructor.calendar') }}" @click="{{ $closeSidebar }}"
           class="su-link su-link--fav {{ request()->routeIs('instructor.calendar') || request()->routeIs('instructor.calendar.events') ? 'is-active' : '' }}">
            <span class="su-link__dot"></span>
            <span class="su-link__txt">{{ __('instructor.my_calendar') }}</span>
        </a>
        @endif
    </div>

    <div class="su-fav-list" x-show="favTab === 'recently'" x-cloak>
        <a href="{{ route('dashboard') }}" @click="{{ $closeSidebar }}" class="su-link su-link--fav">
            <span class="su-link__dot"></span>
            <span class="su-link__txt">{{ __('instructor.overview') }}</span>
        </a>
        @if($showTutoring && Route::has('instructor.tutoring-bookings.index'))
        <a href="{{ route('instructor.tutoring-bookings.index') }}" @click="{{ $closeSidebar }}"
           class="su-link su-link--fav {{ request()->routeIs('instructor.tutoring-bookings.*') ? 'is-active' : '' }}">
            <span class="su-link__dot"></span>
            <span class="su-link__txt">{{ __('instructor.group_bookings') }}</span>
        </a>
        @elseif($showConsultations && Route::has('instructor.consultations.index'))
        <a href="{{ route('instructor.consultations.index') }}" @click="{{ $closeSidebar }}"
           class="su-link su-link--fav {{ request()->routeIs('instructor.consultations.*') ? 'is-active' : '' }}">
            <span class="su-link__dot"></span>
            <span class="su-link__txt">استشاراتي</span>
        </a>
        @endif
    </div>
</div>

<div class="su-nav-scroll">
    @if($isInstructor || $user->hasAnyPermission('instructor.view.courses', 'instructor.manage.lectures', 'instructor.manage.assignments', 'instructor.manage.exams', 'instructor.manage.attendance', 'instructor.view.tasks'))

        <div class="su-sec">{{ __('instructor.dashboards') }}</div>

        <a href="{{ route('dashboard') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-chart-pie"></i></span>
            <span class="su-link__txt">{{ __('instructor.overview') }}</span>
        </a>
        @if($hasTeachingCourses && ($isInstructor || $user->hasPermission('instructor.view.courses')))
        <a href="{{ route('instructor.courses.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.courses.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-book-open"></i></span>
            <span class="su-link__txt">{{ __('instructor.my_courses') }}</span>
            <span class="su-link__badge">{{ $myCoursesCount }}</span>
        </a>
        @endif
        @if($showLearningPaths && Route::has('instructor.learning-paths.index'))
        <a href="{{ route('instructor.learning-paths.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.learning-paths.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-route"></i></span>
            <span class="su-link__txt">مساراتي المسندة</span>
            @if($myPathCount > 0)<span class="su-link__badge">{{ $myPathCount }}</span>@endif
        </a>
        @endif
        @if($showConsultations && Route::has('instructor.consultations.index'))
        <a href="{{ route('instructor.consultations.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.consultations.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-comments-dollar"></i></span>
            <span class="su-link__txt">استشاراتي</span>
        </a>
        @endif
        @if($showTutoring && Route::has('instructor.tutoring-bookings.index'))
        <a href="{{ route('instructor.tutoring-bookings.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.tutoring-bookings.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-calendar-check"></i></span>
            <span class="su-link__txt">{{ __('instructor.group_bookings') }}</span>
            @if($tbUpcoming > 0)<span class="su-link__badge">{{ $tbUpcoming }}</span>@endif
        </a>
        @endif
        @if($showLive && Route::has('instructor.live-sessions.index'))
        <a href="{{ route('instructor.live-sessions.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.live-sessions.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-broadcast-tower"></i></span>
            <span class="su-link__txt">{{ __('instructor.live_broadcast') }}</span>
            @if($liveCount > 0)<span class="su-link__badge">{{ $liveCount }}</span>@endif
        </a>
        @endif

        <div class="su-sec">{{ __('instructor.nav_pages') }}</div>

        @if($showTutoring && Route::has('instructor.tutoring-cohorts.index'))
        <a href="{{ route('instructor.tutoring-cohorts.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.tutoring-cohorts.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-layer-group"></i></span>
            <span class="su-link__txt">{{ __('instructor.class_command') }}</span>
        </a>
        @endif
        @if(Route::has('instructor.private-messages.index'))
        <a href="{{ route('instructor.private-messages.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.private-messages.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-comments"></i></span>
            <span class="su-link__txt">{{ __('instructor.student_messages') }}</span>
        </a>
        @endif
        @if(Route::has('instructor.notifications.index'))
        <a href="{{ route('instructor.notifications.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.notifications.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-bell"></i></span>
            <span class="su-link__txt">{{ __('instructor.notifications') }}</span>
        </a>
        @endif
        @if($showTutoring && Route::has('instructor.tutor-work-schedule.index'))
        <a href="{{ route('instructor.tutor-work-schedule.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.tutor-work-schedule.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-users"></i></span>
            <span class="su-link__txt">{{ __('instructor.group_work_schedule') }}</span>
        </a>
        @endif
        @if($showTutoring && Route::has('instructor.one-to-one-sessions.index'))
        <a href="{{ route('instructor.one-to-one-sessions.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.one-to-one-sessions.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-chalkboard-teacher"></i></span>
            <span class="su-link__txt">{{ __('instructor.private_lessons') }}</span>
        </a>
        @endif
        @if(($showTutoring || $showConsultations) && Route::has('instructor.one-to-one-availability.index'))
        <a href="{{ route('instructor.one-to-one-availability.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.one-to-one-availability.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-calendar-week"></i></span>
            <span class="su-link__txt">نوافذ المواعيد</span>
        </a>
        @endif

        @if($canAccessCurriculumLibrary && Route::has('instructor.libraries.curriculum.index'))
        <a href="{{ route('instructor.libraries.curriculum.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.libraries.curriculum.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-sitemap"></i></span>
            <span class="su-link__txt">{{ __('instructor.curriculum_library') }}</span>
        </a>
        @endif
        @if($canAccessCurriculumLibrary && Route::has('instructor.libraries.materials.index'))
        <a href="{{ route('instructor.libraries.materials.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.libraries.materials.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-folder-open"></i></span>
            <span class="su-link__txt">{{ __('instructor.materials_library') }}</span>
        </a>
        @endif
        @if($canAccessCurriculumLibrary && Route::has('instructor.libraries.videos.index'))
        <a href="{{ route('instructor.libraries.videos.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.libraries.videos.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-film"></i></span>
            <span class="su-link__txt">{{ __('instructor.videos_for_students') }}</span>
        </a>
        @endif
        @if($showCourses && Route::has('instructor.lecture-recordings.index'))
        <a href="{{ route('instructor.lecture-recordings.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.lecture-recordings.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-video"></i></span>
            <span class="su-link__txt">{{ __('instructor.lecture_recordings') }}</span>
        </a>
        @endif
        @if($hasTeachingCourses && ($isInstructor || $user->hasPermission('instructor.manage.lectures')))
        <a href="{{ route('instructor.lectures.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.lectures.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-chalkboard"></i></span>
            <span class="su-link__txt">{{ __('instructor.lectures') }}</span>
        </a>
        @endif
        @if($showCourses && ($isInstructor || $user->hasPermission('instructor.manage.assignments')))
        <a href="{{ route('instructor.assignments.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.assignments.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-tasks"></i></span>
            <span class="su-link__txt">{{ __('instructor.assignments') }}</span>
        </a>
        @endif
        @if($showCourses && ($isInstructor || $user->hasPermission('instructor.manage.exams')))
        <a href="{{ route('instructor.exams.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.exams.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-clipboard-check"></i></span>
            <span class="su-link__txt">{{ __('instructor.exams') }}</span>
        </a>
        @endif
        @if($showCourses && $isInstructor)
        <a href="{{ route('instructor.question-banks.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.question-banks.*') || request()->routeIs('instructor.questions.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-database"></i></span>
            <span class="su-link__txt">{{ __('instructor.question_banks') }}</span>
        </a>
        @endif
        @if($hasTeachingCourses && ($isInstructor || $user->hasPermission('instructor.manage.attendance')))
        <a href="{{ route('instructor.attendance.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.attendance.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-clipboard-list"></i></span>
            <span class="su-link__txt">{{ __('instructor.attendance') }}</span>
        </a>
        @endif
        @if($isInstructor || $user->hasPermission('instructor.view.tasks'))
        <a href="{{ route('instructor.tasks.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.tasks.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-check-square"></i></span>
            <span class="su-link__txt">{{ __('instructor.tasks_from_management') }}</span>
        </a>
        @endif
        @if(($isInstructor || $user->hasPermission('instructor.view.tasks')) && Route::has('instructor.management-requests.index'))
        <a href="{{ route('instructor.management-requests.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.management-requests.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-paper-plane"></i></span>
            <span class="su-link__txt">{{ __('instructor.submit_requests_to_management') }}</span>
        </a>
        @endif
        <a href="{{ route('instructor.agreements.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.agreements.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-handshake"></i></span>
            <span class="su-link__txt">{{ __('instructor.agreements_system') }}</span>
        </a>
        <a href="{{ route('instructor.transfer-account.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.transfer-account.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-university"></i></span>
            <span class="su-link__txt">{{ __('instructor.transfer_account') }}</span>
        </a>
        <a href="{{ route('instructor.withdrawals.index') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.withdrawals.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-money-bill-wave"></i></span>
            <span class="su-link__txt">{{ __('instructor.withdrawal_requests') }}</span>
        </a>
        <a href="{{ route('instructor.personal-branding.edit') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('instructor.personal-branding.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-user-tie"></i></span>
            <span class="su-link__txt">{{ __('instructor.personal_branding') }}</span>
        </a>
    @endif

    <div class="su-sec">{{ __('instructor.nav_account') }}</div>
    @if($user->isAdmin())
        <a href="{{ route('admin.dashboard') }}" @click="{{ $closeSidebar }}"
           class="su-link {{ request()->routeIs('admin.*') ? 'is-active' : '' }}">
            <span class="su-link__ico"><i class="fas fa-shield-alt"></i></span>
            <span class="su-link__txt">{{ __('instructor.admin_panel') }}</span>
        </a>
    @endif
    <a href="{{ route('instructor.profile') }}" @click="{{ $closeSidebar }}"
       class="su-link {{ request()->routeIs('instructor.profile*') ? 'is-active' : '' }}">
        <span class="su-link__ico"><i class="fas fa-user"></i></span>
        <span class="su-link__txt">{{ __('instructor.profile') }}</span>
        @if($totalStudents > 0)<span class="su-link__badge">{{ $totalStudents }}</span>@endif
    </a>
    @if($user->hasPermission('student.view.settings'))
    <a href="{{ route('settings') }}" @click="{{ $closeSidebar }}"
       class="su-link {{ request()->routeIs('settings') ? 'is-active' : '' }}">
        <span class="su-link__ico"><i class="fas fa-cog"></i></span>
        <span class="su-link__txt">{{ __('instructor.settings') }}</span>
    </a>
    @endif
    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <button type="submit" class="su-link" style="width:100%;border:0;background:transparent;cursor:pointer;text-align:inherit;font:inherit">
            <span class="su-link__ico"><i class="fas fa-sign-out-alt"></i></span>
            <span class="su-link__txt">{{ __('instructor.logout') }}</span>
        </button>
    </form>
</div>
