<?php

/**
 * TADRIS LAB — Notification Layer (Brief V3 §12).
 * Channels MVP: WhatsApp + Email. SMS / Push reserved for later.
 */
return [

    'enabled' => env('TADRIS_NOTIFICATIONS_ENABLED', true),

    /*
    | Active channels now. Later: sms, push (see platform.notifications.channels_later).
    */
    'channels' => array_values(array_filter(
        explode(',', (string) env('TADRIS_NOTIFICATION_CHANNELS', 'whatsapp,email'))
    )) ?: ['whatsapp', 'email'],

    /*
    | Minutes before a confirmed consultation to send Booking Reminder.
    */
    'booking_reminder_minutes' => (int) env('TADRIS_BOOKING_REMINDER_MINUTES', 60),

    'events' => [
        'payment_successful' => [
            'subject' => 'تم الدفع بنجاح — تدريس لاب',
            'body' => "مرحباً :name،\nتم استلام دفعتك بنجاح.\nالطلب: #:order_id\nالمبلغ: :amount :currency\n\nتدريس لاب | TADRIS LAB",
        ],
        'payment_failed' => [
            'subject' => 'تعذّر إتمام الدفع — تدريس لاب',
            'body' => "مرحباً :name،\nلم يكتمل الدفع للطلب #:order_id.\n:reason\nيمكنك المحاولة مرة أخرى من حسابك.\n\nتدريس لاب | TADRIS LAB",
        ],
        'booking_confirmed' => [
            'subject' => 'تأكيد حجز الاستشارة — تدريس لاب',
            'body' => "تم تأكيد حجز استشارتك: :service\nالموعد: :when\n:join_line\nتدريس لاب | TADRIS LAB",
        ],
        'booking_reminder' => [
            'subject' => 'تذكير بموعد الاستشارة — تدريس لاب',
            'body' => "تذكير: استشارتك «:service» بعد قليل.\nالموعد: :when\n:join_line\nتدريس لاب | TADRIS LAB",
        ],
        'order_status_changed' => [
            'subject' => 'تحديث حالة الطلب — تدريس لاب',
            'body' => "مرحباً :name،\nتم تحديث حالة طلبك #:order_id إلى: :status_label.\n:extra\n\nتدريس لاب | TADRIS LAB",
        ],
        'access_subscription_activated' => [
            'subject' => 'تم تفعيل اشتراكك — تدريس لاب',
            'body' => "مرحباً :name،\nتم تفعيل وصولك: :product\n:extra\n\nتدريس لاب | TADRIS LAB",
        ],
        'new_inquiry' => [
            'subject' => 'استفسار جديد — تدريس لاب',
            'body' => "استفسار جديد :reference\nالنوع: :type\nالاسم: :name\nالهاتف: :phone\nالبريد: :email\nالمصدر: :source\n:message\n\nتدريس لاب | TADRIS LAB",
        ],
        'institution_program_status_changed' => [
            'subject' => 'تحديث برنامج مؤسسة — تدريس لاب',
            'body' => "مرحباً :name،\nتم تحديث حالة البرنامج «:program» إلى: :status_label.\nالمؤسسة: :institution\n\nتدريس لاب | TADRIS LAB",
        ],
    ],
];
