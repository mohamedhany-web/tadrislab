<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $laslesCss = public_path('css/landing/lasles.css');
    $laslesVer = is_file($laslesCss) ? (string) filemtime($laslesCss) : (string) time();
    $brand = 'TADRIS LAB';
    $brandAr = 'تدريس لاب';
    $img = function (string $file) {
        $path = public_path('img/lasles/'.$file);
        $url = asset('img/lasles/'.$file);
        if (is_file($path)) {
            $url .= '?v='.filemtime($path);
        }

        return $url;
    };
    $langSwitch = fn (string $lang) => request()->fullUrlWithQuery(array_merge(request()->query(), ['lang' => $lang]));
    $laslesNavActive = $laslesNavActive ?? '';
    $pageTitle = $pageTitle ?? ($isRtl ? $brandAr : $brand);
    $pageDescription = $pageDescription ?? __('landing.meta.description');
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php echo $__env->make('components.seo-meta', [
        'title' => $pageTitle,
        'description' => $pageDescription,
        'keywords' => __('landing.meta.keywords'),
        'image' => \App\Services\SeoAssets::ogImageUrl(),
        'imageAlt' => $pageTitle,
        'url' => url()->current(),
        'type' => 'website',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.favicon-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Lato:wght@400;700;900&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="theme-color" content="#1E4E8C">
    <link rel="stylesheet" href="<?php echo e(route('assets.landing.css', ['sheet' => 'lasles'])); ?>?v=<?php echo e($laslesVer); ?>">
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="lasles-home <?php echo e($bodyClass ?? ''); ?>">
<?php echo $__env->make('partials.landing.lasles.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<main>
    <?php echo $__env->yieldContent('content'); ?>
</main>
<?php echo $__env->make('partials.landing.lasles.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.landing.lasles.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/layouts/lasles-public.blade.php ENDPATH**/ ?>