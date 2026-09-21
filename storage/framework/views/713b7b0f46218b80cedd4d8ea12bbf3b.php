<?php
    $featuredPaths = $featuredPaths ?? collect();
    $isRtl = $isRtl ?? (app()->getLocale() === 'ar');
?>
<section class="lasles-showcase lasles-showcase--paths" id="home-paths" aria-labelledby="home-paths-title">
  <div class="lasles-container">
    <div class="lasles-showcase__head">
      <div class="lasles-showcase__intro">
        <p class="lasles-path-kicker"><?php echo e(__('landing.home.showcase_paths_kicker')); ?></p>
        <h2 id="home-paths-title" class="lasles-section-title"><?php echo e(__('landing.home.showcase_paths_title')); ?></h2>
        <p class="lasles-section-lead"><?php echo e(__('landing.home.showcase_paths_lead')); ?></p>
      </div>
      <a href="<?php echo e(route('public.learning-paths.index')); ?>" class="lasles-showcase__more lasles-btn-outline">
        <?php echo e(__('landing.home.showcase_paths_cta')); ?>

        <span aria-hidden="true"><?php echo e($isRtl ? '←' : '→'); ?></span>
      </a>
    </div>

    <?php if($featuredPaths->isEmpty()): ?>
      <div class="lasles-showcase__empty">
        <p><?php echo e(__('landing.home.showcase_paths_empty')); ?></p>
        <a href="<?php echo e(route('public.learning-paths.index')); ?>" class="lasles-btn-primary"><?php echo e(__('landing.home.showcase_paths_cta')); ?></a>
      </div>
    <?php else: ?>
      <div class="lasles-showcase__grid lasles-showcase__grid--paths">
        <?php $__currentLoopData = $featuredPaths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $path): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $skill = $path->skillFocus() ?: null;
            $showSkill = filled($skill) && $skill !== $path->title();
            $thumb = filled($path->thumbnail)
              ? (str_starts_with((string) $path->thumbnail, 'http') ? $path->thumbnail : asset('storage/'.$path->thumbnail))
              : null;
          ?>
          <a href="<?php echo e(route('public.learning-paths.show', $path->slug)); ?>" class="lasles-path-card-pro <?php echo e($i === 0 ? 'is-featured' : ''); ?>">
            <div class="lasles-path-card-pro__glow" aria-hidden="true"></div>
            <?php
              $mediaStyle = $thumb ? 'style="--path-cover: url(\''.e($thumb).'\')"' : '';
            ?>
            <div class="lasles-path-card-pro__media <?php echo e($thumb ? '' : 'lasles-path-card-pro__media--accent'); ?>" <?php echo $mediaStyle; ?>>
              <?php if($thumb): ?>
                <img src="<?php echo e($thumb); ?>" alt="" loading="lazy" decoding="async">
                <span class="lasles-path-card-pro__shade" aria-hidden="true"></span>
                <span class="lasles-path-card-pro__index lasles-path-card-pro__index--on-media"><?php echo e(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
              <?php else: ?>
                <span class="lasles-path-card-pro__index"><?php echo e(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
              <?php endif; ?>
            </div>
            <div class="lasles-path-card-pro__body">
              <div class="lasles-path-card-pro__top">
                <?php if($showSkill): ?>
                  <span class="lasles-path-card-pro__skill"><?php echo e($skill); ?></span>
                <?php endif; ?>
                <span class="lasles-path-card-pro__meta">
                  <?php echo e(__('landing.learning_paths.units_count', ['count' => $path->units_count])); ?>

                  <?php if($path->estimated_minutes): ?>
                    · <?php echo e($path->estimated_minutes); ?> <?php echo e(__('landing.learning_paths.minutes')); ?>

                  <?php endif; ?>
                </span>
              </div>
              <h3 class="lasles-path-card-pro__title"><?php echo e($path->title()); ?></h3>
              <?php if($path->summary()): ?>
                <p class="lasles-path-card-pro__summary"><?php echo e(\Illuminate\Support\Str::limit($path->summary(), 120)); ?></p>
              <?php endif; ?>
              <span class="lasles-path-card-pro__cta">
                <?php echo e(__('landing.learning_paths.view_path')); ?>

                <span aria-hidden="true"><?php echo e($isRtl ? '←' : '→'); ?></span>
              </span>
            </div>
          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <div class="lasles-showcase__foot">
        <a href="<?php echo e(route('public.learning-paths.index')); ?>" class="lasles-showcase__more-link">
          <?php echo e(__('landing.home.showcase_paths_cta')); ?>

          <span aria-hidden="true"><?php echo e($isRtl ? '←' : '→'); ?></span>
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/home-paths.blade.php ENDPATH**/ ?>