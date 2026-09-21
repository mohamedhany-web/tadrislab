<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $brand = config('app.name', 'TADRIS LAB');
    $footer = \App\Services\PublicFooterSettings::payload();
    $waUrl = $footer['whatsapp_url'] ?? '#';
    $thumbUrl = $course->thumbnail_url;
    $introVideoUrl = trim((string) ($course->video_url ?? ''));
    $introEmbedUrl = \App\Helpers\VideoHelper::getEmbedUrl($introVideoUrl);
    $introDirectVideo = \App\Helpers\VideoHelper::getDirectVideoUrl($introVideoUrl);
    $categoryDisplay = $course->courseCategory?->name ?? __('public.course_category_not_set');
    $isMonthly = $course->isMonthlyBilling();
    $checkoutPrice = $course->effectiveCheckoutPrice();
    $isPaid = $checkoutPrice > 0 && ! ($course->is_free ?? false);
    $hasPromo = $isPaid && $course->hasPromotionalPrice();
    $listPrice = $hasPromo ? $course->listPriceAmount() : 0;
    $savedAmount = $hasPromo ? max(0, $listPrice - $checkoutPrice) : 0;
    $discountPct = ($hasPromo && $listPrice > 0)
        ? (int) round((1 - ($checkoutPrice / $listPrice)) * 100)
        : 0;
    $instructorApproved = $course->instructor
        && \App\Models\InstructorProfile::where('user_id', $course->instructor->id)->where('status', 'approved')->exists();
    $subjectName = $course->academicSubject->name ?? __('public.course_category_not_set');
    $learnPoints = $course->what_you_learn
        ? array_values(array_filter(array_map('trim', explode("\n", $course->what_you_learn))))
        : [];
    $isOneToOne = $course->isOneToOne();
    $from = $from ?? ($isOneToOne ? 'one_to_one' : 'groups');
    $catalogUrl = route('public.courses');
    $deliveryLabel = $isOneToOne
        ? ($isRtl ? 'فردي 1:1' : '1:1 private')
        : ($isRtl ? 'جماعي' : 'Group');
    $heroCover = $thumbUrl ?: asset('img/lasles/features-illustration.png');
    $pageTitle = ($course->title ?? __('public.course_detail_title')).' — '.($isRtl ? 'تدريس لاب' : $brand);
    $pageDescription = \Illuminate\Support\Str::limit(strip_tags($course->description ?? ''), 160);
    $bodyClass = 'lasles-course-detail-page';
    $laslesNavActive = 'courses';

    $primaryCtaHref = null;
    $primaryCtaLabel = __('landing.learning_paths.cta_start');
    $primaryIsForm = false;
    if (auth()->check()) {
        if ($isEnrolled ?? false) {
            $primaryCtaHref = route('my-courses.show', $course);
            $primaryCtaLabel = __('public.start_learning_now');
        } elseif ($isPaid) {
            $primaryCtaHref = route('public.course.checkout', $course->id);
            $primaryCtaLabel = __('public.buy_now');
        } else {
            $primaryIsForm = true;
            $primaryCtaLabel = __('public.register_free');
        }
    } else {
        $redirect = $isPaid
            ? route('public.course.checkout', $course->id)
            : route('public.course.show', $course->id);
        $primaryCtaHref = route('register', ['redirect' => $redirect]);
        $primaryCtaLabel = $isPaid ? __('public.buy_now') : __('public.register_free');
    }
?>

<?php $__env->startSection('content'); ?>
<?php if(session('success') || session('info') || session('error')): ?>
  <div class="lasles-container lasles-course-flash-wrap">
    <?php if(session('success')): ?>
      <div class="lasles-course-flash lasles-course-flash--ok" data-flash>
        <p><?php echo e(session('success')); ?></p>
        <button type="button" data-flash-close aria-label="<?php echo e($isRtl ? 'إغلاق' : 'Close'); ?>">×</button>
      </div>
    <?php endif; ?>
    <?php if(session('info')): ?>
      <div class="lasles-course-flash lasles-course-flash--info" data-flash>
        <p><?php echo e(session('info')); ?></p>
        <button type="button" data-flash-close aria-label="<?php echo e($isRtl ? 'إغلاق' : 'Close'); ?>">×</button>
      </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
      <div class="lasles-course-flash lasles-course-flash--err" data-flash>
        <p><?php echo e(session('error')); ?></p>
        <button type="button" data-flash-close aria-label="<?php echo e($isRtl ? 'إغلاق' : 'Close'); ?>">×</button>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>

