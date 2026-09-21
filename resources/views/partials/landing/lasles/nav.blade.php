@php
    use App\Support\TadrisPublicNav;
    use App\Services\PublicFooterSettings;
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
    $img = $img ?? fn (string $file) => asset('img/lasles/'.$file);
    $langSwitch = $langSwitch ?? fn (string $lang) => request()->fullUrlWithQuery(array_merge(request()->query(), ['lang' => $lang]));
    $services = TadrisPublicNav::services();
    $servicesActive = TadrisPublicNav::serviceIsActive();
    $homeActive = request()->routeIs('home');
    $aboutActive = request()->routeIs('public.about');
    $coursesActive = request()->routeIs('public.courses') || request()->routeIs('public.course.*');
    $pathsActive = request()->routeIs('public.learning-paths.*');
    $contactActive = request()->routeIs('public.contact');
    $contact = PublicFooterSettings::payload();
    $topPhone = trim((string) ($contact['phone'] ?? ''));
    $topEmail = trim((string) ($contact['email'] ?? ''));
    $topSocials = $contact['socials'] ?? [];
    $topMid = __('landing.topbar.mid');
    $telHref = $topPhone !== '' ? ('tel:'.preg_replace('/\D+/', '', $topPhone)) : '';
@endphp
<header class="lasles-nav" id="lasles-nav">
  @if($topPhone !== '' || $topEmail !== '' || count($topSocials) > 0)
  <div class="lasles-topbar" role="complementary" aria-label="{{ __('landing.topbar.aria') }}">
    <div class="lasles-container lasles-topbar__inner">
      <div class="lasles-topbar__zone lasles-topbar__zone--start">
        <div class="lasles-topbar__contacts">
          @if($topPhone !== '')
            <a href="{{ $telHref }}" class="lasles-topbar__link">
              <span class="lasles-topbar__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              </span>
              <span class="lasles-topbar__text" dir="ltr">{{ $topPhone }}</span>
            </a>
          @endif
          @if($topPhone !== '' && $topEmail !== '')
            <span class="lasles-topbar__sep" aria-hidden="true"></span>
          @endif
          @if($topEmail !== '')
            <a href="mailto:{{ $topEmail }}" class="lasles-topbar__link">
              <span class="lasles-topbar__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              </span>
              <span class="lasles-topbar__text lasles-topbar__email">{{ $topEmail }}</span>
            </a>
          @endif
        </div>
      </div>

      <div class="lasles-topbar__zone lasles-topbar__zone--mid">
        <p class="lasles-topbar__mid">{{ $topMid }}</p>
      </div>

      <div class="lasles-topbar__zone lasles-topbar__zone--end">
        @if(count($topSocials) > 0)
          <div class="lasles-topbar__social" aria-label="{{ $isRtl ? 'وسائل التواصل' : 'Social media' }}">
            @foreach($topSocials as $social)
              @php
                $kind = $social['kind'] ?? null;
                if (! $kind) {
                    $label = mb_strtolower((string) ($social['label'] ?? ''));
                    $kind = 'link';
                    if (str_contains($label, 'facebook')) $kind = 'facebook';
                    elseif (str_contains($label, 'instagram')) $kind = 'instagram';
                    elseif (str_contains($label, 'youtube')) $kind = 'youtube';
                    elseif (str_contains($label, 'linkedin')) $kind = 'linkedin';
                    elseif (str_contains($label, 'tiktok')) $kind = 'tiktok';
                    elseif (str_contains($label, 'telegram')) $kind = 'telegram';
                    elseif (str_contains($label, 'snapchat')) $kind = 'snapchat';
                    elseif (str_contains($label, 'whatsapp')) $kind = 'whatsapp';
                    elseif (str_contains($label, 'twitter') || $label === 'x' || str_starts_with($label, 'x ')) $kind = 'x';
                }
              @endphp
              <a href="{{ $social['url'] }}" target="_blank" rel="noopener" class="lasles-topbar__social-btn" aria-label="{{ $social['label'] }}">
                @include('partials.landing.lasles.social-icon', ['kind' => $kind])
              </a>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
  @endif

  <div class="lasles-container lasles-nav__inner">
    <a href="{{ route('home') }}" class="lasles-brand">
      <img src="{{ $img('logo-mark.png') }}" width="40" height="40" alt="{{ $isRtl ? 'تدريس لاب' : 'TADRIS LAB' }}">
      <span>@if($isRtl)<b>تدريس لاب</b>@else<b>TADRIS</b> <em>LAB</em>@endif</span>
    </a>

    <nav class="lasles-nav__links" aria-label="{{ $isRtl ? 'القائمة' : 'Main' }}">
      <a href="{{ route('home') }}" class="{{ $homeActive ? 'is-active' : '' }}">{{ __('site.nav_short.home') }}</a>
      <a href="{{ route('public.about') }}" class="{{ $aboutActive ? 'is-active' : '' }}">{{ __('site.nav_short.about') }}</a>

      <div class="lasles-nav__dropdown {{ $servicesActive ? 'is-active' : '' }}">
        <button type="button" class="lasles-nav__parent {{ $servicesActive ? 'is-active' : '' }}" aria-haspopup="true" aria-expanded="false">
          {{ __('site.nav_short.services') }}
          <span class="lasles-nav__caret" aria-hidden="true">▾</span>
        </button>
        <div class="lasles-nav__menu" role="menu">
          @foreach($services as $service)
            <a href="{{ $service['url'] }}" role="menuitem" class="{{ request()->routeIs($service['route']) ? 'is-active' : '' }}">
              {{ $service['label'] }}
            </a>
          @endforeach
        </div>
      </div>

      <a href="{{ route('public.courses') }}" class="{{ $coursesActive ? 'is-active' : '' }}">{{ __('site.nav_short.courses') }}</a>
      <a href="{{ route('public.learning-paths.index') }}" class="{{ $pathsActive ? 'is-active' : '' }}">{{ __('landing.home.cta_explore_programs') }}</a>
      <a href="{{ route('public.contact') }}" class="{{ $contactActive ? 'is-active' : '' }}">{{ __('landing.home.cta_contact') }}</a>
    </nav>

    <div class="lasles-nav__actions">
      <a
        href="{{ $langSwitch($isRtl ? 'en' : 'ar') }}"
        class="lasles-lang-toggle"
        data-active="{{ $isRtl ? 'ar' : 'en' }}"
        aria-label="{{ $isRtl ? 'Switch to English' : 'التبديل إلى العربية' }}"
        title="{{ $isRtl ? 'English' : 'العربية' }}"
      >
        <span class="lasles-lang-toggle__globe" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9"/>
            <path d="M3 12h18"/>
            <path d="M12 3a14 14 0 0 1 0 18a14 14 0 0 1 0-18"/>
          </svg>
        </span>
        <span class="lasles-lang-toggle__track">
          <span class="lasles-lang-toggle__thumb" aria-hidden="true"></span>
          <span class="lasles-lang-toggle__opt {{ $isRtl ? 'is-active' : '' }}">عربي</span>
          <span class="lasles-lang-toggle__opt {{ $isRtl ? '' : 'is-active' }}">EN</span>
        </span>
      </a>
      @auth
        @php
          $navUser = auth()->user();
          $navPhotoUrl = $navUser?->profile_image_url;
          $navAccountLabel = __('site.nav_short.account');
        @endphp
        <a href="{{ url('/dashboard') }}" class="lasles-nav__account" aria-label="{{ $navAccountLabel }}" title="{{ $navAccountLabel }}">
          @if($navPhotoUrl)
            <img src="{{ $navPhotoUrl }}" alt="" class="lasles-nav__avatar" width="36" height="36" loading="lazy" decoding="async">
          @else
            <span class="lasles-nav__avatar lasles-nav__avatar--icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21a8 8 0 0 0-16 0"/>
                <circle cx="12" cy="8" r="4"/>
              </svg>
            </span>
          @endif
        </a>
      @else
        <a href="{{ route('login') }}" class="lasles-nav__account lasles-nav__account--guest" aria-label="{{ $isRtl ? 'دخول' : 'Sign In' }}" title="{{ $isRtl ? 'دخول' : 'Sign In' }}">
          <span class="lasles-nav__avatar lasles-nav__avatar--icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21a8 8 0 0 0-16 0"/>
              <circle cx="12" cy="8" r="4"/>
            </svg>
          </span>
        </a>
        <a href="{{ route('register') }}" class="lasles-btn-primary">{{ __('landing.home.cta_start_journey') }}</a>
      @endauth
      <button type="button" class="lasles-burger" id="lasles-burger" aria-expanded="false" aria-controls="lasles-mobile" aria-label="{{ $isRtl ? 'القائمة' : 'Menu' }}">
        <span aria-hidden="true">☰</span>
      </button>
    </div>
  </div>
