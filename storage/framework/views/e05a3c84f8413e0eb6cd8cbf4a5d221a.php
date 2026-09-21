<?php $reviews = __('landing.home.reviews'); ?>
<section class="lasles-reviews" id="testimonials">
  <div class="lasles-container">
    <div class="lasles-trust">
      <p class="lasles-trust__title"><?php echo e(__('landing.home.trust_logos_title')); ?></p>
    </div>
    <h2 class="lasles-section-title"><?php echo e(__('landing.home.reviews_title')); ?></h2>
    <p class="lasles-section-lead"><?php echo e(__('landing.home.reviews_lead')); ?></p>
    <div class="lasles-reviews__track">
      <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <article class="lasles-review <?php echo e($i === 0 ? 'is-active' : ''); ?>">
          <div class="lasles-review__top">
            <img src="<?php echo e($img('avatar-'.(($i % 3) + 1).'.png')); ?>" width="50" height="50" alt="">
            <div>
              <p class="lasles-review__name"><?php echo e($review['name']); ?></p>
              <p class="lasles-review__loc"><?php echo e($review['role']); ?></p>
            </div>
            <div class="lasles-review__rating">5.0 <img src="<?php echo e($img('star.svg')); ?>" width="14" height="14" alt=""></div>
          </div>
          <p class="lasles-review__quote">“<?php echo e($review['quote']); ?>”</p>
        </article>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="lasles-reviews__controls">
      <div class="lasles-dots" aria-hidden="true">
        <span class="is-on"></span><span></span><span></span>
      </div>
      <div class="lasles-reviews__arrows">
        <button type="button" aria-label="<?php echo e(app()->getLocale() === 'ar' ? 'السابق' : 'Previous'); ?>"><img src="<?php echo e($img('arrow-left.svg')); ?>" width="50" height="50" alt=""></button>
        <button type="button" aria-label="<?php echo e(app()->getLocale() === 'ar' ? 'التالي' : 'Next'); ?>"><img src="<?php echo e($img('arrow-right.svg')); ?>" width="50" height="50" alt=""></button>
      </div>
    </div>
  </div>
</section>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/testimonials.blade.php ENDPATH**/ ?>