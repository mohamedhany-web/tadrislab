<?php

/**
 * واجهة المعلم (متلقّي الخدمة) — تدريس لاب.
 * إخفاء إرث طالب اللغة / الخصوصي دون حذف البيانات.
 */
return [
    // إرث كورسات لغة / طالب
    'show_courses' => false,
    'show_exams' => false,
    'show_certificates' => false,
    'show_achievements' => false,
    'show_wallet' => false,
    'show_invoices' => false,
    'show_orders' => true,
    'show_referrals' => false,
    'show_consultations' => true,
    'show_legacy_calendar' => false,
    'show_course_progress' => false,
    'show_live_broadcast' => false,
    'show_support' => true,

    // إرث مجموعات / مدرسة خصوصي
    'show_school' => false,
    'show_classes' => false,
    'show_private_lessons' => false,
    'show_assignments' => false,
    'show_libraries' => false,

    // نواة تدريس لاب
    'show_notifications' => true,
    'show_profile' => true,
    'show_settings' => true,
    'show_entitlements' => false, // replaced by packages
    'show_learning_paths' => true,
    'show_tools' => true,
    'show_packages' => true,
    'show_pricing' => true,
    'show_teacher_assistant' => true,
    'show_institution_portal' => true,

    'reminder_minutes' => 30,
];
