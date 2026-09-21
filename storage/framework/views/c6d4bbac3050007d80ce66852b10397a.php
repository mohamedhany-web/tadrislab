<?php
    use App\Support\TadrisPublicNav;
    $services = TadrisPublicNav::services();
    $pillarCopy = __('landing.home.pillars');
    $icons = [
        'learning_paths' => 'icon-paths.svg',
        'tools_resources' => 'icon-tools.svg',
        'consultations' => 'check-list.svg',
        'packages' => 'check-green.svg',
        'schools_institutions' => 'icon-teachers.svg',
    ];
?>
<section class="lasles-pillars" id="services" aria-labelledby="pillars-title">
  <div class="lasles-container">
    <div class="lasles-pillars__head">
      <p class="lasles-path-kicker"><?php echo e(__('landing.home.pillars_kicker')); ?></p>
      <h2 id="pillars-title" class="lasles-section-title"><?php echo e(__('landing.home.pillars_title')); ?></h2>
      <p class="lasles-section-lead"><?php echo e(__('landing.home.pillars_lead')); ?></p>
    </div>
    <div class="lasles-pillars__grid">
      <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $copy = is_array($pillarCopy) ? ($pillarCopy[$service['key']] ?? []) : [];
          $step = $copy['step'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
        ?>
        <a class="lasles-pillar" href="<?php echo e($service['url']); ?>">
          <span class="lasles-pillar__top">
            <span class="lasles-pillar__icon" aria-hidden="true">
              <img src="<?php echo e($img($icons[$service['key']] ?? 'icon-user.svg')); ?>" width="28" height="28" alt="">
            </span>
            <span class="lasles-pillar__step"><?php echo e($step); ?></span>
          </span>
          <h3><?php echo e($copy['title'] ?? $service['label']); ?></h3>
          <p><?php echo e($copy['body'] ?? ''); ?></p>
          <span class="lasles-pillar__link">
            <?php echo e(__('landing.home.pillars_cta')); ?>

            <span class="lasles-pillar__arrow" aria-hidden="true"></span>
          </span>
        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</section>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/pillars.blade.php ENDPATH**/ ?>