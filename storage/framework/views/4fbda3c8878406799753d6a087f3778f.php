<?php
    use App\Support\TadrisPublicNav;
    use App\Services\PublicFooterSettings;
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
    $brand = 'TADRIS LAB';
    $brandAr = 'تدريس لاب';
    $img = $img ?? fn (string $file) => asset('img/lasles/'.$file);
    $services = TadrisPublicNav::services();
    $footer = PublicFooterSettings::payload();
    $whatsapp = $footer['whatsapp_url'] ?? '';
    $email = $footer['email'] ?? 'info@tadrislab.com';
    $phone = $footer['phone'] ?? '';
    $socials = $footer['socials'] ?? [];
    $tagline = $footer['brand_tagline'] ?? ($isRtl ? 'تطوير الممارسات المهنية للمعلمين.' : 'Professional development for teachers.');

    $socialKind = static function (string $label): string {
        $l = mb_strtolower($label);
        if (str_contains($l, 'facebook')) return 'facebook';
        if (str_contains($l, 'instagram')) return 'instagram';
        if (str_contains($l, 'youtube')) return 'youtube';
        if (str_contains($l, 'linkedin')) return 'linkedin';
        if (str_contains($l, 'tiktok')) return 'tiktok';
        if (str_contains($l, 'telegram')) return 'telegram';
        if (str_contains($l, 'snapchat')) return 'snapchat';
        if (str_contains($l, 'whatsapp')) return 'whatsapp';
        if (str_contains($l, 'twitter') || $l === 'x' || str_starts_with($l, 'x ')) return 'x';
        return 'link';
    };
?>
<footer class="lasles-footer">
  <div class="lasles-container lasles-footer__grid">
    <div class="lasles-footer__about">
      <a href="<?php echo e(route('home')); ?>" class="lasles-brand lasles-footer__brand">
        <img src="<?php echo e($img('logo-mark.png')); ?>" width="36" height="36" alt="<?php echo e($isRtl ? 'تدريس لاب' : 'TADRIS LAB'); ?>">
        <span><?php if($isRtl): ?><b>تدريس لاب</b><?php else: ?><b>TADRIS</b> <em>LAB</em><?php endif; ?></span>
      </a>
      <p class="lasles-footer__blurb"><?php echo e($tagline); ?></p>

      <div class="lasles-footer__meta">
        <?php if($email): ?>
          <a href="mailto:<?php echo e($email); ?>" class="lasles-footer__meta-link"><?php echo e($email); ?></a>
        <?php endif; ?>
        <?php if($phone): ?>
          <a href="tel:<?php echo e(preg_replace('/\s+/', '', $phone)); ?>" class="lasles-footer__meta-link"><?php echo e($phone); ?></a>
        <?php endif; ?>
      </div>

      <div class="lasles-social" aria-label="<?php echo e($isRtl ? 'حسابات التواصل' : 'Social links'); ?>">
        <?php $__empty_1 = true; $__currentLoopData = $socials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php $kind = $socialKind($social['label'] ?? ''); ?>
          <a href="<?php echo e($social['url']); ?>" target="_blank" rel="noopener" class="lasles-social__btn" aria-label="<?php echo e($social['label']); ?>">
            <?php echo $__env->make('partials.landing.lasles.social-icon', ['kind' => $kind], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <?php if($whatsapp): ?>
            <a href="<?php echo e($whatsapp); ?>" target="_blank" rel="noopener" class="lasles-social__btn" aria-label="WhatsApp">
              <?php echo $__env->make('partials.landing.lasles.social-icon', ['kind' => 'whatsapp'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </a>
          <?php endif; ?>
          <a href="<?php echo e(route('public.contact')); ?>" class="lasles-social__btn" aria-label="<?php echo e($isRtl ? 'تواصل معنا' : 'Contact'); ?>">
            <?php echo $__env->make('partials.landing.lasles.social-icon', ['kind' => 'mail'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <nav class="lasles-footer__cols" aria-label="<?php echo e($isRtl ? 'روابط الفوتر' : 'Footer links'); ?>">
      <div>
        <h4><?php echo e(__('site.nav_short.services')); ?></h4>
        <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e($service['url']); ?>"><?php echo e($service['label']); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <div>
        <h4><?php echo e($isRtl ? 'المنصة' : 'Platform'); ?></h4>
        <a href="<?php echo e(route('public.about')); ?>"><?php echo e(__('site.nav.about')); ?></a>
        <a href="<?php echo e(route('public.contact')); ?>"><?php echo e(__('landing.home.cta_contact')); ?></a>
        <a href="<?php echo e(route('register')); ?>"><?php echo e(__('landing.home.cta_start_journey')); ?></a>
      </div>
      <div>
        <h4><?php echo e($isRtl ? 'قانوني' : 'Legal'); ?></h4>
        <a href="<?php echo e(route('public.privacy')); ?>"><?php echo e($isRtl ? 'سياسة الخصوصية' : 'Privacy Policy'); ?></a>
        <a href="<?php echo e(route('public.terms')); ?>"><?php echo e($isRtl ? 'الشروط والأحكام' : 'Terms of Service'); ?></a>
        <a href="<?php echo e(route('public.faq')); ?>"><?php echo e($isRtl ? 'الأسئلة الشائعة' : 'FAQ'); ?></a>
      </div>
    </nav>
  </div>
  <div class="lasles-container lasles-footer__bottom">
    <p class="lasles-copy">©<?php echo e(date('Y')); ?> <?php echo e($isRtl ? $brandAr : $brand); ?></p>
  </div>
</footer>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/footer.blade.php ENDPATH**/ ?>