<section class="lasles-course-detail-hero" aria-labelledby="course-detail-title">
  <div class="lasles-course-detail-hero__bg" style="--course-cover: url('<?php echo e($heroCover); ?>')" aria-hidden="true"></div>
  <div class="lasles-course-detail-hero__veil" aria-hidden="true"></div>
  <div class="lasles-container lasles-course-detail-hero__inner">
    <p class="lasles-course-detail-hero__crumb">
      <a href="<?php echo e(route('home')); ?>"><?php echo e(__('site.nav_short.home')); ?></a>
      <span aria-hidden="true">/</span>
      <a href="<?php echo e($catalogUrl); ?>"><?php echo e(__('landing.course_detail.crumb_courses')); ?></a>
      <span aria-hidden="true">/</span>
      <span><?php echo e(\Illuminate\Support\Str::limit($course->title ?? '', 48)); ?></span>
    </p>

    <div class="lasles-course-detail-hero__copy">
      <p class="lasles-course-detail-hero__kicker"><?php echo e(__('landing.course_detail.kicker')); ?></p>
      <h1 id="course-detail-title" class="lasles-course-detail-hero__title"><?php echo e($course->title ?? __('public.course_title_fallback')); ?></h1>
      <?php if($course->description): ?>
        <p class="lasles-course-detail-hero__lead"><?php echo e(\Illuminate\Support\Str::limit(strip_tags($course->description), 180)); ?></p>
      <?php endif; ?>

      <div class="lasles-course-detail-hero__chips">
        <span class="lasles-path-chip <?php echo e($isOneToOne ? 'lasles-path-chip--gold' : ''); ?>"><?php echo e($deliveryLabel); ?></span>
        <?php if($course->is_featured ?? false): ?>
          <span class="lasles-path-chip lasles-path-chip--gold"><?php echo e(__('public.featured_course_badge')); ?></span>
        <?php endif; ?>
        <?php if(($course->duration_hours ?? 0) > 0): ?>
          <span class="lasles-path-chip"><?php echo e($course->duration_hours); ?> <?php echo e(__('public.hours')); ?></span>
        <?php endif; ?>
        <?php if(($course->lessons_count ?? 0) > 0): ?>
          <span class="lasles-path-chip"><?php echo e($course->lessons_count); ?> <?php echo e(__('landing.learning_paths.stat_lessons')); ?></span>
        <?php endif; ?>
        <?php if($discountPct > 0): ?>
          <span class="lasles-path-chip lasles-path-chip--blue"><?php echo e($isRtl ? "خصم {$discountPct}%" : "{$discountPct}% off"); ?></span>
        <?php elseif(! $isPaid): ?>
          <span class="lasles-path-chip lasles-path-chip--blue"><?php echo e(__('public.free_price')); ?></span>
        <?php endif; ?>
      </div>

      <p class="lasles-course-detail-hero__promise"><?php echo e(__('landing.course_detail.promise')); ?></p>

      <div class="lasles-course-detail-hero__actions">
        <?php if($primaryIsForm): ?>
          <form action="<?php echo e(route('public.course.enroll.free', $course->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="lasles-btn-primary"><?php echo e($primaryCtaLabel); ?></button>
          </form>
        <?php else: ?>
          <a href="<?php echo e($primaryCtaHref); ?>" class="lasles-btn-primary"><?php echo e($primaryCtaLabel); ?></a>
        <?php endif; ?>
        <a href="<?php echo e($catalogUrl); ?>" class="lasles-btn-outline lasles-btn-outline--on-dark"><?php echo e(__('landing.course_detail.cta_explore')); ?></a>
      </div>
    </div>
  </div>
</section>

