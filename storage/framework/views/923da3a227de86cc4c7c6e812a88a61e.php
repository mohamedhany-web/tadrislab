<?php
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
    $img = $img ?? fn (string $file) => asset('img/lasles/'.$file);
    $content = $content ?? [];
?>
<section class="lasles-about-hero" aria-labelledby="about-hero-title">
  <div class="lasles-container lasles-about-hero__grid">
    <div class="lasles-about-hero__copy">
      <p class="lasles-path-kicker"><?php echo e($content['kicker'] ?? ''); ?></p>
      <p class="lasles-about-hero__brand"><?php echo e($isRtl ? 'تدريس لاب' : 'TADRIS LAB'); ?></p>
      <h1 id="about-hero-title" class="lasles-about-hero__title">
        <?php echo e($content['title_before'] ?? ''); ?>

        <em><?php echo e($content['title_strong'] ?? ''); ?></em>
      </h1>
      <p class="lasles-about-hero__lead"><?php echo e($content['lead'] ?? ''); ?></p>
      <div class="lasles-hero__actions">
        <a href="<?php echo e(route('register')); ?>" class="lasles-btn-primary"><?php echo e(__('landing.home.cta_start_journey')); ?></a>
        <a href="<?php echo e(route('public.learning-paths.index')); ?>" class="lasles-btn-outline"><?php echo e(__('landing.home.cta_explore_programs')); ?></a>
      </div>
    </div>
    <div class="lasles-about-hero__art">
      <?php echo $__env->make('partials.landing.lasles.page-art', ['variant' => 'features', 'img' => $img], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
  </div>
</section>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/about-hero.blade.php ENDPATH**/ ?>