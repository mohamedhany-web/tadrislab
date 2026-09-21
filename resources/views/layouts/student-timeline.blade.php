@php
    $appLocale = app()->getLocale();
    $appRtl = $appLocale === 'ar';
    $user = auth()->user();
    $firstName = explode(' ', trim((string) ($user?->name ?? '')))[0] ?? '';
    $avatarUrl = $user?->avatarDisplayUrl() ?? \App\Models\User::placeholderAvatarUrl();
    $brandLogoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
    $brandLogoFallback = \App\Services\AdminPanelBranding::inlineFallbackDataUri();

    $navItems = [
        ['route' => 'dashboard', 'match' => ['dashboard'], 'label' => __('student_timeline.nav_home'), 'icon' => 'home.svg'],
        ['route' => 'student.packages.index', 'match' => ['student.packages.*'], 'label' => __('student_timeline.nav_packages'), 'fa' => 'fas fa-box-open', 'ui' => 'show_packages'],
        ['route' => 'student.learning-paths.index', 'match' => ['student.learning-paths.*'], 'label' => __('student_timeline.nav_paths'), 'fa' => 'fas fa-route', 'ui' => 'show_learning_paths'],
        ['route' => 'student.tools.index', 'match' => ['student.tools.*'], 'label' => __('student_timeline.nav_tools'), 'fa' => 'fas fa-toolbox', 'ui' => 'show_tools'],
        ['route' => 'student.teacher-assistant.index', 'match' => ['student.teacher-assistant.*'], 'label' => 'مساعد المعلم', 'fa' => 'fas fa-magic', 'ui' => 'show_teacher_assistant'],
        ['route' => 'consultations.index', 'match' => ['consultations.*', 'public.consultations.*'], 'label' => __('student_timeline.nav_consultations'), 'fa' => 'fas fa-comments', 'ui' => 'show_consultations'],
        ['route' => 'institution.portal.index', 'match' => ['institution.portal.*'], 'label' => 'جهتي', 'fa' => 'fas fa-building', 'ui' => 'show_institution_portal'],
        ['route' => 'public.pricing', 'match' => ['public.pricing', 'public.packages.*', 'public.package.*'], 'label' => 'الباقات', 'fa' => 'fas fa-tags', 'ui' => 'show_pricing'],
        ['route' => 'orders.index', 'match' => ['orders.*'], 'label' => __('student_timeline.nav_orders'), 'fa' => 'fas fa-receipt', 'ui' => 'show_orders'],
        ['route' => 'student.support.index', 'match' => ['student.support.*'], 'label' => __('student_timeline.nav_support'), 'fa' => 'fas fa-headset', 'ui' => 'show_support'],
        ['route' => 'notifications', 'match' => ['notifications*'], 'label' => __('student_timeline.nav_messages'), 'icon' => 'notifications.svg', 'ui' => 'show_notifications'],
        ['route' => 'settings', 'match' => ['settings'], 'label' => __('student_timeline.nav_settings'), 'icon' => 'settings.svg', 'ui' => 'show_settings'],
    ];

    if ($user?->isAdmin() && Route::has('admin.dashboard')) {
        $navItems[] = [
            'route' => 'admin.dashboard',
            'match' => ['admin.*'],
            'label' => __('student.admin_panel'),
            'fa' => 'fas fa-shield-alt',
        ];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ $appLocale }}" dir="{{ $appRtl ? 'rtl' : 'ltr' }}" class="{{ $appRtl ? 'st-rtl' : 'st-ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', __('student_timeline.timeline'))</title>
    @include('partials.favicon-links')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Lato:wght@400;700;900&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ route('assets.student-timeline.css') }}?v=st-inst-1">
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
    @php
        $showContentProtection = ! empty(trim((string) ($__env->yieldContent('enable-content-protection') ?? '')));
    @endphp
    @if($showContentProtection)
        <script>
            window.Laravel = { user: { name: '{{ auth()->check() ? auth()->user()->name : "زائر" }}' } };
        </script>
        <script src="{{ versioned_asset('js/platform-protection.js') }}"></script>
    @endif
    @stack('styles')
</head>
<body class="st-dash">

<script>
(function () {
  try {
    if (localStorage.getItem('st-rail-open') === '1') {
      window.__stRailPreferOpen = true;
    }
  } catch (e) {}
})();
</script>

<div class="st-shell{{ $appRtl ? ' is-rtl' : ' is-ltr' }}{{ View::hasSection('events') ? ' has-events' : ' is-wide' }}" id="stShell">
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
    <aside class="st-rail" id="stRail" aria-label="{{ __('student_timeline.nav_home') }}">
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

        <a href="{{ route('profile') }}" class="st-rail__profile" title="{{ __('student_timeline.profile') }}">
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
                    if (! empty($item['ui']) && ! student_ui($item['ui'], true)) {
                        continue;
                    }
                    if (! empty($item['needs_libraries']) && ! student_ui('show_libraries', true)) {
                        continue;
                    }
                    $href = route($item['route']);
                    $active = request()->routeIs(...$item['match']);
                @endphp
                <a href="{{ $href }}" class="st-rail__link {{ $active ? 'is-active' : '' }}" title="{{ $item['label'] }}" aria-label="{{ $item['label'] }}">
                    <span class="st-rail__icon-box">
                        @if(! empty($item['icon']))
                            <img class="st-rail__icon" src="{{ asset('img/student-timeline/nav/'.$item['icon']) }}" alt="" width="24" height="24">
                        @elseif(! empty($item['fa']))
                            <i class="{{ $item['fa'] }}" aria-hidden="true" style="font-size:16px;opacity:.88"></i>
                        @endif
                    </span>
                    <span class="st-rail__label">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="st-rail__foot" hidden aria-hidden="true"></div>
    </aside>

    <main class="st-main">
        @yield('content')
    </main>

    <aside class="st-events" aria-label="{{ __('student_timeline.events') }}">
        @yield('events')
    </aside>
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

    function isMobile() {
        return mq.matches;
    }

    function isOpen() {
        return shell.classList.contains('is-rail-open');
    }

    function syncToggleIcon(open) {
        var mobileIcon = open ? 'fas fa-xmark' : 'fas fa-bars';
        var icon = btn.querySelector('i');
        var text = btn.querySelector('.st-rail__toggle-text');
        if (icon) {
            if (isMobile()) {
                icon.className = mobileIcon;
            } else {
                icon.className = open
                    ? ('fas fa-angles-' + (isRtl ? 'right' : 'left'))
                    : ('fas fa-angles-' + (isRtl ? 'left' : 'right'));
            }
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
    try { preferOpen = localStorage.getItem('st-rail-open') === '1' || window.__stRailPreferOpen === true; } catch (e) {}
    setOpen(isMobile() ? false : (shell.classList.contains('is-rail-open') || preferOpen), false);

    btn.addEventListener('click', function () {
        setOpen(!isOpen());
    });

    var topMenu = document.getElementById('stTopMenu');
    if (topMenu) {
        topMenu.addEventListener('click', function () {
            setOpen(!isOpen());
        });
    }

    shell.addEventListener('click', function (e) {
        if (!isMobile() || !isOpen()) return;
        var link = e.target.closest('.st-rail a[href]');
        if (link) setOpen(false);
    });

    if (backdrop) {
        backdrop.addEventListener('click', function () { setOpen(false); });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen()) {
            setOpen(false);
        }
    });

    function onViewportChange() {
        if (isMobile()) {
            setOpen(false, false);
        } else {
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
