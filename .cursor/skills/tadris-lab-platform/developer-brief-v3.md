---
# Developer Brief V3 — التصور الوظيفي الداخلي | TADRIS LAB

**Status:** Source of truth for internal functional architecture (saved 2026-09-11).  
**Architecture decision:** Custom Modular Platform (not a content-only site, not a locked generic SaaS).  
**Related:** [mvp-scope.md](mvp-scope.md) · runtime `config/platform.php`

---

## 1. Document purpose

This is the **initial functional vision** the developer builds against.

- Goal is **not** a content marketing site only.
- Goal is a **dedicated modular platform nucleus** that can grow: services, paths, packages, schools/institutions, and future integrations **without rebuilding the system**.
- Phase 1 = clear, limited **MVP**; architecture must **not** constrain platform growth.

---

## 2. Primary commercial / functional structure

Exactly **five pillars** (keep simple and clear):

| Pillar | EN | AR |
|--------|----|----|
| `learning_paths` | Learning Paths | المسارات التعليمية |
| `consultations` | Consultations | الاستشارات |
| `schools_institutions` | Schools & Institutions | المدارس والمؤسسات |
| `packages` | Packages | الباقات |
| `tools_resources` | Tools & Resources | الأدوات والموارد |

**Rule:** Schools and institutions are **one** commercial axis (`schools_institutions`). Do **not** create separate commercial branches for “schools” vs “institutions”.

---

## 3. Learning Paths | المسارات التعليمية

- Core educational products for teachers/individuals.
- Sellable **standalone** or **included in a package**.

### Example path catalogue (admin-managed, not hard-coded)

| Key | EN | AR |
|-----|----|----|
| `classroom_management` | Classroom Management | إدارة الصف |
| `teaching_strategies` | Teaching Strategies | استراتيجيات التدريس |
| `lesson_time_management` | Lesson Time Management | إدارة وقت الحصة |
| `lesson_planning` | Lesson Planning | التخطيط للدرس |
| `assessment` | Assessment | التقويم وقياس التعلم |
| `teacher_skills` | Teacher Skills | مهارات المعلم |
| `professional_development` | Professional Development | التطوير المهني للمعلم |

### Internal path structure

```
Path
  → Modules / Units
    → Lessons / Content
    → Practical Tools (as needed)
    → Activity / Assessment (as needed)
  → Progress & Completion
```

**Admin must:** create, edit, reorder paths, modules, lessons, tools; link them to products/packages.

---

## 4. Consultations | الاستشارات

- **Independent** service area — **not** part of learning paths.
- Individual teacher consultation  
- Future: Coaching / Mentoring  
- Specialized educational consultations  
- School/institution consultations when needed  

### Consultation journey

1. Choose consultation type  
2. Show service, duration, price  
3. Choose available slot  
4. Enter required data  
5. Payment  
6. Booking confirmation  
7. Send appointment details via WhatsApp / Email  
8. Deliver session  
9. Record outcome / recommendations when needed  

### Booking statuses

`New` → `Confirmed` → `Rescheduled` → `Completed` → `Cancelled`

---

## 5. Schools & Institutions | المدارس والمؤسسات

One axis for all institutional education clients: school, center, educational institution/entity.

### Services under this pillar

| Key | EN | AR |
|-----|----|----|
| `school_institutional_training` | School & Institutional Training | تدريب المدارس والمؤسسات |
| `pd_programs` | Professional Development Programs | برامج التطوير المهني |
| `workshops` | Workshops | ورش العمل |
| `customized_training` | Customized Training | التدريب المخصص حسب الاحتياج |
| `educational_leadership_training` | Educational Leadership Training | تدريب القيادات التعليمية |
| `needs_assessment` | Needs Assessment | تحليل الاحتياج عند الحاجة |
| `institutional_development` | Institutional Development | التطوير المؤسسي |
| `followup_evaluation` | Follow-up & Evaluation | المتابعة والتقييم |

### 5.1 Internal model

```
Institution/School Account
  → Coordinator/Admin
  → Teachers/Participants
  → Programs
  → Training/Development
  → Progress
  → Reports
```

Capabilities: org account · coordinator · add/link teachers · assign to programs · track participation · basic reports.

### 5.2 Institutional training flow

Ready-made or customized program:

