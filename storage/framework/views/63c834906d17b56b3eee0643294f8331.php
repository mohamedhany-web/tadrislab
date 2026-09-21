<?php $__env->startSection('title', __('student_timeline.timeline')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $game = $game ?? [
        'xp' => 0,
        'level' => 1,
        'streak' => ['current' => 0],
        'daily_missions' => collect(),
        'weekly_missions' => collect(),
    ];
    $classes = $classes ?? collect();
    $weekDays = $weekDays ?? collect();
    $upcoming = $upcoming ?? collect();
    $todayItems = $todayItems ?? collect();
    $scheduleRows = $scheduleRows ?? collect();
    $viewMode = $viewMode ?? 'week';
    $sortMode = $sortMode ?? 'classes';
    $searchQuery = $searchQuery ?? '';
    $weekAnchor = ($weekAnchor ?? now())->locale($locale);
    $viewerTz = auth()->user()?->timezoneCode() ?? \App\Support\AppTimezone::academy();
    $weekAnchor = $weekAnchor->copy()->timezone($viewerTz);
    $cal = $weekAnchor->copy();
    $monthStart = $cal->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SATURDAY);
    $monthEnd = $cal->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::FRIDAY);
    $calendarDays = collect();
    for ($d = $monthStart->copy(); $d->lte($monthEnd); $d->addDay()) {
        $calendarDays->push($d->copy());
    }
    $markedDates = $weekDays
        ->filter(fn ($day) => ($day->items ?? collect())->isNotEmpty())
        ->mapWithKeys(fn ($day) => [$day->date->toDateString() => true]);
    $weekdays = $isRtl
        ? ['س', 'ح', 'ن', 'ث', 'ر', 'خ', 'ج']
        : ['Sa', 'Su', 'Mo', 'Tu', 'We', 'Th', 'Fr'];
    $subjectTones = ['blue', 'orange', 'purple', 'green'];
    $subjectIcons = [
        asset('img/student-timeline/sqrt.svg'),
        asset('img/student-timeline/earth.svg'),
        asset('img/student-timeline/clock.png'),
        asset('img/student-timeline/teacher.png'),
    ];
    $todoItems = collect($game['daily_missions'] ?? [])->take(3);
    $scheduleJoinUrl = function ($slot) {
        if (! empty($slot->join_url)) {
            return $slot->join_url;
        }
        if (isset($slot->type, $slot->ref_id) && Route::has('student.schedule.join')) {
            return route('student.schedule.join', ['type' => $slot->type, 'id' => $slot->ref_id]);
        }

        return null;
    };
    $brandLogoUrl = \App\Services\AdminPanelBranding::logoPublicUrl();
    $brandLogoFallback = \App\Services\AdminPanelBranding::inlineFallbackDataUri();
    if ($scheduleRows->isEmpty() && !empty($todayMission)) {
        $scheduleRows = collect([(object) [
            'starts_at' => $todayMission->starts_at,
            'title' => $todayMission->title,
            'join_url' => $todayMission->is_joinable ? $todayMission->join_url : ($todayMission->class_url ?: '#'),
        ]]);
    }
?>