</header>

<div class="lasles-drawer" id="lasles-mobile" aria-hidden="true">
  <button type="button" class="lasles-drawer__backdrop" id="lasles-drawer-backdrop" tabindex="-1" aria-label="{{ $isRtl ? 'إغلاق القائمة' : 'Close menu' }}"></button>
  <aside class="lasles-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="lasles-drawer-title">
    <div class="lasles-drawer__head">
      <a href="{{ route('home') }}" class="lasles-brand lasles-drawer__brand" id="lasles-drawer-title">
        <img src="{{ $img('logo-mark.png') }}" width="36" height="36" alt="{{ $isRtl ? 'تدريس لاب' : 'TADRIS LAB' }}">
        <span>@if($isRtl)<b>تدريس لاب</b>@else<b>TADRIS</b> <em>LAB</em>@endif</span>
      </a>
      <button type="button" class="lasles-drawer__close" id="lasles-drawer-close" aria-label="{{ $isRtl ? 'إغلاق' : 'Close' }}">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
          <path d="M6 6l12 12M18 6L6 18"/>
        </svg>
      </button>
    </div>

    <nav class="lasles-drawer__nav" aria-label="{{ $isRtl ? 'قائمة الجوال' : 'Mobile menu' }}">
      <a href="{{ route('home') }}" class="lasles-drawer__link {{ $homeActive ? 'is-active' : '' }}">{{ __('site.nav_short.home') }}</a>
      <a href="{{ route('public.about') }}" class="lasles-drawer__link {{ $aboutActive ? 'is-active' : '' }}">{{ __('site.nav_short.about') }}</a>
      <a href="{{ route('public.courses') }}" class="lasles-drawer__link {{ $coursesActive ? 'is-active' : '' }}">{{ __('site.nav_short.courses') }}</a>
      <a href="{{ route('public.learning-paths.index') }}" class="lasles-drawer__link {{ $pathsActive ? 'is-active' : '' }}">{{ __('landing.home.cta_explore_programs') }}</a>
      <a href="{{ route('public.contact') }}" class="lasles-drawer__link {{ $contactActive ? 'is-active' : '' }}">{{ __('landing.home.cta_contact') }}</a>

      <p class="lasles-drawer__label">{{ __('site.nav_short.services') }}</p>
      @foreach($services as $service)
        <a href="{{ $service['url'] }}" class="lasles-drawer__link lasles-drawer__link--sub {{ request()->routeIs($service['route']) ? 'is-active' : '' }}">
          {{ $service['label'] }}
        </a>
      @endforeach
    </nav>

    <div class="lasles-drawer__foot">
      <a
        href="{{ $langSwitch($isRtl ? 'en' : 'ar') }}"
        class="lasles-lang-toggle lasles-lang-toggle--mobile"
        data-active="{{ $isRtl ? 'ar' : 'en' }}"
        aria-label="{{ $isRtl ? 'Switch to English' : 'التبديل إلى العربية' }}"
      >
        <span class="lasles-lang-toggle__globe" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9"/>
            <path d="M3 12h18"/>
            <path d="M12 3a14 14 0 0 1 0 18a14 14 0 0 1 0-18"/>
          </svg>
        </span>
        <span class="lasles-lang-toggle__track">
          <span class="lasles-lang-toggle__thumb" aria-hidden="true"></span>
          <span class="lasles-lang-toggle__opt {{ $isRtl ? 'is-active' : '' }}">عربي</span>
          <span class="lasles-lang-toggle__opt {{ $isRtl ? '' : 'is-active' }}">EN</span>
        </span>
      </a>

      <div class="lasles-drawer__actions">
        @auth
          <a href="{{ url('/dashboard') }}" class="lasles-btn-primary lasles-drawer__cta">{{ __('site.nav_short.account') }}</a>
        @else
          <a href="{{ route('login') }}" class="lasles-drawer__signin">{{ $isRtl ? 'دخول' : 'Sign In' }}</a>
          <a href="{{ route('register') }}" class="lasles-btn-primary lasles-drawer__cta">{{ __('landing.home.cta_start_journey') }}</a>
        @endauth
      </div>
    </div>
  </aside>
</div>
