<?php
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
    $img = $img ?? fn (string $file) => asset('img/lasles/'.$file);
    $steps = [
        ['num' => '01', 'key' => 'diagnose'],
        ['num' => '02', 'key' => 'access'],
        ['num' => '03', 'key' => 'develop'],
        ['num' => '04', 'key' => 'measure'],
    ];
?>
<section class="lasles-path-home" id="path">
  <div class="lasles-container">
    <div class="lasles-path-home__head">
      <p class="lasles-path-kicker"><?php echo e(__('landing.path.kicker')); ?></p>
      <h2 class="lasles-section-title lasles-section-title--center"><?php echo e(__('landing.path.home_title')); ?></h2>
      <p class="lasles-section-lead lasles-section-lead--center"><?php echo e(__('landing.path.home_lead')); ?></p>
    </div>
    <ol class="lasles-path-grid lasles-path-grid--home">
      <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="lasles-path-card lasles-path-card--compact">
          <span class="lasles-path-card__num" aria-hidden="true"><?php echo e($step['num']); ?></span>
          <h3 class="lasles-path-card__title"><?php echo e(__('landing.path.steps.'.$step['key'].'.title')); ?></h3>
          <p class="lasles-path-card__body"><?php echo e(__('landing.path.steps.'.$step['key'].'.short')); ?></p>
        </li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ol>
    <div class="lasles-path-home__actions">
      <a href="<?php echo e(route('register')); ?>" class="lasles-btn-primary"><?php echo e(__('landing.home.cta_start_journey')); ?></a>
      <a href="<?php echo e(route('public.learning-paths.index')); ?>" class="lasles-btn-outline"><?php echo e(__('landing.home.cta_explore_programs')); ?></a>
    </div>
  </div>
</section>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/path-section.blade.php ENDPATH**/ ?>