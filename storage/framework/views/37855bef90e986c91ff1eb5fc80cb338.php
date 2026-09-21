<?php $__env->startSection('title', __('instructor.my_calendar')); ?>
<?php $__env->startSection('page_title', __('instructor.my_calendar')); ?>

<?php $__env->startPush('styles'); ?>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isRtl = app()->getLocale() === 'ar';
    $viewerTz = $viewerTz ?? auth()->user()?->timezoneCode() ?? \App\Support\AppTimezone::academy();
    $fcLocale = $isRtl ? 'ar' : 'en';
    $upcoming = collect($events ?? [])->filter(fn ($e) => ($e->start_date ?? now()) >= now())->take(12);
    $next = $upcoming->first();
    $total = (int) ($stats['total'] ?? 0);
    $upcomingCount = (int) ($stats['upcoming'] ?? $upcoming->count());
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
?>

<section class="st-join-hero" aria-label="<?php echo e(__('instructor.my_calendar')); ?>">
    <div class="st-join-hero__copy">
        <p class="st-join-hero__kicker">TADRIS LAB</p>
        <?php if($next): ?>
            <h2 class="st-join-hero__title"><?php echo e($next->title); ?></h2>
            <p class="st-join-hero__meta">
                <?php if (isset($component)) { $__componentOriginal4bdb6aac1f9ecf59585773b3a0097468 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468 = $attributes; } ?>
<?php $component = App\View\Components\AppDatetime::resolve(['at' => $next->start_date,'timezone' => $viewerTz,'pattern' => $isRtl ? 'l، d M · g:i A' : 'D, M j · g:i A'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                · <?php echo e(\App\Support\AppTimezone::label($viewerTz)); ?>

            </p>
        <?php else: ?>
            <h2 class="st-join-hero__title"><?php echo e(__('instructor.my_calendar')); ?></h2>
            <p class="st-join-hero__meta">
                <?php echo e(__('instructor.calendar_subtitle')); ?>

                <strong><?php echo e(\App\Support\AppTimezone::label($viewerTz)); ?></strong>
            </p>
        <?php endif; ?>
    </div>
    <div class="st-join-hero__actions">
        <?php if($next && !empty($next->url)): ?>
            <a href="<?php echo e($next->url); ?>" class="st-pill st-pill--solid st-pill--lg">
                <?php echo e($isRtl ? 'فتح الموعد' : 'Open event'); ?>

            </a>
        <?php endif; ?>
        <?php if(Route::has('instructor.consultations.index') && instructor_ui('show_consultations', true)): ?>
            <a href="<?php echo e(route('instructor.consultations.index')); ?>" class="st-pill st-pill--outline">استشاراتي</a>
        <?php endif; ?>
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="<?php echo e(__('instructor.my_calendar')); ?>">
    <article class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask1); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e(__('instructor.calendar_total')); ?></p>
        <p class="st-stat-card__value"><?php echo e(number_format($total)); ?></p>
        <p class="st-stat-card__hint"><?php echo e($isRtl ? 'كل المواعيد في النطاق' : 'All events in range'); ?></p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask2); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e(__('instructor.upcoming')); ?></p>
        <p class="st-stat-card__value"><?php echo e(number_format($upcomingCount)); ?></p>
        <p class="st-stat-card__hint"><?php echo e($isRtl ? 'مواعيد قادمة' : 'Upcoming slots'); ?></p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask1); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e($isRtl ? 'منطقتك' : 'Your timezone'); ?></p>
        <p class="st-stat-card__value st-stat-card__value--text"><?php echo e(\App\Support\AppTimezone::label($viewerTz)); ?></p>
        <p class="st-stat-card__hint"><?php echo e($viewerTz); ?></p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask2); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e($isRtl ? 'العرض الافتراضي' : 'Default view'); ?></p>
        <p class="st-stat-card__value st-stat-card__value--text"><?php echo e($isRtl ? 'أسبوعي' : 'Week'); ?></p>
        <p class="st-stat-card__hint"><?php echo e($isRtl ? 'شهر · يوم · قائمة' : 'Month · Day · List'); ?></p>
    </article>
</section>

<section class="st-msg-intro">
    <div>
        <h2><?php echo e(__('instructor.my_calendar')); ?></h2>
        <p><?php echo e($isRtl ? 'مساراتك واستشاراتك ومحاضراتك في مكان واحد — بتوقيتك المحلي.' : 'Paths, consultations, and lectures in one place — on your local clock.'); ?></p>
    </div>
</section>

