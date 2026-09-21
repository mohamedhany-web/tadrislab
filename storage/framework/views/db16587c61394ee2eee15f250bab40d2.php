<?php
    $featuredCourses = $featuredCourses ?? collect();
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
?>
<section class="lasles-showcase lasles-showcase--courses" id="home-courses" aria-labelledby="home-courses-title">
  <div class="lasles-container">
    <div class="lasles-showcase__head">
      <div class="lasles-showcase__intro">
        <p class="lasles-path-kicker"><?php echo e(__('landing.home.showcase_courses_kicker')); ?></p>
        <h2 id="home-courses-title" class="lasles-section-title"><?php echo e(__('landing.home.showcase_courses_title')); ?></h2>
        <p class="lasles-section-lead"><?php echo e(__('landing.home.showcase_courses_lead')); ?></p>
      </div>
      <a href="<?php echo e(route('public.courses')); ?>" class="lasles-showcase__more lasles-btn-outline">
        <?php echo e(__('landing.home.showcase_courses_cta')); ?>

        <span aria-hidden="true"><?php echo e($isRtl ? '←' : '→'); ?></span>
      </a>
    </div>

    <?php if($featuredCourses->isEmpty()): ?>
      <div class="lasles-showcase__empty">
        <p><?php echo e(__('landing.home.showcase_courses_empty')); ?></p>
        <a href="<?php echo e(route('public.courses')); ?>" class="lasles-btn-primary"><?php echo e(__('landing.home.showcase_courses_cta')); ?></a>
      </div>
    <?php else: ?>
      <div class="lasles-showcase__grid lasles-showcase__grid--courses lasles-courses-grid">
        <?php $__currentLoopData = $featuredCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php echo $__env->make('partials.landing.lasles.course-card', ['course' => $course], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <div class="lasles-showcase__foot">
        <a href="<?php echo e(route('public.courses')); ?>" class="lasles-showcase__more-link">
          <?php echo e(__('landing.home.showcase_courses_cta')); ?>

          <span aria-hidden="true"><?php echo e($isRtl ? '←' : '→'); ?></span>
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/home-courses.blade.php ENDPATH**/ ?>