<?php
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
?>
<header class="lasles-nav" id="lasles-nav">
  <?php if($topPhone !== '' || $topEmail !== '' || count($topSocials) > 0): ?>
  <div class="lasles-topbar" role="complementary" aria-label="<?php echo e(__('landing.topbar.aria')); ?>">
    <div class="lasles-container lasles-topbar__inner">
      <div class="lasles-topbar__zone lasles-topbar__zone--start">
        <div class="lasles-topbar__contacts">
          <?php if($topPhone !== ''): ?>
            <a href="<?php echo e($telHref); ?>" class="lasles-topbar__link">
              <span class="lasles-topbar__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
              </span>
              <span class="lasles-topbar__text" dir="ltr"><?php echo e($topPhone); ?></span>
            </a>
          <?php endif; ?>
          <?php if($topPhone !== '' && $topEmail !== ''): ?>
            <span class="lasles-topbar__sep" aria-hidden="true"></span>
          <?php endif; ?>
          <?php if($topEmail !== ''): ?>
            <a href="mailto:<?php echo e($topEmail); ?>" class="lasles-topbar__link">
              <span class="lasles-topbar__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              </span>
              <span class="lasles-topbar__text lasles-topbar__email"><?php echo e($topEmail); ?></span>
            </a>
          <?php endif; ?>
        </div>
      </div>

      <div class="lasles-topbar__zone lasles-topbar__zone--mid">
        <p class="lasles-topbar__mid"><?php echo e($topMid); ?></p>
      </div>

      <div class="lasles-topbar__zone lasles-topbar__zone--end">
        <?php if(count($topSocials) > 0): ?>
          <div class="lasles-topbar__social" aria-label="<?php echo e($isRtl ? 'وسائل التواصل' : 'Social media'); ?>">
            <?php $__currentLoopData = $topSocials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
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
              ?>
              <a href="<?php echo e($social['url']); ?>" target="_blank" rel="noopener" class="lasles-topbar__social-btn" aria-label="<?php echo e($social['label']); ?>">
                <?php echo $__env->make('partials.landing.lasles.social-icon', ['kind' => $kind], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
              </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div class="lasles-container lasles-nav__inner">
    <a href="<?php echo e(route('home')); ?>" class="lasles-brand">
      <img src="<?php echo e($img('logo-mark.png')); ?>" width="40" height="40" alt="<?php echo e($isRtl ? 'تدريس لاب' : 'TADRIS LAB'); ?>">
      <span><?php if($isRtl): ?><b>تدريس لاب</b><?php else: ?><b>TADRIS</b> <em>LAB</em><?php endif; ?></span>
    </a>

    <nav class="lasles-nav__links" aria-label="<?php echo e($isRtl ? 'القائمة' : 'Main'); ?>">
      <a href="<?php echo e(route('home')); ?>" class="<?php echo e($homeActive ? 'is-active' : ''); ?>"><?php echo e(__('site.nav_short.home')); ?></a>
      <a href="<?php echo e(route('public.about')); ?>" class="<?php echo e($aboutActive ? 'is-active' : ''); ?>"><?php echo e(__('site.nav_short.about')); ?></a>

      <div class="lasles-nav__dropdown <?php echo e($servicesActive ? 'is-active' : ''); ?>">
        <button type="button" class="lasles-nav__parent <?php echo e($servicesActive ? 'is-active' : ''); ?>" aria-haspopup="true" aria-expanded="false">
          <?php echo e(__('site.nav_short.services')); ?>

          <span class="lasles-nav__caret" aria-hidden="true">▾</span>
        </button>
        <div class="lasles-nav__menu" role="menu">
          <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($service['url']); ?>" role="menuitem" class="<?php echo e(request()->routeIs($service['route']) ? 'is-active' : ''); ?>">
              <?php echo e($service['label']); ?>

            </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

      <a href="<?php echo e(route('public.courses')); ?>" class="<?php echo e($coursesActive ? 'is-active' : ''); ?>"><?php echo e(__('site.nav_short.courses')); ?></a>
      <a href="<?php echo e(route('public.learning-paths.index')); ?>" class="<?php echo e($pathsActive ? 'is-active' : ''); ?>"><?php echo e(__('landing.home.cta_explore_programs')); ?></a>
      <a href="<?php echo e(route('public.contact')); ?>" class="<?php echo e($contactActive ? 'is-active' : ''); ?>"><?php echo e(__('landing.home.cta_contact')); ?></a>
    </nav>

    <div class="lasles-nav__actions">
      <a
        href="<?php echo e($langSwitch($isRtl ? 'en' : 'ar')); ?>"
        class="lasles-lang-toggle"
        data-active="<?php echo e($isRtl ? 'ar' : 'en'); ?>"
        aria-label="<?php echo e($isRtl ? 'Switch to English' : 'التبديل إلى العربية'); ?>"
        title="<?php echo e($isRtl ? 'English' : 'العربية'); ?>"
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
          <span class="lasles-lang-toggle__opt <?php echo e($isRtl ? 'is-active' : ''); ?>">عربي</span>
          <span class="lasles-lang-toggle__opt <?php echo e($isRtl ? '' : 'is-active'); ?>">EN</span>
        </span>
      </a>
      <?php if(auth()->guard()->check()): ?>
        <?php
          $navUser = auth()->user();
          $navPhotoUrl = $navUser?->profile_image_url;
          $navAccountLabel = __('site.nav_short.account');
        ?>
        <a href="<?php echo e(url('/dashboard')); ?>" class="lasles-nav__account" aria-label="<?php echo e($navAccountLabel); ?>" title="<?php echo e($navAccountLabel); ?>">
          <?php if($navPhotoUrl): ?>
            <img src="<?php echo e($navPhotoUrl); ?>" alt="" class="lasles-nav__avatar" width="36" height="36" loading="lazy" decoding="async">
          <?php else: ?>
            <span class="lasles-nav__avatar lasles-nav__avatar--icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21a8 8 0 0 0-16 0"/>
                <circle cx="12" cy="8" r="4"/>
              </svg>
            </span>
          <?php endif; ?>
        </a>
      <?php else: ?>
        <a href="<?php echo e(route('login')); ?>" class="lasles-nav__account lasles-nav__account--guest" aria-label="<?php echo e($isRtl ? 'دخول' : 'Sign In'); ?>" title="<?php echo e($isRtl ? 'دخول' : 'Sign In'); ?>">
          <span class="lasles-nav__avatar lasles-nav__avatar--icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21a8 8 0 0 0-16 0"/>
              <circle cx="12" cy="8" r="4"/>
            </svg>
          </span>
        </a>
        <a href="<?php echo e(route('register')); ?>" class="lasles-btn-primary"><?php echo e(__('landing.home.cta_start_journey')); ?></a>
      <?php endif; ?>
      <button type="button" class="lasles-burger" id="lasles-burger" aria-expanded="false" aria-controls="lasles-mobile" aria-label="<?php echo e($isRtl ? 'القائمة' : 'Menu'); ?>">
        <span aria-hidden="true">☰</span>
      </button>
    </div>
  </div>
</header>

<div class="lasles-drawer" id="lasles-mobile" aria-hidden="true">
  <button type="button" class="lasles-drawer__backdrop" id="lasles-drawer-backdrop" tabindex="-1" aria-label="<?php echo e($isRtl ? 'إغلاق القائمة' : 'Close menu'); ?>"></button>
  <aside class="lasles-drawer__panel" role="dialog" aria-modal="true" aria-labelledby="lasles-drawer-title">
    <div class="lasles-drawer__head">
      <a href="<?php echo e(route('home')); ?>" class="lasles-brand lasles-drawer__brand" id="lasles-drawer-title">
        <img src="<?php echo e($img('logo-mark.png')); ?>" width="36" height="36" alt="<?php echo e($isRtl ? 'تدريس لاب' : 'TADRIS LAB'); ?>">
        <span><?php if($isRtl): ?><b>تدريس لاب</b><?php else: ?><b>TADRIS</b> <em>LAB</em><?php endif; ?></span>
      </a>
      <button type="button" class="lasles-drawer__close" id="lasles-drawer-close" aria-label="<?php echo e($isRtl ? 'إغلاق' : 'Close'); ?>">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
          <path d="M6 6l12 12M18 6L6 18"/>
        </svg>
      </button>
    </div>

    <nav class="lasles-drawer__nav" aria-label="<?php echo e($isRtl ? 'قائمة الجوال' : 'Mobile menu'); ?>">
      <a href="<?php echo e(route('home')); ?>" class="lasles-drawer__link <?php echo e($homeActive ? 'is-active' : ''); ?>"><?php echo e(__('site.nav_short.home')); ?></a>
      <a href="<?php echo e(route('public.about')); ?>" class="lasles-drawer__link <?php echo e($aboutActive ? 'is-active' : ''); ?>"><?php echo e(__('site.nav_short.about')); ?></a>
      <a href="<?php echo e(route('public.courses')); ?>" class="lasles-drawer__link <?php echo e($coursesActive ? 'is-active' : ''); ?>"><?php echo e(__('site.nav_short.courses')); ?></a>
      <a href="<?php echo e(route('public.learning-paths.index')); ?>" class="lasles-drawer__link <?php echo e($pathsActive ? 'is-active' : ''); ?>"><?php echo e(__('landing.home.cta_explore_programs')); ?></a>
      <a href="<?php echo e(route('public.contact')); ?>" class="lasles-drawer__link <?php echo e($contactActive ? 'is-active' : ''); ?>"><?php echo e(__('landing.home.cta_contact')); ?></a>

      <p class="lasles-drawer__label"><?php echo e(__('site.nav_short.services')); ?></p>
      <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($service['url']); ?>" class="lasles-drawer__link lasles-drawer__link--sub <?php echo e(request()->routeIs($service['route']) ? 'is-active' : ''); ?>">
          <?php echo e($service['label']); ?>

        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

    <div class="lasles-drawer__foot">
      <a
        href="<?php echo e($langSwitch($isRtl ? 'en' : 'ar')); ?>"
        class="lasles-lang-toggle lasles-lang-toggle--mobile"
        data-active="<?php echo e($isRtl ? 'ar' : 'en'); ?>"
        aria-label="<?php echo e($isRtl ? 'Switch to English' : 'التبديل إلى العربية'); ?>"
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
          <span class="lasles-lang-toggle__opt <?php echo e($isRtl ? 'is-active' : ''); ?>">عربي</span>
          <span class="lasles-lang-toggle__opt <?php echo e($isRtl ? '' : 'is-active'); ?>">EN</span>
        </span>
      </a>

      <div class="lasles-drawer__actions">
        <?php if(auth()->guard()->check()): ?>
          <a href="<?php echo e(url('/dashboard')); ?>" class="lasles-btn-primary lasles-drawer__cta"><?php echo e(__('site.nav_short.account')); ?></a>
        <?php else: ?>
          <a href="<?php echo e(route('login')); ?>" class="lasles-drawer__signin"><?php echo e($isRtl ? 'دخول' : 'Sign In'); ?></a>
          <a href="<?php echo e(route('register')); ?>" class="lasles-btn-primary lasles-drawer__cta"><?php echo e(__('landing.home.cta_start_journey')); ?></a>
        <?php endif; ?>
      </div>
    </div>
  </aside>
</div>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/nav.blade.php ENDPATH**/ ?>