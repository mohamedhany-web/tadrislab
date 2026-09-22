@php
    $user = auth()->user();
    $isStudent = $user && ($user->role === 'student' || strtolower((string) $user->role) === 'student');
    $closeSidebar = 'if (window.innerWidth < 1024) setTimeout(() => { sidebarOpen = false }, 50)';

    $tbUpcoming = 0;
    if ($user && \Illuminate\Support\Facades\Schema::hasTable('tutoring_group_bookings')) {
        $tbUpcoming = \App\Models\TutoringGroupBooking::where('user_id', $user->id)
            ->where('status', 'confirmed')->where('starts_at', '>=', now())->count();
    }

    $weekAppts = 0;
    if ($user && function_exists('student_ui')) {
        try {
            $weekAppts = \App\Services\StudentScheduleService::weekAppointments($user)->count();
        } catch (\Throwable $e) {
            $weekAppts = 0;
        }
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
            <p class="text-[11px] text-white/70 font-medium mt-0.5">{{ app()->getLocale() === 'ar' ? 'لوحة المعلم' : 'Teacher panel' }}</p>
        </div>
    </div>

    @if(student_ui('show_school', false))
    <div class="px-3 py-3 flex-shrink-0">
        <div class="rounded-2xl border border-[#E8EEF6] dark:border-gray-700 bg-[#F7F8FB] dark:bg-gray-800/80 p-3">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[11px] font-bold text-[#5B6577] dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'مواعيد هذا الأسبوع' : 'This week' }}</span>
                <span class="text-sm font-black text-[#1E4E8C] dark:text-blue-300 tabular-nums">{{ $weekAppts }}</span>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-1">
                <a href="{{ route('dashboard') }}" class="rounded-xl bg-white dark:bg-gray-900 border border-[#E8EEF6] dark:border-gray-700 px-2.5 py-2 text-center hover:border-[#A88050]/50 transition-colors">
                    <p class="text-lg font-black text-[#8A6A00] tabular-nums leading-none">📅</p>
                    <p class="text-[10px] font-bold text-[#8A94A6] mt-1">{{ app()->getLocale() === 'ar' ? 'تقويمي' : 'Calendar' }}</p>
                </a>
                <a href="{{ Route::has('student.classes.index') ? route('student.classes.index') : route('dashboard') }}"
                   class="rounded-xl bg-white dark:bg-gray-900 border border-[#E8EEF6] dark:border-gray-700 px-2.5 py-2 text-center hover:border-[#1E4E8C]/30 transition-colors">
                    <p class="text-lg font-black text-[#1E4E8C] dark:text-blue-300 tabular-nums leading-none">{{ $tbUpcoming }}</p>
                    <p class="text-[10px] font-bold text-[#8A94A6] mt-1">{{ app()->getLocale() === 'ar' ? 'حصص قادمة' : 'Upcoming' }}</p>
                </a>
            </div>
        </div>
    </div>
    @endif

    <nav class="flex-1 overflow-y-auto sidebar-scroll px-0 py-1 space-y-0.5 min-h-0">
        @if($isStudent || ($user && $user->hasAnyPermission('student.view.courses', 'student.view.my-courses', 'student.view.orders', 'student.view.invoices', 'student.view.wallet', 'student.view.certificates', 'student.view.achievements', 'student.view.exams', 'student.view.calendar', 'student.view.notifications', 'student.view.profile', 'student.view.settings')))

            <div class="ins-nav-group">
                <span><i class="fas fa-home text-[9px] opacity-50"></i> {{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home' }}</span>
            </div>
            <a href="{{ route('dashboard') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('dashboard') || request()->routeIs('student.school.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-home"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'نظرة عامة' : 'Overview' }}</span>
            </a>

            @if(student_ui('show_packages', true) && Route::has('student.packages.index'))
            <a href="{{ route('student.packages.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.packages.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-box-open"></i></span>
                <span class="flex-1 truncate">باقاتي</span>
            </a>
            @endif

            @if(student_ui('show_school', false))
            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-book-reader text-[9px] opacity-50"></i> {{ app()->getLocale() === 'ar' ? 'تعلّمي' : 'Learning' }}</span>
            </div>
            @if(student_ui('show_classes', true) && Route::has('student.classes.index'))
            <a href="{{ route('student.classes.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.classes.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-chalkboard"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'فصولي' : 'My classes' }}</span>
            </a>
            @endif

            @if(student_ui('show_private_lessons', true) && Route::has('student.private-lectures.index'))
            <a href="{{ route('student.private-lectures.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.private-lectures.*') || request()->routeIs('student.one-to-one-sessions.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-chalkboard-teacher"></i></span>
                <span class="flex-1 truncate">{{ app()->getLocale() === 'ar' ? 'حصصي الخاصة' : 'Private Lessons' }}</span>
            </a>
            @endif

            @if(student_ui('show_entitlements', true) && Route::has('student.service-entitlements.index'))
            <a href="{{ route('student.service-entitlements.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.service-entitlements.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-coins"></i></span>
                <span class="flex-1 truncate">رصيد الحصص</span>
            </a>
            @endif
            @endif

            @if(student_ui('show_libraries', true))
            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-book text-[9px] opacity-50"></i> {{ app()->getLocale() === 'ar' ? 'مكتبتي' : 'Library' }}</span>
            </div>
            @if(Route::has('student.library.materials'))
            <a href="{{ route('student.library.materials') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.library.materials') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-book-open"></i></span>
                <span class="flex-1 truncate">مكتبة الماتريال</span>
            </a>
            @endif
            @if(Route::has('student.library.videos'))
            <a href="{{ route('student.library.videos') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.library.videos') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-film"></i></span>
                <span class="flex-1 truncate">مكتبة الفيديوهات</span>
            </a>
            @endif
            @if(student_ui('show_assignments', true) && Route::has('student.assignments.index'))
            <a href="{{ route('student.assignments.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.assignments.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-tasks"></i></span>
                <span class="flex-1 truncate">واجباتي</span>
            </a>
            @endif
            @if(Route::has('student.lectures.index'))
            <a href="{{ route('student.lectures.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('student.lectures.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-chalkboard"></i></span>
                <span class="flex-1 truncate">محاضراتي</span>
            </a>
            @endif
            @endif

            @if(student_ui('show_learning_paths', true) && Route::has('student.learning-paths.index'))
            <div class="ins-nav-group mt-2"><span>تطويري المهني</span></div>
            <a href="{{ route('student.learning-paths.index') }}" @click="{{ $closeSidebar }}" class="ins-nav {{ request()->routeIs('student.learning-paths.*') ? 'active' : '' }}"><span class="ins-icon"><i class="fas fa-route"></i></span><span class="flex-1 truncate">المسارات التعليمية</span></a>
            @if(student_ui('show_tools', true) && Route::has('student.tools.index'))
            <a href="{{ route('student.tools.index') }}" @click="{{ $closeSidebar }}" class="ins-nav {{ request()->routeIs('student.tools.*') ? 'active' : '' }}"><span class="ins-icon"><i class="fas fa-toolbox"></i></span><span class="flex-1 truncate">أدواتي ومواردي</span></a>
            @endif
            @if(student_ui('show_teacher_assistant', true) && Route::has('student.teacher-assistant.index'))
            <a href="{{ route('student.teacher-assistant.index') }}" @click="{{ $closeSidebar }}" class="ins-nav {{ request()->routeIs('student.teacher-assistant.*') ? 'active' : '' }}"><span class="ins-icon"><i class="fas fa-magic"></i></span><span class="flex-1 truncate">مساعد المعلم</span></a>
            @endif
            @endif

            @if(student_ui('show_institution_portal', true) && Route::has('institution.portal.index'))
            <a href="{{ route('institution.portal.index') }}" @click="{{ $closeSidebar }}" class="ins-nav {{ request()->routeIs('institution.portal.*') ? 'active' : '' }}"><span class="ins-icon"><i class="fas fa-building"></i></span><span class="flex-1 truncate">لوحة جهتي</span></a>
            @endif

            @if(student_ui('show_consultations', true) && Route::has('consultations.index'))
            <a href="{{ route('consultations.index') }}" @click="{{ $closeSidebar }}" class="ins-nav {{ request()->routeIs('consultations.*') || request()->routeIs('public.consultations.*') ? 'active' : '' }}"><span class="ins-icon"><i class="fas fa-comments"></i></span><span class="flex-1 truncate">استشاراتي</span></a>
            @endif

            {{-- أقسام مخفية احتياطياً (البيانات محفوظة) — تُعاد عبر config/student_ui.php --}}
            @if(student_ui('show_courses', false))
            <div class="ins-nav-group mt-2"><span>كورساتي</span></div>
            <a href="{{ route('public.courses') }}" @click="{{ $closeSidebar }}" class="ins-nav"><span class="ins-icon"><i class="fas fa-compass"></i></span><span class="flex-1 truncate">{{ __('student.browse_courses') }}</span></a>
            <a href="{{ route('my-courses.index') }}" @click="{{ $closeSidebar }}" class="ins-nav"><span class="ins-icon"><i class="fas fa-bookmark"></i></span><span class="flex-1 truncate">{{ __('student.my_courses') }}</span></a>
            @endif

            @if(student_ui('show_exams', false) && Route::has('student.exams.index'))
            <a href="{{ route('student.exams.index') }}" @click="{{ $closeSidebar }}" class="ins-nav"><span class="ins-icon"><i class="fas fa-clipboard-check"></i></span><span class="flex-1 truncate">{{ __('student.exams') }}</span></a>
            @endif

            @if(student_ui('show_certificates', false) && Route::has('student.certificates.index'))
            <a href="{{ route('student.certificates.index') }}" @click="{{ $closeSidebar }}" class="ins-nav"><span class="ins-icon"><i class="fas fa-award"></i></span><span class="flex-1 truncate">{{ __('student.certificates') }}</span></a>
            @endif

            @if(student_ui('show_legacy_calendar', false) && Route::has('calendar'))
            <a href="{{ route('calendar') }}" @click="{{ $closeSidebar }}" class="ins-nav"><span class="ins-icon"><i class="fas fa-calendar-alt"></i></span><span class="flex-1 truncate">{{ __('student.calendar') }}</span></a>
            @endif

            @if(student_ui('show_wallet', false) && Route::has('student.wallet.index'))
            <a href="{{ route('student.wallet.index') }}" @click="{{ $closeSidebar }}" class="ins-nav"><span class="ins-icon"><i class="fas fa-wallet"></i></span><span class="flex-1 truncate">{{ __('student.wallet') }}</span></a>
            @endif

            @if(student_ui('show_orders', true) && Route::has('orders.index'))
            <a href="{{ route('orders.index') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-receipt"></i></span>
                <span class="flex-1 truncate">{{ __('student.orders') }}</span>
            </a>
            @endif

            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-user text-[9px] opacity-50"></i> الحساب</span>
            </div>

            @if(student_ui('show_notifications', true))
            <a href="{{ route('notifications') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('notifications') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-bell"></i></span>
                <span class="flex-1 truncate">{{ __('student.notifications') }}</span>
            </a>
            @endif

            @if(student_ui('show_profile', true))
            <a href="{{ route('profile') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('profile') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-user"></i></span>
                <span class="flex-1 truncate">{{ __('student.profile') }}</span>
            </a>
            @endif

            @if(student_ui('show_settings', true))
            <a href="{{ route('settings') }}" @click="{{ $closeSidebar }}"
               class="ins-nav {{ request()->routeIs('settings') ? 'active' : '' }}">
                <span class="ins-icon"><i class="fas fa-cog"></i></span>
                <span class="flex-1 truncate">{{ __('student.settings') }}</span>
            </a>
            @endif
        @endif

        @if($user && ($user->isAdmin() || $user->isInstructor()))
            <div class="ins-nav-group mt-2">
                <span><i class="fas fa-exchange-alt text-[9px] opacity-50"></i> لوحة أخرى</span>
            </div>
            @if($user->isAdmin())
                <a href="{{ route('admin.dashboard') }}" @click="{{ $closeSidebar }}" class="ins-nav">
                    <span class="ins-icon"><i class="fas fa-shield-alt"></i></span>
                    <span class="flex-1 truncate">{{ __('student.admin_panel') }}</span>
                </a>
            @endif
        @endif
    </nav>

    <div class="px-3 py-3 flex-shrink-0 border-t border-[#E8EEF6] dark:border-gray-700/80">
        <div class="ins-user-card flex items-center gap-3">
            <div class="u-avatar flex-shrink-0 w-10 h-10 rounded-xl">
                @if($user?->profile_image)
                    <img src="{{ $user->profile_image_url }}" alt="" class="w-full h-full object-cover rounded-xl">
                @else
                    {{ mb_substr($user?->name ?? 'U', 0, 1) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate leading-tight">{{ $user?->name }}</p>
                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ __('student.student_role') }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 text-red-500 dark:text-red-400 flex items-center justify-center transition-colors" title="تسجيل الخروج">
                    <i class="fas fa-sign-out-alt text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</div>
