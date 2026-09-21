<?php $__env->startSection('title', __('instructor.dashboard_title')); ?>
<?php $__env->startSection('page_title', __('instructor.overview')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isRtl = app()->getLocale() === 'ar';
    $showCourses = instructor_ui('show_courses', false);
    $showTutoring = instructor_ui('show_tutoring', false);
    $showLive = instructor_ui('show_live_broadcast', false);
    $pending = (int) ($stats['pending_submissions'] ?? 0);
    $students = (int) ($stats['total_students'] ?? 0);
    $courses = (int) ($stats['my_courses'] ?? 0);
    $lectures = (int) ($stats['total_lectures'] ?? 0);
    $upcomingTutoring = (int) ($stats['upcoming_tutoring'] ?? 0);
    $upcomingLectures = (int) ($stats['upcoming_lectures'] ?? 0);
    $exams = (int) ($stats['total_exams'] ?? 0);
    $assignments = (int) ($stats['total_assignments'] ?? 0);
    $pathCount = (int) ($stats['learning_paths_count'] ?? auth()->user()?->teachingLearningPathIds()->count() ?? 0);
    $consultCount = (int) ($stats['consultations_count'] ?? 0);
    $upcomingConsult = (int) ($stats['upcoming_consultations'] ?? 0);
    $bookingsHref = Route::has('instructor.tutoring-bookings.index')
        ? route('instructor.tutoring-bookings.index')
        : route('dashboard');
    $cohortsHref = Route::has('instructor.tutoring-cohorts.index')
        ? route('instructor.tutoring-cohorts.index')
        : $bookingsHref;
    $liveHref = Route::has('instructor.live-sessions.index')
        ? route('instructor.live-sessions.index')
        : route('dashboard');
    $calendarHref = Route::has('instructor.calendar')
        ? route('instructor.calendar')
        : route('dashboard');
    $pathsHref = Route::has('instructor.learning-paths.index')
        ? route('instructor.learning-paths.index')
        : route('dashboard');
    $consultHref = Route::has('instructor.consultations.index')
        ? route('instructor.consultations.index')
        : route('dashboard');

    if ($showCourses) {
        $workload = [
            ['label' => __('instructor.my_courses'), 'value' => $courses, 'href' => route('instructor.courses.index')],
            ['label' => __('instructor.total_students'), 'value' => $students, 'href' => route('instructor.courses.index')],
            ['label' => __('instructor.upcoming_lectures'), 'value' => $upcomingLectures, 'href' => route('instructor.lectures.index')],
            ['label' => __('instructor.group_bookings'), 'value' => $upcomingTutoring, 'href' => $bookingsHref],
            ['label' => __('instructor.need_grading'), 'value' => $pending, 'href' => route('instructor.assignments.index')],
            ['label' => __('instructor.exams'), 'value' => $exams, 'href' => route('instructor.exams.index')],
        ];
    } else {
        $workload = [
            ['label' => 'مساراتي المسندة', 'value' => $pathCount, 'href' => $pathsHref],
            ['label' => 'استشاراتي', 'value' => $consultCount, 'href' => $consultHref],
            ['label' => __('instructor.upcoming'), 'value' => $upcomingConsult, 'href' => $consultHref],
            ['label' => __('instructor.my_calendar'), 'value' => $upcomingLectures + $upcomingTutoring + $upcomingConsult, 'href' => $calendarHref],
        ];
    }
    $maxWorkload = max(1, collect($workload)->max('value'));
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
?>

<section class="st-join-hero" aria-label="<?php echo e(__('instructor.overview')); ?>">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">TADRIS LAB</p>
        <h2 class="st-join-hero__title"><?php echo e(__('instructor.welcome')); ?>، <?php echo e(auth()->user()->name); ?></h2>
        <p class="st-join-hero__meta">
            <?php echo e($isRtl ? 'لوحة المدرب — مسارات واستشارات ومواعيد مسندة إليك.' : 'Coach panel — assigned paths, consultations, and slots.'); ?>

            · <?php echo e(now()->translatedFormat($isRtl ? 'l، j F' : 'D, M j')); ?>

        </p>
    </div>
    <div class="st-join-hero__actions">
        <?php if(Route::has('instructor.consultations.index') && instructor_ui('show_consultations', true)): ?>
            <a href="<?php echo e(route('instructor.consultations.index')); ?>" class="st-pill st-pill--solid st-pill--lg">استشاراتي</a>
        <?php endif; ?>
        <?php if(Route::has('instructor.learning-paths.index') && instructor_ui('show_learning_paths', true)): ?>
            <a href="<?php echo e(route('instructor.learning-paths.index')); ?>" class="st-pill st-pill--outline">مساراتي</a>
        <?php elseif(Route::has('instructor.calendar')): ?>
            <a href="<?php echo e(route('instructor.calendar')); ?>" class="st-pill st-pill--outline"><?php echo e(__('instructor.my_calendar')); ?></a>
        <?php endif; ?>
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="<?php echo e(__('instructor.overview')); ?>">
    <?php if($showCourses): ?>
        <a href="<?php echo e(route('instructor.courses.index')); ?>" class="st-subject st-subject--blue st-stat-card">
            <img class="st-subject__blob" src="<?php echo e($subjMask1); ?>" alt="" width="132" height="132">
            <p class="st-stat-card__label"><?php echo e(__('instructor.my_courses')); ?></p>
            <p class="st-stat-card__value"><?php echo e(number_format($courses)); ?></p>
            <p class="st-stat-card__hint"><?php echo e($lectures); ?> <?php echo e(__('instructor.lectures')); ?></p>
        </a>
        <a href="<?php echo e(route('instructor.courses.index')); ?>" class="st-subject st-subject--pink st-stat-card">
            <img class="st-subject__blob" src="<?php echo e($subjMask2); ?>" alt="" width="132" height="132">
            <p class="st-stat-card__label"><?php echo e(__('instructor.total_students')); ?></p>
            <p class="st-stat-card__value"><?php echo e(number_format($students)); ?></p>
            <p class="st-stat-card__hint"><?php echo e($isRtl ? 'طلاب مسجّلون' : 'Enrolled learners'); ?></p>
        </a>
        <a href="<?php echo e(route('instructor.lectures.index')); ?>" class="st-subject st-subject--orange st-stat-card">
            <img class="st-subject__blob" src="<?php echo e($subjMask1); ?>" alt="" width="132" height="132">
            <p class="st-stat-card__label"><?php echo e(__('instructor.upcoming_lectures')); ?></p>
            <p class="st-stat-card__value"><?php echo e(number_format($upcomingLectures)); ?></p>
            <p class="st-stat-card__hint"><?php echo e(__('instructor.upcoming')); ?></p>
        </a>
        <a href="<?php echo e(route('instructor.assignments.index')); ?>" class="st-subject st-subject--purple st-stat-card">
            <img class="st-subject__blob" src="<?php echo e($subjMask2); ?>" alt="" width="132" height="132">
            <p class="st-stat-card__label"><?php echo e(__('instructor.need_grading')); ?></p>
            <p class="st-stat-card__value"><?php echo e(number_format($pending)); ?></p>
            <p class="st-stat-card__hint"><?php echo e(__('instructor.assignments')); ?></p>
        </a>
    <?php else: ?>
        <a href="<?php echo e($pathsHref); ?>" class="st-subject st-subject--blue st-stat-card">
            <img class="st-subject__blob" src="<?php echo e($subjMask1); ?>" alt="" width="132" height="132">
            <p class="st-stat-card__label">مساراتي المسندة</p>
            <p class="st-stat-card__value"><?php echo e(number_format($pathCount)); ?></p>
            <p class="st-stat-card__hint"><?php echo e($isRtl ? 'مسارات للتدريب' : 'Assigned paths'); ?></p>
        </a>
        <a href="<?php echo e($consultHref); ?>" class="st-subject st-subject--pink st-stat-card">
            <img class="st-subject__blob" src="<?php echo e($subjMask2); ?>" alt="" width="132" height="132">
            <p class="st-stat-card__label">استشاراتي</p>
            <p class="st-stat-card__value"><?php echo e(number_format($consultCount)); ?></p>
            <p class="st-stat-card__hint"><?php echo e($isRtl ? 'إجمالي الطلبات' : 'Total requests'); ?></p>
        </a>
        <a href="<?php echo e($consultHref); ?>" class="st-subject st-subject--orange st-stat-card">
            <img class="st-subject__blob" src="<?php echo e($subjMask1); ?>" alt="" width="132" height="132">
            <p class="st-stat-card__label"><?php echo e(__('instructor.upcoming')); ?></p>
            <p class="st-stat-card__value"><?php echo e(number_format($upcomingConsult)); ?></p>
            <p class="st-stat-card__hint"><?php echo e($isRtl ? 'استشارات قادمة' : 'Upcoming consultations'); ?></p>
        </a>
        <a href="<?php echo e($calendarHref); ?>" class="st-subject st-subject--purple st-stat-card">
            <img class="st-subject__blob" src="<?php echo e($subjMask2); ?>" alt="" width="132" height="132">
            <p class="st-stat-card__label"><?php echo e(__('instructor.my_calendar')); ?></p>
            <p class="st-stat-card__value"><?php echo e(number_format($upcomingLectures + $upcomingTutoring + $upcomingConsult)); ?></p>
            <p class="st-stat-card__hint"><?php echo e($isRtl ? 'مواعيد قادمة' : 'Upcoming slots'); ?></p>
        </a>
    <?php endif; ?>
</section>

<section class="st-msg-intro">
    <div>
        <h2><?php echo e(__('instructor.overview')); ?></h2>
        <p><?php echo e($isRtl ? 'اختصارات سريعة لما تحتاجه اليوم.' : 'Quick shortcuts for what you need today.'); ?></p>
    </div>
</section>

<section class="su-quick-grid" style="margin-bottom:1.25rem">
    <?php if(Route::has('instructor.calendar')): ?>
    <a href="<?php echo e(route('instructor.calendar')); ?>" class="su-quick">
        <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-calendar-alt"></i></span>
        <span>
            <strong><?php echo e(__('instructor.my_calendar')); ?></strong>
            <em><?php echo e(($showCourses ? $upcomingLectures : $upcomingConsult) + ($showTutoring ? $upcomingTutoring : 0)); ?> <?php echo e(__('instructor.upcoming')); ?></em>
        </span>
    </a>
    <?php endif; ?>
    <?php if(instructor_ui('show_learning_paths', true) && Route::has('instructor.learning-paths.index')): ?>
    <a href="<?php echo e(route('instructor.learning-paths.index')); ?>" class="su-quick">
        <span class="su-rail-ico su-rail-ico--a"><i class="fas fa-route"></i></span>
        <span>
            <strong>مساراتي المسندة</strong>
            <em><?php echo e($pathCount); ?> مسار</em>
        </span>
    </a>
    <?php endif; ?>
    <?php if(instructor_ui('show_consultations', true) && Route::has('instructor.consultations.index')): ?>
    <a href="<?php echo e(route('instructor.consultations.index')); ?>" class="su-quick">
        <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-comments"></i></span>
        <span>
            <strong>استشاراتي</strong>
            <em><?php echo e($consultCount); ?> · <?php echo e($upcomingConsult); ?> قادمة</em>
        </span>
    </a>
    <?php endif; ?>
    <?php if($showTutoring && Route::has('instructor.tutoring-bookings.index')): ?>
    <a href="<?php echo e(route('instructor.tutoring-bookings.index')); ?>" class="su-quick">
        <span class="su-rail-ico su-rail-ico--a"><i class="fas fa-users"></i></span>
        <span>
            <strong><?php echo e(__('instructor.group_bookings')); ?></strong>
            <em><?php echo e($upcomingTutoring); ?> <?php echo e(__('instructor.upcoming')); ?></em>
        </span>
    </a>
    <?php endif; ?>
    <?php if($showCourses): ?>
    <a href="<?php echo e(route('instructor.assignments.index')); ?>" class="su-quick">
        <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-tasks"></i></span>
        <span>
            <strong><?php echo e(__('instructor.assignments')); ?></strong>
            <em><?php if($pending > 0): ?><?php echo e($pending); ?> <?php echo e(__('instructor.need_grading')); ?><?php else: ?><?php echo e(__('instructor.all_assignments_graded')); ?><?php endif; ?></em>
        </span>
    </a>
    <?php endif; ?>
    <?php if($showLive && Route::has('instructor.live-sessions.index')): ?>
    <a href="<?php echo e(route('instructor.live-sessions.index')); ?>" class="su-quick">
        <span class="su-rail-ico su-rail-ico--a"><i class="fas fa-broadcast-tower"></i></span>
        <span>
            <strong><?php echo e(__('instructor.live_broadcast')); ?></strong>
            <em><?php echo e(__('instructor.manage_streams')); ?></em>
        </span>
    </a>
    <?php endif; ?>
</section>

<?php if(!empty($upcomingTutoringBooking) && $showTutoring): ?>
    <section class="st-join-hero st-join-hero--muted" style="margin-bottom:1.25rem">
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker"><?php echo e(__('instructor.next_live_session')); ?></p>
            <h2 class="st-join-hero__title"><?php echo e($upcomingTutoringBooking->tutoringGroup?->title ?? __('instructor.group_session')); ?></h2>
            <p class="st-join-hero__meta">
                <?php if (isset($component)) { $__componentOriginal4bdb6aac1f9ecf59585773b3a0097468 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468 = $attributes; } ?>
<?php $component = App\View\Components\AppDatetime::resolve(['at' => $upcomingTutoringBooking->starts_at,'pattern' => 'D j M · g:i A'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-datetime'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppDatetime::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468)): ?>
<?php $attributes = $__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468; ?>
<?php unset($__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4bdb6aac1f9ecf59585773b3a0097468)): ?>
<?php $component = $__componentOriginal4bdb6aac1f9ecf59585773b3a0097468; ?>
<?php unset($__componentOriginal4bdb6aac1f9ecf59585773b3a0097468); ?>
<?php endif; ?>
                <?php if($upcomingTutoringBooking->user): ?> · <?php echo e($upcomingTutoringBooking->user->name); ?> <?php endif; ?>
            </p>
        </div>
        <div class="st-join-hero__actions">
            <?php if($upcomingTutoringBooking->classroomMeeting): ?>
                <form method="POST" action="<?php echo e(route('instructor.classroom.start-meeting', $upcomingTutoringBooking->classroomMeeting)); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="st-pill st-pill--solid"><?php echo e(__('instructor.start_live')); ?></button>
                </form>
            <?php endif; ?>
            <?php if(Route::has('instructor.tutoring-bookings.show')): ?>
                <a href="<?php echo e(route('instructor.tutoring-bookings.show', $upcomingTutoringBooking)); ?>" class="st-pill st-pill--outline"><?php echo e(__('instructor.view_details')); ?></a>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<section class="su-bottom">
    <?php if($showCourses): ?>
    <div class="su-block st-panel">
        <div class="flex items-center justify-between mb-3">
            <div class="su-block__title" style="margin:0"><?php echo e(__('instructor.upcoming_lectures')); ?></div>
            <a href="<?php echo e(route('instructor.lectures.index')); ?>" class="su-rail-m"><?php echo e(__('instructor.view_all')); ?></a>
        </div>
        <div class="space-y-1">
            <?php $__empty_1 = true; $__currentLoopData = ($upcoming_lectures ?? collect())->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lecture): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('instructor.lectures.show', $lecture)); ?>" class="su-rail-item">
                    <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-chalkboard"></i></span>
                    <div class="min-w-0">
                        <div class="su-rail-t truncate"><?php echo e($lecture->title); ?></div>
                        <div class="su-rail-m truncate">
                            <?php echo e($lecture->course->title ?? __('instructor.not_specified')); ?>

                            · <?php echo e($lecture->scheduled_at?->diffForHumans()); ?>

                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="su-rail-m" style="padding:16px;text-align:center"><?php echo e(__('instructor.no_lectures')); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php elseif(Route::has('instructor.consultations.index')): ?>
    <div class="su-block st-panel">
        <div class="flex items-center justify-between mb-3">
            <div class="su-block__title" style="margin:0">استشارات قادمة</div>
            <a href="<?php echo e($consultHref); ?>" class="su-rail-m"><?php echo e(__('instructor.view_all')); ?></a>
        </div>
        <div class="space-y-1">
            <?php $__empty_1 = true; $__currentLoopData = ($upcoming_consultations ?? collect())->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('instructor.consultations.show', $c)); ?>" class="su-rail-item">
                    <span class="su-rail-ico su-rail-ico--a"><i class="fas fa-comments"></i></span>
                    <div class="min-w-0">
                        <div class="su-rail-t truncate"><?php echo e($c->contact_name ?? $c->student?->name ?? 'استشارة'); ?></div>
                        <div class="su-rail-m truncate"><?php echo e($c->scheduled_at?->diffForHumans() ?? $c->statusLabel()); ?></div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="su-rail-m" style="padding:16px;text-align:center">لا استشارات قادمة حاليًا</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="su-block st-panel">
        <div class="su-block__title"><?php echo e(__('instructor.workload_summary')); ?></div>
        <div class="su-workload">
            <?php $__currentLoopData = $workload; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($row['href']); ?>" class="su-workload-row">
                    <span class="su-workload-label truncate"><?php echo e($row['label']); ?></span>
                    <div class="su-workload-bar" aria-hidden="true">
                        <i style="width: <?php echo e(max(8, (int) round(($row['value'] / $maxWorkload) * 100))); ?>%"></i>
                    </div>
                    <strong><?php echo e(number_format($row['value'])); ?></strong>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<?php if($showCourses): ?>
