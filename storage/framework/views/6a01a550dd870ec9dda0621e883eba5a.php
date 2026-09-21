<?php $__env->startSection('content'); ?>
<?php
    $isRtl = app()->getLocale() === 'ar';
    $img = fn (string $file) => asset('img/lasles/'.$file);
    $content = is_array($content ?? null) ? $content : [];
?>

<?php echo $__env->make('partials.landing.lasles.about-hero', ['content' => $content, 'img' => $img, 'isRtl' => $isRtl], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.landing.lasles.about-story', ['content' => $content, 'img' => $img], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.landing.lasles.about-why', ['content' => $content], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.landing.lasles.about-loop', ['content' => $content], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.landing.lasles.about-cta', ['content' => $content], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lasles-public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/public/about.blade.php ENDPATH**/ ?>