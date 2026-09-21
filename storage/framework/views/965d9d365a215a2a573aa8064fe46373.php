<section class="lasles-features" id="features">
  <div class="lasles-container lasles-features__grid">
    <div class="lasles-features__art">
      <img src="<?php echo e($img('features-illustration.png')); ?>" width="508" height="414" alt="" loading="lazy" decoding="async">
    </div>
    <div>
      <h2 class="lasles-section-title"><?php echo e(__('landing.home.features_title')); ?></h2>
      <p class="lasles-section-lead"><?php echo e(__('landing.home.features_lead')); ?></p>
      <ul class="lasles-checklist">
        <?php $__currentLoopData = __('landing.home.features'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><img src="<?php echo e($img('check-green.svg')); ?>" width="24" height="24" alt=""><?php echo e($item); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
      <div class="lasles-hero__actions" style="margin-top:1.25rem">
        <a href="<?php echo e(route('register')); ?>" class="lasles-btn-primary"><?php echo e(__('landing.home.cta_start_journey')); ?></a>
        <a href="<?php echo e(route('public.learning-paths.index')); ?>" class="lasles-btn-outline"><?php echo e(__('landing.home.cta_explore_programs')); ?></a>
      </div>
    </div>
  </div>
</section>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/features.blade.php ENDPATH**/ ?>