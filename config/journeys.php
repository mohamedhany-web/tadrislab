<?php

/**
 * TADRIS LAB — core User Journeys (Brief V3).
 * status: works | partial | deferred
 * MVP does not require every deferred step (e.g. institution Report).
 */
return [

    'buy_learning_path' => [
        'label_ar' => 'شراء مسار',
        'label_en' => 'Buy Learning Path',
        'steps' => [
            ['key' => 'path', 'label' => 'Path', 'route' => 'public.learning-paths.index', 'status' => 'works'],
            ['key' => 'details', 'label' => 'Details', 'route' => 'public.learning-paths.show', 'status' => 'works'],
            ['key' => 'checkout', 'label' => 'Checkout', 'route' => 'public.learning-paths.checkout', 'status' => 'works'],
            ['key' => 'payment', 'label' => 'Payment', 'route' => null, 'status' => 'works'],
            ['key' => 'webhook', 'label' => 'Webhook', 'route' => null, 'status' => 'works'],
            ['key' => 'order', 'label' => 'Order', 'route' => 'admin.orders.index', 'status' => 'works'],
            ['key' => 'entitlement', 'label' => 'Entitlement', 'route' => null, 'status' => 'works'],
            ['key' => 'access', 'label' => 'Access', 'route' => 'student.learning-paths.index', 'status' => 'works'],
            ['key' => 'notify', 'label' => 'WhatsApp/Email', 'route' => null, 'status' => 'works'],
        ],
    ],

    'buy_package' => [
        'label_ar' => 'شراء باقة',
        'label_en' => 'Buy Package',
        'steps' => [
            ['key' => 'package', 'label' => 'Package', 'route' => 'public.pricing', 'status' => 'works'],
            ['key' => 'checkout', 'label' => 'Checkout', 'route' => 'public.packages.checkout', 'status' => 'works'],
            ['key' => 'payment', 'label' => 'Payment', 'route' => null, 'status' => 'works'],
            ['key' => 'entitlement', 'label' => 'Entitlement/Subscription', 'route' => null, 'status' => 'works'],
            ['key' => 'activate_services', 'label' => 'Activate included services', 'route' => null, 'status' => 'works'],
            ['key' => 'notify', 'label' => 'WhatsApp/Email', 'route' => null, 'status' => 'works'],
        ],
    ],

    'book_consultation' => [
        'label_ar' => 'حجز استشارة',
        'label_en' => 'Book Consultation',
        'steps' => [
            ['key' => 'consultation', 'label' => 'Consultation', 'route' => 'public.consultations.book', 'status' => 'works'],
            ['key' => 'availability', 'label' => 'Availability', 'route' => null, 'status' => 'works'],
            ['key' => 'booking', 'label' => 'Booking', 'route' => 'public.consultations.book.store', 'status' => 'works'],
            ['key' => 'payment', 'label' => 'Payment', 'route' => null, 'status' => 'works'],
            ['key' => 'confirmation', 'label' => 'Confirmation', 'route' => null, 'status' => 'works'],
            ['key' => 'reminder', 'label' => 'Reminder', 'route' => null, 'status' => 'works'],
            ['key' => 'session', 'label' => 'Session', 'route' => null, 'status' => 'works'],
            ['key' => 'completed', 'label' => 'Completed', 'route' => null, 'status' => 'works'],
        ],
    ],

    'school_training' => [
        'label_ar' => 'تدريب مدرسة/مؤسسة',
        'label_en' => 'School/Institution Training',
        'steps' => [
            ['key' => 'institution', 'label' => 'School/Institution', 'route' => 'public.institutions.inquiry', 'status' => 'works'],
            ['key' => 'program_inquiry', 'label' => 'Program/Inquiry', 'route' => null, 'status' => 'works'],
            ['key' => 'proposal', 'label' => 'Proposal', 'route' => 'admin.institution-programs.index', 'status' => 'works'],
            ['key' => 'approval', 'label' => 'Approval', 'route' => null, 'status' => 'works'],
            ['key' => 'payment', 'label' => 'Payment', 'route' => null, 'status' => 'deferred'],
            ['key' => 'participants', 'label' => 'Participants', 'route' => null, 'status' => 'works'],
            ['key' => 'training', 'label' => 'Training', 'route' => null, 'status' => 'partial'],
            ['key' => 'progress', 'label' => 'Progress', 'route' => null, 'status' => 'works'],
            ['key' => 'report', 'label' => 'Report', 'route' => null, 'status' => 'deferred'],
        ],
    ],

    'institutional_development' => [
        'label_ar' => 'تطوير مؤسسي',
        'label_en' => 'Institutional Development',
        'steps' => [
            ['key' => 'institution', 'label' => 'School/Institution', 'route' => 'public.institutions.inquiry', 'status' => 'works'],
            ['key' => 'inquiry', 'label' => 'Inquiry', 'route' => null, 'status' => 'works'],
            ['key' => 'needs_assessment', 'label' => 'Needs Assessment', 'route' => null, 'status' => 'partial'],
            ['key' => 'proposal', 'label' => 'Proposal', 'route' => null, 'status' => 'partial'],
            ['key' => 'approval', 'label' => 'Approval', 'route' => null, 'status' => 'works'],
            ['key' => 'project', 'label' => 'Project', 'route' => null, 'status' => 'partial'],
            ['key' => 'delivery', 'label' => 'Delivery', 'route' => null, 'status' => 'partial'],
            ['key' => 'evaluation', 'label' => 'Evaluation', 'route' => null, 'status' => 'deferred'],
        ],
    ],

    'inquiry' => [
        'label_ar' => 'استفسار',
        'label_en' => 'Inquiry',
        'steps' => [
            ['key' => 'visitor', 'label' => 'User/Visitor', 'route' => 'public.contact', 'status' => 'works'],
            ['key' => 'channel', 'label' => 'WhatsApp/Contact', 'route' => 'admin.inquiries.create', 'status' => 'works'],
            ['key' => 'context', 'label' => 'Inquiry Context', 'route' => null, 'status' => 'works'],
            ['key' => 'follow_up', 'label' => 'Admin Follow-up', 'route' => 'admin.inquiries.index', 'status' => 'works'],
            ['key' => 'resolved', 'label' => 'Resolved', 'route' => null, 'status' => 'works'],
        ],
    ],
];