<header class="st-top">
    <div class="st-top__row st-top__row--primary">
        <a href="<?php echo e(route('dashboard')); ?>" class="st-top__brand" title="<?php echo e(config('app.name')); ?>">
            <span class="st-top__brand-mark">
                <img src="<?php echo e($brandLogoUrl); ?>" alt="<?php echo e(config('app.name')); ?>" width="36" height="36" loading="eager" decoding="async" onerror="this.onerror=null;this.src='<?php echo e($brandLogoFallback); ?>';">
            </span>
        </a>

        <h1 class="st-top__title"><?php echo e(__('student_timeline.timeline')); ?></h1>

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

    <div class="st-top__row st-top__row--secondary">
        <div class="st-datepill" aria-label="<?php echo e($cal->translatedFormat('l, F j')); ?>">
            <a href="<?php echo e($timelinePrevUrl ?? '#'); ?>" class="st-datepill__nav" aria-label="<?php echo e($viewMode === 'day' ? __('student_timeline.prev_day') : __('student_timeline.prev_week')); ?>">
                <i class="fas <?php echo e($isRtl ? 'fa-chevron-right' : 'fa-chevron-left'); ?>" aria-hidden="true"></i>
            </a>
            <a href="<?php echo e($timelineTodayUrl ?? route('dashboard')); ?>" class="st-datepill__label"><?php echo e($cal->translatedFormat($isRtl ? 'l، j F' : 'l, F j')); ?></a>
            <a href="<?php echo e($timelineNextUrl ?? '#'); ?>" class="st-datepill__nav" aria-label="<?php echo e($viewMode === 'day' ? __('student_timeline.next_day') : __('student_timeline.next_week')); ?>">
                <i class="fas <?php echo e($isRtl ? 'fa-chevron-left' : 'fa-chevron-right'); ?>" aria-hidden="true"></i>
            </a>
        </div>

        <div class="st-top__chips">
            <a href="<?php echo e($timelineSortUrl ?? route('dashboard')); ?>" class="st-chip <?php echo e($sortMode === 'progress' ? 'is-active' : ''); ?>" title="<?php echo e($sortMode === 'progress' ? __('student_timeline.sort_by_progress') : __('student_timeline.sort_by_classes')); ?>">
                <img src="<?php echo e(asset('img/student-timeline/filter.svg')); ?>" alt="" width="14" height="14">
                <?php echo e($sortMode === 'progress' ? __('student_timeline.sort_by_progress') : __('student_timeline.sort_by_classes')); ?>

            </a>
            <a href="<?php echo e($timelineViewUrl ?? route('dashboard')); ?>" class="st-chip <?php echo e($viewMode === 'week' ? 'is-active' : ''); ?>" title="<?php echo e($viewMode === 'week' ? __('student_timeline.week_view') : __('student_timeline.day_view')); ?>">
                <?php echo e($viewMode === 'week' ? __('student_timeline.week_view') : __('student_timeline.day_view')); ?>

            </a>
        </div>
    </div>

    <form class="st-search" method="get" action="<?php echo e(url()->current()); ?>" role="search">
        <?php if(request('week')): ?>
            <input type="hidden" name="week" value="<?php echo e(request('week')); ?>">
        <?php endif; ?>
        <?php if($viewMode !== 'week'): ?>
            <input type="hidden" name="view" value="<?php echo e($viewMode); ?>">
        <?php endif; ?>
        <?php if($sortMode !== 'classes'): ?>
            <input type="hidden" name="sort" value="<?php echo e($sortMode); ?>">
        <?php endif; ?>
        <?php if(request('lang')): ?>
            <input type="hidden" name="lang" value="<?php echo e(request('lang')); ?>">
        <?php endif; ?>
        <i class="fas fa-search text-[11px]"></i>
        <input type="search" name="q" value="<?php echo e($searchQuery); ?>" placeholder="<?php echo e(__('student_timeline.search')); ?>" aria-label="<?php echo e(__('student_timeline.search')); ?>">
    </form>
</header>

<?php
    $firstName = explode(' ', trim((string) (auth()->user()->name ?? '')))[0] ?? '';
    $progress = $progress ?? ['percent' => 0, 'attended' => 0, 'completed_sessions' => 0, 'label' => ''];
    $credits = $credits ?? ['total_left' => 0];
    $trialUrl = route('home').'?open_trial=1';
    $groupsUrl = Route::has('public.groups') ? route('public.groups') : route('dashboard');
    $eventMasks = [
        asset('img/student-timeline/event-mask-1.svg'),
        asset('img/student-timeline/event-mask-2.svg'),
        asset('img/student-timeline/event-mask-3.svg'),
    ];
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
?>


