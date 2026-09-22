<?php

/**
 * تطابق عناصر سايدبار لوحة الإدارة (resources/views/layouts/admin-sidebar.blade.php)
 * مع أسماء الصلاحيات في جدول permissions. يُستخدم في صفحة أدوار الإدارة.
 *
 * - permissions: أي اسم يكفي لإظهار الرابط (منطق OR) ما لم يُضبط match على «all».
 * - children: عناصر فرعية داخل مجموعة قابلة للطي في السايدبار.
 */
return [
    'intro' => [
        'لوحة التحكم والملف الشخصي تظهران لمن يملك حق الدخول إلى مسارات الإدارة (middleware).',
        'صلاحية view.dashboard تُضاف تلقائياً عند حفظ الدور.',
        'صلاحيات «الطالب» و«المدرب» في الجدول لا تظهر في هذا السايدبار — تُعرض أسفل الصفحة ضمن «صلاحيات أخرى».',
    ],
    'sections' => [
        [
            'title' => 'روابط علوية',
            'items' => [
                ['label' => 'تحليلات وتوجهات الأكاديمية', 'permissions' => ['view.dashboard', 'admin.access', 'view.reports'], 'note' => 'قسم قرار لحظي في أعلى السايدبار'],
                ['label' => 'لوحة التحكم', 'permissions' => ['view.dashboard', 'admin.access']],
                ['label' => 'الملف الشخصي', 'permissions' => ['view.dashboard', 'admin.access']],
                ['label' => 'وارد الإشعارات', 'permissions' => ['manage.notifications'], 'note' => 'للموظف RBAC: يظهر إن وُجدت الصلاحية'],
                ['label' => 'الاستفسارات', 'permissions' => ['manage.contact-messages']],
                ['label' => 'رسائل التواصل', 'permissions' => ['manage.contact-messages']],
                ['label' => 'الحصة المجانية', 'permissions' => ['manage.free-trial-bookings']],
                ['label' => 'خدمات الموقع', 'permissions' => ['manage.site-services']],
                ['label' => 'آراء الموقع (الرئيسية)', 'permissions' => ['manage.site-testimonials', 'manage.site-services']],
                ['label' => 'إعدادات النظام', 'permissions' => ['manage.system-settings']],
                ['label' => 'صفحة من نحن', 'permissions' => ['manage.about-page']],
                ['label' => 'الأسئلة الشائعة', 'permissions' => ['manage.faq']],
            ],
        ],
        [
            'title' => 'تسمية: أقسام حسب الوظيفة',
            'note' => 'تظهر مجموعة «التحكم الشامل بالطلاب…» إذا وُجدت إحدى: manage.users, manage.students-accounts, manage.enrollments, manage.student-control, manage.support-tickets, manage.consultations, manage.quality-control, view.reports',
            'items' => [
                [
                    'label' => 'التحكم الشامل بالطلاب والخدمات',
                    'children' => [
                        ['label' => 'إدارة الطلاب والحسابات', 'permissions' => ['manage.users', 'manage.students-accounts']],
                        ['label' => 'تسجيلات الطلاب', 'permissions' => ['manage.enrollments']],
                        ['label' => 'الدعم الفني (التذاكر)', 'permissions' => ['manage.support-tickets']],
                        ['label' => 'تصنيفات دعم الطلاب', 'permissions' => ['manage.support-tickets']],
                        ['label' => 'استشارات المدربين', 'permissions' => ['manage.consultations']],
                        ['label' => 'مراقبة شاملة على الطلاب', 'permissions' => ['manage.student-control', 'manage.quality-control']],
                        ['label' => 'تقارير الطلاب', 'permissions' => ['view.reports', 'manage.student-control']],
                    ],
                ],
            ],
        ],
        [
            'title' => 'قسم المبيعات',
            'items' => [
                ['label' => 'العملاء المحتملون (Leads)', 'permissions' => ['manage.leads']],
                ['label' => 'لوحة تحليلات المبيعات', 'permissions' => ['view.sales-analytics']],
                ['label' => 'الطلبات', 'permissions' => ['manage.orders']],
                ['label' => 'الكوبونات والخصومات', 'permissions' => ['manage.coupons']],
                ['label' => 'عمولات كوبونات التسويق', 'permissions' => ['manage.coupons']],
                ['label' => 'برامج الإحالة', 'permissions' => ['manage.referrals']],
                ['label' => 'الإحالات', 'permissions' => ['manage.referrals']],
            ],
        ],
        [
            'title' => 'قسم الموارد البشرية',
            'items' => [
                ['label' => 'الموظفين', 'permissions' => ['manage.users']],
                ['label' => 'الوظائف', 'permissions' => ['manage.users']],
                ['label' => 'مهام الموظفين', 'permissions' => ['manage.users']],
                ['label' => 'طلبات الإجازة', 'permissions' => ['manage.leaves']],
                ['label' => 'اتفاقيات الموظفين ورواتبهم', 'permissions' => ['manage.employee-agreements']],
            ],
        ],
        [
            'title' => 'قسم المحاسبة (مكرر في السايدبار مع «المالية»)',
            'items' => [
                ['label' => 'الفواتير', 'permissions' => ['manage.invoices']],
                ['label' => 'المدفوعات', 'permissions' => ['manage.payments']],
                ['label' => 'المعاملات', 'permissions' => ['manage.transactions']],
                ['label' => 'الحسابات', 'permissions' => ['manage.wallets', 'view.wallets']],
                ['label' => 'رواتب المدربين', 'permissions' => ['manage.salaries']],
                ['label' => 'اتفاقيات الموظفين', 'permissions' => ['manage.employee-agreements']],
                ['label' => 'المصروفات', 'permissions' => ['manage.expenses']],
                ['label' => 'اتفاقيات التقسيط', 'permissions' => ['manage.installments']],
                ['label' => 'تقارير المحاسبة', 'permissions' => ['manage.invoices', 'manage.payments', 'manage.transactions']],
            ],
        ],
        [
            'title' => 'إدارة النظام (مجموعة)',
            'items' => [
                ['label' => 'المستخدمون', 'permissions' => ['manage.users']],
                ['label' => 'بوابات الدفع', 'permissions' => ['manage.system-settings']],
                ['label' => 'الطلبات', 'permissions' => ['manage.orders']],
                ['label' => 'الإشعارات', 'permissions' => ['manage.notifications']],
                ['label' => 'إشعارات الموظفين', 'permissions' => ['manage.notifications']],
                ['label' => 'إشعارات البريد (Gmail)', 'permissions' => ['manage.email-broadcasts']],
                ['label' => 'سجل النشاطات', 'permissions' => ['view.activity-log']],
                ['label' => 'سجلات المصادقة الثنائية', 'permissions' => ['manage.two-factor-logs']],
                ['label' => 'الإحصائيات', 'permissions' => ['view.statistics']],
                ['label' => 'مراقبة أداء المنصة', 'permissions' => ['manage.performance']],
            ],
        ],
        [
            'title' => 'نظام الاتفاقيات',
            'items' => [
                ['label' => 'اتفاقيات المدربين', 'permissions' => ['manage.agreements']],
                ['label' => 'اتفاقيات الموظفين', 'permissions' => ['manage.employee-agreements']],
                ['label' => 'طلبات السحب', 'permissions' => ['manage.withdrawals']],
            ],
        ],
        [
            'title' => 'المالية (مجموعة)',
            'items' => [
                ['label' => 'الفواتير', 'permissions' => ['manage.invoices']],
                ['label' => 'المدفوعات', 'permissions' => ['manage.payments']],
                ['label' => 'المعاملات', 'permissions' => ['manage.transactions']],
                ['label' => 'الحسابات', 'permissions' => ['manage.wallets', 'view.wallets']],
                ['label' => 'مالية المدربين', 'permissions' => ['manage.salaries']],
                ['label' => 'حسابات المدربين', 'permissions' => ['manage.instructor-accounts']],
                ['label' => 'اتفاقيات الموظفين ورواتبهم', 'permissions' => ['manage.employee-agreements']],
                ['label' => 'المصروفات', 'permissions' => ['manage.expenses']],
                ['label' => 'إدارة التقسيط (خطط + اتفاقيات)', 'permissions' => ['manage.installments']],
                ['label' => 'تقارير المحاسبة', 'permissions' => ['manage.invoices', 'manage.payments', 'manage.transactions']],
            ],
        ],
        [
            'title' => 'التسويق (مجموعة)',
            'items' => [
                ['label' => 'الإعلانات المنبثقة', 'permissions' => ['manage.popup-ads']],
                ['label' => 'العلامة الشخصية', 'permissions' => ['manage.personal-branding']],
                ['label' => 'الكوبونات والخصومات', 'permissions' => ['manage.coupons']],
                ['label' => 'عمولات كوبونات التسويق', 'permissions' => ['manage.coupons']],
                ['label' => 'برامج الإحالة', 'permissions' => ['manage.referrals']],
                ['label' => 'الإحالات', 'permissions' => ['manage.referrals']],
            ],
        ],
        [
            'title' => 'العناصر المدفوعة',
            'items' => [
                ['label' => 'الباقات والأسعار', 'permissions' => ['manage.packages']],
            ],
        ],
        [
            'title' => 'التعليم',
            'items' => [
                ['label' => 'التسجيلات — التسجيلات عبر الإنترنت', 'permissions' => ['manage.enrollments']],
                [
                    'label' => 'إدارة المحتوى (مجموعة)',
                    'children' => [
                        ['label' => 'إدارة الكورسات', 'permissions' => ['manage.courses']],
                        ['label' => 'تصنيفات الكورسات', 'permissions' => ['manage.courses']],
                        ['label' => 'المحاضرات', 'permissions' => ['manage.lectures']],
                        ['label' => 'الواجبات والمشاريع', 'permissions' => ['manage.assignments']],
                        ['label' => 'الامتحانات', 'permissions' => ['manage.exams']],
                        ['label' => 'بنك الأسئلة (والتصنيفات)', 'permissions' => ['manage.question-bank']],
                        ['label' => 'الحضور والانصراف', 'permissions' => ['manage.attendance']],
                        ['label' => 'الإنجازات', 'permissions' => ['manage.achievements']],
                        ['label' => 'الشارات', 'permissions' => ['manage.badges']],
                        ['label' => 'التقييمات والمراجعات', 'permissions' => ['manage.reviews']],
                    ],
                ],
                [
                    'label' => 'جلسات البث المباشر والمعلمين',
                    'children' => [
                        ['label' => 'جلسات البث المباشر', 'permissions' => ['manage.live-sessions']],
                        ['label' => 'تسجيلات الجلسات', 'permissions' => ['manage.live-sessions']],
                        ['label' => 'تسجيلات Classroom', 'permissions' => ['manage.live-sessions']],
                        ['label' => 'سيرفرات البث (VPS)', 'permissions' => ['manage.live-servers']],
                        ['label' => 'لوحة التحكم بالسيرفرات', 'permissions' => ['manage.live-servers']],
                        ['label' => 'إعدادات نظام اللايف', 'permissions' => ['manage.live-servers']],
                    ],
                ],
            ],
        ],
        [
            'title' => 'الفريق',
            'items' => [
                ['label' => 'الموظفين / الوظائف / مهام الموظفين', 'permissions' => ['manage.users']],
                ['label' => 'الإشراف الأكاديمي', 'permissions' => ['academic_supervision.manage']],
                ['label' => 'مهام المدربين', 'permissions' => ['manage.tasks']],
                ['label' => 'طلبات انضمام المدربين', 'permissions' => ['manage.instructor-requests']],
                ['label' => 'الإجازات', 'permissions' => ['manage.leaves']],
            ],
        ],
        [
            'title' => 'الرقابة والجودة',
            'items' => [
                ['label' => 'لوحة الرقابة', 'permissions' => ['manage.quality-control', 'view.statistics']],
                ['label' => 'رقابة الطلاب', 'permissions' => ['manage.quality-control', 'manage.student-control']],
                ['label' => 'رقابة المدربين / الموظفين / العمليات', 'permissions' => ['manage.quality-control']],
            ],
        ],
        [
            'title' => 'متقدم',
            'items' => [
                [
                    'label' => 'الشهادات',
                    'children' => [
                        ['label' => 'قائمة الشهادات / إصدار / المعلقة', 'permissions' => ['manage.certificates']],
                    ],
                ],
                [
                    'label' => 'الأدوار والصلاحيات',
                    'children' => [
                        ['label' => 'الأدوار', 'permissions' => ['manage.roles']],
                        ['label' => 'الصلاحيات', 'permissions' => ['manage.permissions']],
                        ['label' => 'صلاحيات المستخدمين', 'permissions' => ['manage.user-permissions']],
                    ],
                ],
                ['label' => 'المهام (رابط مستقل)', 'permissions' => ['manage.tasks', 'view.tasks']],
                ['label' => 'الرسائل', 'permissions' => ['manage.messages']],
                [
                    'label' => 'التقارير الشاملة',
                    'children' => [
                        ['label' => 'لوحة التقارير', 'permissions' => ['view.reports', 'view.statistics']],
                        ['label' => 'تقارير المستخدمين', 'permissions' => ['view.reports', 'manage.users']],
                        ['label' => 'تقارير الكورسات', 'permissions' => ['view.reports', 'manage.courses']],
                        ['label' => 'التقارير المالية', 'permissions' => ['view.financial-reports', 'manage.invoices']],
                        ['label' => 'التقارير الأكاديمية', 'permissions' => ['view.academic-reports', 'manage.courses']],
                        ['label' => 'تقارير النشاط', 'permissions' => ['view.reports', 'view.activity-log']],
                        ['label' => 'التقرير الشامل', 'permissions' => ['view.reports', 'view.statistics']],
                    ],
                ],
            ],
        ],
    ],
];
