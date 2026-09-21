<?php $__env->startSection('title', 'مساراتي المسندة'); ?>
<?php $__env->startSection('page_title', 'مساراتي المسندة'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isRtl = app()->getLocale() === 'ar';
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
    $tones = ['blue', 'pink', 'orange', 'purple'];
    $pathCount = $paths->count();
    $unitsTotal = (int) $paths->sum('units_count');
    $lessonsTotal = (int) $paths->sum('lessons_count');
    $enrollTotal = (int) $paths->sum('enrollments_count');
?>

<section class="st-join-hero" aria-label="مساراتي المسندة">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">TADRIS LAB</p>
        <h2 class="st-join-hero__title"><?php echo e($isRtl ? 'مساراتي المسندة' : 'Assigned learning paths'); ?></h2>
        <p class="st-join-hero__meta">
            <?php echo e($isRtl
                ? 'المسارات المسموح لك بتقديمها للمعلمين — مسار → وحدات → دروس → تطبيقات.'
                : 'Paths you can coach teachers through — path → units → lessons → practices.'); ?>

        </p>
    </div>
    <div class="st-join-hero__actions">
        <?php if(Route::has('instructor.calendar')): ?>
            <a href="<?php echo e(route('instructor.calendar')); ?>" class="st-pill st-pill--outline"><?php echo e(__('instructor.my_calendar')); ?></a>
        <?php endif; ?>
        <?php if(Route::has('instructor.consultations.index') && instructor_ui('show_consultations', true)): ?>
            <a href="<?php echo e(route('instructor.consultations.index')); ?>" class="st-pill st-pill--solid">استشاراتي</a>
        <?php endif; ?>
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="<?php echo e($isRtl ? 'ملخص المسارات' : 'Paths summary'); ?>">
    <article class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask1); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e($isRtl ? 'المسارات' : 'Paths'); ?></p>
        <p class="st-stat-card__value"><?php echo e(number_format($pathCount)); ?></p>
        <p class="st-stat-card__hint"><?php echo e($isRtl ? 'مسندة إليك' : 'Assigned to you'); ?></p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask2); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e($isRtl ? 'الوحدات' : 'Units'); ?></p>
        <p class="st-stat-card__value"><?php echo e(number_format($unitsTotal)); ?></p>
        <p class="st-stat-card__hint"><?php echo e($isRtl ? 'عبر كل المسارات' : 'Across all paths'); ?></p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask1); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e($isRtl ? 'الدروس' : 'Lessons'); ?></p>
        <p class="st-stat-card__value"><?php echo e(number_format($lessonsTotal)); ?></p>
        <p class="st-stat-card__hint"><?php echo e($isRtl ? 'محتوى تدريبي' : 'Training content'); ?></p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask2); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e($isRtl ? 'المعلمون' : 'Teachers'); ?></p>
        <p class="st-stat-card__value"><?php echo e(number_format($enrollTotal)); ?></p>
        <p class="st-stat-card__hint"><?php echo e($isRtl ? 'تسجيلات نشطة' : 'Active enrollments'); ?></p>
    </article>
</section>

<section class="st-msg-intro">
    <div>
        <h2><?php echo e($isRtl ? 'كتالوج المسارات' : 'Path catalog'); ?></h2>
        <p><?php echo e($isRtl ? 'افتح مسارًا لمراجعة الوحدات والدروس والمعلمين المسجّلين.' : 'Open a path to review units, lessons, and enrolled teachers.'); ?></p>
    </div>
</section>

<?php if($paths->isEmpty()): ?>
    <section class="st-panel st-lp-empty">
        <i class="fas fa-route" aria-hidden="true"></i>
        <h3><?php echo e($isRtl ? 'لا مسارات ممنوحة بعد' : 'No paths assigned yet'); ?></h3>
        <p><?php echo e($isRtl
            ? 'اطلب من الإدارة تفعيل خدمة «المسارات التعليمية» وربط المسارات بحسابك.'
            : 'Ask admin to enable learning paths and assign them to your account.'); ?></p>
    </section>
<?php else: ?>
    <section class="st-lp-grid" aria-label="<?php echo e($isRtl ? 'المسارات' : 'Paths'); ?>">
        <?php $__currentLoopData = $paths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $tone = $tones[$i % count($tones)]; ?>
            <a href="<?php echo e(route('instructor.learning-paths.show', $path)); ?>" class="st-lp-card st-lp-card--<?php echo e($tone); ?>">
                <img class="st-lp-card__blob" src="<?php echo e($i % 2 ? $subjMask2 : $subjMask1); ?>" alt="" width="120" height="120">
                <span class="st-lp-card__index"><?php echo e(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                <h3 class="st-lp-card__title"><?php echo e($path->title()); ?></h3>
                <p class="st-lp-card__focus"><?php echo e($path->skillFocus() ?: '—'); ?></p>
                <ul class="st-lp-card__meta">
                    <li><i class="fas fa-layer-group" aria-hidden="true"></i> <?php echo e($path->units_count); ?> <?php echo e($isRtl ? 'وحدة' : 'units'); ?></li>
                    <li><i class="fas fa-book-open" aria-hidden="true"></i> <?php echo e($path->lessons_count); ?> <?php echo e($isRtl ? 'درس' : 'lessons'); ?></li>
                    <li><i class="fas fa-screwdriver-wrench" aria-hidden="true"></i> <?php echo e($path->practices_count); ?> <?php echo e($isRtl ? 'تطبيق' : 'practices'); ?></li>
                    <li><i class="fas fa-user-graduate" aria-hidden="true"></i> <?php echo e($path->enrollments_count); ?> <?php echo e($isRtl ? 'معلّم' : 'teachers'); ?></li>
                </ul>
                <span class="st-lp-card__cta">
                    <?php echo e($isRtl ? 'عرض المسار' : 'View path'); ?>

                    <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>" aria-hidden="true"></i>
                </span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.instructor-timeline', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/instructor/learning-paths/index.blade.php ENDPATH**/ ?>