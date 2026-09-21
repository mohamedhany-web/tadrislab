<?php
    $img = $img ?? fn (string $file) => asset('img/lasles/'.$file);
    $content = $content ?? [];
    $points = $content['story_points'] ?? [];
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
?>
<section class="lasles-about-story" id="story">
  <div class="lasles-container lasles-about-story__grid">
    <div class="lasles-about-story__copy">
      <p class="lasles-path-kicker"><?php echo e($content['story_kicker'] ?? ''); ?></p>
      <h2 class="lasles-section-title"><?php echo e($content['story_title'] ?? ''); ?></h2>
      <p class="lasles-section-lead"><?php echo e($content['story_body'] ?? ''); ?></p>
      <?php if(is_array($points) && count($points)): ?>
        <ul class="lasles-checklist">
          <?php $__currentLoopData = $points; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><img src="<?php echo e($img('check-green.svg')); ?>" width="24" height="24" alt=""><?php echo e($item); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      <?php endif; ?>
    </div>
    <aside class="lasles-about-story__panel" aria-hidden="true">
      <p class="lasles-about-story__panel-label"><?php echo e($isRtl ? 'حلقة التطوير' : 'Growth loop'); ?></p>
      <ol class="lasles-about-story__loop">
        <li><span>01</span><?php echo e(__('landing.path.steps.diagnose.title')); ?></li>
        <li><span>02</span><?php echo e(__('landing.path.steps.access.title')); ?></li>
        <li><span>03</span><?php echo e(__('landing.path.steps.develop.title')); ?></li>
        <li><span>04</span><?php echo e(__('landing.path.steps.measure.title')); ?></li>
      </ol>
    </aside>
  </div>
</section>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/about-story.blade.php ENDPATH**/ ?>