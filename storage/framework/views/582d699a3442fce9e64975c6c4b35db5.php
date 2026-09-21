<?php
    $content = $content ?? [];
    $principles = $content['principles'] ?? [];
?>
<section class="lasles-about-why" id="why">
  <div class="lasles-container">
    <div class="lasles-about-why__head">
      <h2 class="lasles-section-title lasles-section-title--center"><?php echo e($content['why_title'] ?? ''); ?></h2>
      <p class="lasles-section-lead lasles-section-lead--center"><?php echo e($content['why_lead'] ?? ''); ?></p>
    </div>
    <?php if(is_array($principles) && count($principles)): ?>
      <div class="lasles-about-why__grid">
        <?php $__currentLoopData = $principles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <article class="lasles-about-principle">
            <span class="lasles-about-principle__num" aria-hidden="true"><?php echo e(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
            <h3><?php echo e($item['title'] ?? ''); ?></h3>
            <p><?php echo e($item['body'] ?? ''); ?></p>
          </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/about-why.blade.php ENDPATH**/ ?>