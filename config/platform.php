<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Product identity — TADRIS LAB (تدريس لاب)
    |--------------------------------------------------------------------------
    | Functional SoT: .cursor/skills/tadris-lab-platform/developer-brief-v3.md
    | MVP notes:     .cursor/skills/tadris-lab-platform/mvp-scope.md
    */
    'product' => [
        'name' => 'TADRIS LAB',
        'name_ar' => 'تدريس لاب',
        'tagline_ar' => 'منصة مخصصة قابلة للتوسع لتطوير الممارسات المهنية للمعلمين',
        'tagline_en' => 'Custom modular platform for teacher professional practice development',
        'phase' => 'mvp',
        'architecture' => 'custom_modular',
        'brief_version' => 'v3',
        'principles' => [
            'no_hardcoded_catalog',
            'modular_expandable',
            'content_separated_from_logic',
            'mvp_first',
            'schools_institutions_single_pillar',
            'payment_backend_webhooks',
            'whatsapp_operational',
            'admin_managed_catalog',
            'ai_ready_contracts',
        ],
        // Brief §16 checklist runtime map: config/expandability.php
        'expandability_map' => 'expandability',
    ],

    /*
    |--------------------------------------------------------------------------
    | Five commercial pillars (Developer Brief V3 §2)
    |--------------------------------------------------------------------------
    */
    'pillars' => [
        'learning_paths' => [
            'label_ar' => 'المسارات التعليمية',
            'label_en' => 'Learning Paths',
        ],
        'consultations' => [
            'label_ar' => 'الاستشارات',
            'label_en' => 'Consultations',
        ],
        'schools_institutions' => [
            'label_ar' => 'المدارس والمؤسسات',
            'label_en' => 'Schools & Institutions',
            'note' => 'Single commercial axis — do not split schools vs institutions',
        ],
        'packages' => [
            'label_ar' => 'الباقات',
            'label_en' => 'Packages',
        ],
        'tools_resources' => [
            'label_ar' => 'الأدوات والموارد',
            'label_en' => 'Tools & Resources',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles (Brief V3 §8) — map onto RBAC when implementing
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'platform_admin' => [
            'label_ar' => 'مدير المنصة',
            'label_en' => 'Platform Admin',
            'primary' => false,
        ],
        'institution_admin' => [
            'label_ar' => 'مسؤول / منسق مدرسة أو مؤسسة',
            'label_en' => 'Institution/School Admin or Coordinator',
            'primary' => false,
        ],
        'teacher_participant' => [
            'label_ar' => 'معلم / مشارك تابع للجهة',
            'label_en' => 'Teacher / Participant',
            'primary' => false,
        ],
        'individual_user' => [
            'label_ar' => 'مستخدم فردي (معلم/ممارس)',
            'label_en' => 'Individual User',
            'primary' => true,
        ],
    ],

    'client_kinds' => [
        'individual_user' => [
            'label_ar' => 'مستخدم فردي',
            'label_en' => 'Individual user',
        ],
        'institution_user' => [
            'label_ar' => 'مستخدم تابع لمدرسة/مؤسسة',
            'label_en' => 'Institution/school user',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Learning path catalogue examples (admin-managed content)
    |--------------------------------------------------------------------------
    */
    'learning_paths' => [
        'classroom_management' => [
            'en' => 'Classroom Management',
            'ar' => 'إدارة الصف',
        ],
        'teaching_strategies' => [
            'en' => 'Teaching Strategies',
            'ar' => 'استراتيجيات التدريس',
        ],
        'lesson_time_management' => [
            'en' => 'Lesson Time Management',
            'ar' => 'إدارة وقت الحصة',
        ],
        'lesson_planning' => [
            'en' => 'Lesson Planning',
            'ar' => 'التخطيط للحصة',
        ],
        'assessment' => [
            'en' => 'Assessment',
            'ar' => 'التقويم وقياس التعلم',
        ],
        'teacher_skills' => [
            'en' => 'Teacher Skills',
            'ar' => 'مهارات المعلم',
        ],
        'professional_development' => [
            'en' => 'Professional Development',
            'ar' => 'التطوير المهني للمعلم',
        ],
    ],

    'path_structure' => [
        'path',
        'modules_units',
        'lessons_content',
        'practical_tools',
        'activity_assessment',
        'progress_completion',
    ],

    /*
    |--------------------------------------------------------------------------
    | Consultations (independent of paths)
    |--------------------------------------------------------------------------
    */
    'consultations' => [
        'types' => [
            'teacher_individual' => ['ar' => 'استشارة فردية للمعلم', 'en' => 'Individual teacher consultation'],
            'specialized' => ['ar' => 'استشارات تربوية متخصصة', 'en' => 'Specialized educational consultations'],
            'institution' => ['ar' => 'استشارات للمدارس والمؤسسات', 'en' => 'School/institution consultations'],
            'coaching_mentoring' => ['ar' => 'توجيه / إرشاد (مستقبلاً)', 'en' => 'Coaching / Mentoring (later)', 'mvp' => false],
        ],
        'booking_statuses' => ['new', 'confirmed', 'rescheduled', 'completed', 'cancelled'],
        'journey' => [
            'choose_type',
            'show_service_duration_price',
            'choose_slot',
            'enter_data',
            'payment',
            'confirm_booking',
            'notify_whatsapp_email',
            'deliver_session',
            'record_outcome',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Schools & Institutions — services under one pillar
    |--------------------------------------------------------------------------
    */
    'schools_institutions' => [
        'services' => [
            'school_institutional_training' => ['en' => 'School & Institutional Training', 'ar' => 'تدريب المدارس والمؤسسات'],
            'pd_programs' => ['en' => 'Professional Development Programs', 'ar' => 'برامج التطوير المهني'],
            'workshops' => ['en' => 'Workshops', 'ar' => 'ورش العمل'],
            'customized_training' => ['en' => 'Customized Training', 'ar' => 'التدريب المخصص حسب الاحتياج'],
            'educational_leadership_training' => ['en' => 'Educational Leadership Training', 'ar' => 'تدريب القيادات التعليمية'],
            'needs_assessment' => ['en' => 'Needs Assessment', 'ar' => 'تحليل الاحتياج'],
            'institutional_development' => ['en' => 'Institutional Development', 'ar' => 'التطوير المؤسسي'],
            'followup_evaluation' => ['en' => 'Follow-up & Evaluation', 'ar' => 'المتابعة والتقييم'],
        ],
        /*
        | مساران للتعاقد ضمن نفس الركيزة (لا فرع مدارس vs مؤسسات):
        | 1) direct_delivery — تعاقد مباشر: المنصة تعيّن مدربًا/منفّذًا يقدّم الخدمة للجهة
        | 2) platform_access — تعاقد منصة: مقاعد للمشاركين + لوحة منسق الجهة (تفعيل/تقدّم/تقارير)
        */
        'engagement_modes' => [
            'direct_delivery' => [
                'key' => 'direct_delivery',
                'label_ar' => 'تعاقد مباشر (تنفيذ عبر مدرب)',
                'label_en' => 'Direct delivery (coach-led)',
                'admin_focus' => ['academy_instructors', 'consultations', 'crm_leads'],
                'note_ar' => 'الجهة تتعاقد عبر المنصة؛ التنفيذ يتم بواسطة مدرب/منفّذ معيَّن — بدون لوحة مقاعد للمشاركين بالضرورة.',
            ],
            'platform_access' => [
                'key' => 'platform_access',
                'label_ar' => 'تعاقد منصة (مقاعد + متابعة الجهة)',
                'label_en' => 'Platform access (seats + org portal)',
                'admin_focus' => ['institutions', 'programs', 'participants', 'progress', 'reports'],
                'note_ar' => 'الجهة تحصل على حساب ومنسق ولوحة ترى المشاركين المفعّلين وتقدّمهم وتقاريرهم.',
            ],
        ],
        'training_program_statuses' => [
            'inquiry', 'proposal', 'approved', 'scheduled', 'in_progress', 'completed',
        ],
        'account_model' => [
            'institution_account',
            'coordinator_admin',
            'teachers_participants',
            'programs',
            'training_development',
            'progress',
            'reports',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Packages (not hard-coded)
    |--------------------------------------------------------------------------
    */
    'packages' => [
        'types' => [
            'free' => ['en' => 'Free Package', 'ar' => 'باقة مجانية'],
            'individual' => ['en' => 'Individual Package', 'ar' => 'باقة فردية'],
            'advanced' => ['en' => 'Advanced Package', 'ar' => 'باقة متقدمة'],
            'school_institution' => ['en' => 'School & Institution Package', 'ar' => 'باقة مدارس ومؤسسات'],
            'custom' => ['en' => 'Custom Package', 'ar' => 'باقة مخصصة'],
        ],
        'admin_fields' => [
            'name', 'description', 'price', 'currency', 'validity_period',
            'included_paths', 'tools_resources', 'consultation_sessions',
            'participant_seats', 'benefits_discounts', 'status',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tools & Resources entity types
    |--------------------------------------------------------------------------
    */
    'tools_resources' => [
        'types' => [
            'templates' => ['en' => 'Templates', 'ar' => 'نماذج'],
            'checklists' => ['en' => 'Checklists', 'ar' => 'قوائم تحقق'],
            'classroom_tools' => ['en' => 'Classroom Tools', 'ar' => 'أدوات صفية'],
            'planning_tools' => ['en' => 'Planning Tools', 'ar' => 'أدوات التخطيط'],
            'assessment_resources' => ['en' => 'Assessment Resources', 'ar' => 'موارد التقويم'],
            'downloadable' => ['en' => 'Downloadable Resources', 'ar' => 'موارد قابلة للتحميل'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Teacher Assistant — AI-ready candidate (prior brief; not a V3 pillar)
    |--------------------------------------------------------------------------
    */
    'teacher_assistant' => [
        'pillar' => 'tools_resources',
        'inputs' => ['lesson_name', 'subject_optional', 'grade_optional'],
        'outputs' => [
            'warmup' => ['ar' => 'التهيئة', 'en' => 'Warm-up'],
            'opener' => ['ar' => 'فكرة افتتاح الدرس', 'en' => 'Lesson opener'],
            'strategy' => ['ar' => 'استراتيجية مناسبة', 'en' => 'Suitable strategy'],
            'activity' => ['ar' => 'نشاط تفاعلي', 'en' => 'Interactive activity'],
            'check_understanding' => ['ar' => 'أسئلة للتحقق من الفهم', 'en' => 'Check-for-understanding questions'],
            'assessment_idea' => ['ar' => 'فكرة للتقويم', 'en' => 'Assessment idea'],
            'closure' => ['ar' => 'خاتمة للدرس', 'en' => 'Lesson closure'],
        ],
        'ai_ready' => true,
        'ai_provider' => env('TADRIS_TA_AI_PROVIDER', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment, WhatsApp, inquiries, notifications
    |--------------------------------------------------------------------------
    */
    'payment' => [
        'flow' => [
            'select_product', 'checkout', 'payment_gateway',
            'backend_verify_webhook', 'create_order_transaction',
            'activate_entitlement_or_booking', 'send_confirmation',
        ],
        'markets' => ['qatar', 'gcc'],
        'features' => [
            'visa_mastercard', 'apple_pay_when_available', 'mada_study_if_ksa',
            'recurring_if_subscriptions', 'refunds', 'failed_cancelled_handling',
            'link_to_user_org_order', 'swappable_gateway',
        ],
        'gateway' => env('TADRIS_PAYMENT_GATEWAY', null),
    ],

    'whatsapp' => [
        'operational' => true,
        'uses' => [
            'payment_success_fail',
            'consultation_confirm_reminder',
            'institution_program_updates',
            'inquiry_entry',
        ],
        'provider_swappable' => true,
        'provider' => env('TADRIS_WHATSAPP_PROVIDER', null),
    ],

    'inquiries' => [
        'statuses' => ['new', 'in_progress', 'resolved'],
        'types' => [
            'learning_path', 'package', 'consultation', 'payment',
            'technical', 'school_institution', 'general',
        ],
        'fields' => [
            'name_contact', 'type', 'user_id', 'order_booking_institution_id',
            'date', 'status', 'admin_notes',
        ],
    ],

    'notifications' => [
        'channels_mvp' => ['whatsapp', 'email'],
        'channels_later' => ['sms', 'push'],
        'events' => [
            'payment_successful',
            'payment_failed',
            'booking_confirmed',
            'booking_reminder',
            'order_status_changed',
            'access_subscription_activated',
            'new_inquiry',
            'institution_program_status_changed',
        ],
        // Runtime templates + channel wiring: config/notifications.php
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin dashboard sections (Brief V3 §13)
    |--------------------------------------------------------------------------
    */
    'admin_sections' => [
        // 1 · المنتجات
        'learning_paths',
        'recorded_courses',
        'content_tools',
        'packages_products',
        // 2 · المدارس والمؤسسات (مساران: منصة | مباشر)
        'institution_accounts',
        'institution_platform_access',
        'institution_direct_delivery',
        // 3 · الاستشارات
        'consultations_bookings',
        // 4 · التشغيل التجاري
        'orders',
        'accounting',
        'crm',
        'inquiries',
        // 5 · التواصل والإعدادات
        'users',
        'notifications',
        'analytics',
        'settings',
    ],

    /*
    |--------------------------------------------------------------------------
    | Data model (Brief V3) — expandable architecture, not an MVP build list
    |--------------------------------------------------------------------------
    |
    | mvp_required = needed for phase-1 nucleus.
    | Everything else may exist as stubs/FKs/relations without full product flows.
    | Product/Service is a conceptual union of separate catalog tables (no hard-coded
    | single Product table required in MVP).
    |
    */
    'data_model_entities' => [
        'user', 'school_institution', 'product_service', 'package', 'entitlement',
        'order', 'payment_transaction', 'booking', 'training_program',
        'inquiry', 'notification', 'progress',
    ],

    'data_model' => [
        'user' => [
            'mvp_required' => true,
            'model' => \App\Models\User::class,
            'table' => 'users',
            'owns' => ['orders', 'bookings', 'progress', 'inquiries', 'notifications'],
            'notes' => 'Individual teacher/learner; institution roles via InstitutionMember',
        ],
        'school_institution' => [
            'mvp_required' => true,
            'model' => \App\Models\Institution::class,
            'table' => 'institutions',
            'owns' => ['members', 'programs', 'orders', 'inquiries'],
            'related' => [
                'members' => \App\Models\InstitutionMember::class,
                'programs' => \App\Models\InstitutionProgram::class,
            ],
            'notes' => 'Single commercial axis schools+institutions; reports deferred',
        ],
        'product_service' => [
            'mvp_required' => true,
            'model' => null, // conceptual union — not a single table in MVP
            'kinds' => [
                'learning_path' => \App\Models\LearningPath::class,
                'consultation' => \App\Models\ConsultationService::class,
                'training_program' => \App\Models\InstitutionProgram::class,
                'institutional_service' => \App\Models\InstitutionProgram::class,
                'resource' => \App\Models\TeacherTool::class,
            ],
            'notes' => 'Expand later via morph purchasable / products table if needed',
        ],
        'package' => [
            'mvp_required' => true,
            'model' => \App\Models\Package::class,
            'table' => 'packages',
            'owns' => ['learning_paths', 'tools', 'entitlements'],
            'notes' => 'Admin-managed bundle; entitlements via UserPackageEntitlement',
        ],
        'entitlement' => [
            'mvp_required' => true,
            'model' => \App\Models\UserPackageEntitlement::class,
            'table' => 'user_package_entitlements',
            'notes' => 'Access after purchase/activation; path enrollments parallel',
        ],
        'order' => [
            'mvp_required' => true,
            'model' => \App\Models\Order::class,
            'table' => 'orders',
            'links' => ['user_id', 'institution_id', 'package_id', 'payment_id'],
            'notes' => 'Buyer = User and/or Institution; product via package/legacy FKs',
        ],
        'payment_transaction' => [
            'mvp_required' => true,
            'models' => [\App\Models\Payment::class, \App\Models\Transaction::class],
            'tables' => ['payments', 'transactions'],
            'notes' => 'Gateway response on payments; accounting row on transactions',
        ],
        'booking' => [
            'mvp_required' => true,
            'model' => \App\Models\ConsultationRequest::class,
            'table' => 'consultation_requests',
            'links' => ['student_id', 'institution_id', 'consultation_service_id', 'order_id', 'scheduled_at'],
            'notes' => 'Consultation booking; order_id optional until checkout unified',
        ],
        'training_program' => [
            'mvp_required' => true,
            'model' => \App\Models\InstitutionProgram::class,
            'table' => 'institution_programs',
            'related' => ['participants' => \App\Models\InstitutionProgramParticipant::class],
            'notes' => 'Training + institutional development kinds on same table',
        ],
        'inquiry' => [
            'mvp_required' => true,
            'model' => \App\Models\Inquiry::class,
            'table' => 'inquiries',
            'links' => ['user_id', 'institution_id', 'order_id', 'consultation_request_id'],
            'notes' => 'Internal CRM-style intake including WhatsApp-started chats',
        ],
        'notification' => [
            'mvp_required' => true,
            'models' => [\App\Models\Notification::class, \App\Models\NotificationDelivery::class],
            'tables' => ['notifications', 'notification_deliveries'],
            'notes' => 'In-app + multi-channel delivery ledger (WhatsApp/Email now; SMS/Push later)',
        ],
        'progress' => [
            'mvp_required' => true,
            'models' => [
                \App\Models\TeacherPathEnrollment::class,
                \App\Models\TeacherPathProgressItem::class,
                \App\Models\InstitutionProgramParticipant::class,
            ],
            'notes' => 'Path + institution progress; unified Progress aggregate deferred',
        ],
        // Explicitly deferred — architecture may reference later without MVP build
        'institution_reports' => [
            'mvp_required' => false,
            'model' => null,
            'notes' => 'Deferred; do not block Order/Institution FKs',
        ],
        'unified_product_table' => [
            'mvp_required' => false,
            'model' => null,
            'notes' => 'Optional later; kinds cover catalog today',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | MVP in-scope flags (Brief V3 §18)
    |--------------------------------------------------------------------------
    */
    'mvp' => [
        'accounts_roles' => true,
        'learning_paths_content' => true,
        'products_packages' => true,
        'checkout_payment_webhooks' => true,
        'orders_transactions' => true,
        'consultations_booking' => true,
        'whatsapp_email_notifications_core' => true,
        'whatsapp_inquiry_entry' => true,
        'schools_basic_account_participants_programs' => true,
        'admin_dashboard' => true,
        'advanced_reports' => false,
        'advanced_automation' => false,
        'complex_institutional_development' => false,
        'mobile_apps' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Module enable flags (gate UI/routes when implemented)
    |--------------------------------------------------------------------------
    */
    'modules' => [
        'learning_paths' => [
            'enabled' => true,
            'label_ar' => 'المسارات التعليمية',
            'label_en' => 'Learning Paths',
        ],
        'consultations' => [
            'enabled' => true,
            'label_ar' => 'الاستشارات',
            'label_en' => 'Consultations',
        ],
        'schools_institutions' => [
            'enabled' => true,
            'label_ar' => 'المدارس والمؤسسات',
            'label_en' => 'Schools & Institutions',
        ],
        'packages' => [
            'enabled' => true,
            'label_ar' => 'الباقات',
            'label_en' => 'Packages',
        ],
        'courses' => [
            'enabled' => true,
            'label_ar' => 'الكورسات المسجّلة',
            'label_en' => 'Recorded Courses',
        ],
        'tools_resources' => [
            'enabled' => true,
            'label_ar' => 'الأدوات والموارد',
            'label_en' => 'Tools & Resources',
        ],
        'teacher_assistant' => [
            'enabled' => true,
            'label_ar' => 'مساعد المعلم',
            'label_en' => 'Teacher Assistant',
            'note' => 'Candidate under tools_resources; AI-ready',
        ],
        'payments' => [
            'enabled' => true,
            'label_ar' => 'الدفع',
            'label_en' => 'Payment',
        ],
        'whatsapp_comms' => [
            'enabled' => true,
            'label_ar' => 'واتساب والتواصل',
            'label_en' => 'WhatsApp & Communication',
        ],
        'inquiries' => [
            'enabled' => true,
            'label_ar' => 'الاستفسارات',
            'label_en' => 'Inquiries',
        ],
        'notifications' => [
            'enabled' => true,
            'label_ar' => 'الإشعارات',
            'label_en' => 'Notifications',
        ],
        'admin_dashboard' => [
            'enabled' => true,
            'label_ar' => 'لوحة التحكم',
            'label_en' => 'Admin Dashboard',
        ],

        // Inherited Glottical surplus — soft-disabled (UI + routes). Flip to true to restore.
        'tutoring' => [
            'enabled' => false,
            'label_ar' => 'حصص خصوصي / مجموعات (إرث)',
            'label_en' => 'Legacy tutoring / groups',
        ],
        'free_trial' => [
            'enabled' => false,
            'label_ar' => 'الحصة التجريبية (إرث)',
            'label_en' => 'Legacy free trial',
        ],
        'parent_progress' => [
            'enabled' => false,
            'label_ar' => 'ولي الأمر — مُزال من المنتج',
            'label_en' => 'Parent role — removed from product',
        ],
        'crm_sales' => [
            'enabled' => true,
            'label_ar' => 'مسار البيع والفرص',
            'label_en' => 'Sales pipeline & leads',
        ],
        'tutor_hiring' => [
            'enabled' => false,
            'label_ar' => 'توظيف مدرسين (إرث)',
            'label_en' => 'Legacy tutor hiring',
        ],
        'live_classroom' => [
            'enabled' => false,
            'label_ar' => 'بث الحصص / LiveKit (إرث)',
            'label_en' => 'Legacy live classroom',
        ],
        'community' => [
            'enabled' => false,
            'label_ar' => 'مجتمع / مسابقات (إرث)',
            'label_en' => 'Legacy community',
        ],
        'loyalty_referrals' => [
            'enabled' => false,
            'label_ar' => 'إحالات وإعلانات منبثقة (إرث)',
            'label_en' => 'Legacy referrals / popups',
        ],
        'language_games' => [
            'enabled' => false,
            'label_ar' => 'ألعاب لغة (إرث)',
            'label_en' => 'Legacy language games',
        ],
        'student_panel' => [
            'enabled' => false,
            'label_ar' => 'لوحة طالب لغة (إرث)',
            'label_en' => 'Legacy language-student panel',
        ],
        'placement' => [
            'enabled' => false,
            'label_ar' => 'اختبار تحديد مستوى لغة (إرث)',
            'label_en' => 'Legacy language placement',
        ],
        'legacy_finance' => [
            'enabled' => false,
            'label_ar' => 'رواتب مدربين / تقسيط (إرث) — حسابات التحويل اليدوي تحت الدفع',
            'label_en' => 'Legacy instructor salaries / installments (manual receiving accounts live under payments)',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Open decisions (Brief V3 §19) — lock in Scope/Contract before first invoice
    |--------------------------------------------------------------------------
    */
    'open_decisions' => [
        'custom_modular_adopted' => true,
        'five_pillars_adopted' => true,
        'schools_institutions_single_axis' => true,
        'mvp_scope_locked' => false,
        'tech_stack_locked' => false,
        'data_model_permissions_locked' => false,
        'payment_gateway_chosen' => false,
        'webhooks_access_activation_confirmed' => false,
        'whatsapp_provider_chosen' => false,
        'inquiry_ops_defined' => false,
        'admin_manageable_surface_defined' => false,
        'ownership_repo_defined' => false,
        'hosting_backup_maintenance_defined' => false,
        'external_fees_ownership_defined' => false,
        'scope_contract_signed' => false,
    ],

    'homepage_stats' => [
        'learners_min' => (int) env('HOME_STATS_LEARNERS_MIN', 5000),
        'learners_show_plus' => env('HOME_STATS_LEARNERS_SHOW_PLUS', true),
    ],

    'academy_timezone' => env('ACADEMY_TIMEZONE', 'Africa/Cairo'),
];
