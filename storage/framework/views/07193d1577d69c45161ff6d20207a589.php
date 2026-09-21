<?php
    $content = $content ?? [];
    $steps = [
        ['num' => '01', 'key' => 'diagnose'],
        ['num' => '02', 'key' => 'access'],
        ['num' => '03', 'key' => 'develop'],
        ['num' => '04', 'key' => 'measure'],
    ];
?>
<section class="lasles-about-loop" id="how">
  <div class="lasles-container">
    <div class="lasles-about-loop__head">
      <h2 class="lasles-section-title lasles-section-title--center"><?php echo e($content['loop_title'] ?? ''); ?></h2>
      <p class="lasles-section-lead lasles-section-lead--center"><?php echo e($content['loop_lead'] ?? ''); ?></p>
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
  </div>
</section>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/about-loop.blade.php ENDPATH**/ ?>