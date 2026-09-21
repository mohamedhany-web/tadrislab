
<?php
    $brandIcon = \App\Services\AdminPanelBranding::logoPublicUrl();
    $fallbackIcon = asset('img/lasles/logo-mark.png');
    $icon = $brandIcon ?: $fallbackIcon;
?>
<link rel="icon" href="<?php echo e($icon); ?>" sizes="any">
<link rel="shortcut icon" href="<?php echo e(asset('favicon.ico')); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(asset('apple-touch-icon.png')); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(asset('favicon-32x32.png')); ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(asset('favicon-16x16.png')); ?>">
<link rel="icon" type="image/png" sizes="192x192" href="<?php echo e(asset('android-chrome-192x192.png')); ?>">
<link rel="icon" type="image/png" sizes="512x512" href="<?php echo e(asset('android-chrome-512x512.png')); ?>">
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/favicon-links.blade.php ENDPATH**/ ?>