<section class="st-event-card st-event-card--blue st-biz-banner">
    <img class="st-event-card__mask" src="<?php echo e($eventMasks[1]); ?>" alt="" width="160" height="160">
    <div class="st-biz-banner__row">
        <div>
            <p class="st-event-card__kicker"><?php echo e(__('student_timeline.school_gate')); ?></p>
            <h3><?php echo e($greeting ?? ''); ?>، <?php echo e($firstName); ?></h3>
            <p class="st-event-card__sub">
                <?php if(!empty($primaryClass)): ?>
                    <?php echo e(__('student_timeline.you_are_in')); ?>

                    <?php echo e($primaryClass->title); ?>

                    <?php if($primaryClass->year_name): ?> · <?php echo e($primaryClass->year_name); ?> <?php endif; ?>
                <?php else: ?>
                    <?php echo e(__('student_timeline.hero_out')); ?>

                <?php endif; ?>
            </p>
        </div>
        <div class="st-biz-banner__actions">
            <?php if(!empty($todayMission) && $todayMission->is_joinable): ?>
                <a href="<?php echo e($todayMission->join_url); ?>" class="st-pill st-pill--light"><?php echo e(__('student_timeline.join_class_now')); ?></a>
            <?php elseif(Route::has('student.classes.index')): ?>
                <a href="<?php echo e(route('student.classes.index')); ?>" class="st-pill st-pill--light"><?php echo e(__('student_timeline.my_classes')); ?></a>
            <?php endif; ?>
            <a href="<?php echo e($groupsUrl); ?>" class="st-pill st-pill--ghost"><?php echo e(__('student_timeline.explore_school')); ?></a>
        </div>
    </div>
</section>


<section class="st-stats" aria-label="<?php echo e(__('student_timeline.school_gate')); ?>">
    <article class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask1); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e(__('student_timeline.path_progress')); ?></p>
        <p class="st-stat-card__value"><?php echo e($progress['percent']); ?>%</p>
        <div class="st-stat-card__bar"><span style="width: <?php echo e($progress['percent']); ?>%"></span></div>
        <p class="st-stat-card__hint"><?php echo e($progress['label']); ?></p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask2); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e(__('student_timeline.attendance')); ?></p>
        <p class="st-stat-card__value"><?php echo e($progress['attended']); ?></p>
        <p class="st-stat-card__hint"><?php echo e(__('student_timeline.of_completed', ['count' => $progress['completed_sessions']])); ?></p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask1); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e(__('student_timeline.session_credits')); ?></p>
        <p class="st-stat-card__value"><?php echo e($credits['total_left']); ?></p>
        <p class="st-stat-card__hint">
            <?php if(Route::has('student.service-entitlements.index')): ?>
                <a href="<?php echo e(route('student.service-entitlements.index')); ?>"><?php echo e(__('student_timeline.view_credits')); ?></a>
            <?php else: ?>
                —
            <?php endif; ?>
        </p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask2); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e(__('student_timeline.active_classes')); ?></p>
        <p class="st-stat-card__value"><?php echo e($classes->count()); ?></p>
        <p class="st-stat-card__hint">⭐ <?php echo e(number_format($game['xp'] ?? 0)); ?> · Lv <?php echo e($game['level'] ?? 1); ?> · 🔥 <?php echo e($game['streak']['current'] ?? 0); ?></p>
    </article>
</section>

<?php if(!empty($todayMission)): ?>
    <?php
        $missionUrl = $todayMission->is_joinable
            ? $todayMission->join_url
            : ($todayMission->class_url ?: '#');
    ?>
    <a href="<?php echo e($missionUrl); ?>" class="st-event-card st-event-card--green st-biz-banner">
        <img class="st-event-card__mask" src="<?php echo e($eventMasks[0]); ?>" alt="" width="160" height="160">
        <div class="st-biz-banner__row">
            <div>
                <p class="st-event-card__kicker">
                    <?php echo e($todayMission->is_today ? __('student_timeline.today_mission') : __('student_timeline.next_mission')); ?>

                </p>
                <h3><?php echo e($todayMission->title); ?></h3>
                <p class="st-event-card__sub">
                    <?php echo e($todayMission->subtitle); ?>

                    · <?php if (isset($component)) { $__componentOriginal4bdb6aac1f9ecf59585773b3a0097468 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4bdb6aac1f9ecf59585773b3a0097468 = $attributes; } ?>