Determine program → participants count → duration/delivery mode → pricing → schedule → add participants → deliver → close & record outcome.

**Statuses:** `Inquiry` → `Proposal` → `Approved` → `Scheduled` → `In Progress` → `Completed`

### 5.3 Institutional development

Broader than training — custom project:

Needs diagnosis → practice analysis → teacher performance → leadership development → improvement plan → programs/consultations → follow-up & evaluation.

**Not required** to be a direct path checkout like buying a learning path. May start as **Inquiry → Proposal → Approval → Payment** per agreement.

---

## 6. Packages | الباقات

Packages bundle products, services, and entitlements into one offer.

### Types

| Key | Label |
|-----|--------|
| `free` | Free Package |
| `individual` | Individual Package |
| `advanced` | Advanced Package |
| `school_institution` | School & Institution Package |
| `custom` | Custom Package (when needed) |

### Admin-configurable fields

- Name & description  
- Price & currency  
- Validity / subscription period  
- Included learning paths  
- Tools & resources  
- Consultation session count (if any)  
- Participant/user seats (institutional)  
- Benefits & discounts  
- Package status  

**Critical:** Packages are **not hard-coded** — create/edit from admin.

---

## 7. Tools & Resources | الأدوات والموارد

May belong to paths, packages, or become standalone products later.

Types: Templates · Checklists · Classroom Tools · Planning Tools · Assessment Resources · Downloadable Resources  

**Design as manageable entities**, not static files in code.

---

## 8. Users & Roles

### Client kinds

| Kind | Meaning |
|------|---------|
| `individual_user` | Teacher/practitioner using the platform personally |
| `institution_user` | User under an org account; permissions set by org admin |

### Core roles (RBAC, expandable)

| Role | Notes |
|------|--------|
| `platform_admin` | Platform Admin |
| `institution_admin` | Institution/School Admin or Coordinator |
| `teacher_participant` | Teacher / Participant (under institution) |
| `individual_user` | Individual User |

---

## 9. Payment & Checkout

Real payment system tied to **backend** (not a fake pay page).

### Flow

Select path/package/consultation/service → Checkout → Payment Gateway → Backend Verification + Webhook → Create Order/Transaction → Activate entitlement or booking → Send Confirmation.

### Requirements

- Gateway suitable for **Qatar & GCC**  
- Support methods the gateway offers (e.g. Visa/Mastercard, Apple Pay when available)  
- Study **Mada** if KSA is in scope  
- Recurring payments if subscriptions adopted  
- Refunds when needed  
- Handle Failed / Cancelled payments  
- Link every payment to user/org + order  
- Gateway must be **swappable** later  

---

## 10. WhatsApp Integration

Operational system component — **not** just a chat button.

| Area | Messages |
|------|----------|
| Payment | Success: confirm + service/package + order id + access link; Failure: notice + retry when available |
| Consultations | Booking confirm · date/time · session details/link · pre-session reminder |
| Schools & Institutions | Order/program confirm when needed · program status updates as admin decides |
| Inquiries | Clear WhatsApp CTA · ask about paths/packages/consultations/payment/tech/institutional · pass context (order id / service type) when possible |

Integration must be **provider-swappable** (API vendor changeable).

---

## 11. Inquiries | الاستفسارات

Manage inquiries internally even if conversation starts on WhatsApp.

### Fields

Name & contact · inquiry type · User ID if registered · Order/Booking/Institution ID when linked · date · status · admin notes  

### Statuses

`New` → `In Progress` → `Resolved`

### Types

`Learning Path` · `Package` · `Consultation` · `Payment` · `Technical` · `School & Institution` · `General`

---

## 12. Notification System

Central **Notification Layer** driven by domain **Events**.

### Initial events

- Payment Successful / Failed  
- Booking Confirmed / Reminder  
- Order Status Changed  
- Access/Subscription Activated  
- New Inquiry  
- Institution/School Program Status Changed  

### Channels (phase 1)

WhatsApp + Email  

Later: SMS, Push — architecture should allow adding channels.

---

## 13. Admin Dashboard

Must manage most entities **without code changes**:

Dashboard & Analytics · Users · Schools & Institutions · Learning Paths · Content & Tools · Consultations & Bookings · Training Programs · Institutional Development · Packages & Products · Orders · Payments/Transactions · Coupons · Inquiries · Notifications · Settings  

