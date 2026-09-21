<section class="lasles-institutions" id="institutions">
  <div class="lasles-container lasles-features__grid">
    <div>
      <h2 class="lasles-section-title"><?php echo e(__('landing.home.institutions_title')); ?></h2>
      <p class="lasles-institutions__outcome"><?php echo e(__('landing.home.institutions_outcome')); ?></p>
      <p class="lasles-section-lead"><?php echo e(__('landing.home.institutions_lead')); ?></p>
      <ul class="lasles-checklist">
        <?php $__currentLoopData = __('landing.home.institutions_points'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><img src="<?php echo e($img('check-green.svg')); ?>" width="24" height="24" alt=""><?php echo e($item); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
      <div class="lasles-hero__actions" style="margin-top:1.35rem">
        <a href="<?php echo e(route('public.institutions.inquiry')); ?>" class="lasles-btn-primary"><?php echo e(__('landing.home.institutions_cta')); ?></a>
        <a href="<?php echo e(route('public.contact')); ?>" class="lasles-btn-outline"><?php echo e(__('landing.home.cta_contact')); ?></a>
      </div>
    </div>
    <div class="lasles-institutions__panel" aria-hidden="true">
      <div class="lasles-institutions__panel-head">
        <p><?php echo e(__('landing.home.institutions_panel_title')); ?></p>
        <span class="lasles-institutions__chip"><?php echo e(__('landing.home.institutions_panel_chip')); ?></span>
      </div>
      <div class="lasles-institutions__metrics">
        <div class="lasles-institutions__metric">
          <strong><?php echo e(__('landing.home.institutions_metric_1_value')); ?></strong>
          <span><?php echo e(__('landing.home.institutions_metric_1_label')); ?></span>
        </div>
        <div class="lasles-institutions__metric">
          <strong><?php echo e(__('landing.home.institutions_metric_2_value')); ?></strong>
          <span><?php echo e(__('landing.home.institutions_metric_2_label')); ?></span>
        </div>
        <div class="lasles-institutions__metric">
          <strong><?php echo e(__('landing.home.institutions_metric_3_value')); ?></strong>
          <span><?php echo e(__('landing.home.institutions_metric_3_label')); ?></span>
        </div>
      </div>
      <div class="lasles-institutions__art">
        <img src="<?php echo e($img('map-global.svg')); ?>" width="508" height="220" alt="" loading="lazy" decoding="async">
      </div>
    </div>
  </div>
</section>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/institutions.blade.php ENDPATH**/ ?>