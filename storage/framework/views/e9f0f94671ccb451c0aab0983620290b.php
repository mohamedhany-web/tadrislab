<?php
  $isRtl = app()->getLocale() === 'ar';
  $coursesCss = public_path('css/landing/lasles-courses.css');
  $coursesVer = is_file($coursesCss) ? (string) filemtime($coursesCss) : (string) time();
  $pageTitle = __('public.courses_page_title').' — TADRIS LAB';
  $pageDescription = __('public.courses_subtitle');
  $bodyClass = 'lasles-courses-page';

  $filterUrl = function (array $overrides = []) use ($filters) {
      $query = array_filter(array_merge([
          'q' => $filters['q'] ?: null,
          'category' => $filters['category'] ?: null,
          'level' => $filters['level'] ?: null,
          'featured' => ! empty($filters['featured']) ? 1 : null,
      ], $overrides), fn ($v) => $v !== null && $v !== '' && $v !== false);

      return route('public.courses', $query);
  };

  $activeFilters = 0;
  if (! empty($filters['q'])) { $activeFilters++; }
  if (! empty($filters['category'])) { $activeFilters++; }
  if (! empty($filters['level'])) { $activeFilters++; }
  if (! empty($filters['featured'])) { $activeFilters++; }
?>


<?php $__env->startPush('head'); ?>
  <link rel="stylesheet" href="<?php echo e(route('assets.landing.css', ['sheet' => 'lasles-courses'])); ?>?v=<?php echo e($coursesVer); ?>">
  <link rel="canonical" href="<?php echo e(route('public.courses')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="lasles-courses-hero">
  <div class="lasles-container lasles-courses-hero__inner">
    <p class="lasles-site-breadcrumb" style="margin-bottom:.85rem">
      <a href="<?php echo e(route('home')); ?>"><?php echo e(__('site.nav_short.home')); ?></a>
      <span aria-hidden="true">/</span>
      <span><?php echo e(__('site.nav_short.courses')); ?></span>
    </p>
    <h1 class="lasles-courses-hero__title"><?php echo e(__('public.courses_hero')); ?></h1>
    <p class="lasles-courses-hero__lead"><?php echo e(__('public.courses_subtitle')); ?></p>
    <div class="lasles-courses-hero__meta">
      <span class="lasles-courses-chip"><?php echo e(number_format($courses->total())); ?> <?php echo e(__('public.courses_stats_available')); ?></span>
      <span class="lasles-courses-chip"><?php echo e(number_format($totalActive)); ?> <?php echo e(__('public.courses_catalog_total')); ?></span>
      <a class="lasles-courses-chip" href="<?php echo e(route('public.learning-paths.index')); ?>"><?php echo e(__('site.nav.learning_paths')); ?></a>
    </div>
  </div>
</section>

<section class="lasles-container lasles-courses-toolbar">
  <form action="<?php echo e(route('public.courses')); ?>" method="get" class="lasles-courses-search" role="search">
    <?php if(!empty($filters['category'])): ?>
      <input type="hidden" name="category" value="<?php echo e($filters['category']); ?>">
    <?php endif; ?>
    <?php if(!empty($filters['level'])): ?>
      <input type="hidden" name="level" value="<?php echo e($filters['level']); ?>">
    <?php endif; ?>
    <?php if(!empty($filters['featured'])): ?>
      <input type="hidden" name="featured" value="1">
    <?php endif; ?>
    <input
      type="search"
      name="q"
      value="<?php echo e($filters['q']); ?>"
      placeholder="<?php echo e(__('public.courses_search_placeholder')); ?>"
      aria-label="<?php echo e(__('public.courses_search_placeholder')); ?>"
    >
    <button type="submit" class="lasles-btn-primary"><?php echo e($isRtl ? 'بحث' : 'Search'); ?></button>
  </form>

  <div class="lasles-courses-filters" aria-label="<?php echo e(__('public.courses_filters_label')); ?>">
    <a href="<?php echo e($filterUrl(['category' => null, 'level' => null, 'featured' => null])); ?>" class="<?php echo e(empty($filters['category']) && empty($filters['level']) && empty($filters['featured']) ? 'is-on' : ''); ?>">
      <?php echo e(__('public.courses_filter_all')); ?>

    </a>
    <a href="<?php echo e($filterUrl(['featured' => empty($filters['featured']) ? 1 : null])); ?>" class="<?php echo e(!empty($filters['featured']) ? 'is-on' : ''); ?>">
      <?php echo e(__('public.courses_filter_featured')); ?>

    </a>
    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a href="<?php echo e($filterUrl(['category' => (int) $filters['category'] === (int) $category->id ? null : $category->id])); ?>"
         class="<?php echo e((int) ($filters['category'] ?? 0) === (int) $category->id ? 'is-on' : ''); ?>">
        <?php echo e($category->name); ?>

      </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lvl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a href="<?php echo e($filterUrl(['level' => ($filters['level'] ?? '') === $lvl ? null : $lvl])); ?>"
         class="<?php echo e(($filters['level'] ?? '') === $lvl ? 'is-on' : ''); ?>">
        <?php echo e($lvl); ?>

      </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if($activeFilters > 0): ?>
      <a href="<?php echo e(route('public.courses')); ?>" class="lasles-courses-clear"><?php echo e(__('public.courses_reset_search')); ?></a>
    <?php endif; ?>
  </div>
</section>

<section class="lasles-container">
  <?php if($courses->isEmpty()): ?>
    <div class="lasles-courses-empty">
      <h2><?php echo e(__('public.courses_empty_title')); ?></h2>
      <p><?php echo e(__('public.courses_empty_body')); ?></p>
      <div class="lasles-hero__actions" style="justify-content:center;margin-top:1.25rem">
        <a href="<?php echo e(route('public.courses')); ?>" class="lasles-btn-outline"><?php echo e(__('public.courses_reset_search')); ?></a>
        <a href="<?php echo e(route('public.learning-paths.index')); ?>" class="lasles-btn-primary"><?php echo e(__('site.nav.learning_paths')); ?></a>
      </div>
    </div>
  <?php else: ?>
    <div class="lasles-courses-grid">
      <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('partials.landing.lasles.course-card', ['course' => $course], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php if($courses->hasPages()): ?>
      <div class="lasles-courses-pager">
        <?php echo e($courses->onEachSide(1)->links()); ?>

      </div>
    <?php endif; ?>
  <?php endif; ?>

  <div class="lasles-courses-cta">
    <div>
      <h2><?php echo e(__('public.courses_cta_title')); ?></h2>
      <p><?php echo e(__('public.courses_cta_desc')); ?></p>
    </div>
    <div class="lasles-courses-cta__actions">
      <a href="<?php echo e(route('register')); ?>" class="lasles-btn-primary"><?php echo e(__('public.courses_cta_register')); ?></a>
      <a href="<?php echo e(route('public.contact')); ?>" class="lasles-btn-outline"><?php echo e(__('site.cta.contact')); ?></a>
    </div>
  </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lasles-public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/public/courses/index.blade.php ENDPATH**/ ?>