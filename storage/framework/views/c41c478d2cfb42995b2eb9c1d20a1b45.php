<?php
    $locale = $locale ?? app()->getLocale();
    $brandLogoUrl = $brandLogoUrl ?? \App\Services\AdminPanelBranding::logoPublicUrl();
    $brandLogoFallback = $brandLogoFallback ?? \App\Services\AdminPanelBranding::inlineFallbackDataUri();
    $pageTitle = $pageTitle ?? __('student_timeline.timeline');
    $crumbs = $crumbs ?? [];
    $toolbarView = $toolbarView ?? null;
    $toolbarData = $toolbarData ?? [];
?>
<header class="st-top">
    <div class="st-top__row st-top__row--primary">
        <a href="<?php echo e(route('dashboard')); ?>" class="st-top__brand" title="<?php echo e(config('app.name')); ?>">
            <span class="st-top__brand-mark">
                <img src="<?php echo e($brandLogoUrl); ?>" alt="<?php echo e(config('app.name')); ?>" width="36" height="36" loading="eager" decoding="async" onerror="this.onerror=null;this.src='<?php echo e($brandLogoFallback); ?>';">
            </span>
        </a>

        <div class="st-top__heading">
            <?php if(! empty($crumbs)): ?>
                <nav class="st-crumb" aria-label="breadcrumb">
                    <?php $__currentLoopData = $crumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($i > 0): ?><span class="st-crumb__sep" aria-hidden="true">/</span><?php endif; ?>
                        <?php if(! empty($crumb['url']) && ! $loop->last): ?>
                            <a href="<?php echo e($crumb['url']); ?>"><?php echo e($crumb['label']); ?></a>
                        <?php else: ?>
                            <span class="<?php echo e($loop->last ? 'is-current' : ''); ?>"><?php echo e($crumb['label']); ?></span>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </nav>
            <?php endif; ?>
            <h1 class="st-top__title"><?php echo e($pageTitle); ?></h1>
        </div>

        <div class="st-top__actions">
            <div class="st-lang" role="group" aria-label="Language">
                <a href="<?php echo e(request()->fullUrlWithQuery(['lang' => 'ar'])); ?>" class="<?php echo e($locale === 'ar' ? 'is-active' : ''); ?>"><?php echo e(__('student_timeline.lang_ar')); ?></a>
                <a href="<?php echo e(request()->fullUrlWithQuery(['lang' => 'en'])); ?>" class="<?php echo e($locale === 'en' ? 'is-active' : ''); ?>"><?php echo e(__('student_timeline.lang_en')); ?></a>
            </div>

            <?php echo $__env->make('partials.student-timeline-bell', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <button type="button" class="st-top__menu" id="stTopMenu" aria-expanded="false" aria-controls="stRail" aria-label="<?php echo e(__('student_timeline.toggle_sidebar')); ?>">
                <i class="fas fa-bars" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <?php if($toolbarView): ?>
        <?php echo $__env->make($toolbarView, $toolbarData, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
</header>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/student-timeline-top.blade.php ENDPATH**/ ?>