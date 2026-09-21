@php
    $appLocale = app()->getLocale();
    $appRtl = $appLocale === 'ar';
    $user = auth()->user();
    $firstName = explode(' ', trim((string) ($user?->name ?? '')))[0] ?? '';
    $avatarUrl = $user?->avatarDisplayUrl() ?? \App\Models\User::placeholderAvatarUrl();
    $brandLogoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
    $brandLogoFallback = \App\Services\AdminPanelBranding::inlineFallbackDataUri();

    $showCourses = instructor_ui('show_courses', false);
    $showPaths = instructor_ui('show_learning_paths', true);
    $showConsultations = instructor_ui('show_consultations', true);
    $showTutoring = instructor_ui('show_tutoring', false);
    $showLive = instructor_ui('show_live_broadcast', false);
    $showLibraries = instructor_ui('show_libraries', true);
    $showCalendar = instructor_ui('show_calendar', true);
    $hasCourses = $showCourses && $user && $user->teachingAdvancedCourseIds()->isNotEmpty();
    $canLibraries = $showLibraries && $user && $user->isAcademyWorkingInstructor();

    $navItems = [
        ['route' => 'dashboard', 'match' => ['dashboard'], 'label' => __('instructor.overview'), 'fa' => 'fas fa-chart-pie'],
    ];

    if ($showCalendar && Route::has('instructor.calendar')) {
        $navItems[] = ['route' => 'instructor.calendar', 'match' => ['instructor.calendar*'], 'label' => __('instructor.my_calendar'), 'fa' => 'fas fa-calendar-alt'];
    }
    if ($showPaths && Route::has('instructor.learning-paths.index')) {
        $navItems[] = ['route' => 'instructor.learning-paths.index', 'match' => ['instructor.learning-paths.*'], 'label' => 'مساراتي المسندة', 'fa' => 'fas fa-route'];
    }
    if ($showConsultations && Route::has('instructor.consultations.index')) {
        $navItems[] = ['route' => 'instructor.consultations.index', 'match' => ['instructor.consultations.*'], 'label' => 'استشاراتي', 'fa' => 'fas fa-comments'];
    }
    if ($hasCourses && Route::has('instructor.courses.index')) {
        $navItems[] = ['route' => 'instructor.courses.index', 'match' => ['instructor.courses.*'], 'label' => __('instructor.my_courses'), 'fa' => 'fas fa-book-open'];
    }
    if ($showTutoring && Route::has('instructor.tutoring-bookings.index')) {
        $navItems[] = ['route' => 'instructor.tutoring-bookings.index', 'match' => ['instructor.tutoring-bookings.*'], 'label' => __('instructor.group_bookings'), 'fa' => 'fas fa-users'];
    }
    if ($showLive && Route::has('instructor.live-sessions.index')) {
        $navItems[] = ['route' => 'instructor.live-sessions.index', 'match' => ['instructor.live-sessions.*'], 'label' => __('instructor.live_broadcast'), 'fa' => 'fas fa-broadcast-tower'];
    }
    if (($showTutoring || $showConsultations) && Route::has('instructor.one-to-one-availability.index')) {
        $navItems[] = ['route' => 'instructor.one-to-one-availability.index', 'match' => ['instructor.one-to-one-availability.*'], 'label' => 'نوافذ المواعيد', 'fa' => 'fas fa-calendar-week'];
    }
    if ($canLibraries && Route::has('instructor.libraries.curriculum.index')) {
        $navItems[] = ['route' => 'instructor.libraries.curriculum.index', 'match' => ['instructor.libraries.*'], 'label' => __('instructor.curriculum_library'), 'fa' => 'fas fa-folder-open'];
    }
    if ($hasCourses && Route::has('instructor.lectures.index')) {
        $navItems[] = ['route' => 'instructor.lectures.index', 'match' => ['instructor.lectures.*'], 'label' => __('instructor.lectures'), 'fa' => 'fas fa-chalkboard'];
    }
    if ($showCourses && Route::has('instructor.assignments.index')) {
        $navItems[] = ['route' => 'instructor.assignments.index', 'match' => ['instructor.assignments.*'], 'label' => __('instructor.assignments'), 'fa' => 'fas fa-tasks'];
    }
    if ($showCourses && Route::has('instructor.exams.index')) {
        $navItems[] = ['route' => 'instructor.exams.index', 'match' => ['instructor.exams.*'], 'label' => __('instructor.exams'), 'fa' => 'fas fa-clipboard-check'];
    }
    if (Route::has('instructor.tasks.index')) {
        $navItems[] = ['route' => 'instructor.tasks.index', 'match' => ['instructor.tasks.*'], 'label' => __('instructor.tasks_from_management'), 'fa' => 'fas fa-check-square'];
    }
    if (Route::has('instructor.agreements.index')) {
        $navItems[] = ['route' => 'instructor.agreements.index', 'match' => ['instructor.agreements.*'], 'label' => __('instructor.agreements_system'), 'fa' => 'fas fa-handshake'];
    }
    if (Route::has('instructor.notifications.index')) {
        $navItems[] = ['route' => 'instructor.notifications.index', 'match' => ['instructor.notifications.*'], 'label' => __('instructor.notifications'), 'fa' => 'fas fa-bell'];
    }
    if (Route::has('instructor.profile')) {
        $navItems[] = ['route' => 'instructor.profile', 'match' => ['instructor.profile*'], 'label' => __('instructor.profile'), 'fa' => 'fas fa-user'];
    }
    if ($user?->isAdmin() && Route::has('admin.dashboard')) {
        $navItems[] = ['route' => 'admin.dashboard', 'match' => ['admin.*'], 'label' => __('instructor.admin_panel'), 'fa' => 'fas fa-shield-alt'];
    }

    $pageTitle = trim($__env->yieldContent('page_title') ?: $__env->yieldContent('title') ?: __('instructor.overview'));
    $stCssVer = 'st-coach-11';