---

## 14. Data model (expansion-ready)

Intent: a model that **allows growth**. Not every entity is mandatory in MVP if not required.

```
User → Orders / Bookings / Progress / Inquiries
School/Institution → Admins/Coordinators / Teachers / Programs / Orders / Reports
Product/Service → Learning Path | Consultation | Training Program | Institutional Development | Resource
Package → Products/Services + Entitlements
Order → User | School/Institution + Product/Package + Payment
Payment/Transaction → Order + Gateway Response + Webhook Status
Booking → User/Institution + Consultation + Time Slot + Order
Training Program → School/Institution + Participants + Schedule + Progress
Inquiry → User/Institution + Order/Booking/Service (when linked)
Notification → Event + Recipient + Channel + Delivery Status
```

---

## 15. Core user journeys

1. **Buy path:** Path → Details → Checkout → Payment → Webhook → Order → Entitlement → Access → WhatsApp/Email  
2. **Buy package:** Package → Checkout → Payment → Entitlement/Subscription → activate included services  
3. **Book consultation:** Consultation → Availability → Booking → Payment → Confirmation → Reminder → Session → Completed  
4. **School training:** School/Institution → Program/Inquiry → Proposal → Approval → Payment (as needed) → Participants → Training → Progress → Report  
5. **Institutional development:** School/Institution → Inquiry → Needs Assessment → Proposal → Approval → Project → Delivery → Evaluation  
6. **Inquiry:** User/Visitor → WhatsApp/Contact → Inquiry Context → Admin Follow-up → Resolved  

---

## 16. Must remain expandable

Add paths · modules/lessons/tools · products/services · packages/prices · consultation types · school training programs · orgs & participants · notification channels · swap payment gateway · expand roles/permissions  

**Forbidden:** hard-coding products/packages/paths/services so every small change needs a developer.

---

## 17. Ownership (contractual + technical)

Because this is a **Custom Modular Platform**, settle clearly:

- User/content/order/payment data belongs to **TADRIS LAB** per contract  
- Usage/operation rights of the system  
- Source code & repository access posture  
- Ability to hand project to another developer  
- Hosting, backups, maintenance, future costs  
- Third-party integrations and who pays their fees  

---

## 18. MVP boundaries (sellable runnable nucleus)

**In MVP:**

- Accounts + Roles  
- Learning Paths + Content  
- Products / Packages  
- Checkout + Payment + Webhooks  
- Orders / Transactions  
- Consultations + Booking  
- Core WhatsApp/Email notifications  
- WhatsApp / Inquiry entry  
- Schools & Institutions: basic account + participant management + core programs  
- Admin Dashboard  

**Later (architecture must not block):** advanced reports, advanced automation, complex institutional development, mobile apps.

---

## 19. Decisions to lock with developer before build / first invoice

1. Adopt Custom Modular Platform  
2. Adopt five pillars (paths, consultations, schools & institutions, packages, tools)  
3. Confirm schools & institutions = **one** admin/architecture axis  
4. Lock MVP in vs deferred  
5. Tech stack + architecture  
6. Data model + permissions  
7. Payment gateway for Qatar/GCC  
8. Webhooks + post-payment access activation  
9. WhatsApp integration + API provider  
10. Inquiry management mechanism  
11. What is fully admin-manageable  
12. Data/code/repo ownership  
13. Hosting, backup, maintenance  
14. Future external service costs  
15. Fix all of the above in **Scope/Contract** before first payment  

---

## 20. Executive direction to the developer

Build TADRIS LAB as a **dedicated modular custom system**, not a giant phase-1 platform and not a closed generic SaaS.

Commercial spine: **Learning Paths · Consultations · Schools & Institutions · Packages · Tools & Resources**.

Schools & Institutions = one pillar (training, institutional development, consultations, custom programs underneath).

Products, services, packages, and entitlements = **admin-managed**.

Payment = backend + webhooks. WhatsApp = part of notifications + inquiries ops.

---

## Related prior brief

Teacher Assistant (AI-ready lesson helper) from the earlier product save remains a **candidate product/tool** — design AI-ready if built; it is **not** a sixth commercial pillar in V3. Prefer placing it under Tools/Resources or as a path-linked tool until product decides otherwise.