<section class="lasles-course-detail-body">
  <div class="lasles-container lasles-course-detail-body__grid">
    <div class="lasles-course-detail-body__main">
      <?php if($introEmbedUrl || $introDirectVideo || $thumbUrl): ?>
        <div class="lasles-course-detail-media">
          <p class="lasles-path-kicker"><?php echo e(__('landing.course_detail.media_title')); ?></p>
          <div class="lasles-course-detail-media__frame">
            <?php if($introEmbedUrl): ?>
              <iframe src="<?php echo e($introEmbedUrl); ?>" title="<?php echo e(__('public.course_intro_video')); ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen; web-share" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
            <?php elseif($introDirectVideo): ?>
              <video src="<?php echo e($introDirectVideo); ?>" controls playsinline preload="metadata" poster="<?php echo e($thumbUrl); ?>"><?php echo e(__('public.course_intro_video_unsupported')); ?></video>
            <?php else: ?>
              <img src="<?php echo e($thumbUrl); ?>" alt="<?php echo e($course->title); ?>" width="960" height="540" loading="lazy" decoding="async">
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="lasles-course-detail-about">
        <p class="lasles-path-kicker"><?php echo e(__('landing.course_detail.about_kicker')); ?></p>
        <h2 class="lasles-course-detail-about__title"><?php echo e(__('landing.course_detail.about_title')); ?></h2>
        <p class="lasles-course-detail-about__text"><?php echo e($course->description ?? __('public.course_desc_fallback')); ?></p>

        <div class="lasles-course-detail-delivery <?php echo e($isOneToOne ? 'is-solo' : ''); ?>">
          <strong><?php echo e($isOneToOne ? __('landing.groups_page.solo_label') : __('landing.groups_page.group_label')); ?></strong>
          <p>
            <?php echo e($isOneToOne ? __('landing.course_detail.solo_blurb') : __('landing.course_detail.group_blurb')); ?>

            <?php if($course->instructor): ?>
              <?php echo e(__('landing.course_detail.tutor_label')); ?>: <b><?php echo e($course->instructor->name); ?></b>.
            <?php endif; ?>
          </p>
        </div>
      </div>

      <?php if($course->objectives): ?>
        <div class="lasles-course-detail-block">
          <h2><?php echo e(__('landing.course_detail.objectives_title')); ?></h2>
          <div class="lasles-course-detail-block__box"><?php echo e($course->objectives); ?></div>
        </div>
      <?php endif; ?>

      <?php if(count($learnPoints)): ?>
        <div class="lasles-course-detail-block">
          <h2><?php echo e(__('landing.course_detail.learn_title')); ?></h2>
          <ul class="lasles-course-detail-learn">
            <?php $__currentLoopData = $learnPoints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><span aria-hidden="true"></span><?php echo e($point); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>

      <?php if($course->requirements): ?>
        <div class="lasles-course-detail-block">
          <h2><?php echo e(__('landing.course_detail.requirements_title')); ?></h2>
          <div class="lasles-course-detail-block__box"><?php echo e($course->requirements); ?></div>
        </div>
      <?php endif; ?>

      <div class="lasles-course-detail-block">
        <h2><?php echo e(__('landing.course_detail.details_title')); ?></h2>
        <dl class="lasles-course-detail-facts">
          <div><dt><?php echo e(__('landing.course_detail.delivery')); ?></dt><dd><?php echo e($deliveryLabel); ?></dd></div>
          <div><dt><?php echo e(__('public.course_category_label')); ?></dt><dd><?php echo e($categoryDisplay); ?></dd></div>
          <div><dt><?php echo e(__('public.subject_label')); ?></dt><dd><?php echo e($subjectName); ?></dd></div>
          <div><dt><?php echo e(__('public.duration')); ?></dt><dd><?php echo e($course->duration_hours ?? 0); ?> <?php echo e(__('public.hours')); ?></dd></div>
          <div><dt><?php echo e(__('public.lectures_count_label')); ?></dt><dd><?php echo e($course->lessons_count ?? 0); ?></dd></div>
          <?php if($course->instructor): ?>
            <div>
              <dt><?php echo e(__('public.instructor_label')); ?></dt>
              <dd>
                <?php if($instructorApproved): ?>
                  <a href="<?php echo e(route('public.instructors.show', $course->instructor)); ?>"><?php echo e($course->instructor->name); ?></a>
                <?php else: ?>
                  <?php echo e($course->instructor->name); ?>

                <?php endif; ?>
              </dd>
            </div>
          <?php endif; ?>
          <div>
            <dt><?php echo e(__('landing.course_detail.billing')); ?></dt>
            <dd>
              <?php if(! $isPaid): ?>
                <?php echo e(__('public.free_price')); ?>

              <?php elseif($isMonthly): ?>
                <?php echo e(__('public.checkout_monthly_price_label')); ?>

              <?php else: ?>
                <?php echo e(__('public.checkout_benefit_lifetime')); ?>

              <?php endif; ?>
            </dd>
          </div>
        </dl>
      </div>
    </div>

    <aside class="lasles-course-detail-side">
      <div class="lasles-course-detail-side__card">
        <p class="lasles-course-detail-side__kicker"><?php echo e(__('landing.course_detail.side_kicker')); ?></p>
        <h2><?php echo e(__('landing.course_detail.side_title')); ?></h2>

        <?php if($course->instructor): ?>
          <p class="lasles-course-detail-side__tutor">
            <?php if($instructorApproved): ?>
              <a href="<?php echo e(route('public.instructors.show', $course->instructor)); ?>"><?php echo e($course->instructor->name); ?></a>
            <?php else: ?>
              <?php echo e($course->instructor->name); ?>

            <?php endif; ?>
          </p>
        <?php endif; ?>

        <div class="lasles-course-detail-side__price">
          <?php if($isPaid): ?>
            <p class="lasles-course-detail-side__amount">
              <?php echo e(number_format($checkoutPrice, 0)); ?>

              <small><?php echo e(__('public.currency_egp')); ?><?php if($isMonthly): ?> / <?php echo e(__('public.per_month')); ?><?php endif; ?></small>
            </p>
            <?php if($hasPromo): ?>
              <p class="lasles-course-detail-side__old"><?php echo e(number_format($listPrice, 0)); ?> <?php echo e(__('public.currency_egp')); ?></p>
              <?php if($savedAmount > 0): ?>
                <span class="lasles-course-detail-side__save"><?php echo e(__('landing.course_detail.save_amount', ['amount' => number_format($savedAmount, 0).' '.__('public.currency_egp')])); ?></span>
              <?php endif; ?>
            <?php endif; ?>
          <?php else: ?>
            <p class="lasles-course-detail-side__amount is-free"><?php echo e(__('public.free_price')); ?></p>
          <?php endif; ?>
          <p class="lasles-course-detail-side__access"><?php echo e(__('landing.course_detail.access_note')); ?></p>
        </div>

        <dl class="lasles-course-detail-side__specs">
          <div><dt><?php echo e(__('landing.course_detail.delivery')); ?></dt><dd><?php echo e($deliveryLabel); ?></dd></div>
          <div><dt><?php echo e(__('public.duration')); ?></dt><dd><?php echo e($course->duration_hours ?? 0); ?> <?php echo e(__('public.hours')); ?></dd></div>
          <div><dt><?php echo e(__('public.lectures_count_label')); ?></dt><dd><?php echo e($course->lessons_count ?? 0); ?></dd></div>
          <div><dt><?php echo e(__('public.course_category_label')); ?></dt><dd><?php echo e($categoryDisplay); ?></dd></div>
        </dl>

        <div class="lasles-course-detail-side__actions">
          <?php if($primaryIsForm): ?>
            <form action="<?php echo e(route('public.course.enroll.free', $course->id)); ?>" method="POST">
              <?php echo csrf_field(); ?>
              <button type="submit" class="lasles-btn-primary lasles-course-detail-side__cta"><?php echo e($primaryCtaLabel); ?></button>
            </form>
          <?php else: ?>
            <a href="<?php echo e($primaryCtaHref); ?>" class="lasles-btn-primary lasles-course-detail-side__cta"><?php echo e($primaryCtaLabel); ?></a>
          <?php endif; ?>
          <a href="<?php echo e($waUrl); ?>" class="lasles-course-detail-side__wa" target="_blank" rel="noopener"><?php echo e(__('landing.course_detail.whatsapp')); ?></a>
          <a href="<?php echo e($catalogUrl); ?>" class="lasles-course-detail-side__link"><?php echo e(__('landing.course_detail.cta_explore')); ?></a>
        </div>

        <div class="lasles-course-detail-trust">
          <div><strong><?php echo e(__('landing.course_detail.trust_secure')); ?></strong></div>
          <div><strong><?php echo e(__('landing.course_detail.trust_fast')); ?></strong></div>
          <div><strong><?php echo e(__('landing.course_detail.trust_cert')); ?></strong></div>
        </div>
      </div>
    </aside>
  </div>
