<?php

/**
 * Brief V3 §16 — Must remain expandable (ما يجب أن يكون قابلًا للتوسع).
 *
 * Rule: catalogue content (paths, packages, services, tools, orgs…) is admin/DB-managed.
 * Forbidden: hard-coding product rows so every small change needs a developer.
 *
 * status: works | partial | deferred
 * admin_route: primary admin entry (must exist when status=works)
 */
return [

    'learning_paths' => [
        'label_ar' => 'إضافة مسارات جديدة',
        'label_en' => 'Add learning paths',
        'status' => 'works',
        'admin_route' => 'admin.learning-paths.index',
        'managed_via' => 'db',
        'notes' => 'LearningPath CRUD + units/lessons/practices nested admin.',
    ],

    'path_content_tools' => [
        'label_ar' => 'إضافة وحدات ودروس وأدوات',
        'label_en' => 'Add units, lessons, and tools',
        'status' => 'works',
        'admin_route' => 'admin.teacher-tools.index',
        'managed_via' => 'db',
        'notes' => 'Path content on learning-path show; tools via TeacherTool. Tool category keys live in platform.php taxonomy.',
    ],

    'products_services' => [
        'label_ar' => 'إضافة منتجات وخدمات جديدة',
        'label_en' => 'Add products and services',
        'status' => 'works',
        'admin_route' => 'admin.packages.index',
        'managed_via' => 'db',
        'notes' => 'Separate catalog tables (paths, packages, consultation services, tools) — no single Product table required in MVP.',
    ],

    'packages_pricing' => [
        'label_ar' => 'إنشاء باقات وتعديل أسعارها',
        'label_en' => 'Create packages and edit prices',
        'status' => 'works',
        'admin_route' => 'admin.packages.index',
        'managed_via' => 'db',
        'notes' => 'Package CRUD + price/bulk; public pricing reads active packages from DB.',
    ],

    'consultation_types' => [
        'label_ar' => 'إضافة أنواع استشارات',
        'label_en' => 'Add consultation types',
        'status' => 'partial',
        'admin_route' => 'admin.consultations.services.index',
        'managed_via' => 'config_plus_db',
        'notes' => 'ConsultationService rows are admin CRUD. Type taxonomy keys: config/platform.php consultations.types (not per-row hard-code in blades).',
    ],

    'school_training_programs' => [
        'label_ar' => 'إضافة برامج تدريب للمدارس والمؤسسات',
        'label_en' => 'Add school/institution training programs',
        'status' => 'works',
        'admin_route' => 'admin.institution-programs.index',
        'managed_via' => 'db',
        'notes' => 'InstitutionProgram CRUD (kind=training). Public entry via inquiry; destroy optional.',
    ],

    'institutions_participants' => [
        'label_ar' => 'إضافة مدارس/مؤسسات ومشاركين',
        'label_en' => 'Add schools/institutions and participants',
        'status' => 'works',
        'admin_route' => 'admin.institutions.index',
        'managed_via' => 'db',
        'notes' => 'Institution + members + program participants.',
    ],

    'notification_channels' => [
        'label_ar' => 'إضافة قنوات إشعارات',
        'label_en' => 'Add notification channels',
        'status' => 'partial',
        'admin_route' => 'admin.notification-center.index',
        'managed_via' => 'config_plus_code',
        'notes' => 'WhatsApp/Email work; SMS/Push stubs. New channel = Channel class + config/notifications.php + admin toggle — not hard-coded message bodies.',
    ],

    'payment_gateway' => [
        'label_ar' => 'تغيير Payment Gateway',
        'label_en' => 'Change payment gateway',
        'status' => 'works',
        'admin_route' => 'admin.payment-gateways.index',
        'managed_via' => 'settings',
        'notes' => 'Enable/creds for PayPal, Kashier, Fawaterak from admin. Brand-new provider adapter still needs code (architecture allows swap).',
    ],

    'roles_permissions' => [
        'label_ar' => 'توسيع الصلاحيات والأدوار',
        'label_en' => 'Expand roles and permissions',
        'status' => 'partial',
        'admin_route' => 'admin.roles.index',
        'managed_via' => 'db_plus_config',
        'notes' => 'Roles/permissions CRUD in DB. Sidebar/route maps in rbac_* config still need a developer when wiring brand-new permission keys to UI.',
    ],
];