<?php $component = App\View\Components\AppDatetime::resolve(['at' => $todayMission->starts_at,'pattern' => 'D g:i A'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                    · <?php echo e($todayMission->duration_minutes); ?> <?php echo e(__('student_timeline.minutes')); ?>

                </p>
            </div>
            <span class="st-pill st-pill--light">
                <?php echo e($todayMission->is_joinable ? __('student_timeline.continue_learning') : __('student_timeline.open_class')); ?>

            </span>
        </div>
    </a>
<?php endif; ?>

<section id="st-subjects">
    <div class="st-section-head">
        <div>
            <h2><?php echo e(__('student_timeline.subjects')); ?></h2>
            <p><?php echo e(__('student_timeline.upcoming_classes')); ?></p>
        </div>
        <?php if(Route::has('student.classes.index')): ?>
            <a class="st-see" href="<?php echo e(route('student.classes.index')); ?>"><?php echo e(__('student_timeline.see_all')); ?></a>
        <?php endif; ?>
    </div>

    <?php if($classes->isNotEmpty()): ?>
        <div class="st-subjects">
            <?php $__currentLoopData = $classes->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $class): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $tone = $subjectTones[$i % count($subjectTones)];
                    $icon = $subjectIcons[$i % count($subjectIcons)];
                    $mask = $i % 2 === 0
                        ? asset('img/student-timeline/subj-mask-1.svg')
                        : asset('img/student-timeline/subj-mask-2.svg');
                    $label = $class->subject_name ?: $class->title;
                ?>
                <a href="<?php echo e($class->url); ?>" class="st-subject st-subject--<?php echo e($tone); ?>" title="<?php echo e($class->title); ?> · <?php echo e($class->progress_percent); ?>%">
                    <img class="st-subject__blob" src="<?php echo e($mask); ?>" alt="" width="132" height="132">
                    <span class="st-subject__icon">
                        <img src="<?php echo e($icon); ?>" alt="" width="22" height="22">
                    </span>
                    <img class="st-subject__more" src="<?php echo e(asset('img/student-timeline/ellipsis.svg')); ?>" alt="" width="24" height="24">
                    <div class="st-subject__foot">
                        <h3 class="st-subject__name"><?php echo e($label); ?></h3>
                        <span class="st-subject__meta"><?php echo e($class->completed_sessions); ?>/<?php echo e($class->total_sessions); ?> · <?php echo e($class->progress_percent); ?>%</span>
                        <span class="st-subject__bar" aria-hidden="true"><i style="width: <?php echo e(max(4, (int) $class->progress_percent)); ?>%"></i></span>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="st-empty-row">
            <div class="st-subject st-subject--blue" style="min-width: 200px;">
                <img class="st-subject__blob" src="<?php echo e(asset('img/student-timeline/subj-mask-1.svg')); ?>" alt="" width="132" height="132">
                <h3 class="st-subject__name"><?php echo e(__('student_timeline.no_classes')); ?></h3>
            </div>
            <div class="st-biz-banner__actions">
                <?php if(!empty($recommendedYear) && Route::has('public.school.year')): ?>
                    <a href="<?php echo e(route('public.school.year', $recommendedYear->slug)); ?>" class="st-pill st-pill--solid"><?php echo e($recommendedYear->name); ?></a>
                <?php endif; ?>
                <a href="<?php echo e($trialUrl); ?>" class="st-pill st-pill--solid"><?php echo e(__('student_timeline.placement_test')); ?></a>
                <a href="<?php echo e($groupsUrl); ?>" class="st-pill st-pill--outline"><?php echo e(__('student_timeline.browse_school')); ?></a>
            </div>
        </div>
    <?php endif; ?>
</section>

<section class="st-cal" aria-label="<?php echo e(__('student_timeline.calendar')); ?>">
    <div class="st-cal__todo">
        <div class="st-cal__todo-head">
            <div class="st-cal__daynum"><?php echo e($cal->format('d')); ?></div>
            <div class="st-cal__month"><?php echo e($cal->translatedFormat('F')); ?></div>
            <div class="st-cal__year"><?php echo e($cal->format('Y')); ?></div>
        </div>

        <div class="st-cal__todo-body">
            <div class="st-cal__label"><?php echo e(__('student_timeline.todo_list')); ?></div>
            <?php $__empty_1 = true; $__currentLoopData = $todoItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="st-cal__note <?php echo e($loop->even ? 'st-cal__note--dark' : ''); ?>">
                    <?php echo e($mission->title); ?>

                    <?php if(!empty($mission->completed)): ?> ✓ <?php else: ?> · <?php echo e($mission->progress); ?>/<?php echo e($mission->target); ?> <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="st-cal__note"><?php echo e(__('student_timeline.notes_to_be_made')); ?></div>
                <div class="st-cal__note st-cal__note--dark"><?php echo e(__('student_timeline.dont_forget_activities')); ?></div>
            <?php endif; ?>

            <div class="st-cal__sched-label">
                <?php echo e($viewMode === 'day' ? __('student_timeline.day_view') : __('student_timeline.my_week')); ?>

            </div>
            <?php $__empty_1 = true; $__currentLoopData = $scheduleRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $schedHref = $scheduleJoinUrl($row); ?>
                <?php if($schedHref): ?>
                    <a href="<?php echo e($schedHref); ?>" class="st-cal__row st-cal__row--link">
                        <span><?php echo e(trim(($row->day_short ?? '').' · '.($row->starts_at ? \App\Support\AppTimezone::formatFor($row->starts_at, $viewerTz, 'g:i A', $locale) : '—'), ' ·')); ?></span>
                        <span><?php echo e(\Illuminate\Support\Str::limit($row->title ?? '', 28)); ?></span>
                    </a>
                <?php else: ?>
                    <div class="st-cal__row">
                        <span><?php echo e(trim(($row->day_short ?? '').' · '.($row->starts_at ? \App\Support\AppTimezone::formatFor($row->starts_at, $viewerTz, 'g:i A', $locale) : '—'), ' ·')); ?></span>
                        <span><?php echo e(\Illuminate\Support\Str::limit($row->title ?? '', 28)); ?></span>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="st-cal__row">
                    <span>—</span>
                    <span><?php echo e(__('student_timeline.no_events')); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="st-cal__grid">
        <img class="st-cal__grid-bg" src="<?php echo e(asset('img/student-timeline/cal-wave.svg')); ?>" alt="" aria-hidden="true">
        <div class="st-cal__grid-head">
            <h3><?php echo e(__('student_timeline.calendar')); ?></h3>
            <div class="st-cal__months">
                <a href="<?php echo e($timelineMonthPrevUrl ?? '#'); ?>" class="st-cal__month-link" aria-label="<?php echo e(__('student_timeline.prev_month')); ?>">
                    <?php echo e($cal->copy()->subMonth()->translatedFormat('M')); ?>

                    <small><?php echo e($cal->copy()->subMonth()->format('Y')); ?></small>
                </a>
                <span class="is-active" aria-current="true">
                    <?php echo e($cal->translatedFormat('M')); ?>

                    <small><?php echo e($cal->format('Y')); ?></small>
                </span>
                <a href="<?php echo e($timelineMonthNextUrl ?? '#'); ?>" class="st-cal__month-link" aria-label="<?php echo e(__('student_timeline.next_month')); ?>">
                    <?php echo e($cal->copy()->addMonth()->translatedFormat('M')); ?>

                    <small><?php echo e($cal->copy()->addMonth()->format('Y')); ?></small>
                </a>
            </div>
        </div>
        <div class="st-cal__board">
            <div class="st-weekdays" role="row">
                <?php $__currentLoopData = $weekdays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="st-weekday"><?php echo e($wd); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="st-days" role="grid">
                <?php $__currentLoopData = $calendarDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isCurrentMonth = $day->month === $cal->month;
                        $isToday = $day->isToday();
                        $isMarked = $markedDates->has($day->toDateString());
                    ?>
                    <span class="st-day <?php echo e(! $isCurrentMonth ? 'is-muted' : ''); ?> <?php echo e($isToday ? 'is-today' : ''); ?> <?php echo e($isMarked && ! $isToday ? 'is-mark' : ''); ?>">
                        <?php if($isMarked && ! $isToday): ?>
                            <img class="st-day__ring" src="<?php echo e(asset('img/student-timeline/day-circle.svg')); ?>" alt="" width="37" height="37">
                        <?php endif; ?>
                        <span class="st-day__num"><?php echo e($day->day); ?></span>
                    </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>

<?php if(!empty($recommendedYear) || !empty($placement?->admin_notes)): ?>
    <section class="st-event-card st-event-card--purple st-biz-banner st-path-card">
        <img class="st-event-card__mask" src="<?php echo e($eventMasks[1]); ?>" alt="" width="160" height="160">
        <div class="st-path-card__body">
            <p class="st-event-card__kicker"><?php echo e(__('student_timeline.school_path')); ?></p>
            <?php if(!empty($recommendedYear)): ?>
                <h3 class="st-text-auto"><?php echo e($recommendedYear->name); ?></h3>
                <?php if($recommendedYear->tagline): ?>
                    <p class="st-event-card__sub st-text-auto"><?php echo e($recommendedYear->tagline); ?></p>
                <?php endif; ?>
                <?php if(Route::has('public.school.year')): ?>
                    <a href="<?php echo e(route('public.school.year', $recommendedYear->slug)); ?>" class="st-pill st-pill--light"><?php echo e(__('student_timeline.open_year_path')); ?></a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(!empty($placement?->admin_notes)): ?>
                <p class="st-event-card__sub st-text-auto" style="margin-top:10px;"><?php echo e($placement->admin_notes); ?></p>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('events'); ?>
<?php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    $game = $game ?? ['weekly_missions' => collect(), 'daily_missions' => collect()];
    $upcoming = $upcoming ?? collect();
    $weekDays = $weekDays ?? collect();
    $eventTones = ['blue', 'orange', 'green'];
    $eventMasks = [
        asset('img/student-timeline/event-mask-1.svg'),
        asset('img/student-timeline/event-mask-2.svg'),
        asset('img/student-timeline/event-mask-3.svg'),
    ];
    $eventCards = collect();
    if (!empty($todayMission)) {
        $eventCards->push((object) [
            'title' => $todayMission->title,
            'subtitle' => $todayMission->subtitle,
            'meta' => $todayMission->starts_at
                ? \App\Support\AppTimezone::formatFor($todayMission->starts_at, $viewerTz, $isRtl ? 'l · g:i A' : 'D · g:i A', $locale)
                : null,
            'url' => $todayMission->is_joinable ? $todayMission->join_url : ($todayMission->class_url ?: '#'),
            'person' => $primaryClass->instructor_name ?? null,
        ]);
    }
    foreach ($upcoming->take(3) as $session) {
        if (!empty($todayMission) && isset($todayMission->session_id) && (int) $todayMission->session_id === (int) $session->id) {
            continue;
        }
        $eventCards->push((object) [
            'title' => method_exists($session, 'displayTitle') ? $session->displayTitle() : ($session->title ?? __('student_timeline.events')),
            'subtitle' => $session->cohort?->title ?: ($session->tutoringGroup?->title ?: ''),
            'meta' => $session->starts_at
                ? \App\Support\AppTimezone::formatFor($session->starts_at, $viewerTz, $isRtl ? 'l · g:i A' : 'D · g:i A', $locale)
                : null,
            'url' => \Illuminate\Support\Facades\Route::has('student.schedule.join')
                ? route('student.schedule.join', ['type' => 'class', 'id' => $session->id])
                : '#',
            'person' => null,
        ]);
        if ($eventCards->count() >= 3) {
            break;
        }
    }
    foreach (($game['weekly_missions'] ?? collect())->take(3) as $mission) {
        if ($eventCards->count() >= 3) {
            break;
        }
        $eventCards->push((object) [
            'title' => $mission->title,
            'subtitle' => $mission->description,
            'meta' => __('student_timeline.xp_reward_meta', ['n' => $mission->xp_reward]),
            'url' => Route::has('student.classes.index') ? route('student.classes.index') : route('dashboard'),
            'person' => null,
        ]);
    }
    $nextDayItems = $weekDays
        ->filter(fn ($day) => empty($day->is_today) && ($day->date?->gt(now()->startOfDay()) ?? false))
        ->flatMap(fn ($day) => $day->items ?? collect())
        ->take(2);
?>

<div class="st-events__top">
    <h2><?php echo e(__('student_timeline.events')); ?></h2>
    <div class="st-events__filter-wrap" data-st-filter>
        <button type="button" class="st-events__filter" id="stEventsFilterBtn" aria-expanded="false" aria-haspopup="true" aria-controls="stEventsFilterMenu">
            <img src="<?php echo e(asset('img/student-timeline/filter.svg')); ?>" alt="" width="14" height="14">
            <span data-st-filter-label><?php echo e(__('student_timeline.filter')); ?></span>
        </button>
        <div class="st-events__filter-menu" id="stEventsFilterMenu" role="menu" hidden>
            <button type="button" role="menuitem" data-st-filter-value="all"><?php echo e(__('student_timeline.filter_all')); ?></button>
            <button type="button" role="menuitem" data-st-filter-value="activities"><?php echo e(__('student_timeline.filter_activities')); ?></button>
            <button type="button" role="menuitem" data-st-filter-value="reminders"><?php echo e(__('student_timeline.filter_reminders')); ?></button>
        </div>
    </div>
</div>

<div class="st-tabs" data-st-tabs>
    <button type="button" class="is-active" data-tab="activities"><?php echo e(__('student_timeline.activities')); ?></button>
    <button type="button" data-tab="reminders"><?php echo e(__('student_timeline.reminders')); ?></button>
</div>

<div data-tab-panel="activities">
    <?php $__empty_1 = true; $__currentLoopData = $eventCards->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e($card->url); ?>" class="st-event-card st-event-card--<?php echo e($eventTones[$i % 3]); ?>">
            <img class="st-event-card__mask" src="<?php echo e($eventMasks[$i % 3]); ?>" alt="" width="160" height="160">
            <h3><?php echo e($card->title); ?></h3>
            <p class="st-event-card__sub"><?php echo e($card->subtitle); ?></p>
            <div class="st-event-card__meta">
                <?php if($card->person): ?>
                    <img src="<?php echo e(asset('img/student-timeline/teacher.png')); ?>" alt="" width="16" height="16">
                    <span><?php echo e($card->person); ?></span>
                <?php else: ?>
                    <img src="<?php echo e(asset('img/student-timeline/location.svg')); ?>" alt="" width="14" height="14">
                    <span><?php echo e($card->meta); ?></span>
                <?php endif; ?>
            </div>
            <?php if($card->person && $card->meta): ?>
                <div class="st-event-card__meta">
                    <img src="<?php echo e(asset('img/student-timeline/clock.png')); ?>" alt="" width="14" height="14">
                    <span><?php echo e($card->meta); ?></span>
                </div>
            <?php endif; ?>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="st-events__empty"><?php echo e(__('student_timeline.no_events')); ?></p>
    <?php endif; ?>
</div>

<div data-tab-panel="reminders" hidden>
    <?php $__empty_1 = true; $__currentLoopData = ($game['daily_missions'] ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e(Route::has('student.classes.index') ? route('student.classes.index') : route('dashboard')); ?>" class="st-event-card st-event-card--orange">
            <img class="st-event-card__mask" src="<?php echo e($eventMasks[2]); ?>" alt="" width="160" height="160">
            <h3><?php echo e($mission->title); ?></h3>
            <p class="st-event-card__sub"><?php echo e($mission->description); ?></p>
            <div class="st-event-card__meta">
                <span><?php echo e($mission->progress); ?>/<?php echo e($mission->target); ?> · <?php echo e(__('student_timeline.xp_reward_meta', ['n' => $mission->xp_reward])); ?></span>
            </div>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="st-events__empty"><?php echo e(__('student_timeline.dont_forget_activities')); ?></p>
    <?php endif; ?>
</div>

<div class="st-events__next" data-st-next-day>
    <h3><?php echo e(__('student_timeline.next_day')); ?></h3>
    <?php $__empty_1 = true; $__currentLoopData = $nextDayItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <a href="<?php echo e($slot->join_url ?: '#'); ?>" class="st-event-card st-event-card--<?php echo e($eventTones[($i + 1) % 3]); ?>" style="min-height:110px;margin-bottom:10px;">
            <img class="st-event-card__mask" src="<?php echo e($eventMasks[($i + 1) % 3]); ?>" alt="" width="140" height="140">
            <h3><?php echo e($slot->title); ?></h3>
            <div class="st-event-card__meta">
                <img src="<?php echo e(asset('img/student-timeline/clock.png')); ?>" alt="" width="14" height="14">
                <span><?php echo e($slot->starts_at ? \App\Support\AppTimezone::formatFor($slot->starts_at, $viewerTz, 'g:i A', $locale) : '—'); ?></span>
            </div>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="st-events__empty"><?php echo e(__('student_timeline.no_events')); ?></p>
    <?php endif; ?>
</div>

<div class="st-events__see">
    <a href="<?php echo e(Route::has('student.classes.index') ? route('student.classes.index') : route('dashboard')); ?>"><?php echo e(__('student_timeline.see_all')); ?></a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var labels = {
        all: <?php echo json_encode(__('student_timeline.filter_all'), 15, 512) ?>,
        activities: <?php echo json_encode(__('student_timeline.filter_activities'), 15, 512) ?>,
        reminders: <?php echo json_encode(__('student_timeline.filter_reminders'), 15, 512) ?>,
        default: <?php echo json_encode(__('student_timeline.filter'), 15, 512) ?>
    };
    var wrap = document.querySelector('[data-st-filter]');
    var btn = document.getElementById('stEventsFilterBtn');
    var menu = document.getElementById('stEventsFilterMenu');
    var labelEl = wrap ? wrap.querySelector('[data-st-filter-label]') : null;
    var nextDay = document.querySelector('[data-st-next-day]');
    var tabBtns = document.querySelectorAll('[data-st-tabs] button');
    var panels = document.querySelectorAll('[data-tab-panel]');
    var current = 'all';

    function setTab(tab) {
        tabBtns.forEach(function (b) {
            b.classList.toggle('is-active', b.getAttribute('data-tab') === tab);
        });
        panels.forEach(function (panel) {
            var name = panel.getAttribute('data-tab-panel');
            if (current === 'all') {
                panel.hidden = name !== 'activities';
            } else {
                panel.hidden = name !== tab;
            }
        });
        if (nextDay) {
            nextDay.hidden = current === 'reminders';
        }
    }

    function applyFilter(value) {
        current = value;
        if (labelEl) {
            labelEl.textContent = labels[value] || labels.default;
        }
        if (menu) {
            menu.querySelectorAll('[data-st-filter-value]').forEach(function (item) {
                item.classList.toggle('is-active', item.getAttribute('data-st-filter-value') === value);
            });
        }
        setTab(value === 'reminders' ? 'reminders' : 'activities');
        closeMenu();
    }

    function closeMenu() {
        if (!menu || !btn) return;
        menu.hidden = true;
        btn.setAttribute('aria-expanded', 'false');
    }

    function openMenu() {
        if (!menu || !btn) return;
        menu.hidden = false;
        btn.setAttribute('aria-expanded', 'true');
    }

    tabBtns.forEach(function (tabBtn) {
        tabBtn.addEventListener('click', function () {
            var tab = tabBtn.getAttribute('data-tab');
            current = tab;
            if (labelEl) {
                labelEl.textContent = labels[tab] || labels.default;
            }
            if (menu) {
                menu.querySelectorAll('[data-st-filter-value]').forEach(function (item) {
                    item.classList.toggle('is-active', item.getAttribute('data-st-filter-value') === tab);
                });
            }
            setTab(tab);
            closeMenu();
        });
    });

    if (btn && menu) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (menu.hidden) openMenu();
            else closeMenu();
        });
        menu.querySelectorAll('[data-st-filter-value]').forEach(function (item) {
            item.addEventListener('click', function () {
                applyFilter(item.getAttribute('data-st-filter-value'));
            });
        });
        document.addEventListener('click', function (e) {
            if (wrap && !wrap.contains(e.target)) closeMenu();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeMenu();
        });
    }
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.student-timeline', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/student/school/home.blade.php ENDPATH**/ ?>