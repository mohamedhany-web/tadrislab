---
# TADRIS LAB — MVP product blueprint (source of truth)

Saved from product brief. Phase 1 = essentials only; no unnecessary features.
Architecture: **Custom Modular Platform** — content managed from admin, not hard-coded.

## Goal

Flexible, scalable educational platform. Start with core functions. Modular structure; separate content from system logic. Clear runnable MVP; advanced features later.

## Phase-1 modules

### 1. Teacher Learning Paths | المسارات التعليمية للمعلم

| Path key | EN | AR |
|----------|----|----|
| `classroom_management` | Classroom Management | إدارة الصف |
| `lesson_planning` | Lesson Planning | التخطيط للحصة |
| `teaching_strategies` | Teaching Strategies | استراتيجيات التدريس |
| `lesson_time_management` | Lesson Time Management | إدارة وقت الحصة |
| `assessment` | Assessment | التقويم وقياس التعلم |
| `teacher_pd` | Teacher Professional Development | التطوير المهني للمعلم |

Content for paths must be **admin-manageable** (no hard-code of path catalogue in views).

### 2. Teacher Assistant | مساعد المعلم

- Teacher enters **lesson name**; optionally **subject** and **grade**.
- Outputs practical classroom delivery ideas:
  - تهيئة
  - فكرة افتتاح الدرس
  - استراتيجية مناسبة
  - نشاط تفاعلي
  - أسئلة للتحقق من الفهم
  - فكرة للتقويم
  - خاتمة للدرس
- Design **AI-ready from day one** (stable I/O contract / service boundary) so an AI engine can be plugged later **without rebuilding the platform**.

### 3. Schools & Institutions | المدارس والمؤسسات

- Training | التدريب  
- Professional Development | التطوير المهني  
- Institutional Development | التطوير المؤسسي  

### 4. Consultations | الاستشارات

- Teacher consultation | استشارة للمعلم  
- School/institution consultation | استشارة للمدرسة/المؤسسة  

### 5. Packages | الباقات

- Individual | باقة فردية  
- Schools & institutions | باقة مدارس ومؤسسات  
- Custom | باقة مخصصة  

### 6. Tools & Resources | الأدوات والموارد

- Applied teacher tools | أدوات تطبيقية للمعلم  
- Templates & reusable resources | نماذج وقوالب وموارد قابلة للاستخدام  

## Users & roles

| Role key | Label | Notes |
|----------|-------|-------|
| `teacher` | معلم / مستخدم فردي | Primary end user |
| `institution_rep` | ممثل مدرسة أو مؤسسة | B2B contact |
| `admin` | مدير النظام | Content, services, permissions via dashboard |

## Payment

- Checkout + Payment Gateway  
- Backend verification / Webhook  
- Order / Transaction  
- Unlock content or booking **after successful payment**  
- Notifications on payment success/failure  

## WhatsApp & communication

- Payment & booking confirmations  
- Consultation reminders & follow-up  
- Schools/institutions updates  
- Inbound inquiries  

## Admin dashboard

- Users  
- Learning paths & content  
- Teacher Assistant content/settings  
- Consultations, schools/institutions, packages  
- Orders & payments  
- Inquiries & notifications  
- Basic statistics  

## Technical principles

1. **No hard-code** of paths, packages, or services in UI — admin-managed.  
2. **Modular** — expandable for future paths, services, tools, features.  
3. **Separate content from system logic** as far as practical.  
4. Phase 1 = **MVP only**; defer advanced features.  
5. Stable core + ability to add content/services/paths/packages later.

## Mapping to existing loop

| Loop step | Served by |
|-----------|-----------|
| تشخيص | Assessment path + consultations + Teacher Assistant prompts |
| وصول | Paths + tools/resources + Teacher Assistant outputs |
| تطوير | Paths (PD + strategies…) + school training/PD |
| قياس | Assessment path + progress after purchase/access |

## Out of MVP (do not build in phase 1 unless explicitly requested)

- Full AI engine (only the **interface/contract** for Teacher Assistant)
- Live classroom / tutoring marketplace leftovers
- CRM sales desk, parent progress, language-student journeys
- Advanced analytics beyond basic admin stats
