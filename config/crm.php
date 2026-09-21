<?php

return [
    /*
    | نسب العمولة الافتراضية (%) من مبلغ الطلب المعتمد
    */
    'commission_rates' => [
        'marketing' => (float) env('CRM_COMMISSION_MARKETING', 5),
        'sales' => (float) env('CRM_COMMISSION_SALES', 10),
        'team_leader' => (float) env('CRM_COMMISSION_TEAM_LEADER', 2),
    ],

    /*
    | أدوار CRM الداخلية
    */
    'roles' => [
        'super_admin' => 'super_admin',
        'team_leader' => 'team_leader',
        'marketing' => 'marketing',
        'sales' => 'sales',
        'finance' => 'finance',
    ],

    'employee_job_codes' => [
        'marketing' => 'crm_marketing',
        'team_leader' => 'crm_team_leader',
        'finance' => 'crm_finance',
        'sales' => 'sales',
    ],

    /*
    | وظائف قسم المبيعات (للتحكم الفردي بالصلاحيات من الإدارة)
    */
    'sales_department_job_codes' => [
        'sales',
        'crm_marketing',
        'crm_team_leader',
        'crm_finance',
    ],

    /*
    | صلاحيات CRM الدقيقة — تُضبط من إدارة الوظائف (employee_jobs.permissions)
    */
    'permissions' => [
        'crm_desk' => 'الوصول للوحة CRM (مؤسسات ومعلمون)',
        'crm_create_leads' => 'إنشاء فرص مدارس/معلمين',
        'crm_submit_to_sales' => 'إرسال الفرص لصندوق المبيعات',
        'crm_edit_leads' => 'تعديل بيانات الفرص',
        'crm_assign_leads' => 'تعيين الفرص للمبيعات',
        'crm_transition_leads' => 'تحديث حالة الفرصة',
        'crm_add_notes' => 'إضافة ملاحظات على الفرص',
        'crm_manage_team' => 'إدارة أعضاء الفريق',
        'crm_view_team_performance' => 'عرض أداء أعضاء الفريق',
        'crm_submit_reports' => 'رفع تقارير CRM',
        'crm_view_messages' => 'قراءة رسائل CRM',
        'crm_send_messages' => 'إرسال رسائل CRM',
        'crm_view_all_orders' => 'عرض كل الطلبات (مثل الإدارة)',
        'crm_approve_payments' => 'تأكيد المدفوعات',
        'crm_approve_commissions' => 'اعتماد العمولات',
        'crm_view_all_leads' => 'عرض كل العملاء المحتملين',
        'crm_view_sales_financial_reports' => 'التقارير المالية لمبيعات CRM',
        'crm_view_submitted_reports' => 'عرض تقارير الفريق المرفوعة',
    ],

    /*
    | الصلاحيات الافتراضية لكل دور CRM (عند عدم ضبطها يدوياً في الوظيفة)
    */
    'role_default_permissions' => [
        'marketing' => [
            'crm_desk', 'crm_create_leads', 'crm_submit_to_sales', 'crm_edit_leads', 'crm_add_notes',
            'crm_submit_reports', 'crm_view_messages', 'crm_send_messages',
        ],
        'sales' => [
            'crm_desk', 'crm_edit_leads', 'crm_transition_leads', 'crm_add_notes',
            'crm_submit_reports', 'crm_view_messages', 'crm_send_messages',
        ],
        'team_leader' => [
            'crm_desk', 'crm_assign_leads', 'crm_manage_team', 'crm_view_team_performance',
            'crm_transition_leads', 'crm_add_notes', 'crm_submit_reports', 'crm_view_messages', 'crm_send_messages',
        ],
        'finance' => [
            'crm_desk',
            'crm_view_all_leads',
            'crm_view_all_orders',
            'crm_approve_payments',
            'crm_approve_commissions',
            'crm_view_sales_financial_reports',
            'crm_view_submitted_reports',
            'crm_view_team_performance',
            'crm_view_messages',
            'crm_send_messages',
            'crm_add_notes',
        ],
        'super_admin' => ['*'],
    ],
];
