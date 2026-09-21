# User Journeys | رحلات المستخدم الأساسية

Source of truth for **runtime mapping**: `config/journeys.php` + `App\Support\UserJourneys`.

Brief V3 journeys (do not invent parallel funnels):

| Key | AR | Flow |
|-----|----|------|
| `buy_learning_path` | شراء مسار | Path → Details → Checkout → Payment → Webhook → Order → Entitlement → Access → Notify |
| `buy_package` | شراء باقة | Package → Checkout → Payment → Entitlement/Subscription → Activate included services |
| `book_consultation` | حجز استشارة | Consultation → Availability → Booking → Payment → Confirmation → Reminder → Session → Completed |
| `school_training` | تدريب مدرسة/مؤسسة | Institution → Program/Inquiry → Proposal → Approval → Payment → Participants → Training → Progress → Report |
| `institutional_development` | تطوير مؤسسي | Institution → Inquiry → Needs Assessment → Proposal → Approval → Project → Delivery → Evaluation |
| `inquiry` | استفسار | Visitor → WhatsApp/Contact → Context → Admin Follow-up → Resolved |

## Status labels (per step)

- **works** — wired end-to-end for MVP
- **partial** — usable; some UX/automation incomplete
- **deferred** — architecture allows; do **not** build in MVP (e.g. institution Report, evaluation)

## Implementation map (buy path / package)

| Step | Code |
|------|------|
| Catalogue | `public.learning-paths.*`, `public.pricing`, `public.package.show` |
| Checkout | `CatalogCheckoutController` · routes `public.learning-paths.checkout(.store)`, `public.packages.checkout(.store)` |
| Payment | Kashier / PayPal / bank_transfer · `PayPalCheckoutController` · Kashier callback |
| Order approve | `CheckoutController::approveOrderAfterOnlinePayment` · admin `OrderController::approve` |
| Fulfill | `CatalogOrderFulfillmentService` → `PackageEntitlementService` / `LearningPathAccessService` |
| Notify | `AccessSubscriptionActivated` · `OrderStatusChanged` → WhatsApp/Email |

## Consultation / Institution / Inquiry

- Consultation: public book + **online PayPal/Kashier or bank Order** → `ConsultationOrderFulfillmentService` marks paid; admin confirm + `bookings:send-reminders`; join for confirmed/rescheduled; room end → completed; package session restore on cancel.
- Institution: inquiry → program + Inquiry link; coordinator accept/reject proposal; enroll + progress on `/institution`; training delivery still metadata (**partial**).
- Instructor: paths/consultations scoped; tutoring/live 404 when `instructor_ui` flags off.
- School training / institutional development: inquiry → admin program/status; payment & report **deferred**.
- Inquiry: contact / WhatsApp admin entry / institution form → `InquiryService` → admin CRUD.

## Agent rules

1. Prefer extending these six journeys; do not add a seventh commercial funnel without Brief update.
2. When fixing a journey gap, update `config/journeys.php` step `status` and this file if the map changes.
3. Do not implement deferred institution Report/evaluation modules unless explicitly requested.