<section class="su-bottom" style="margin-top:20px">
    <div class="su-block st-panel">
        <div class="flex items-center justify-between mb-3">
            <div class="su-block__title" style="margin:0"><?php echo e(__('instructor.my_recent_courses')); ?></div>
            <a href="<?php echo e(route('instructor.courses.index')); ?>" class="su-rail-m"><?php echo e(__('instructor.view_all')); ?></a>
        </div>
        <div class="space-y-1">
            <?php $__empty_1 = true; $__currentLoopData = ($my_courses ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('instructor.courses.show', $course)); ?>" class="su-rail-item">
                    <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-book"></i></span>
                    <div class="min-w-0">
                        <div class="su-rail-t truncate"><?php echo e($course->title); ?></div>
                        <div class="su-rail-m"><?php echo e($course->active_students_count ?? 0); ?> <?php echo e(__('instructor.student_single')); ?></div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="su-rail-m" style="padding:16px;text-align:center"><?php echo e(__('instructor.no_courses_assigned')); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="su-block st-panel">
        <div class="flex items-center justify-between mb-3">
            <div class="su-block__title" style="margin:0"><?php echo e(__('instructor.assignments_need_grading')); ?></div>
            <?php if($pending > 0): ?><span class="su-link__badge"><?php echo e($pending); ?></span><?php endif; ?>
        </div>
        <div class="space-y-1">
            <?php $__empty_1 = true; $__currentLoopData = ($pending_assignments ?? collect())->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $submission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('instructor.assignments.submissions', $submission->assignment)); ?>" class="su-rail-item">
                    <span class="su-rail-avatar"><?php echo e(mb_substr($submission->student->name ?? 'S', 0, 1)); ?></span>
                    <div class="min-w-0">
                        <div class="su-rail-t truncate"><?php echo e($submission->assignment->title ?? __('instructor.assignment_default')); ?></div>
                        <div class="su-rail-m"><?php echo e($submission->student->name ?? ''); ?> · <?php echo e($submission->created_at->diffForHumans()); ?></div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="su-rail-m" style="padding:16px;text-align:center"><?php echo e(__('instructor.all_assignments_graded')); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.instructor-timeline', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/dashboard/instructor.blade.php ENDPATH**/ ?>