@endphp
<!DOCTYPE html>
<html lang="{{ $appLocale }}" dir="{{ $appRtl ? 'rtl' : 'ltr' }}" class="{{ $appRtl ? 'st-rtl' : 'st-ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', __('instructor.overview'))</title>
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Lato:wght@400;700;900&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#F7F1E8',300:'#A88050',500:'#A88050',700:'#1E4E8C',900:'#152A4A' },
                        acad: { blue:'#1E4E8C', dark:'#184888', gold:'#A88050', canvas:'#F7F8FB', ink:'#152A4A' },
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ route('assets.student-timeline.css') }}?v={{ $stCssVer }}">
    @php
        $ipCssFile = public_path('css/instructor-panel.css');
        $ipCssVer = is_file($ipCssFile) ? (string) filemtime($ipCssFile) : (string) time();
    @endphp
    <link rel="stylesheet" href="{{ asset('css/instructor-panel.css') }}?v={{ $ipCssVer }}">
    <link rel="stylesheet" href="{{ asset('css/instructor-st-bridge.css') }}?v={{ $stCssVer }}">
    <script>
        (function () {
            try {
                var mobile = window.matchMedia('(max-width: 768px)').matches;
                if (!mobile && localStorage.getItem('st-rail-open') === '1') {
                    document.documentElement.classList.add('st-rail-pref-open');
                }
            } catch (e) {}
        })();
    </script>
    @stack('styles')
</head>
<body class="st-dash st-coach">

<div class="st-shell is-wide{{ $appRtl ? ' is-rtl' : ' is-ltr' }}" id="stShell">
    <script>
    (function () {
        var shell = document.getElementById('stShell');
        if (!shell) return;
        try {
            if (window.matchMedia('(min-width: 769px)').matches && localStorage.getItem('st-rail-open') === '1') {
                shell.classList.add('is-rail-open');
            }
        } catch (e) {}
    })();
    </script>
    <button type="button" class="st-rail-backdrop" id="stRailBackdrop" aria-label="{{ __('student_timeline.close_sidebar') }}" tabindex="-1"></button>
    <aside class="st-rail" id="stRail" aria-label="{{ __('instructor.overview') }}">
        <div class="st-rail__head">
            <a href="{{ route('dashboard') }}" class="st-rail__brand" title="{{ config('app.name') }}">
                <span class="st-rail__mark">
                    <img src="{{ $brandLogoUrl }}" alt="{{ config('app.name') }}" class="st-rail__logo" width="40" height="40" loading="eager" decoding="async" onerror="this.onerror=null;this.src='{{ $brandLogoFallback }}';">
                </span>
                <span class="st-rail__brand-name">{{ config('app.name') }}</span>
            </a>
            <button type="button" class="st-rail__toggle" id="stRailToggle" aria-expanded="false" aria-controls="stRail" title="{{ __('student_timeline.toggle_sidebar') }}">
                <i class="fas fa-angles-{{ $appRtl ? 'left' : 'right' }}" aria-hidden="true"></i>
                <span class="st-rail__toggle-text">{{ __('student_timeline.open_sidebar') }}</span>
            </button>
        </div>

        <a href="{{ Route::has('instructor.profile') ? route('instructor.profile') : route('dashboard') }}" class="st-rail__profile" title="{{ __('instructor.profile') }}">
            <img src="{{ $avatarUrl }}" alt="" class="st-rail__avatar" width="40" height="40">
            <div class="st-rail__who">
                <span class="st-rail__name">{{ $firstName }}</span>
                <span class="st-rail__role">{{ \App\Support\TadrisRoles::labelAr($user) }}</span>
            </div>
        </a>

        <nav class="st-rail__nav">
            @foreach($navItems as $item)
                @php
                    if (! Route::has($item['route'])) {
                        continue;
                    }
                    $href = route($item['route']);
                    $active = request()->routeIs(...$item['match']);
                @endphp
                <a href="{{ $href }}" class="st-rail__link {{ $active ? 'is-active' : '' }}" title="{{ $item['label'] }}" aria-label="{{ $item['label'] }}">
                    <span class="st-rail__icon-box">
                        <i class="{{ $item['fa'] }}" aria-hidden="true" style="font-size:16px;opacity:.88"></i>
                    </span>
                    <span class="st-rail__label">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
        <div class="st-rail__foot" hidden aria-hidden="true"></div>
    </aside>

    <main class="st-main st-coach-main">
        @if (! View::hasSection('hide_st_top'))
            @include('partials.student-timeline-top', [
                'locale' => $appLocale,
                'pageTitle' => $pageTitle,
                'crumbs' => [
                    ['label' => __('instructor.dashboards'), 'url' => route('dashboard')],
                    ['label' => $pageTitle, 'url' => null],
                ],
            ])
        @endif

        @if(session('success'))
            <div class="st-flash st-flash--ok">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="st-flash st-flash--err">{{ session('error') }}</div>
        @endif

        <div class="st-coach-content">
            @yield('content')
        </div>
    </main>
