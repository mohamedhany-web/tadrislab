@php
    $user = auth()->user();
    $isInstructor = $user && ($user->isInstructor() || $user->isTeacher() || in_array(strtolower((string) $user->role), ['teacher', 'instructor'], true));
    $closeSidebar = 'if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)';

    $teachingCourseIds = $user->teachingAdvancedCourseIds();
    $myCoursesCount = $teachingCourseIds->count();
    $hasTeachingCourses = $myCoursesCount > 0;
    $canAccessCurriculumLibrary = $user->isAcademyWorkingInstructor();
    $totalStudents = $teachingCourseIds->isEmpty()
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

<div class="flex flex-col h-full">
    <div class="ins-sidebar-brand flex items-center gap-3 px-4 py-5 flex-shrink-0 relative">
        <button @click="if (window.innerWidth < 1024) sidebarOpen = false" type="button"
                class="lg:hidden absolute top-3 left-3 w-8 h-8 rounded-lg bg-white/15 text-white hover:bg-white/25 flex items-center justify-center transition-colors z-10"
                aria-label="إغلاق">
            <i class="fas fa-times text-xs"></i>
        </button>
        <div class="w-11 h-11 rounded-xl bg-[#A88050] text-[#184888] flex items-center justify-center flex-shrink-0 shadow-lg shadow-black/20">
            <i class="fas fa-chalkboard-teacher text-lg"></i>
        </div>
        <div class="flex-1 min-w-0 relative z-10">
            <h2 class="text-base font-extrabold text-white leading-tight truncate">{{ config('app.name') }}</h2>
            <p class="text-[11px] text-white/70 font-medium mt-0.5">{{ __('instructor.instructor_panel') }}</p>
        </div>
    </div>

    <div class="px-3 py-3 flex-shrink-0">
        <div class="rounded-2xl border border-[#E8EEF6] dark:border-gray-700 bg-[#F7F8FB] dark:bg-gray-800/80 p-3">
            <div class="grid grid-cols-2 gap-2">
                @if($hasTeachingCourses)
                <a href="{{ route('instructor.courses.index') }}" class="rounded-xl bg-white dark:bg-gray-900 border border-[#E8EEF6] dark:border-gray-700 px-2.5 py-2 text-center hover:border-[#1E4E8C]/30 transition-colors">
                    <p class="text-lg font-black text-[#1E4E8C] dark:text-blue-300 tabular-nums leading-none">{{ $myCoursesCount }}</p>
                    <p class="text-[10px] font-bold text-[#8A94A6] mt-1">{{ __('instructor.courses') }}</p>
                </a>
                @else
                <div class="rounded-xl bg-white dark:bg-gray-900 border border-[#E8EEF6] dark:border-gray-700 px-2.5 py-2 text-center">
                    <p class="text-lg font-black text-[#1E4E8C] dark:text-blue-300 tabular-nums leading-none">{{ $totalStudents }}</p>
                    <p class="text-[10px] font-bold text-[#8A94A6] mt-1">{{ __('instructor.students') }}</p>
                </div>
                @endif
                <a href="{{ Route::has('instructor.tutoring-bookings.index') ? route('instructor.tutoring-bookings.index') : route('dashboard') }}"
                   class="rounded-xl bg-white dark:bg-gray-900 border border-[#E8EEF6] dark:border-gray-700 px-2.5 py-2 text-center hover:border-[#A88050]/50 transition-colors">
                    <p class="text-lg font-black text-[#8A6A00] tabular-nums leading-none">{{ $tbUpcoming }}</p>
                    <p class="text-[10px] font-bold text-[#8A94A6] mt-1">حصص قادمة</p>
                </a>
            </div>
            @if($hasTeachingCourses)
            <p class="mt-2.5 text-[11px] text-[#5B6577] dark:text-gray-400 text-center">
                <span class="font-black text-[#1E4E8C] dark:text-blue-300 tabular-nums">{{ $totalStudents }}</span>
                {{ __('instructor.students') }}
            </p>
            @endif
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto sidebar-scroll px-0 py-1 space-y-0.5 min-h-0">
        @if($isInstructor || $user->hasAnyPermission('instructor.view.courses', 'instructor.manage.lectures', 'instructor.manage.assignments', 'instructor.manage.exams', 'instructor.manage.attendance', 'instructor.view.tasks'))

            <div class="ins-nav-group">
                <span><i class="fas fa-home text-[9px] opacity-50"></i> {{ __('instructor.overview') }}</span>
            </div>
            <a href="{{ route('dashboard') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-th-large"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.dashboard') }}</span>
            </a>
            @if(Route::has('instructor.calendar'))
            <a href="{{ route('instructor.calendar') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.calendar') || request()->routeIs('instructor.calendar.events') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-calendar-alt"></i></span>
                <span class="flex-1 truncate">تقويمي</span>
            </a>
            @endif

            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-video text-[9px] opacity-50"></i> تدريس مباشر</span>
            </div>

            @if(Route::has('instructor.tutoring-bookings.index'))
            <a href="{{ route('instructor.tutoring-bookings.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.tutoring-bookings.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-calendar-check"></i></span>
                <span class="flex-1 truncate">حجوزات المجموعات</span>
                @if($tbUpcoming > 0)
                    <span class="ins-nav-badge">{{ $tbUpcoming }}</span>
                @endif
            </a>
            @endif
            @if(Route::has('instructor.tutoring-cohorts.index'))
            <a href="{{ route('instructor.tutoring-cohorts.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.tutoring-cohorts.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-layer-group"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'قيادة الفصول' : 'Class Command' }}</span>
            </a>
            @endif
            @if(Route::has('instructor.private-messages.index'))
            <a href="{{ route('instructor.private-messages.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.private-messages.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-comments"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'رسائل الطلاب' : 'Student messages' }}</span>
            </a>
            @endif
            @if(Route::has('instructor.notifications.index'))
            <a href="{{ route('instructor.notifications.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.notifications.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-bell"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'الإشعارات' : 'Notifications' }}</span>
            </a>
            @endif
            @if(Route::has('instructor.tutor-work-schedule.index'))
            <a href="{{ route('instructor.tutor-work-schedule.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.tutor-work-schedule.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-users"></i></span>
                <span class="flex-1 truncate">جدول عمل المجموعات</span>
            </a>
            @endif
            @if(Route::has('instructor.live-sessions.index'))
            <a href="{{ route('instructor.live-sessions.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.live-sessions.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-broadcast-tower"></i></span>
                <span class="flex-1 truncate">البث المباشر</span>
                @if($liveCount > 0)
                    <span class="ins-nav-badge">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse inline-block ml-1"></span>{{ $liveCount }}
                    </span>
                @endif
            </a>
            @endif
            @if(Route::has('instructor.one-to-one-sessions.index'))
            <a href="{{ route('instructor.one-to-one-sessions.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.one-to-one-sessions.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-chalkboard-teacher"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'الحصص الخاصة' : 'Private Lessons' }}</span>
            </a>
            @endif
            @if(Route::has('instructor.one-to-one-availability.index'))
            <a href="{{ route('instructor.one-to-one-availability.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.one-to-one-availability.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-calendar-week"></i></span>
                <span class="flex-1 truncate">{{ __('student.one_to_one_availability_title') }}</span>
            </a>
            @endif
            @if(Route::has('instructor.consultations.index'))
            <a href="{{ route('instructor.consultations.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.consultations.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-comments-dollar"></i></span>
                <span class="flex-1 truncate">استشارات الطلاب</span>
            </a>
            @endif
            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-book-open text-[9px] opacity-50"></i> {{ $hasTeachingCourses ? 'الكورسات والأدوات' : 'الأدوات' }}</span>
            </div>

            @if($canAccessCurriculumLibrary && Route::has('instructor.libraries.curriculum.index'))
            <a href="{{ route('instructor.libraries.curriculum.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.libraries.curriculum.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-sitemap"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'مكتبة المناهج' : 'Curriculum library' }}</span>
            </a>
            @endif

            @if($canAccessCurriculumLibrary && Route::has('instructor.libraries.materials.index'))
            <a href="{{ route('instructor.libraries.materials.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.libraries.materials.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-folder-open"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'مكتبة الماتريال' : 'Materials library' }}</span>
            </a>
            @endif
            @if($canAccessCurriculumLibrary && Route::has('instructor.libraries.videos.index'))
            <a href="{{ route('instructor.libraries.videos.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.libraries.videos.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-film"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'فيديوهات لطلابك' : 'Videos for students' }}</span>
            </a>
            @endif

            @if(Route::has('instructor.lecture-recordings.index'))
            <a href="{{ route('instructor.lecture-recordings.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.lecture-recordings.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-video"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'تسجيل المحاضرات' : 'Lecture recordings' }}</span>
            </a>
            @endif

            @if($hasTeachingCourses && ($isInstructor || $user->hasPermission('instructor.view.courses')))
            <a href="{{ route('instructor.courses.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.courses.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-book-open"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.my_courses') }}</span>
                <span class="ins-nav-badge">{{ $myCoursesCount }}</span>
            </a>
            @endif
            @if($hasTeachingCourses && ($isInstructor || $user->hasPermission('instructor.manage.lectures')))
            <a href="{{ route('instructor.lectures.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.lectures.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-chalkboard"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.lectures') }}</span>
            </a>
            @endif
            @if($isInstructor || $user->hasPermission('instructor.manage.assignments'))
            <a href="{{ route('instructor.assignments.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.assignments.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-tasks"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.assignments') }}</span>
            </a>
            @endif
            @if($isInstructor || $user->hasPermission('instructor.manage.exams'))
            <a href="{{ route('instructor.exams.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.exams.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-clipboard-check"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.exams') }}</span>
            </a>
            @endif
            @if($isInstructor)
            <a href="{{ route('instructor.question-banks.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.question-banks.*') || request()->routeIs('instructor.questions.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-database"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.question_banks') }}</span>
            </a>
            @endif
            @if($hasTeachingCourses && ($isInstructor || $user->hasPermission('instructor.manage.attendance')))
            <a href="{{ route('instructor.attendance.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.attendance.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-clipboard-list"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.attendance') }}</span>
            </a>
            @endif

            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-briefcase text-[9px] opacity-50"></i> الإدارة</span>
            </div>

            @if($isInstructor || $user->hasPermission('instructor.view.tasks'))
            <a href="{{ route('instructor.tasks.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.tasks.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-check-square"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.tasks_from_management') }}</span>
            </a>
            @endif
            @if(($isInstructor || $user->hasPermission('instructor.view.tasks')) && Route::has('instructor.management-requests.index'))
            <a href="{{ route('instructor.management-requests.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.management-requests.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-paper-plane"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.submit_requests_to_management') }}</span>
            </a>
            @endif
            <a href="{{ route('instructor.agreements.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.agreements.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-handshake"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.agreements_system') }}</span>
            </a>

            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-coins text-[9px] opacity-50"></i> المالية</span>
            </div>
            <a href="{{ route('instructor.transfer-account.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.transfer-account.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-university"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.transfer_account') }}</span>
            </a>
            <a href="{{ route('instructor.withdrawals.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.withdrawals.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-money-bill-wave"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.withdrawal_requests') }}</span>
            </a>
            <a href="{{ route('instructor.personal-branding.edit') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('instructor.personal-branding.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-user-tie"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.personal_branding') }}</span>
            </a>
        @endif

        <div class="ins-nav-group mt-2">
            <span><i class="fas fa-user-cog text-[9px] opacity-50"></i> الحساب</span>
        </div>
        @if($user->isAdmin())
            <a href="{{ route('admin.dashboard') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-shield-alt"></i></span>
                <span class="flex-1 truncate">{{ __('instructor.admin_panel') }}</span>
            </a>
        @endif
        <a href="{{ route('instructor.profile') }}" @click="{{ $closeSidebar }}"
           class="ins-nav {{ request()->routeIs('instructor.profile*') ? 'active' : '' }}">
            <span class="ins-icon"><i class="fas fa-user"></i></span>
            <span class="flex-1 truncate">{{ __('instructor.profile') }}</span>
        </a>
        @if($user->hasPermission('student.view.settings'))
        <a href="{{ route('settings') }}" @click="{{ $closeSidebar }}"
           class="ins-nav {{ request()->routeIs('settings') ? 'active' : '' }}">
            <span class="ins-icon"><i class="fas fa-cog"></i></span>
            <span class="flex-1 truncate">{{ __('instructor.settings') }}</span>
        </a>
        @endif
    </nav>

    <div class="px-3 py-3 flex-shrink-0 border-t border-[#E8EEF6] dark:border-gray-700/80">
        <div class="ins-user-card flex items-center gap-3">
            <div class="u-avatar flex-shrink-0 w-10 h-10 rounded-xl">
                @if($user->profile_image)
                    <img src="{{ $user->profile_image_url }}" alt="" class="w-full h-full object-cover rounded-xl">
                @else
                    {{ mb_substr($user->name, 0, 1) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate leading-tight">{{ $user->name }}</p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ __('instructor.instructor_role') }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-500 dark:text-red-400 flex items-center justify-center transition-colors" title="{{ __('instructor.logout') }}">
                    <i class="fas fa-sign-out-alt text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>
