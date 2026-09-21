<?php $__env->startSection('title', __('instructor.profile') . ' - ' . config('app.name')); ?>
<?php $__env->startSection('page_title', __('instructor.profile')); ?>

<?php $__env->startSection('content'); ?>
<?php
    $user = auth()->user();
    $memberSince = $user->created_at
        ? $user->created_at->copy()->locale(app()->getLocale())->translatedFormat('d F Y')
        : '—';
    $myCoursesCount = \App\Models\AdvancedCourse::where('instructor_id', $user->id)->count();
    $totalStudents = \App\Models\StudentCourseEnrollment::whereHas('course', function ($q) use ($user) {
        $q->where('instructor_id', $user->id);
    })->where('status', 'active')->distinct('user_id')->count();
    $lastLogin = $user->last_login_at
        ? $user->last_login_at->copy()->locale(app()->getLocale())->diffForHumans()
        : '—';
    $isRtl = app()->getLocale() === 'ar';
    $subjMask1 = asset('img/student-timeline/subj-mask-1.svg');
    $subjMask2 = asset('img/student-timeline/subj-mask-2.svg');
    $initial = mb_substr($user->name ?? 'م', 0, 1);
    $tzOptions = \App\Support\AppTimezone::commonZones();
    $tzCurrent = old('timezone', $user->timezone ?: \App\Support\AppTimezone::academy());
    if ($tzCurrent && ! array_key_exists($tzCurrent, $tzOptions)) {
        $tzOptions = [$tzCurrent => $tzCurrent] + $tzOptions;
    }
?>

<section class="st-join-hero st-profile-hero" aria-label="<?php echo e(__('instructor.profile')); ?>">
    <div class="st-profile-hero__identity">
        <div class="st-profile-hero__avatar" aria-hidden="true">
            <?php if($user->profile_image): ?>
                <img src="<?php echo e($user->profile_image_url); ?>" alt=""
                     onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('is-hidden');">
                <span class="is-hidden"><?php echo e($initial); ?></span>
            <?php else: ?>
                <span><?php echo e($initial); ?></span>
            <?php endif; ?>
        </div>
        <div class="st-join-hero__copy">
            <p class="st-join-hero__kicker">TADRIS LAB · <?php echo e(__('instructor.instructor_role')); ?></p>
            <h2 class="st-join-hero__title"><?php echo e($user->name); ?></h2>
            <p class="st-join-hero__meta">
                <?php if($user->email): ?> <?php echo e($user->email); ?> <?php endif; ?>
                <?php if($user->email && $user->phone): ?> · <?php endif; ?>
                <?php if($user->phone): ?> <?php echo e($user->phone); ?> <?php endif; ?>
            </p>
        </div>
    </div>
    <div class="st-join-hero__actions">
        <?php if(Route::has('instructor.courses.index')): ?>
            <a href="<?php echo e(route('instructor.courses.index')); ?>" class="st-pill st-pill--outline"><?php echo e(__('instructor.my_courses')); ?></a>
        <?php endif; ?>
        <a href="<?php echo e(route('dashboard')); ?>" class="st-pill st-pill--solid"><?php echo e(__('instructor.back_to_dashboard')); ?></a>
    </div>
</section>

<section class="st-stats st-stats--classes" aria-label="<?php echo e(__('instructor.profile')); ?>">
    <article class="st-subject st-subject--blue st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask1); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e(__('instructor.my_courses')); ?></p>
        <p class="st-stat-card__value"><?php echo e(number_format($myCoursesCount)); ?></p>
        <p class="st-stat-card__hint"><?php echo e($isRtl ? 'مسندة إليك' : 'Assigned to you'); ?></p>
    </article>
    <article class="st-subject st-subject--pink st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask2); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e(__('instructor.students')); ?></p>
        <p class="st-stat-card__value"><?php echo e(number_format($totalStudents)); ?></p>
        <p class="st-stat-card__hint"><?php echo e($isRtl ? 'تسجيلات نشطة' : 'Active enrollments'); ?></p>
    </article>
    <article class="st-subject st-subject--orange st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask1); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e(__('instructor.join_date')); ?></p>
        <p class="st-stat-card__value st-stat-card__value--text"><?php echo e($memberSince); ?></p>
        <p class="st-stat-card__hint"><?php echo e($isRtl ? 'مع المنصة' : 'On the platform'); ?></p>
    </article>
    <article class="st-subject st-subject--purple st-stat-card">
        <img class="st-subject__blob" src="<?php echo e($subjMask2); ?>" alt="" width="132" height="132">
        <p class="st-stat-card__label"><?php echo e(__('instructor.last_login')); ?></p>
        <p class="st-stat-card__value st-stat-card__value--text"><?php echo e($lastLogin); ?></p>
        <p class="st-stat-card__hint"><?php echo e($isRtl ? 'من هذا الجهاز' : 'Recent activity'); ?></p>
    </article>