<div class="st-cal-layout">
    <section class="st-panel st-fc su-fc" dir="<?php echo e($isRtl ? 'rtl' : 'ltr'); ?>" aria-label="<?php echo e(__('instructor.my_calendar')); ?>">
        <div id="calendar" class="su-fc__mount"></div>
        <div class="st-fc__legend">
            <span><i style="background:#7c3aed"></i> <?php echo e(__('instructor.cal_private')); ?></span>
            <span><i style="background:#1E4E8C"></i> <?php echo e(__('instructor.cal_group')); ?></span>
            <span><i style="background:#3B7BC4"></i> <?php echo e(__('instructor.cal_classroom')); ?></span>
            <span><i style="background:#A88050"></i> <?php echo e(__('instructor.cal_consultation')); ?></span>
            <span><i style="background:#EF4444"></i> <?php echo e(__('instructor.cal_live')); ?></span>
        </div>
    </section>

    <aside class="st-panel st-cal-side" aria-label="<?php echo e(__('instructor.upcoming')); ?>">
        <div class="st-section-head">
            <h2><?php echo e(__('instructor.upcoming')); ?></h2>
            <p><?php echo e($isRtl ? 'أقرب المواعيد المسندة إليك' : 'Your next assigned slots'); ?></p>
        </div>
        <div class="st-cal-upcoming">
            <?php $__empty_1 = true; $__currentLoopData = $upcoming; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e($event->url ?? '#'); ?>" class="st-cal-upcoming__item">
                    <span class="st-cal-upcoming__ico" aria-hidden="true">
                        <i class="fas fa-calendar-day"></i>
                    </span>
                    <span class="st-cal-upcoming__body">
                        <strong><?php echo e($event->title); ?></strong>
                        <em>
                            <?php if (isset($component)) { $__componentOriginal4bdb6aac1f9ecf59585773b3a0097468 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468 = $attributes; } ?>
<?php $component = App\View\Components\AppDatetime::resolve(['at' => $event->start_date,'timezone' => $viewerTz,'pattern' => 'D j M · g:i A'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                        </em>
                    </span>
                    <i class="fas fa-chevron-<?php echo e($isRtl ? 'left' : 'right'); ?> st-cal-upcoming__chev" aria-hidden="true"></i>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="st-cal-upcoming__empty">
                    <i class="fas fa-calendar-times" aria-hidden="true"></i>
                    <p><?php echo e(__('instructor.calendar_no_upcoming')); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </aside>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/ar.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var isRtl = <?php echo json_encode($isRtl, 15, 512) ?>;
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl || typeof FullCalendar === 'undefined') {
        if (calendarEl) {
            calendarEl.innerHTML = '<p style="padding:40px;text-align:center;color:#6b7a93;font-weight:700"><?php echo e($isRtl ? 'تعذر تحميل التقويم' : 'Calendar failed to load'); ?></p>';
        }
        return;
    }

    var isMobile = window.matchMedia('(max-width: 640px)').matches;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: <?php echo json_encode($fcLocale, 15, 512) ?>,
        direction: isRtl ? 'rtl' : 'ltr',
        timeZone: <?php echo json_encode($viewerTz, 15, 512) ?>,
        initialView: isMobile ? 'listWeek' : 'timeGridWeek',
        headerToolbar: isRtl
            ? { right: 'prev,next today', center: 'title', left: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' }
            : { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' },
        buttonText: isRtl
            ? { today: 'اليوم', month: 'شهر', week: 'أسبوع', day: 'يوم', list: 'قائمة' }
            : { today: 'Today', month: 'Month', week: 'Week', day: 'Day', list: 'List' },
        events: {
            url: <?php echo json_encode(route('instructor.calendar.events'), 15, 512) ?>,
            failure: function () {
                console.error('Failed to load calendar events');
            }
        },
        eventClick: function (info) {
            if (info.event.url) {
                info.jsEvent.preventDefault();
                window.location.href = info.event.url;
            }
        },
        height: 'auto',
        contentHeight: isMobile ? 480 : 640,
        firstDay: isRtl ? 6 : 0,
        navLinks: true,
        dayMaxEvents: 3,
        nowIndicator: true,
        stickyHeaderDates: true
    });
    calendar.render();

    window.addEventListener('resize', function () {
        var mobile = window.matchMedia('(max-width: 640px)').matches;
        calendar.setOption('contentHeight', mobile ? 480 : 640);
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.instructor-timeline', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/instructor/calendar/index.blade.php ENDPATH**/ ?>