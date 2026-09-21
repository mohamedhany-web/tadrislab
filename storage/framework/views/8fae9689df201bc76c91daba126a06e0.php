<?php
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
    $brand = 'TADRIS LAB';
    $brandAr = 'تدريس لاب';
    $img = fn (string $file) => asset('img/lasles/'.$file);
    $langSwitch = fn (string $lang) => request()->fullUrlWithQuery(array_merge(request()->query(), ['lang' => $lang]));
?>

<?php echo $__env->make('partials.landing.lasles.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<main>
  <?php echo $__env->make('partials.landing.lasles.hero', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.lasles.stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.lasles.path-section', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.lasles.home-paths', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.lasles.home-courses', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.lasles.pillars', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.lasles.features', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.lasles.pricing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.lasles.institutions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.lasles.testimonials', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('partials.landing.lasles.subscribe', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</main>
<?php echo $__env->make('partials.landing.lasles.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.landing.lasles.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/welcome-lasles.blade.php ENDPATH**/ ?>