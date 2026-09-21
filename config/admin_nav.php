<?php

/**
 * TADRIS LAB Admin Dashboard nav (Brief V3 §13).
 * Source of truth for primary product sidebar — CRUD stays in existing controllers.
 *
 * Hubs = localized sidebar section labels (label_ar / label_en).
 * Legacy Glottical sections remain in admin-sidebar.blade.php behind show_legacy_ops.
 */
return [

    'enabled' => true,

    /*
    | Soft-hide duplicate Glottical links once primary Brief nav is shown.
    */
    'hide_legacy_duplicates' => true,

    /*
    | Show the long Glottical ops tree under Brief hubs (طلاب / موقع / تعليم إرث…).
    | Default off so the admin sidebar stays Brief-aligned for TADRIS LAB.
    | Set ADMIN_NAV_SHOW_LEGACY_OPS=true in .env when ops staff need legacy tools.
    */
    'show_legacy_ops' => (bool) env('ADMIN_NAV_SHOW_LEGACY_OPS', false),

    /*
    | Sidebar hubs (section labels). Groups below reference these keys.
    */
    'hubs' => [
        'product' => [
            'label_ar' => '١ · المنتجات والمحتوى',
            'label_en' => '1 · Products & Content',
        ],
        'institutions' => [
            'label_ar' => '٢ · المدارس والمؤسسات',
            'label_en' => '2 · Schools & Institutions',
        ],
        'consultations' => [
            'label_ar' => '٣ · الاستشارات',
            'label_en' => '3 · Consultations',
        ],
        'commerce' => [
            'label_ar' => '٤ · التشغيل التجاري',
            'label_en' => '4 · Commerce Ops',
        ],
        'comms' => [
            'label_ar' => '٥ · التواصل والإعدادات',
            'label_en' => '5 · Comms & Settings',
        ],
    ],

    'sections' => [
        // ——— المنتجات والمحتوى ———
        'learning_paths' => [
            'hub' => 'product',
            'label_ar' => 'إدارة محتوى المسارات',
            'label_en' => 'Path content CMS',
            'icon' => 'fas fa-route',
            'module' => 'learning_paths',
            'permissions' => ['manage.courses', 'manage.packages'],
            'items' => [
                [
                    'label_ar' => 'المسارات والوحدات والدروس',
                    'label_en' => 'Paths, units & lessons',
                    'route' => 'admin.learning-paths.index',
                    'route_is' => 'admin.learning-paths.*',
                ],
            ],
        ],
        'content_tools' => [
            'hub' => 'product',
            'label_ar' => 'الأدوات والموارد',
            'label_en' => 'Tools & Resources',
            'icon' => 'fas fa-toolbox',
            'module' => 'tools_resources',
            'permissions' => ['manage.courses', 'manage.packages'],
            'items' => [
                [
                    'label_ar' => 'مكتبة الأدوات والموارد',
                    'label_en' => 'Tools & resources library',
                    'route' => 'admin.teacher-tools.index',
                    'route_is' => 'admin.teacher-tools.*',
                ],
            ],
            'note_ar' => 'مساعد المعلم متاح للمعلمين عبر /my-assistant (عقد AI-ready)',
            'note_en' => 'Teacher Assistant is available to teachers at /my-assistant (AI-ready contract)',
        ],
        'packages_products' => [
            'hub' => 'product',
            'label_ar' => 'الباقات',
            'label_en' => 'Packages',
            'icon' => 'fas fa-box-open',
            'module' => 'packages',
            'permissions' => ['manage.packages'],
            'items' => [
                [
                    'label_ar' => 'إدارة الباقات',
                    'label_en' => 'Manage packages',
                    'route' => 'admin.packages.index',
                    'route_is' => 'admin.packages.*',
                ],
            ],
        ],

        // ——— المدارس والمؤسسات ———
        'schools_institutions' => [
            'hub' => 'institutions',
            'label_ar' => 'حسابات الجهات',
            'label_en' => 'Institution accounts',
            'icon' => 'fas fa-building',
            'module' => 'schools_institutions',
            'permissions' => ['manage.institutions', 'manage.packages'],
            'items' => [
                [
                    'label_ar' => 'كل الجهات',
                    'label_en' => 'All institutions',
                    'route' => 'admin.institutions.index',
                    'route_is' => 'admin.institutions.*',
                ],
                [
                    'label_ar' => 'برامج التدريب',
                    'label_en' => 'Training programs',
                    'route' => 'admin.institution-programs.index',
                    'route_is' => 'admin.institution-programs.*',
                    'params' => ['kind' => 'training'],
                    'active_when' => ['kind' => 'training'],
                ],
                [
                    'label_ar' => 'مشاريع التطوير',
                    'label_en' => 'Development projects',
                    'route' => 'admin.institution-programs.index',
                    'route_is' => 'admin.institution-programs.*',
                    'params' => ['kind' => 'development'],
                    'active_when' => ['kind' => 'development'],
                ],
                [
                    'label_ar' => 'كل البرامج',
                    'label_en' => 'All programs',
                    'route' => 'admin.institution-programs.index',
                    'route_is' => 'admin.institution-programs.*',
                    'params' => [],
                    'active_when' => ['kind' => ''],
                ],
            ],
        ],

        // ——— الاستشارات ———
        'consultations_bookings' => [
            'hub' => 'consultations',
            'label_ar' => 'الاستشارات والحجوزات',
            'label_en' => 'Consultations & Bookings',
            'icon' => 'fas fa-comments',
            'module' => 'consultations',
            'permissions' => ['manage.consultations'],
            'items' => [
                [
                    'label_ar' => 'الحجوزات',
                    'label_en' => 'Bookings',
                    'route' => 'admin.consultations.index',
                    'route_is' => 'admin.consultations.index|admin.consultations.show',
                ],
                [
                    'label_ar' => 'خدمات الاستشارة',
                    'label_en' => 'Consultation services',
                    'route' => 'admin.consultations.services.index',
                    'route_is' => 'admin.consultations.services.*',
                ],
            ],
        ],

        // ——— التشغيل التجاري ———
        'orders' => [
            'hub' => 'commerce',
            'label_ar' => 'الطلبات والمدفوعات',
            'label_en' => 'Orders & Payments',
            'icon' => 'fas fa-receipt',
            'permissions' => ['manage.orders', 'manage.payments', 'manage.transactions', 'view.financial-reports'],
            'items' => [
                [
                    'label_ar' => 'الطلبات',
                    'label_en' => 'Orders',
                    'route' => 'admin.orders.index',
                    'route_is' => 'admin.orders.*',
                    'permissions' => ['manage.orders'],
                ],
                [
                    'label_ar' => 'المدفوعات',
                    'label_en' => 'Payments',
                    'route' => 'admin.payments.index',
                    'route_is' => 'admin.payments.*',
                    'permissions' => ['manage.payments'],
                ],
                [
                    'label_ar' => 'المعاملات',
                    'label_en' => 'Transactions',
                    'route' => 'admin.transactions.index',
                    'route_is' => 'admin.transactions.*',
                    'permissions' => ['manage.transactions', 'view.financial-reports'],
                ],
                [
                    'label_ar' => 'الكوبونات',
                    'label_en' => 'Coupons',
                    'route' => 'admin.coupons.index',
                    'route_is' => 'admin.coupons.*',
                    'permissions' => ['manage.coupons'],
                ],
            ],
        ],
        'crm' => [
            'hub' => 'commerce',
            'label_ar' => 'CRM المؤسسات والمعلمين',
            'label_en' => 'Institution & teacher CRM',
            'icon' => 'fas fa-funnel-dollar',
            'module' => 'crm_sales',
            'permissions' => ['manage.leads'],
            'items' => [
                [
                    'label_ar' => 'لوحة CRM',
                    'label_en' => 'CRM dashboard',
                    'route' => 'admin.crm.dashboard',
                    'route_is' => 'admin.crm.dashboard',
                ],
                [
                    'label_ar' => 'مسار البيع',
                    'label_en' => 'Sales pipeline',
                    'route' => 'admin.crm.pipeline',
                    'route_is' => 'admin.crm.pipeline',
                ],
                [
                    'label_ar' => 'فرص ومدارس/معلمون',
                    'label_en' => 'Leads (schools / teachers)',
                    'route' => 'admin.crm.leads.index',
                    'route_is' => 'admin.crm.leads.*',
                ],
                [
                    'label_ar' => 'العمولات',
                    'label_en' => 'Commissions',
                    'route' => 'admin.crm.commissions.index',
                    'route_is' => 'admin.crm.commissions.*',
                ],
                [
                    'label_ar' => 'فرق المبيعات',
                    'label_en' => 'Sales teams',
                    'route' => 'admin.crm.groups.index',
                    'route_is' => 'admin.crm.groups.*',
                ],
                [
                    'label_ar' => 'سجل النشاط',
                    'label_en' => 'Activity log',
                    'route' => 'admin.crm.audit.index',
                    'route_is' => 'admin.crm.audit.*',
                ],
            ],
        ],
        'inquiries' => [
            'hub' => 'commerce',
            'label_ar' => 'الاستفسارات',
            'label_en' => 'Inquiries',
            'icon' => 'fas fa-inbox',
            'module' => 'inquiries',
            'permissions' => ['manage.contact-messages'],
            'items' => [
                [
                    'label_ar' => 'استفسارات المنصة',
                    'label_en' => 'Platform inquiries',
                    'route' => 'admin.inquiries.index',
                    'route_is' => 'admin.inquiries.*',
                ],
                [
                    'label_ar' => 'رسائل التواصل',
                    'label_en' => 'Contact messages',
                    'route' => 'admin.contact-messages.index',
                    'route_is' => 'admin.contact-messages.*',
                ],
            ],
        ],

        // ——— التواصل والإعدادات ———
        'users' => [
            'hub' => 'comms',
            'label_ar' => 'المعلمون والمدربون',
            'label_en' => 'Teachers & coaches',
            'icon' => 'fas fa-users',
            'permissions' => ['manage.users', 'manage.academic-years', 'manage.tutoring-groups'],
            'items' => [
                [
                    'label_ar' => 'المعلمون (متلقّو الخدمة)',
                    'label_en' => 'Teachers (learners)',
                    'route' => 'admin.users.index',
                    'route_is' => 'admin.users.*',
                    'permissions' => ['manage.users'],
                ],
                [
                    'label_ar' => 'المدربون وتعييناتهم',
                    'label_en' => 'Coaches & assignments',
                    'route' => 'admin.academy-instructors.index',
                    'route_is' => 'admin.academy-instructors.*',
                    'permissions' => ['manage.users', 'manage.academic-years', 'manage.tutoring-groups'],
                ],
            ],
        ],
        'notifications' => [
            'hub' => 'comms',
            'label_ar' => 'الإشعارات',
            'label_en' => 'Notifications',
            'icon' => 'fas fa-bell',
            'module' => 'notifications',
            'permissions' => ['manage.notifications', 'manage.system-settings'],
            'items' => [
                [
                    'label_ar' => 'مركز الإشعارات',
                    'label_en' => 'Notification center',
                    'route' => 'admin.notification-center.index',
                    'route_is' => 'admin.notification-center.*',
                ],
                [
                    'label_ar' => 'قوالب WhatsApp / Email',
                    'label_en' => 'WhatsApp / Email templates',
                    'route' => 'admin.notification-center.templates',
                    'route_is' => 'admin.notification-center.templates*',
                    'permissions' => ['manage.system-settings', 'manage.notifications'],
                ],
                [
                    'label_ar' => 'وارد الإشعارات',
                    'label_en' => 'Notification inbox',
                    'route' => 'admin.notifications.inbox',
                    'route_is' => 'admin.notifications.inbox',
                ],
                [
                    'label_ar' => 'بث إشعارات',
                    'label_en' => 'Broadcast notifications',
                    'route' => 'admin.notifications.index',
                    'route_is' => 'admin.notifications.index|admin.notifications.create|admin.notifications.show',
                ],
            ],
        ],
        'analytics' => [
            'hub' => 'comms',
            'label_ar' => 'التحليلات',
            'label_en' => 'Analytics',
            'icon' => 'fas fa-chart-line',
            'module' => 'admin_dashboard',
            'permissions' => ['view.dashboard', 'admin.access', 'view.reports'],
            'items' => [
                [
                    'label_ar' => 'تحليلات وتوجهات',
                    'label_en' => 'Insights & trends',
                    'route' => 'admin.academy-insights.index',
                    'route_is' => 'admin.academy-insights.*',
                ],
                [
                    'label_ar' => 'الإحصائيات',
                    'label_en' => 'Statistics',
                    'route' => 'admin.statistics.index',
                    'route_is' => 'admin.statistics.*',
                    'permissions' => ['view.reports'],
                ],
            ],
        ],
        'settings' => [
            'hub' => 'comms',
            'label_ar' => 'إعدادات المنصة',
            'label_en' => 'Platform settings',
            'icon' => 'fas fa-cog',
            'permissions' => ['manage.system-settings', 'manage.payment-gateways'],
            'items' => [
                [
                    'label_ar' => 'إعدادات النظام',
                    'label_en' => 'System settings',
                    'route' => 'admin.system-settings.edit',
                    'route_is' => 'admin.system-settings.*',
                    'permissions' => ['manage.system-settings'],
                ],
                [
                    'label_ar' => 'بوابات الدفع',
                    'label_en' => 'Payment gateways',
                    'route' => 'admin.payment-gateways.index',
                    'route_is' => 'admin.payment-gateways.*',
                    'permissions' => ['manage.system-settings', 'manage.payment-gateways'],
                ],
            ],
        ],
    ],
];