</section>

<?php if(isset($relatedCourses) && $relatedCourses->isNotEmpty()): ?>
<section class="lasles-course-detail-related" aria-labelledby="related-courses-title">
  <div class="lasles-container">
    <div class="lasles-showcase__head">
      <div class="lasles-showcase__intro">
        <p class="lasles-path-kicker"><?php echo e(__('landing.course_detail.related_kicker')); ?></p>
        <h2 id="related-courses-title" class="lasles-section-title"><?php echo e(__('landing.course_detail.related_title')); ?></h2>
      </div>
      <a href="<?php echo e($catalogUrl); ?>" class="lasles-showcase__more lasles-btn-outline"><?php echo e(__('public.all_courses')); ?></a>
    </div>
    <div class="lasles-showcase__grid lasles-showcase__grid--paths">
      <?php $__currentLoopData = $relatedCourses->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $rThumb = $related->thumbnail_url;
        ?>
        <a href="<?php echo e(route('public.course.show', $related->id)); ?>" class="lasles-path-card-pro">
          <div class="lasles-path-card-pro__media <?php echo e($rThumb ? '' : 'lasles-path-card-pro__media--accent'); ?>">
            <?php if($rThumb): ?>
              <img src="<?php echo e($rThumb); ?>" alt="" loading="lazy" decoding="async">
              <span class="lasles-path-card-pro__shade" aria-hidden="true"></span>
            <?php endif; ?>
            <span class="lasles-path-card-pro__index <?php echo e($rThumb ? 'lasles-path-card-pro__index--on-media' : ''); ?>"><?php echo e(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
          </div>
          <div class="lasles-path-card-pro__body">
            <h3 class="lasles-path-card-pro__title"><?php echo e($related->title); ?></h3>
            <p class="lasles-path-card-pro__summary"><?php echo e($related->instructor->name ?? __('landing.course_detail.tutor_label')); ?></p>
            <span class="lasles-path-card-pro__cta"><?php echo e(__('public.course_detail_title')); ?> <span aria-hidden="true">→</span></span>
          </div>
        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="lasles-path-detail-cta">
  <div class="lasles-container lasles-path-detail-cta__inner">
    <div>
      <h2 class="lasles-section-title"><?php echo e(__('landing.course_detail.cta_title')); ?></h2>
      <p class="lasles-section-lead"><?php echo e(__('landing.course_detail.cta_lead')); ?></p>
    </div>
    <div class="lasles-path-detail-cta__actions">
      <?php if($primaryIsForm): ?>
        <form action="<?php echo e(route('public.course.enroll.free', $course->id)); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <button type="submit" class="lasles-btn-primary"><?php echo e($primaryCtaLabel); ?></button>
        </form>
      <?php else: ?>
        <a href="<?php echo e($primaryCtaHref); ?>" class="lasles-btn-primary"><?php echo e($primaryCtaLabel); ?></a>
      <?php endif; ?>
      <a href="<?php echo e(route('public.learning-paths.index')); ?>" class="lasles-btn-outline"><?php echo e(__('landing.course_detail.cta_explore')); ?></a>
    </div>
  </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.querySelectorAll('[data-flash-close]').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var wrap = btn.closest('[data-flash]');
    if (wrap) wrap.remove();
  });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.lasles-public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/course-show.blade.php ENDPATH**/ ?>