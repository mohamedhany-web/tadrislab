<?php
  $isRtl = app()->getLocale() === 'ar';
  $thumb = $course->thumbnail_url
    ?: asset('img/lasles/features-illustration.png');
  $list = (float) ($course->price ?? 0);
  $pay = (float) ($course->price_after_discount ?? $list);
  if ($pay <= 0) {
      $pay = $list;
  }
  $isFree = ($course->is_free ?? false) || $pay <= 0;
  $hasDiscount = ! $isFree && $list > $pay && $pay > 0;
  $categoryLabel = $course->courseCategory->name
    ?? $course->academicSubject->name
    ?? $course->category
    ?? null;
  $excerpt = \Illuminate\Support\Str::limit(strip_tags((string) ($course->description ?? '')), 110);
  $instructor = $course->instructor->name ?? null;
  $lessonsCount = (int) ($course->lessons_count ?? 0);
?>
<a href="<?php echo e(route('public.course.show', $course->id)); ?>" class="lasles-course-card">
  <div class="lasles-course-card__media">
    <img src="<?php echo e($thumb); ?>" alt="" loading="lazy" decoding="async">
    <?php if($course->is_featured): ?>
      <span class="lasles-course-card__badge lasles-course-card__badge--gold"><?php echo e(__('public.courses_badge_featured')); ?></span>
    <?php elseif($isFree): ?>
      <span class="lasles-course-card__badge"><?php echo e(__('public.courses_badge_free')); ?></span>
    <?php endif; ?>
  </div>
  <div class="lasles-course-card__body">
    <?php if($categoryLabel): ?>
      <p class="lasles-course-card__cat"><?php echo e($categoryLabel); ?></p>
    <?php endif; ?>
    <h3 class="lasles-course-card__title"><?php echo e($course->title); ?></h3>
    <?php if($excerpt !== ''): ?>
      <p class="lasles-course-card__excerpt"><?php echo e($excerpt); ?></p>
    <?php endif; ?>
    <div class="lasles-course-card__meta">
      <?php if($course->level): ?>
        <span><?php echo e($course->level); ?></span>
      <?php endif; ?>
      <?php if($lessonsCount > 0): ?>
        <span><?php echo e(trans_choice('public.courses_lessons_count', $lessonsCount, ['count' => $lessonsCount])); ?></span>
      <?php endif; ?>
      <?php if($instructor): ?>
        <span><?php echo e($instructor); ?></span>
      <?php endif; ?>
    </div>
    <div class="lasles-course-card__foot">
      <p class="lasles-course-card__price">
        <?php if($isFree): ?>
          <?php echo e(__('public.courses_badge_free')); ?>

        <?php else: ?>
          <?php if($hasDiscount): ?><s><?php echo e(number_format($list, 0)); ?></s><?php endif; ?>
          <?php echo e(number_format($pay, 0)); ?> <?php echo e($isRtl ? 'ر.ق' : 'QAR'); ?>

        <?php endif; ?>
      </p>
      <span class="lasles-course-card__cta"><?php echo e(__('public.courses_card_cta')); ?> →</span>
    </div>
  </div>
</a>
<?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/partials/landing/lasles/course-card.blade.php ENDPATH**/ ?>