</section>

<?php if(session('success')): ?>
    <section class="st-panel st-lib-note st-lib-note--ok" role="status">
        <i class="fas fa-check-circle" aria-hidden="true"></i>
        <p><?php echo e(session('success')); ?></p>
    </section>
<?php endif; ?>

<div class="st-course-layout st-profile-layout">
    <div class="st-profile-side">
        <section class="st-panel">
            <div class="st-section-head">
                <h2><?php echo e(__('instructor.account_info')); ?></h2>
                <p><?php echo e(__('instructor.manage_profile_data')); ?></p>
            </div>
            <dl class="st-course-dl">
                <div>
                    <dt><?php echo e(__('instructor.membership_number')); ?></dt>
                    <dd>#<?php echo e(str_pad((string) $user->id, 5, '0', STR_PAD_LEFT)); ?></dd>
                </div>
                <div>
                    <dt><?php echo e(__('instructor.account_type')); ?></dt>
                    <dd><?php echo e(__('instructor.instructor_role')); ?></dd>
                </div>
                <div>
                    <dt><?php echo e(__('common.status')); ?></dt>
                    <dd>
                        <span class="st-course-row__chip <?php echo e($user->is_active ? 'is-ok' : 'is-off'); ?>">
                            <?php echo e($user->is_active ? __('instructor.active_status') : __('instructor.not_active')); ?>

                        </span>
                    </dd>
                </div>
            </dl>
        </section>

        <?php if($user->isAcademyWorkingInstructor()): ?>
            <section class="st-panel">
                <div class="st-section-head">
                    <h2><?php echo e(__('instructor.your_libraries')); ?></h2>
                    <p><?php echo e(__('instructor.your_libraries_desc')); ?></p>
                </div>
                <div class="st-profile-links">
                    <?php if(Route::has('instructor.libraries.materials.index')): ?>
                        <a href="<?php echo e(route('instructor.libraries.materials.index')); ?>" class="st-profile-link">
                            <span><i class="fas fa-file-upload" aria-hidden="true"></i> <?php echo e(__('instructor.upload_materials')); ?></span>
                            <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                    <?php if(Route::has('instructor.libraries.curriculum.index')): ?>
                        <a href="<?php echo e(route('instructor.libraries.curriculum.index')); ?>" class="st-profile-link">
                            <span><i class="fas fa-book-open" aria-hidden="true"></i> <?php echo e(__('instructor.view_academy_curriculum')); ?></span>
                            <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                    <?php if(Route::has('instructor.courses.index')): ?>
                        <a href="<?php echo e(route('instructor.courses.index')); ?>" class="st-profile-link">
                            <span><i class="fas fa-layer-group" aria-hidden="true"></i> <?php echo e(__('instructor.build_course_curriculum')); ?></span>
                            <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                    <?php if(Route::has('instructor.libraries.videos.index')): ?>
                        <a href="<?php echo e(route('instructor.libraries.videos.index')); ?>" class="st-profile-link">
                            <span><i class="fas fa-video" aria-hidden="true"></i> <?php echo e(__('instructor.video_library')); ?></span>
                            <i class="fas fa-arrow-<?php echo e($isRtl ? 'left' : 'right'); ?>" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <section class="st-panel">
            <div class="st-section-head">
                <h2><?php echo e(__('instructor.tips_for_instructor')); ?></h2>
            </div>
            <ul class="st-profile-tips">
                <li>
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                    <div>
                        <strong><?php echo e(__('instructor.update_bio')); ?></strong>
                        <span><?php echo e(__('instructor.add_bio_for_students')); ?></span>
                    </div>
                </li>
                <li>
                    <i class="fas fa-lock" aria-hidden="true"></i>
                    <div>
                        <strong><?php echo e(__('instructor.strong_password')); ?></strong>
                        <span><?php echo e(__('instructor.change_password_regularly')); ?></span>
                    </div>
                </li>
            </ul>
        </section>
    </div>

    <section class="st-panel st-profile-form-panel">
        <div class="st-section-head">
            <h2><?php echo e(__('instructor.update_data')); ?></h2>
            <p><?php echo e(__('instructor.update_data_subtitle')); ?></p>
        </div>

        <form method="POST" action="<?php echo e(route('instructor.profile.update')); ?>" enctype="multipart/form-data" class="st-profile-form">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="st-profile-form__grid">
                <div class="st-course-filters__field">
                    <label for="name"><?php echo e(__('instructor.full_name')); ?></label>
                    <input type="text" name="name" id="name" value="<?php echo e(old('name', $user->name)); ?>" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="st-profile-form__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="st-course-filters__field">
                    <label for="phone"><?php echo e(__('instructor.phone')); ?></label>
                    <input type="text" name="phone" id="phone" value="<?php echo e(old('phone', $user->phone)); ?>" required>
                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="st-profile-form__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="st-course-filters__field st-profile-form__span">
                    <label for="email"><?php echo e(__('instructor.email_optional')); ?></label>
                    <input type="email" name="email" id="email" value="<?php echo e(old('email', $user->email)); ?>">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="st-profile-form__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="st-course-filters__field st-profile-form__span">
                    <label for="timezone"><?php echo e(__('instructor.timezone')); ?></label>
                    <select name="timezone" id="timezone" data-timezone-select required>
                        <?php $__currentLoopData = $tzOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tzId => $tzLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tzId); ?>" <?php if($tzCurrent === $tzId): echo 'selected'; endif; ?>><?php echo e($tzLabel); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span class="st-profile-form__hint"><?php echo e(__('instructor.timezone_hint')); ?></span>
                    <?php $__errorArgs = ['timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="st-profile-form__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="st-course-filters__field st-profile-form__span">
                    <label for="bio"><?php echo e(__('instructor.bio_optional')); ?></label>
                    <textarea name="bio" id="bio" rows="4" placeholder="<?php echo e(__('instructor.bio_placeholder_short')); ?>"><?php echo e(old('bio', $user->bio)); ?></textarea>
                    <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="st-profile-form__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="st-course-filters__field st-profile-form__span">
                    <label><?php echo e(__('instructor.profile_image')); ?></label>
                    <div class="st-profile-upload">
                        <div class="st-profile-upload__preview" aria-hidden="true">
                            <?php if($user->profile_image): ?>
                                <img src="<?php echo e($user->profile_image_url); ?>" alt=""
                                     onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('is-hidden');">
                                <i class="fas fa-user is-hidden"></i>
                            <?php else: ?>
                                <i class="fas fa-user"></i>
                            <?php endif; ?>
                        </div>
                        <label class="st-pill st-pill--outline st-profile-upload__btn">
                            <i class="fas fa-upload" aria-hidden="true"></i>
                            <?php echo e(__('instructor.choose_image_label')); ?>

                            <input type="file" name="profile_image" accept="image/*" class="sr-only">
                        </label>
                    </div>
                    <?php $__errorArgs = ['profile_image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="st-profile-form__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="st-profile-password">
                <h3><?php echo e(__('instructor.change_password')); ?></h3>
                <p><?php echo e(__('instructor.leave_empty_if_no_change')); ?></p>
                <div class="st-profile-form__grid st-profile-form__grid--3">
                    <div class="st-course-filters__field">
                        <label for="current_password"><?php echo e(__('instructor.current_password')); ?></label>
                        <input type="password" name="current_password" id="current_password" autocomplete="current-password">
                        <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="st-profile-form__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="st-course-filters__field">
                        <label for="password"><?php echo e(__('instructor.new_password')); ?></label>
                        <input type="password" name="password" id="password" autocomplete="new-password">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="st-profile-form__error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="st-course-filters__field">
                        <label for="password_confirmation"><?php echo e(__('instructor.confirm_password')); ?></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password">
                    </div>
                </div>
            </div>

            <div class="st-profile-form__actions">
                <a href="<?php echo e(route('dashboard')); ?>" class="st-pill st-pill--outline"><?php echo e(__('instructor.back_to_dashboard')); ?></a>
                <button type="submit" class="st-pill st-pill--solid">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    <?php echo e(__('instructor.save_changes')); ?>

                </button>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.instructor-timeline', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/cityphone/Documents/tadris lab/resources/views/instructor/profile/index.blade.php ENDPATH**/ ?>