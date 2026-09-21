<?php
  /** @var \App\Models\Package $package */
  $img = $img ?? (fn (string $file) => asset('img/lasles/'.$file));
  $featured = $package->is_popular || $package->is_featured;
  $features = array_values(array_filter(is_array($package->features) ? $package->features : []));
  $tools = array_values(array_filter(is_array($package->tools_resources) ? $package->tools_resources : []));
  $allBenefits = array_values(array_unique(array_merge($features, $tools)));
  $previewBenefits = array_slice($allBenefits, 0, 5);
  $extraBenefits = array_slice($allBenefits, 5);
  $typeLabel = $package->typeLabel();
  $audience = filled($package->card_summary)
      ? $package->card_summary
      : (
          \Illuminate\Support\Facades\Lang::has('landing.home.package_audience.'.$package->package_type)
              ? __('landing.home.package_audience.'.$package->package_type)
              : $typeLabel
      );
  $ctaClass = ($featured && ! $package->isQuoteOnly()) ? 'lasles-btn-primary' : 'lasles-btn-outline';
  $ctaText = $package->isQuoteOnly()
      ? __('landing.home.cta_contact')
      : __('landing.home.cta_start_journey');
  $detailsId = 'plan-details-'.$package->id;
?>
<article class="lasles-plan <?php echo e($featured ? 'is-featured' : ''); ?> lasles-plan--type-<?php echo e($package->package_type ?: 'individual'); ?>">
  <?php if($featured): ?>
    <span class="lasles-plan__badge"><?php echo e(__('public.pricing_package_popular')); ?></span>
  <?php endif; ?>

  <?php if($typeLabel !== $package->name): ?>
    <p class="lasles-plan__type"><?php echo e($typeLabel); ?></p>
  <?php endif; ?>
  <h3 class="lasles-plan__name"><?php echo e($package->name); ?></h3>
  <p class="lasles-plan__audience"><?php echo e($audience); ?></p>

  <div class="lasles-plan__pricing">
    <p class="lasles-plan__price">
      <b><?php echo e($package->formattedPrice()); ?></b>
      <?php if($package->formattedOriginalPrice()): ?>
        <span class="lasles-plan__old"><?php echo e($package->formattedOriginalPrice()); ?></span>
      <?php endif; ?>
    </p>
    <?php if($package->durationLabel()): ?>
      <p class="lasles-plan__duration"><?php echo e($package->durationLabel()); ?></p>
    <?php endif; ?>
  </div>

  <div class="lasles-plan__specs">
    <?php if($package->consultation_sessions): ?>
      <span><?php echo e(__('public.pricing_meta_consult', ['count' => $package->consultation_sessions])); ?></span>
    <?php endif; ?>
    <?php if($package->participant_seats && $package->participant_seats > 1): ?>
      <span><?php echo e(__('public.pricing_meta_seats', ['count' => $package->participant_seats])); ?></span>
    <?php endif; ?>
    <?php if($package->includes_tools): ?>
      <span><?php echo e(__('public.pricing_meta_tools')); ?></span>
    <?php endif; ?>
  </div>

  <div class="lasles-plan__cta-wrap">
    <a href="<?php echo e($package->ctaUrl()); ?>" class="<?php echo e($ctaClass); ?>"><?php echo e($ctaText); ?></a>
  </div>

  <?php if(count($previewBenefits)): ?>
    <ul class="lasles-plan__list">
      <?php $__currentLoopData = $previewBenefits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><span class="lasles-plan__check" aria-hidden="true"></span><?php echo e($feature); ?></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
  <?php endif; ?>

  <?php if(count($extraBenefits) || filled($package->discount_note)): ?>
    <button
      type="button"
      class="lasles-plan__more"
      data-plan-more
      data-show-label="<?php echo e(__('landing.home.packages_show_all')); ?>"
      data-hide-label="<?php echo e(__('landing.home.packages_hide_all')); ?>"
      aria-expanded="false"
      aria-controls="<?php echo e($detailsId); ?>"
    >
      <?php echo e(__('landing.home.packages_show_all')); ?>

    </button>
    <div class="lasles-plan__details" id="<?php echo e($detailsId); ?>" hidden>
      <?php if(count($extraBenefits)): ?>
        <ul class="lasles-plan__list">
          <?php $__currentLoopData = $extraBenefits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><span class="lasles-plan__check" aria-hidden="true"></span><?php echo e($feature); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      <?php endif; ?>
      <?php if(filled($package->discount_note)): ?>
        <p class="lasles-plan__note"><?php echo e($package->discount_note); ?></p>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</article>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/package-card.blade.php ENDPATH**/ ?>