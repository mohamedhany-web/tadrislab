<section class="lasles-hero" id="top">
  <div class="lasles-hero__glow" aria-hidden="true"></div>
  <div class="lasles-container lasles-hero__grid">
    <div class="lasles-hero__copy">
      <p class="lasles-hero__brand"><?php echo e(__('landing.home.hero_brand')); ?></p>
      <h1 class="lasles-hero__title"><?php echo e(__('landing.home.hero_title')); ?></h1>
      <p class="lasles-hero__lead"><?php echo e(__('landing.home.hero_lead')); ?></p>
      <div class="lasles-hero__actions">
        <a href="<?php echo e(route('register')); ?>" class="lasles-btn-primary lasles-btn-primary--hero"><?php echo e(__('landing.home.cta_start_journey')); ?></a>
        <a href="<?php echo e(route('public.learning-paths.index')); ?>" class="lasles-btn-outline lasles-btn-outline--hero"><?php echo e(__('landing.home.cta_explore_programs')); ?></a>
      </div>
    </div>
    <div class="lasles-hero__art">
      <div class="lasles-hero__art-frame">
        <img src="<?php echo e($img('hero-illustration.svg')); ?>" width="611" height="382" alt="" decoding="async" fetchpriority="high">
      </div>
    </div>
  </div>
</section>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/hero.blade.php ENDPATH**/ ?>