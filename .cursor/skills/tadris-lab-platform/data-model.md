<?php

/**
 * TADRIS LAB — expandable Data Model (Brief V3).
 *
 * Runtime registry: config/platform.php → data_model (+ App\Support\DataModel).
 * This file is the human map. MVP does NOT require implementing every deferred box.
 */

## Core principle

وجود نموذج يسمح بالتوسع ≠ إلزام تنفيذ كل الكيانات في الـ MVP.

| Layer | Meaning |
|-------|---------|
| **mvp_required** | نواة المرحلة الأولى — جداول + CRUD/تدفقات أساسية موجودة أو قيد الاستخدام |
| **expansion stub** | أعمدة/علاقات nullable أو سجل توصيل — بدون إجبار مسار منتج كامل |
| **deferred** | مذكور في الرؤية فقط (تقارير مؤسسات، جدول Product موحّد، …) |

## Entity map

```
User ──────────────┬── Orders
                   ├── Bookings (ConsultationRequest)
                   ├── Progress (path enrollments / items)
                   └── Inquiries

Institution ───────┬── Members (coordinator / participant)
                   ├── Programs (training | development)
                   ├── Orders (institution_id — expansion)
                   └── Inquiries

Product/Service (conceptual union; separate tables in MVP)
  ├── LearningPath
  ├── ConsultationService
  ├── InstitutionProgram (training / institutional)
  └── TeacherTool (resource)

Package ── pivots ── LearningPath / TeacherTool
        └── UserPackageEntitlement (+ optional order_id)

Order ── User and/or Institution
      ├── Package / legacy product FKs
      └── Payment → Transaction (gateway_response)

Booking (ConsultationRequest)
  ├── User (student) / Institution (optional)
  ├── ConsultationService
  ├── Time slot (preferred_slot_at / scheduled_at)
  └── Order (optional FK)

Inquiry ── User / Institution + Order / Booking when linked

Notification
  ├── In-app Notification (recipient + read)
  └── NotificationDelivery (event + channel + status) ← ledger
```

## Morph aliases (registered in AppServiceProvider)

`user`, `institution`, `order`, `learning_path`, `consultation_service`,
`package`, `teacher_tool`, `institution_program`, `inquiry`

Reserved for future polymorphic purchasable / notifiable links — not required for MVP checkout.
