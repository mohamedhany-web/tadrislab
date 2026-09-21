<?php
    $dbPackages = \App\Models\Package::query()
        ->active()
        ->orderBy('order')
        ->orderByDesc('is_popular')
        ->limit(5)
        ->get();

    $artFor = fn ($type) => match ($type) {
        'free' => 'plan-free.svg',
        'advanced' => 'plan-premium.svg',
        default => 'plan-standard.svg',
    };

    $plansMeta = [
        ['key' => 'free', 'art' => 'plan-free.svg', 'featured' => false, 'cta' => 'register'],
        ['key' => 'individual', 'art' => 'plan-standard.svg', 'featured' => false, 'cta' => 'register'],
        ['key' => 'advanced', 'art' => 'plan-premium.svg', 'featured' => true, 'cta' => 'register'],
        ['key' => 'school', 'art' => 'plan-standard.svg', 'featured' => false, 'cta' => 'contact'],
    ];
?>
<section class="lasles-pricing" id="pricing">
  <div class="lasles-container">
    <h2 class="lasles-section-title"><?php echo e(__('landing.home.packages_title')); ?></h2>
    <p class="lasles-section-lead"><?php echo e(__('landing.home.packages_lead')); ?></p>
    <?php if($dbPackages->isNotEmpty()): ?>
      <div class="lasles-plans lasles-plans--<?php echo e(min(4, $dbPackages->count())); ?>">
        <?php $__currentLoopData = $dbPackages->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php echo $__env->make('partials.landing.lasles.package-card', [
            'package' => $package,
            'art' => $artFor($package->package_type),
            'img' => $img,
          ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <p class="lasles-section-lead" style="margin-top:1.5rem;text-align:center">
        <a href="<?php echo e(route('public.pricing')); ?>" class="lasles-btn-outline"><?php echo e(__('landing.home.packages_all')); ?></a>
      </p>
    <?php else: ?>
      <div class="lasles-plans lasles-plans--4">
        <?php $__currentLoopData = $plansMeta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $plan = __('landing.home.plans.'.$meta['key']); ?>
          <article class="lasles-plan <?php echo e($meta['featured'] ? 'is-featured' : ''); ?>">
            <?php if($meta['featured']): ?>
              <span class="lasles-plan__badge"><?php echo e(__('public.pricing_package_popular')); ?></span>
            <?php endif; ?>
            <h3 class="lasles-plan__name"><?php echo e($plan['name'] ?? ''); ?></h3>
            <p class="lasles-plan__audience"><?php echo e($plan['audience'] ?? ''); ?></p>
            <p class="lasles-plan__price">
              <b><?php echo e($plan['price_html'] ?? ($plan['price'] ?? '')); ?></b>
            </p>
            <div class="lasles-plan__cta-wrap">
              <?php if(($meta['cta'] ?? '') === 'contact'): ?>
                <a href="<?php echo e(route('public.contact')); ?>" class="lasles-btn-outline"><?php echo e(__('landing.home.cta_contact')); ?></a>
              <?php else: ?>
                <a href="<?php echo e(route('register')); ?>" class="<?php echo e($meta['featured'] ? 'lasles-btn-primary' : 'lasles-btn-outline'); ?>">
                  <?php echo e(__('landing.home.cta_start_journey')); ?>

                </a>
              <?php endif; ?>
            </div>
            <ul class="lasles-plan__list">
              <?php $__currentLoopData = array_slice($plan['features'] ?? [], 0, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><img src="<?php echo e($img('check-list.svg')); ?>" width="20" height="20" alt=""><?php echo e($f); ?></li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/pricing.blade.php ENDPATH**/ ?>