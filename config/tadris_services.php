<?php

/**
 * Service page interconnection map — TADRIS LAB
 *
 * Actors:
 * - individual_user / teacher learner = المعلم (متلقّي الخدمة) — ليس «طالب لغة»
 * - instructor (مدرب) = يقدّم كورس موصّف و/أو خدمة من الركائز
 * - packages = تجمع وصول المسارات والأدوات والاستشارات والمقاعد
 *
 * Soft links only (no hard schema FKs yet). Flip/extend freely.
 */
return [

    'actors' => [
        'learner' => [
            'role_keys' => ['individual_user', 'teacher_participant', 'student'], // student = legacy DB role, CX = معلم
            'label_ar' => 'المعلم',
            'label_en' => 'Teacher',
            'cx_note_ar' => 'حساب المتعلّم المهني على المنصة — يستكشف الخدمات ويشترك عبر الباقات.',
        ],
        'instructor' => [
            'role_keys' => ['instructor', 'teacher'],
            'label_ar' => 'المدرب',
            'label_en' => 'Instructor',
            'cx_note_ar' => 'يُفعَّل لتدريس كورسات موصّفة و/أو تقديم خدمات (مسارات، استشارات، ورش، أدوات) ضمن صلاحياته.',
        ],
    ],

    /*
    | pageKey => interconnection
    */
    'pages' => [
        'teacher-development' => [
            'pillar' => 'learning_paths',
            'package_types' => ['free', 'individual', 'advanced', 'school_institution'],
            'related' => ['teacher-paths', 'teacher-courses', 'teacher-resources', 'assessment'],
            'catalog_route' => 'public.courses',
            'delivery' => 'course',
        ],
        'teacher-paths' => [
            'pillar' => 'learning_paths',
            'package_types' => ['individual', 'advanced', 'school_institution'],
            'related' => ['teacher-courses', 'resources', 'assessment', 'consultations-teachers'],
            'catalog_route' => 'public.learning-paths.index',
            'delivery' => 'course',
        ],
        'teacher-courses' => [
            'pillar' => 'learning_paths',
            'package_types' => ['individual', 'advanced', 'school_institution'],
            'related' => ['teacher-paths', 'workshops', 'resources'],
            'catalog_route' => 'public.courses',
            'delivery' => 'course',
        ],
        'teacher-resources' => [
            'pillar' => 'tools_resources',
            'package_types' => ['individual', 'advanced', 'school_institution'],
            'related' => ['resources', 'resources-tools', 'teacher-paths'],
            'catalog_route' => 'public.site.resources',
            'delivery' => 'tools',
        ],
        'consultations' => [
            'pillar' => 'consultations',
            'package_types' => ['advanced', 'school_institution', 'custom'],
            'related' => ['consultations-teachers', 'consultations-specialized', 'assessment'],
            'catalog_route' => 'public.pricing',
            'delivery' => 'consultation',
        ],
        'consultations-teachers' => [
            'pillar' => 'consultations',
            'package_types' => ['advanced', 'individual', 'custom'],
            'related' => ['consultations-specialized', 'teacher-paths', 'assessment-diagnosis'],
            'catalog_route' => 'public.pricing',
            'delivery' => 'consultation',
        ],
        'consultations-specialized' => [
            'pillar' => 'consultations',
            'package_types' => ['school_institution', 'custom', 'advanced'],
            'related' => ['consultations-teachers', 'institutional-solutions'],
            'catalog_route' => 'public.contact',
            'delivery' => 'consultation',
        ],
        'institutional' => [
            'pillar' => 'schools_institutions',
            'package_types' => ['school_institution', 'custom'],
            'related' => ['institutional-schools', 'institutional-training', 'institutional-solutions'],
            'catalog_route' => 'public.contact',
            'delivery' => 'institutional',
        ],
        'institutional-schools' => [
            'pillar' => 'schools_institutions',
            'package_types' => ['school_institution', 'custom'],
            'related' => ['institutional-training', 'consultations-specialized'],
            'catalog_route' => 'public.contact',
            'delivery' => 'institutional',
        ],
        'institutional-training' => [
            'pillar' => 'schools_institutions',
            'package_types' => ['school_institution', 'custom'],
            'related' => ['teacher-courses', 'workshops', 'institutional-schools'],
            'catalog_route' => 'public.contact',
            'delivery' => 'institutional',
        ],
        'institutional-solutions' => [
            'pillar' => 'schools_institutions',
            'package_types' => ['custom', 'school_institution'],
            'related' => ['consultations-specialized', 'institutional-schools'],
            'catalog_route' => 'public.contact',
            'delivery' => 'institutional',
        ],
        'workshops' => [
            'pillar' => 'learning_paths',
            'package_types' => ['individual', 'advanced', 'school_institution'],
            'related' => ['teacher-courses', 'teacher-paths'],
            'catalog_route' => 'public.courses',
            'delivery' => 'course',
        ],
        'resources' => [
            'pillar' => 'tools_resources',
            'package_types' => ['free', 'individual', 'advanced', 'school_institution'],
            'related' => ['resources-materials', 'resources-templates', 'resources-tools'],
            'catalog_route' => 'public.pricing',
            'delivery' => 'tools',
        ],
        'resources-materials' => [
            'pillar' => 'tools_resources',
            'package_types' => ['individual', 'advanced'],
            'related' => ['resources-templates', 'resources-tools', 'teacher-paths'],
            'catalog_route' => 'public.pricing',
            'delivery' => 'tools',
        ],
        'resources-templates' => [
            'pillar' => 'tools_resources',
            'package_types' => ['individual', 'advanced'],
            'related' => ['resources-tools', 'resources-materials'],
            'catalog_route' => 'public.pricing',
            'delivery' => 'tools',
        ],
        'resources-tools' => [
            'pillar' => 'tools_resources',
            'package_types' => ['individual', 'advanced', 'school_institution'],
            'related' => ['assessment', 'teacher-paths'],
            'catalog_route' => 'public.pricing',
            'delivery' => 'tools',
        ],
        'assessment' => [
            'pillar' => 'learning_paths',
            'package_types' => ['free', 'individual', 'advanced'],
            'related' => ['assessment-diagnosis', 'assessment-recommendations', 'teacher-paths'],
            'catalog_route' => 'public.path',
            'delivery' => 'course',
        ],
        'assessment-diagnosis' => [
            'pillar' => 'learning_paths',
            'package_types' => ['free', 'individual', 'advanced'],
            'related' => ['assessment-recommendations', 'consultations-teachers'],
            'catalog_route' => 'public.path',
            'delivery' => 'course',
        ],
        'assessment-recommendations' => [
            'pillar' => 'learning_paths',
            'package_types' => ['individual', 'advanced'],
            'related' => ['teacher-paths', 'consultations-teachers', 'resources'],
            'catalog_route' => 'public.learning-paths.index',
            'delivery' => 'course',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Grantable services for instructor activation (admin allowlist)
    |--------------------------------------------------------------------------
    */
    'grantable_services' => [
        'learning_paths' => [
            'label_ar' => 'المسارات التعليمية',
            'label_en' => 'Learning Paths',
            'hint_ar' => 'تدريس/إدارة مسارات تطوير مهني موصّفة',
        ],
        'courses' => [
            'label_ar' => 'الكورسات والورش',
            'label_en' => 'Courses & workshops',
            'hint_ar' => 'تدريس كورسات لها توصيف واضح في الكتالوج',
        ],
        'consultations' => [
            'label_ar' => 'الاستشارات',
            'label_en' => 'Consultations',
            'hint_ar' => 'جلسات استشارة للمعلمين أو المؤسسات',
        ],
        'tools_resources' => [
            'label_ar' => 'الأدوات والموارد',
            'label_en' => 'Tools & resources',
            'hint_ar' => 'إعداد وربط قوالب وأدوات صفية',
        ],
        'schools_institutions' => [
            'label_ar' => 'برامج المدارس والمؤسسات',
            'label_en' => 'Schools & institutions programs',
            'hint_ar' => 'تدريب أو حلول لعدة مشاركين تحت جهة',
        ],
        'assessment' => [
            'label_ar' => 'التشخيص والتقييم',
            'label_en' => 'Assessment & diagnosis',
            'hint_ar' => 'دعم تشخيص احتياجات التطوير المهني',
        ],
        'workshops' => [
            'label_ar' => 'ورش مكثّفة',
            'label_en' => 'Intensive workshops',
            'hint_ar' => 'ورش قصيرة بمخرجات تطبيقية',
        ],
    ],
];
