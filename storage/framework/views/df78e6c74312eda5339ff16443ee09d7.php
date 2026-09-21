<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $laslesCss = public_path('css/landing/lasles.css');
    $laslesVer = is_file($laslesCss) ? (string) filemtime($laslesCss) : (string) time();
    $coursesCss = public_path('css/landing/lasles-courses.css');
    $coursesVer = is_file($coursesCss) ? (string) filemtime($coursesCss) : (string) time();
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php echo $__env->make('components.seo-meta', [
        'title' => __('landing.meta.title'),
        'description' => __('landing.meta.description'),
        'keywords' => __('landing.meta.keywords'),
        'image' => \App\Services\SeoAssets::ogImageUrl(),
        'imageAlt' => __('landing.meta.og_title'),
        'url' => url('/'),
        'type' => 'website',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="alternate" hreflang="ar" href="<?php echo e(url('/?lang=ar')); ?>">
    <link rel="alternate" hreflang="en" href="<?php echo e(url('/?lang=en')); ?>">
    <link rel="alternate" hreflang="x-default" href="<?php echo e(url('/')); ?>">
    <link rel="manifest" href="<?php echo e(asset('manifest.webmanifest')); ?>">
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.seo-jsonld', ['jsonldType' => 'website'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Lato:wght@400;700;900&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="theme-color" content="#1E4E8C">
    <link rel="stylesheet" href="<?php echo e(route('assets.landing.css', ['sheet' => 'lasles'])); ?>?v=<?php echo e($laslesVer); ?>">
    <link rel="stylesheet" href="<?php echo e(route('assets.landing.css', ['sheet' => 'lasles-courses'])); ?>?v=<?php echo e($coursesVer); ?>">
</head>
<body class="lasles-home">
<?php echo $__env->make('partials.welcome-lasles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if(!empty($popupAd)): ?>
    <?php echo $__env->make('partials.popup-ad', ['ad' => $popupAd], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
</body>
</html>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/welcome.blade.php ENDPATH**/ ?>