</div>

<script>
(function () {
    var shell = document.getElementById('stShell');
    var rail = document.getElementById('stRail');
    var btn = document.getElementById('stRailToggle');
    var backdrop = document.getElementById('stRailBackdrop');
    if (!shell || !btn) return;

    var isRtl = document.documentElement.getAttribute('dir') === 'rtl';
    var openLabel = @json(__('student_timeline.open_sidebar'));
    var closeLabel = @json(__('student_timeline.close_sidebar'));
    var mq = window.matchMedia('(max-width: 768px)');

    function isMobile() { return mq.matches; }
    function isOpen() { return shell.classList.contains('is-rail-open'); }

    function syncToggleIcon(open) {
        var mobileIcon = open ? 'fas fa-xmark' : 'fas fa-bars';
        var icon = btn.querySelector('i');
        var text = btn.querySelector('.st-rail__toggle-text');
        if (icon) {
            icon.className = isMobile()
                ? mobileIcon
                : (open ? ('fas fa-angles-' + (isRtl ? 'right' : 'left')) : ('fas fa-angles-' + (isRtl ? 'left' : 'right')));
        }
        if (text) text.textContent = open ? closeLabel : openLabel;
        var topMenu = document.getElementById('stTopMenu');
        if (topMenu) {
            var topIcon = topMenu.querySelector('i');
            if (topIcon && isMobile()) topIcon.className = mobileIcon;
            topMenu.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
    }

    function setScrollLock(locked) {
        document.documentElement.classList.toggle('st-rail-lock', locked);
        document.body.classList.toggle('st-rail-lock', locked);
    }

    function setOpen(open, persist) {
        shell.classList.toggle('is-rail-open', open);
        document.documentElement.classList.toggle('st-rail-pref-open', open && !isMobile());
        setScrollLock(open && isMobile());
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (rail) rail.setAttribute('aria-hidden', isMobile() && !open ? 'true' : 'false');
        syncToggleIcon(open);
        if (persist !== false && !isMobile()) {
            try { localStorage.setItem('st-rail-open', open ? '1' : '0'); } catch (e) {}
        }
    }

    var preferOpen = false;
    try { preferOpen = localStorage.getItem('st-rail-open') === '1'; } catch (e) {}
    setOpen(isMobile() ? false : (shell.classList.contains('is-rail-open') || preferOpen), false);

    btn.addEventListener('click', function () { setOpen(!isOpen()); });
    var topMenu = document.getElementById('stTopMenu');
    if (topMenu) topMenu.addEventListener('click', function () { setOpen(!isOpen()); });
    shell.addEventListener('click', function (e) {
        if (!isMobile() || !isOpen()) return;
        if (e.target.closest('.st-rail a[href]')) setOpen(false);
    });
    if (backdrop) backdrop.addEventListener('click', function () { setOpen(false); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen()) setOpen(false);
    });
    function onViewportChange() {
        if (isMobile()) setOpen(false, false);
        else {
            var stored = false;
            try { stored = localStorage.getItem('st-rail-open') === '1'; } catch (e) {}
            setOpen(stored, false);
        }
        syncToggleIcon(isOpen());
    }
    if (typeof mq.addEventListener === 'function') mq.addEventListener('change', onViewportChange);
    else if (typeof mq.addListener === 'function') mq.addListener(onViewportChange);
})();
</script>
@stack('scripts')
@include('partials.timezone-sync')
</body>
</html>
